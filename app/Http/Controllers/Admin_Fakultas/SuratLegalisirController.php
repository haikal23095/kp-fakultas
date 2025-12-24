<?php

namespace App\Http\Controllers\Admin_Fakultas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SuratLegalisir;
use App\Models\Notifikasi;
use App\Models\PegawaiFakultas;
use App\Models\Pejabat;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SuratLegalisirController extends Controller
{
    /**
     * Menampilkan daftar pengajuan legalisir
     */
    public function index()
    {
        $this->checkAccess();

        $daftarSurat = SuratLegalisir::with(['tugasSurat', 'user.mahasiswa'])
            ->orderBy('id_no', 'desc')
            ->get();

        return view('admin_fakultas.list_legalisir', compact('daftarSurat'));
    }

    /**
     * Helper untuk mengecek apakah user adalah Pegawai Fakultas
     */
    private function checkAccess()
    {
        $user = Auth::user();
        // Cek apakah user memiliki relasi pegawaiFakultas
        // Asumsi relasi 'pegawaiFakultas' ada di model User
        if (!$user->pegawaiFakultas) {
            abort(403, 'Unauthorized. Hanya Pegawai Fakultas yang dapat mengakses fitur ini.');
        }
    }

    /**
     * 1. Verifikasi Berkas (Pending -> Menunggu Pembayaran)
     * Admin memeriksa berkas fisik/upload, jika oke, lanjut ke pembayaran.
     */
    public function verifikasi(Request $request, $id)
    {
        $this->checkAccess();

        $surat = SuratLegalisir::with('tugasSurat')->findOrFail($id);

        // Validasi status awal
        if ($surat->Status !== 'pending') {
            return redirect()->back()->with('error', 'Status surat tidak valid untuk verifikasi (Harus Pending).');
        }

        // Update Status
        $surat->Status = 'menunggu_pembayaran';
        $surat->save();

        // Update Status Global
        $surat->tugasSurat->Status = 'Proses';
        $surat->tugasSurat->save();

        // Kirim Notifikasi ke Mahasiswa
        Notifikasi::create([
            'Tipe_Notifikasi' => 'Caution', // Kuning/Info
            'Pesan' => 'Berkas legalisir Anda telah diverifikasi. Silakan lakukan pembayaran di loket fakultas.',
            'Dest_user' => $surat->Id_User,
            'Source_User' => Auth::id(),
            'Is_Read' => false,
            'Data_Tambahan' => ['id_surat' => $surat->id_no, 'type' => 'legalisir']
        ]);

        return redirect()->back()->with('success', 'Berkas diverifikasi. Menunggu pembayaran mahasiswa.');
    }

    /**
     * 2. Konfirmasi Pembayaran (Menunggu Pembayaran -> Pembayaran Lunas)
     * Admin menerima uang tunai dan menginput biaya.
     */
    public function konfirmasiPembayaran(Request $request, $id)
    {
        $this->checkAccess();

        $request->validate([
            'biaya' => 'required|integer|min:0',
        ], [
            'biaya.required' => 'Biaya wajib diisi.',
            'biaya.integer' => 'Biaya harus berupa angka.',
        ]);

        $surat = SuratLegalisir::with('tugasSurat')->findOrFail($id);

        // Validasi status awal
        if ($surat->Status !== 'menunggu_pembayaran') {
            return redirect()->back()->with('error', 'Status surat belum siap untuk pembayaran.');
        }

        // Update Data
        $surat->Status = 'pembayaran_lunas';
        $surat->Biaya = $request->biaya;
        $surat->save();

        // Kirim Notifikasi ke Mahasiswa
        Notifikasi::create([
            'Tipe_Notifikasi' => 'Accepted', // Hijau/Positif
            'Pesan' => 'Pembayaran sebesar Rp' . number_format($request->biaya, 0, ',', '.') . ' telah diterima. Berkas sedang diproses untuk stempel/paraf.',
            'Dest_user' => $surat->Id_User,
            'Source_User' => Auth::id(),
            'Is_Read' => false,
            'Data_Tambahan' => ['id_surat' => $surat->id_no, 'type' => 'legalisir']
        ]);

        return redirect()->back()->with('success', 'Pembayaran dikonfirmasi. Status berubah menjadi Lunas.');
    }

    /**
     * 3. Update Progress (Menggerakkan status sesuai alur)
     * Menangani transisi status:
     * - pembayaran_lunas -> proses_stempel_paraf
     * - proses_stempel_paraf -> menunggu_ttd_pimpinan
     * - menunggu_ttd_pimpinan -> siap_diambil
     * - siap_diambil -> selesai
     */
    public function updateProgress(Request $request, $id)
    {
        $this->checkAccess();

        $surat = SuratLegalisir::with('tugasSurat')->findOrFail($id);
        $statusSekarang = $surat->Status;
        $pesanNotifikasi = '';
        $tipeNotifikasi = 'Caution';

        switch ($statusSekarang) {
            case 'pembayaran_lunas':
                // Transisi ke: proses_stempel_paraf
                // Aksi: Admin memberikan Nomor Surat (opsional di sini atau di step sebelumnya)
                $surat->Status = 'proses_stempel_paraf';
                $pesanNotifikasi = 'Berkas Anda sedang dalam proses penomoran dan stempel/paraf.';
                break;

            case 'proses_stempel_paraf':
                // Transisi ke: menunggu_ttd_pimpinan
                // Aksi: Admin memilih Pejabat (Dekan) untuk TTD
                // Cari Pejabat Dekan (Misal ID Jabatan Dekan = 3 atau sesuai data seed)
                $pejabatDekan = Pejabat::where('Nama_Jabatan', 'Dekan')->first();
                if ($pejabatDekan) {
                    $surat->Id_Pejabat = $pejabatDekan->Id_Pejabat;
                }
                
                $surat->Status = 'menunggu_ttd_pimpinan';
                $surat->tugasSurat->Status = 'Diajukan ke Dekan'; // Update status global
                $surat->tugasSurat->save();
                
                $pesanNotifikasi = 'Berkas Anda sedang menunggu tanda tangan pimpinan (Dekan).';
                break;

            case 'menunggu_ttd_pimpinan':
                // Transisi ke: siap_diambil
                // Aksi: Admin memverifikasi bahwa TTD sudah selesai dan stempel basah sudah ada
                $surat->Status = 'siap_diambil';
                $surat->tugasSurat->Status = 'Telah Ditandatangani Dekan'; // Update status global
                $surat->tugasSurat->save();

                $pesanNotifikasi = 'Legalisir Anda telah selesai dan SIAP DIAMBIL di loket fakultas.';
                $tipeNotifikasi = 'Accepted'; // Hijau
                break;

            case 'siap_diambil':
                // Transisi ke: selesai
                // Aksi: Alumni/Mahasiswa mengambil berkas fisik
                $surat->Status = 'selesai';
                $surat->tugasSurat->Status = 'Selesai'; // Update status global
                $surat->tugasSurat->Tanggal_Diselesaikan = Carbon::now();
                $surat->tugasSurat->save();

                $pesanNotifikasi = 'Proses legalisir selesai. Terima kasih.';
                $tipeNotifikasi = 'Accepted';
                break;

            default:
                return redirect()->back()->with('error', 'Status saat ini tidak memungkinkan untuk update progress otomatis.');
        }

        $surat->save();

        // Kirim Notifikasi
        if ($pesanNotifikasi) {
            Notifikasi::create([
                'Tipe_Notifikasi' => $tipeNotifikasi,
                'Pesan' => $pesanNotifikasi,
                'Dest_user' => $surat->Id_User,
                'Source_User' => Auth::id(),
                'Is_Read' => false,
                'Data_Tambahan' => ['id_surat' => $surat->id_no, 'type' => 'legalisir']
            ]);
        }

        return redirect()->back()->with('success', 'Status berhasil diperbarui ke: ' . str_replace('_', ' ', ucfirst($surat->Status)));
    }
}
