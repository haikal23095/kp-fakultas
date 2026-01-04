<?php

namespace App\Http\Controllers\Admin_Fakultas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SuratDispensasi;
use App\Models\TugasSurat;
use App\Models\Mahasiswa;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SuratDispensasiController extends Controller
{
    /**
     * Menampilkan daftar surat dispensasi yang perlu diproses admin
     */
    public function index()
    {
        // Ambil surat dispensasi dengan status 'baru' atau 'proses' atau 'dikerjakan-admin'
        $daftarSurat = SuratDispensasi::with([
            'tugasSurat.pemberiTugas.mahasiswa.prodi',
            'tugasSurat.jenisSurat',
            'user.mahasiswa'
        ])
            ->whereHas('tugasSurat', function($query) {
                $query->whereIn('Status', ['baru', 'proses', 'dikerjakan-admin']);
            })
            ->orderBy('id', 'desc')
            ->get();

        return view('admin_fakultas.surat-dispensasi.index', compact('daftarSurat'));
    }

    /**
     * Menampilkan detail surat dispensasi untuk diproses
     */
    public function show($id)
    {
        $surat = SuratDispensasi::with([
            'tugasSurat.pemberiTugas.mahasiswa.prodi',
            'tugasSurat.jenisSurat',
            'user.mahasiswa.prodi',
            'pejabatWadek3'
        ])->findOrFail($id);

        // Get mahasiswa data
        $mahasiswa = $surat->user->mahasiswa;

        return view('admin_fakultas.surat-dispensasi.detail', compact('surat', 'mahasiswa'));
    }

    /**
     * Download file permohonan
     */
    public function downloadPermohonan($id)
    {
        $surat = SuratDispensasi::findOrFail($id);

        if (!$surat->file_permohonan) {
            return redirect()->back()->with('error', 'File permohonan tidak tersedia.');
        }

        $filePath = storage_path('app/public/' . $surat->file_permohonan);

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File tidak ditemukan di server.');
        }

        return response()->download($filePath);
    }

    /**
     * Download file lampiran (jika ada)
     */
    public function downloadLampiran($id)
    {
        $surat = SuratDispensasi::findOrFail($id);

        if (!$surat->file_lampiran) {
            return redirect()->back()->with('error', 'File lampiran tidak tersedia.');
        }

        $filePath = storage_path('app/public/' . $surat->file_lampiran);

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File tidak ditemukan di server.');
        }

        return response()->download($filePath);
    }

    /**
     * Assign nomor surat (TANPA generate PDF, PDF akan di-generate oleh Wadek3 dengan QR code)
     */
    public function assignNomorSurat(Request $request, $id)
    {
        $request->validate([
            'nomor_surat' => 'required|string|max:100',
        ], [
            'nomor_surat.required' => 'Nomor surat wajib diisi.',
        ]);

        $surat = SuratDispensasi::with('user.mahasiswa.prodi', 'tugasSurat')->findOrFail($id);

        // Update nomor surat
        $surat->nomor_surat = $request->nomor_surat;
        $surat->verifikasi_admin_by = Auth::user()->Id_User;
        $surat->verifikasi_admin_at = Carbon::now()->toDateString();
        $surat->save();

        // Update status Tugas Surat (Proses = menunggu approval Wadek3)
        $surat->tugasSurat->Status = 'Proses';
        $surat->tugasSurat->save();

        return redirect()->route('admin_fakultas.surat.dispensasi')
            ->with('success', 'Nomor surat berhasil diberikan! Surat diteruskan ke Wadek 3 untuk persetujuan.');
    }

    /**
     * Download PDF surat yang sudah di-generate oleh Wadek3 (setelah ACC dengan QR code)
     */
    public function downloadPDF($id)
    {
        $surat = SuratDispensasi::findOrFail($id);

        if (!$surat->file_surat_selesai) {
            return redirect()->back()->with('error', 'PDF surat belum di-generate oleh Wadek 3.');
        }

        $filePath = storage_path('app/public/' . $surat->file_surat_selesai);

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File PDF tidak ditemukan di server.');
        }

        return response()->file($filePath);
    }
}
