<?php

namespace App\Http\Controllers\Admin_Fakultas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SuratLegalisir;
use App\Models\TugasSurat;
use App\Models\JenisSurat;
use App\Models\Mahasiswa;
use App\Models\Notifikasi;
use App\Models\Pejabat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SuratLegalisirController extends Controller
{
    /**
     * Pastikan hanya Role 5 dan 7 yang bisa akses
     */
    private function checkAccess()
    {
        if (!in_array(Auth::user()->Id_Role, [5, 7])) {
            abort(403, 'ANDA TIDAK MEMILIKI HAK AKSES KE FITUR INI.');
        }
    }

    /**
     * Menampilkan daftar pengajuan dengan Eager Loading agar tidak error "property on null"
     */
    public function index()
    {
        $this->checkAccess();

        // Mengambil data dengan relasi user dan mahasiswa sekaligus
        $daftarSurat = SuratLegalisir::with(['user.mahasiswa', 'tugasSurat'])
            ->orderBy('id_no', 'desc')
            ->get();

        return view('admin_fakultas.list_legalisir', compact('daftarSurat'));
    }

    /**
     * Form Input Baru
     */
    public function create()
    {
        $this->checkAccess();
        $daftarMahasiswa = Mahasiswa::with(['user', 'prodi'])->get();
        return view('admin_fakultas.surat_legalisir.create', compact('daftarMahasiswa'));
    }

    /**
     * Simpan Data (Memastikan Biaya Tersimpan ke Kolom 'Biaya')
     */
    public function store(Request $request)
    {
        $this->checkAccess();

        $request->validate([
            'id_user_mahasiswa' => 'required|exists:Users,Id_User',
            'jenis_dokumen'     => 'required|in:Ijazah,Transkrip',
            'jumlah_salinan'    => 'required|integer|min:1',
            'biaya'             => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            // 1. Buat Parent TugasSurat
            $tugasSurat = TugasSurat::create([
                'Id_Pemberi_Tugas_Surat'        => Auth::id(),
                'Id_Jenis_Surat'                => 3, 
                'Tanggal_Diberikan_Tugas_Surat' => Carbon::now(),
                'Judul_Tugas_Surat'             => 'Legalisir ' . $request->jenis_dokumen,
                'Status_Tugas_Surat'            => 'diproses',
            ]);

            // 2. Simpan ke Tabel Surat_Legalisir sesuai kolom DB Anda
            SuratLegalisir::create([
                'Id_Tugas_Surat' => $tugasSurat->Id_Tugas_Surat,
                'Id_User'        => $request->id_user_mahasiswa,
                'Jenis_Dokumen'  => $request->jenis_dokumen,
                'Jumlah_Salinan' => $request->jumlah_salinan,
                'Biaya'          => $request->biaya, // Input Biaya disimpan di sini
                'Status'         => 'menunggu_pembayaran', // Sesuai Enum DB Anda
            ]);

            DB::commit();
            return redirect()->route('admin_fakultas.surat_legalisir.index')
                ->with('success', 'Data legalisir mahasiswa berhasil diinput.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Konfirmasi Pembayaran (Update Status & Tanggal Bayar)
     */
    public function konfirmasiPembayaran(Request $request, $id)
    {
        $this->checkAccess();

        $surat = SuratLegalisir::findOrFail($id);

        if ($surat->Status !== 'menunggu_pembayaran') {
            return redirect()->back()->with('error', 'Status tidak valid untuk konfirmasi.');
        }

        $surat->update([
            'Status'        => 'pembayaran_lunas',
            'Tanggal_Bayar' => Carbon::now(), // Mengisi kolom Tanggal_Bayar otomatis
        ]);

        $this->sendNotification($surat->Id_User, 'Pembayaran Diterima', 'Pembayaran legalisir Anda telah lunas. Berkas masuk tahap stempel.', $surat->id_no);

        return redirect()->back()->with('success', 'Pembayaran lunas telah dikonfirmasi.');
    }

    /**
     * Update Progres Alur Berkas Fisik
     */
    public function updateProgress(Request $request, $id)
    {
        $this->checkAccess();

        $surat = SuratLegalisir::with('tugasSurat')->findOrFail($id);
        $statusSekarang = $surat->Status;
        $pesanNotifikasi = '';

        switch ($statusSekarang) {
            case 'pembayaran_lunas':
                $surat->Status = 'proses_stempel_paraf';
                $pesanNotifikasi = 'Berkas sedang dalam tahap penomoran dan stempel.';
                break;

            case 'proses_stempel_paraf':
                // Set Pejabat (Dekan) otomatis jika ada
                $pejabatDekan = Pejabat::where('Nama_Jabatan', 'LIKE', '%Dekan%')->first();
                if ($pejabatDekan) { $surat->Id_Pejabat = $pejabatDekan->Id_Pejabat; }
                
                $surat->Status = 'menunggu_ttd_pimpinan';
                $pesanNotifikasi = 'Berkas sedang menunggu tanda tangan pimpinan.';
                break;

            case 'menunggu_ttd_pimpinan':
                $surat->Status = 'siap_diambil';
                $pesanNotifikasi = 'Legalisir selesai. Silakan ambil berkas Anda di loket.';
                break;

            case 'siap_diambil':
                $surat->Status = 'selesai';
                if ($surat->tugasSurat) {
                    $surat->tugasSurat->update([
                        'Status_Tugas_Surat'    => 'selesai',
                        'Tanggal_Diselesaikan'  => Carbon::now()
                    ]);
                }
                $pesanNotifikasi = 'Pengajuan selesai. Terima kasih.';
                break;

            default:
                return redirect()->back()->with('error', 'Tidak ada progres lanjutan.');
        }

        $surat->save();

        if ($pesanNotifikasi) {
            $this->sendNotification($surat->Id_User, 'Update Legalisir', $pesanNotifikasi, $surat->id_no);
        }

        return redirect()->back()->with('success', 'Status progres berhasil diperbarui.');
    }

    /**
     * Fungsi Kirim Notifikasi ke Mahasiswa
     */
    private function sendNotification($destUser, $title, $message, $idNo)
    {
        Notifikasi::create([
            'Tipe_Notifikasi' => 'Info',
            'Pesan'           => $message,
            'Dest_user'       => $destUser,
            'Source_User'     => Auth::id(),
            'Is_Read'         => false,
            'Data_Tambahan'   => json_encode(['id' => $idNo, 'type' => 'legalisir'])
        ]);
    }
}