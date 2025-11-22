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
    public function index()
    {
        $user = Auth::user();

        // Query semua surat mahasiswa dengan relasi
        $riwayatSurat = TugasSurat::with([
            'jenisSurat',
            'penerimaTugas',
            'suratMagang',
            'verification' // Relasi ke SuratVerification untuk ambil QR Code
        ])
        ->where('Id_Pemberi_Tugas_Surat', $user->Id_User)
        ->orderBy('Tanggal_Diberikan_Tugas_Surat', 'desc')
        ->get();

        return view('mahasiswa.riwayat', [
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
}
