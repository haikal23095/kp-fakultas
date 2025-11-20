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

        // 2) Ambil Id_Prodi dari user yang login
        $prodiId = $this->getProdiIdFromUser();

        // 3) Ambil daftar tugas yang terfilter berdasarkan prodi
        $daftarTugas = TugasSurat::getByProdi($prodiId);

        // 4) Ambil daftar role untuk dropdown
        $roles = Role::getAllOrdered();

        return view('admin_prodi.manajemen_surat', [
            'daftarTugas' => $daftarTugas,
            'roles' => $roles,
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