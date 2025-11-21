<?php

namespace App\Http\Controllers\Admin_Prodi;


use App\Http\Controllers\Controller;
use App\Models\TugasSurat;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ManajemenSuratController extends Controller
{
    public function index()
    {
        // 1) Update status tugas yang terlambat (delegasi ke model)
        // CATATAN: Setelah normalisasi, method ini tidak berfungsi karena Status ada di tabel spesifik
        // TugasSurat::updateStatusTerlambat();

        // 2) Query surat yang perlu diproses Admin
        // Filter: Status = 'Diterima Admin' (surat baru dari mahasiswa yang belum diproses)
        $daftarTugas = TugasSurat::with([
                'pemberiTugas.role',           // Relasi ke User > Role
                'pemberiTugas.mahasiswa',      // Relasi ke User > Mahasiswa (untuk NIM & Nama)
                'pemberiTugas.dosen',          // Relasi ke User > Dosen
                'pemberiTugas.pegawai',        // Relasi ke User > Pegawai
                'jenisSurat',                  // Relasi ke Jenis_Surat
                'penerimaTugas',               // Relasi ke User penerima
                'suratMagang'                  // Relasi ke Surat_Magang (untuk preview dokumen)
            ])
            ->orderBy('Tanggal_Diberikan_Tugas_Surat', 'desc')
            ->get();

        // 3) Pisahkan data: Pending (perlu diproses) vs Semua
        $suratPending = $daftarTugas->filter(function($tugas) {
            $status = strtolower(trim($tugas->Status ?? ''));
            return $status === 'diterima admin' || $status === 'baru';
        });

        $suratSemua = $daftarTugas->filter(function($tugas) {
            $status = strtolower(trim($tugas->Status ?? ''));
            return $status !== 'selesai' && $status !== 'telah ditandatangani dekan';
        });

        // Log untuk debugging
        \Log::info('Admin Manajemen Surat', [
            'total_surat' => $daftarTugas->count(),
            'pending_count' => $suratPending->count(),
            'semua_count' => $suratSemua->count(),
        ]);

        return view('admin_prodi.manajemen_surat', [
            'daftarTugas' => $suratSemua,
            'suratPending' => $suratPending,
        ]);
    }

    /**
     * Update status tugas surat
     */
    public function updateStatus(Request $request, $id)
    {
        // Validasi user adalah admin
        $user = Auth::user();
        if (!$user || $user->Id_Role != 1) {
            abort(403, 'Unauthorized action.');
        }

        // Validasi input
        $validated = $request->validate([
            'status' => 'required|in:Belum,Proses,Selesai'
        ]);

        // Update status (delegasi ke model)
        $tugas = TugasSurat::updateStatusById($id, $validated['status']);

        if (!$tugas) {
            return redirect()->back()->with('error', 'Tugas tidak ditemukan.');
        }

        return redirect()->back()->with('success', 'Status berhasil diperbarui.');
    }

    /**
     * Tampilkan halaman arsip surat
     */
    public function archive()
    {
        // Ambil arsip tugas yang sudah selesai (delegasi ke model)
        $arsipTugas = TugasSurat::getArsipSelesai();

        return view('admin_prodi.arsip_surat', [
            'arsipTugas' => $arsipTugas
        ]);
    }

    /**
     * Preview dokumen pendukung (PDF) untuk surat magang
     * Menampilkan file dalam iframe/embed
     */
    public function previewDokumen($id)
    {
        $tugasSurat = TugasSurat::with(['suratMagang'])->findOrFail($id);
        
        $dokumenPath = null;

        // 1. Cek di tabel Surat_Magang (Prioritas Utama)
        if ($tugasSurat->suratMagang && $tugasSurat->suratMagang->Dokumen_Proposal) {
            $dokumenPath = $tugasSurat->suratMagang->Dokumen_Proposal;
        }

        // 2. Jika tidak ada, cek di data_spesifik (Fallback / Surat Aktif)
        if (!$dokumenPath) {
            $dataSpesifik = $tugasSurat->data_spesifik;
            $dokumenPath = $dataSpesifik['dokumen_pendukung'] ?? null;
        }
        
        if (!$dokumenPath || !\Storage::disk('public')->exists($dokumenPath)) {
            abort(404, 'Dokumen tidak ditemukan');
        }
        
        // Return file untuk preview (dengan header inline)
        return \Storage::disk('public')->response($dokumenPath, null, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($dokumenPath) . '"'
        ]);
    }


    /**
     * Helper: Ambil Id_Prodi dari user yang login
     */
    private function getProdiIdFromUser()
    {
        $user = Auth::user()->load(['dosen', 'mahasiswa', 'pegawai']);

        return $user->dosen?->Id_Prodi
            ?? $user->mahasiswa?->Id_Prodi
            ?? $user->pegawai?->Id_Prodi;
    }
}