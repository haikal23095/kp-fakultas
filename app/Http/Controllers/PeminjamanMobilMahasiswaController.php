<?php

namespace App\Http\Controllers;

use App\Models\SuratPeminjamanMobil;
use App\Models\TugasSurat;
use App\Models\JenisSurat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PeminjamanMobilMahasiswaController extends Controller
{
    /**
     * Tampilkan form pengajuan peminjaman mobil
     */
    public function create()
    {
        return view('mahasiswa.pengajuan-surat.form_peminjaman_mobil');
    }

    /**
     * Simpan pengajuan peminjaman mobil
     */
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'tujuan' => 'required|string',
            'keperluan' => 'required|string',
            'tanggal_pemakaian_mulai' => 'required|date|after_or_equal:today',
            'tanggal_pemakaian_selesai' => 'required|date|after_or_equal:tanggal_pemakaian_mulai',
            'jumlah_penumpang' => 'required|integer|min:1',
        ], [
            'tujuan.required' => 'Tujuan harus diisi',
            'keperluan.required' => 'Keperluan harus diisi',
            'tanggal_pemakaian_mulai.required' => 'Tanggal mulai harus diisi',
            'tanggal_pemakaian_mulai.after_or_equal' => 'Tanggal mulai tidak boleh di masa lalu',
            'tanggal_pemakaian_selesai.required' => 'Tanggal selesai harus diisi',
            'tanggal_pemakaian_selesai.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai',
            'jumlah_penumpang.required' => 'Jumlah penumpang harus diisi',
            'jumlah_penumpang.min' => 'Jumlah penumpang minimal 1 orang',
        ]);

        DB::beginTransaction();
        try {
            // Ambil jenis surat untuk Peminjaman Mobil
            $jenisSurat = JenisSurat::where('Nama_Surat', 'LIKE', '%Mobil Dinas%')->first();
            
            if (!$jenisSurat) {
                throw new \Exception('Jenis surat Peminjaman Mobil tidak ditemukan. Silakan hubungi admin.');
            }

            // 1. Insert ke Tugas_Surat
            $tugasSurat = TugasSurat::create([
                'Id_User' => Auth::id(),
                'Id_Jenis_Surat' => $jenisSurat->Id_Jenis_Surat,
                'Status_Surat' => 'Baru',
                'Tanggal_Pengajuan' => now(),
            ]);

            // 2. Insert ke Surat_Peminjaman_Mobil
            SuratPeminjamanMobil::create([
                'Id_Tugas_Surat' => $tugasSurat->Id_Tugas_Surat,
                'Id_User' => Auth::id(),
                'tujuan' => $validated['tujuan'],
                'keperluan' => $validated['keperluan'],
                'tanggal_pemakaian_mulai' => $validated['tanggal_pemakaian_mulai'],
                'tanggal_pemakaian_selesai' => $validated['tanggal_pemakaian_selesai'],
                'jumlah_penumpang' => $validated['jumlah_penumpang'],
                'status_pengajuan' => 'Diajukan',
            ]);

            DB::commit();

            return redirect()
                ->route('mahasiswa.riwayat.mobil_dinas')
                ->with('success', 'Pengajuan peminjaman mobil dinas berhasil diajukan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Gagal mengajukan peminjaman mobil: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan riwayat pengajuan peminjaman mobil mahasiswa
     */
    public function riwayat()
    {
        $userId = Auth::id();
        
        $riwayat = SuratPeminjamanMobil::where('Id_User', $userId)
            ->with(['tugasSurat', 'kendaraan', 'pejabat'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('mahasiswa.peminjaman_mobil.riwayat', compact('riwayat'));
    }

    /**
     * Tampilkan detail pengajuan
     */
    public function show($id)
    {
        $peminjaman = SuratPeminjamanMobil::with(['tugasSurat', 'kendaraan', 'pejabat', 'user'])
            ->where('Id_User', Auth::id())
            ->findOrFail($id);

        return view('mahasiswa.peminjaman_mobil.show', compact('peminjaman'));
    }

    /**
     * Preview surat final (HTML)
     */
    public function previewSurat($id)
    {
        $peminjaman = SuratPeminjamanMobil::with(['tugasSurat', 'kendaraan', 'pejabat', 'user', 'verification'])
            ->where('Id_User', Auth::id())
            ->findOrFail($id);

        if ($peminjaman->status_pengajuan != 'Selesai') {
            return back()->with('error', 'Surat belum selesai diproses');
        }

        return view('mahasiswa.peminjaman_mobil.preview_surat', compact('peminjaman'));
    }

    /**
     * Download file surat final
     */
    public function downloadSurat($id)
    {
        $peminjaman = SuratPeminjamanMobil::with(['tugasSurat', 'kendaraan', 'pejabat', 'user', 'verification'])
            ->where('Id_User', Auth::id())
            ->findOrFail($id);

        if ($peminjaman->status_pengajuan != 'Selesai') {
            return back()->with('error', 'Surat belum selesai diproses');
        }

        // Generate PDF
        $pdf = \PDF::loadView('pdf.surat_peminjaman_mobil', compact('peminjaman'));
        
        $fileName = 'Surat_Peminjaman_Mobil_' . $peminjaman->nomor_surat . '.pdf';
        $fileName = str_replace('/', '_', $fileName);
        
        return $pdf->download($fileName);
    }
}
