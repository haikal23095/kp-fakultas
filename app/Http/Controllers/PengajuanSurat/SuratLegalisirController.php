<?php

namespace App\Http\Controllers\PengajuanSurat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SuratLegalisir;
use App\Models\TugasSurat;
use App\Models\JenisSurat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SuratLegalisirController extends Controller
{
    /**
     * Form Input Baru untuk Mahasiswa
     */
    public function create()
    {
        $user = Auth::user();
        return view('mahasiswa.form_legalisir');
    }

    /**
     * Simpan Pengajuan Legalisir
     */
    public function store(Request $request)
    {
        $request->validate([
            'jenis_dokumen' => 'required|in:Ijazah,Transkrip',
            'jumlah_salinan' => 'required|integer|min:1|max:10',
        ]);

        DB::beginTransaction();
        try {
            $user = Auth::user();
            
            // Cari ID Jenis Surat Legalisir
            $jenisSurat = JenisSurat::where('Nama_Surat', 'Surat Legalisir')->first();
            
            // Fallback jika tidak ditemukan (sebaiknya di-seed)
            if (!$jenisSurat) {
                // Coba cari yang mirip atau hardcode jika tahu ID-nya
                // Untuk keamanan, kita throw error jika tidak ada
                throw new \Exception('Jenis Surat Legalisir tidak ditemukan di database.');
            }

            // 1. Buat Parent TugasSurat
            $tugasSurat = TugasSurat::create([
                'Id_Pemberi_Tugas_Surat'        => $user->Id_User,
                'Id_Jenis_Surat'                => $jenisSurat->Id_Jenis_Surat, 
                'Tanggal_Diberikan_Tugas_Surat' => Carbon::now(),
                'Judul_Tugas_Surat'             => 'Permohonan Legalisir ' . $request->jenis_dokumen,
            ]);

            // Hitung Biaya (Contoh: 5000 per lembar)
            // Bisa diambil dari config atau database
            $biayaPerLembar = 5000;
            $totalBiaya = $request->jumlah_salinan * $biayaPerLembar;

            // 2. Simpan ke Tabel Surat_Legalisir
            SuratLegalisir::create([
                'Id_Tugas_Surat' => $tugasSurat->Id_Tugas_Surat,
                'Id_User'        => $user->Id_User,
                'Jenis_Dokumen'  => $request->jenis_dokumen,
                'Jumlah_Salinan' => $request->jumlah_salinan,
                'Biaya'          => $totalBiaya,
                'Status'         => 'Menunggu Pembayaran',
            ]);

            DB::commit();
            return redirect()->route('mahasiswa.riwayat.legalisir')
                ->with('success', 'Pengajuan legalisir berhasil dibuat. Silakan lakukan pembayaran sebesar Rp ' . number_format($totalBiaya, 0, ',', '.'));

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }
}