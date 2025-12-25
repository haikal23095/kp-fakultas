<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TugasSurat;
use App\Models\SuratVerification;
use App\Models\SuratKetAktif;
use App\Models\SuratMagang;
use App\Models\SuratLegalisir;

class RiwayatSuratController extends Controller
{
    /**
     * Tampilkan pilihan jenis surat
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $type = $request->query('type');

        // Hitung jumlah surat per jenis
        $countAktif = TugasSurat::where('Id_Pemberi_Tugas_Surat', $user->Id_User)
            ->where('Id_Jenis_Surat', 3) // ID untuk Surat Keterangan Aktif
            ->count();

        $countMagang = TugasSurat::where('Id_Pemberi_Tugas_Surat', $user->Id_User)
            ->where('Id_Jenis_Surat', 13) // ID untuk Surat Pengantar Magang
            ->count();

        // Cari ID Jenis Surat untuk Legalisir
        $jenisSuratLegalisir = \App\Models\JenisSurat::where('Nama_Surat', 'Surat Legalisir')->first();
        $countLegalisir = 0;
        if ($jenisSuratLegalisir) {
            $countLegalisir = TugasSurat::where('Id_Pemberi_Tugas_Surat', $user->Id_User)
                ->where('Id_Jenis_Surat', $jenisSuratLegalisir->Id_Jenis_Surat)
                ->count();
        }

        return view('mahasiswa.riwayat', [
            'countAktif' => $countAktif,
            'countMagang' => $countMagang,
            'countLegalisir' => $countLegalisir
        ]);
    }

    /**
     * Tampilkan riwayat surat keterangan aktif
     */
    public function riwayatAktif()
    {
        $user = Auth::user();

        // Query surat keterangan aktif dengan relasi ke Surat_Ket_Aktif
        $riwayatSurat = TugasSurat::with([
            'jenisSurat',
            'penerimaTugas',
            'suratKetAktif', // Relasi ke tabel Surat_Ket_Aktif
            'verification'
        ])
            ->where('Id_Pemberi_Tugas_Surat', $user->Id_User)
            ->where('Id_Jenis_Surat', 3) // ID untuk Surat Keterangan Aktif
            ->orderBy('Tanggal_Diberikan_Tugas_Surat', 'desc')
            ->get();

        return view('mahasiswa.riwayat_aktif', [
            'riwayatSurat' => $riwayatSurat
        ]);
    }

    /**
     * Tampilkan riwayat surat pengantar magang
     */
    public function riwayatMagang()
    {
        $user = Auth::user();

        // Query surat magang dengan relasi ke Surat_Magang
        $riwayatSurat = TugasSurat::with([
            'jenisSurat',
            'penerimaTugas',
            'suratMagang', // Relasi ke tabel Surat_Magang
            'verification'
        ])
            ->where('Id_Pemberi_Tugas_Surat', $user->Id_User)
            ->where('Id_Jenis_Surat', 13) // ID untuk Surat Pengantar Magang
            ->orderBy('Tanggal_Diberikan_Tugas_Surat', 'desc')
            ->get();

        return view('mahasiswa.riwayat_magang', [
            'riwayatSurat' => $riwayatSurat
        ]);
    }

    /**
     * Tampilkan riwayat pengajuan legalisir
     */
    public function riwayatLegalisir()
    {
        $user = Auth::user();

        // Cari ID Jenis Surat untuk Legalisir
        $jenisSuratLegalisir = \App\Models\JenisSurat::where('Nama_Surat', 'Surat Legalisir')->first();
        
        if (!$jenisSuratLegalisir) {
            return view('mahasiswa.riwayat_legalisir', [
                'riwayatSurat' => collect([])
            ]);
        }

        // Query surat legalisir dengan relasi ke Surat_Legalisir
        $riwayatSurat = TugasSurat::with([
            'jenisSurat',
            'penerimaTugas',
            'suratLegalisir', // Relasi ke tabel Surat_Legalisir
        ])
            ->where('Id_Pemberi_Tugas_Surat', $user->Id_User)
            ->where('Id_Jenis_Surat', $jenisSuratLegalisir->Id_Jenis_Surat)
            ->orderBy('Tanggal_Diberikan_Tugas_Surat', 'desc')
            ->get();

        return view('mahasiswa.riwayat_legalisir', [
            'riwayatSurat' => $riwayatSurat
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
            'suratMagang.koordinator', // Load Kaprodi info
            'suratMagang.dekan' // Load Dekan info
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
