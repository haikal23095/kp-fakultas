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
     * Membatasi akses hanya untuk Role ID 5 dan 7
     */
    private function checkAccess()
    {
        if (!in_array(Auth::user()->Id_Role, [5, 7])) {
            abort(403, 'ANDA TIDAK MEMILIKI HAK AKSES KE FITUR INI.');
        }
    }

    /**
     * Menampilkan daftar pengajuan legalisir
     */
    public function index()
    {
        $this->checkAccess();

        // HANYA ambil data mahasiswa (bukan dekan/pegawai) dan yang belum selesai
        $daftarSurat = SuratLegalisir::with(['user.mahasiswa.prodi', 'tugasSurat'])
            ->whereHas('user.mahasiswa') // FILTER: Hanya yang punya relasi mahasiswa
            ->where('Status', '!=', 'selesai') // EXCLUDE: Data yang sudah selesai
            ->whereNotNull('File_Scan_Path') // FILTER: Hanya yang ada file scan (data baru)
            ->orderBy('id_no', 'desc')
            ->get();

        return view('admin_fakultas.list_legalisir', compact('daftarSurat'));
    }

    /**
     * Verifikasi file scan mahasiswa
     */
    public function verifikasiFile($id)
    {
        $this->checkAccess();

        $surat = SuratLegalisir::findOrFail($id);

        if ($surat->Status !== 'menunggu_pembayaran') {
            return redirect()->back()->with('error', 'Status berkas tidak valid untuk verifikasi.');
        }

        if ($surat->Is_Verified) {
            return redirect()->back()->with('info', 'File sudah terverifikasi sebelumnya.');
        }

        $surat->update([
            'Is_Verified' => true,
        ]);

        // Mengirim notifikasi ke mahasiswa
        $this->sendNotification($surat->Id_User, 'File Terverifikasi', 'File scan legalisir Anda telah diverifikasi admin. Silakan lakukan pembayaran.', $surat->id_no);

        return redirect()->back()->with('success', 'File scan berhasil diverifikasi!');
    }

    /**
     * Konfirmasi Pembayaran (Mencatat Tanggal_Bayar otomatis)
     */
    public function konfirmasiPembayaran(Request $request, $id)
    {
        $this->checkAccess();

        $surat = SuratLegalisir::findOrFail($id);

        if ($surat->Status !== 'menunggu_pembayaran') {
            return redirect()->back()->with('error', 'Status berkas tidak valid.');
        }

        $surat->update([
            'Status'        => 'pembayaran_lunas',
            'Tanggal_Bayar' => Carbon::now(),
        ]);

        // Mengirim notifikasi ke mahasiswa
        $this->sendNotification($surat->Id_User, 'Pembayaran Lunas', 'Pembayaran legalisir Anda telah diterima. Berkas diproses ke tahap stempel.', $surat->id_no);

        return redirect()->back()->with('success', 'Pembayaran telah dikonfirmasi sebagai LUNAS.');
    }

    /**
     * Input Nomor Surat Legalisir dan kirim ke Dekan
     */
    public function kirimKePimpinan($id)
    {
        $this->checkAccess();

        $surat = SuratLegalisir::with('tugasSurat')->findOrFail($id);

        if ($surat->Status !== 'pembayaran_lunas') {
            return redirect()->back()->with('error', 'Status berkas tidak valid. Harus pembayaran_lunas.');
        }

        $surat->update([
            'Status' => 'menunggu_ttd_pimpinan',
        ]);

        // Update TugasSurat
        if ($surat->tugasSurat) {
            $surat->tugasSurat->update([
                'Status' => 'Menunggu TTD Pimpinan',
            ]);
        }

        // Notifikasi ke mahasiswa
        $this->sendNotification(
            $surat->Id_User, 
            'Berkas Dikirim TTD', 
            'Berkas legalisir Anda telah dikirim ke Dekan dan Wadek1 untuk ditandatangani.', 
            $surat->id_no
        );

        // TODO: Notifikasi ke Dekan dan Wadek1 (akan dibuat di step berikutnya)

        return redirect()->back()->with('success', 'Berkas berhasil dikirim ke Dekan dan Wadek1 untuk ditandatangani!');
    }

    /**
     * Memperbarui Progres Berkas (Alur Kerja)
     * ALUR: siap_diambil → selesai (mahasiswa konfirmasi ambil)
     */
    public function updateProgress(Request $request, $id)
    {
        $this->checkAccess();

        $surat = SuratLegalisir::with('tugasSurat')->findOrFail($id);
        $statusSekarang = $surat->Status;
        $pesanNotif = '';

        switch ($statusSekarang) {
            case 'siap_diambil':
                // Mahasiswa sudah ambil hardfile, proses selesai
                $surat->Status = 'selesai';
                if ($surat->tugasSurat) {
                    $surat->tugasSurat->update([
                        'Status'               => 'Selesai',
                        'Tanggal_Diselesaikan' => Carbon::now()
                    ]);
                }
                $pesanNotif = 'Proses legalisir telah selesai sepenuhnya. Terima kasih.';
                break;

            default:
                return redirect()->back()->with('error', 'Tidak ada progres lanjutan yang tersedia untuk status ini.');
        }

        $surat->save();

        if ($pesanNotif) {
            $this->sendNotification($surat->Id_User, 'Update Legalisir', $pesanNotif, $surat->id_no);
        }

        return redirect()->back()->with('success', 'Status progres berkas berhasil diperbarui!');
    }

    /**
     * Fungsi pembantu untuk mengirim notifikasi internal
     */
/**
 * Fungsi Kirim Notifikasi ke Mahasiswa
 */
    private function sendNotification($destUser, $title, $message, $idNo)
    {
        // Pastikan 'Tipe_Notifikasi' sesuai dengan pilihan ENUM di database
        // Jika di database adalah 'info', maka jangan gunakan 'Info' (kapital)
        Notifikasi::create([
            'Tipe_Notifikasi' => 'Accepted', // <--- UBAH DARI 'Info' MENJADI 'info'
            'Pesan'           => $message,
            'Dest_user'       => $destUser,
            'Source_User'     => Auth::id(),
            'Is_Read'         => false,
            'Data_Tambahan'   => json_encode(['id' => $idNo, 'type' => 'legalisir'])
        ]);
    }
}