<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TugasSurat;
use App\Models\SuratVerification;

class RiwayatSuratController extends Controller
{
    /**
     * Tampilkan riwayat surat mahasiswa
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $type = $request->query('type');

        // Jika tidak ada parameter type, tampilkan menu pilihan
        if (!$type) {
            return view('mahasiswa.riwayat_menu');
        }

        // Query dasar
        $query = TugasSurat::with([
            'jenisSurat',
            'penerimaTugas',
            'suratMagang',
            'suratKetAktif', // Load relasi SuratKetAktif
            'verification' // Relasi ke SuratVerification untuk ambil QR Code
        ])
        ->where('Id_Pemberi_Tugas_Surat', $user->Id_User)
        ->orderBy('Tanggal_Diberikan_Tugas_Surat', 'desc');

        $title = 'Riwayat Pengajuan Surat';

        // Filter berdasarkan tipe
        if ($type === 'magang') {
            $query->whereHas('jenisSurat', function($q) {
                $q->where('Nama_Surat', 'Surat Pengantar KP/Magang');
            });
            $title = 'Riwayat Surat Magang / KP';
        } elseif ($type === 'aktif') {
            $query->whereHas('jenisSurat', function($q) {
                $q->where('Nama_Surat', 'Surat Keterangan Aktif Kuliah');
            });
            $title = 'Riwayat Surat Keterangan Aktif';
        } else {
            // Surat lainnya (Rekomendasi, dll)
            $query->whereHas('jenisSurat', function($q) {
                $q->whereNotIn('Nama_Surat', ['Surat Pengantar KP/Magang', 'Surat Keterangan Aktif Kuliah']);
            });
            $title = 'Riwayat Surat Lainnya';
        }

        $riwayatSurat = $query->get();

        return view('mahasiswa.riwayat', [
            'riwayatSurat' => $riwayatSurat,
            'title' => $title
        ]);
    }

    /**
     * Preview PDF surat dengan QR Code di browser
     */
    public function downloadSurat($id)
    {
        $user = Auth::user();

        // Ambil surat dengan verifikasi QR
        $tugasSurat = TugasSurat::with([
            'jenisSurat',
            'pemberiTugas.mahasiswa.prodi',
            'penerimaTugas',
            'suratMagang',
            'verification.penandatangan.pegawai', // Untuk ambil QR Code + NIP (Pegawai)
            'verification.penandatangan.dosen'    // Untuk ambil QR Code + NIP (Dosen)
        ])
        ->where('Id_Tugas_Surat', $id)
        ->where('Id_Pemberi_Tugas_Surat', $user->Id_User)
        ->firstOrFail();

        // Cek apakah surat sudah selesai
        $statusLower = strtolower(trim($tugasSurat->Status));
        if ($statusLower !== 'selesai' && $statusLower !== 'telah ditandatangani dekan') {
            return redirect()->route('mahasiswa.riwayat')
                ->with('error', 'Surat belum dapat diunduh. Status: ' . $tugasSurat->Status);
        }

        // Get QR Code URL dari database (sudah di-generate saat approve)
        $qrImageUrl = null;
        if ($tugasSurat->verification && $tugasSurat->verification->qr_path) {
            // qr_path berisi URL Google Charts API
            $qrImageUrl = $tugasSurat->verification->qr_path;
        }

        // Render PDF view
        return view('mahasiswa.pdf.surat_dengan_qr', [
            'surat' => $tugasSurat,
            'mahasiswa' => $tugasSurat->pemberiTugas->mahasiswa,
            'jenisSurat' => $tugasSurat->jenisSurat,
            'verification' => $tugasSurat->verification,
            'qrImageUrl' => $qrImageUrl,
            'mode' => 'preview' // Mode preview untuk browser
        ]);
    }

    /**
     * Preview PDF Surat Pengantar (Signed by Kaprodi)
     */
    public function downloadPengantar($id)
    {
        $user = Auth::user();

        // Ambil surat
        $tugasSurat = TugasSurat::with([
            'jenisSurat',
            'pemberiTugas.mahasiswa.prodi',
            'suratMagang.koordinator' // Load Kaprodi info
        ])
        ->where('Id_Tugas_Surat', $id)
        ->where('Id_Pemberi_Tugas_Surat', $user->Id_User)
        ->firstOrFail();

        // Cek apakah surat magang ada
        if (!$tugasSurat->suratMagang) {
            return redirect()->route('mahasiswa.riwayat')
                ->with('error', 'Bukan surat magang.');
        }

        // Cek apakah sudah disetujui koordinator
        if (!$tugasSurat->suratMagang->Acc_Koordinator) {
            return redirect()->route('mahasiswa.riwayat')
                ->with('error', 'Surat Pengantar belum disetujui Koordinator.');
        }

        // Render PDF view
        return view('mahasiswa.pdf.surat_pengantar', [
            'surat' => $tugasSurat,
            'magang' => $tugasSurat->suratMagang,
            'mahasiswa' => $tugasSurat->pemberiTugas->mahasiswa,
            'koordinator' => $tugasSurat->suratMagang->koordinator,
            'mode' => 'preview'
        ]);
    }
}
