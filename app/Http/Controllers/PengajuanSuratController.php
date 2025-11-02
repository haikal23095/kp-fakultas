<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TugasSurat;
use App\Models\FileArsip;
use App\Models\JenisSurat;
use App\Models\JenisPekerjaan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log; // <-- Pastikan ini ada untuk logging

class PengajuanSuratController extends Controller
{
    /**
     * Menyimpan pengajuan surat baru dari mahasiswa.
     */
    public function store(Request $request)
    {
        // === 1. VALIDASI DATA ===
        $validator = Validator::make($request->all(), [
            'Id_Jenis_Surat' => 'required|numeric',
            // Anda bisa tambahkan validasi lain di sini jika perlu
            // 'data_spesifik.semester' => 'required_if:Id_Jenis_Surat,3|numeric', // Contoh
            // 'file_pendukung_aktif' => 'required_if:Id_Jenis_Surat,3|file|mimes:pdf|max:2048', // Contoh
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // === 2. INISIALISASI VARIABEL ===
        $mahasiswaId = Auth::id();
        $jenisSuratId = $request->input('Id_Jenis_Surat');
        $dataSpesifik = $request->input('data_spesifik');

        $filePendukung = null;
        $deskripsi = null;

        // === 3. LOGIKA BERDASARKAN JENIS SURAT ===

        $jenisSurat = JenisSurat::find($jenisSuratId);
        $judul = $jenisSurat ? "Pengajuan " . $jenisSurat->Nama_Surat : "Pengajuan Surat";

        // Cek file mana yang diupload berdasarkan jenis surat (lebih baik dari has())
        if ($request->file('file_pendukung_aktif') && $request->file('file_pendukung_aktif')->isValid()) {
             $filePendukung = $request->file('file_pendukung_aktif');
             $deskripsi = $request->input('Deskripsi_Tugas_Surat_Aktif');
        } else if ($request->file('file_pendukung_magang') && $request->file('file_pendukung_magang')->isValid()) {
             $filePendukung = $request->file('file_pendukung_magang');
             $instansi = $dataSpesifik['nama_instansi'] ?? 'Instansi Tujuan';
             $deskripsi = "Pengajuan surat pengantar magang/KP ke " . $instansi;
        }
  // ... di dalam public function store(Request $request)

    // ...
    // === 4. SIMPAN KE TABEL Tugas_Surat ===

    $tugasSurat = new TugasSurat();
    $tugasSurat->Id_Penerima_Tugas_Surat = $mahasiswaId;
    $tugasSurat->Id_Jenis_Surat = $jenisSuratId;
    $tugasSurat->Judul_Tugas_Surat = $judul;
    $tugasSurat->Deskripsi_Tugas_Surat = $deskripsi;
    $tugasSurat->data_spesifik = $dataSpesifik;
    
    // ======================================================
    // 				INI PERBAIKANNYA
    // ======================================================
    
    // $tugasSurat->Status = 'tugas_baru'; // <-- INI PENYEBAB ERROR
    
    // Ganti dengan nilai yang sesuai dengan kolom database kamu
    // Misalnya, jika database hanya mau 'baru'
    $tugasSurat->Status = 'baru'; 
    
    // ======================================================
    
    $tugasSurat->Tanggal_Diberikan_Tugas_Surat = Carbon::now();
    $tugasSurat->Id_Pemberi_Tugas_Surat = 1; // Asumsi ID 1 adalah Admin

    if ($jenisSurat) {
     

            // Menggunakan getAttribute() untuk memastikan kita membaca kolom DB
            $pekerjaan = $jenisSurat->getAttribute('Jenis_Pekerjaan');

            // <-- LOGGING YANG DIMINTA -->
            Log::info("Nilai \$pekerjaan dari getAttribute: " . json_encode($pekerjaan));

            $jenisPekerjaan = JenisPekerjaan::where('Jenis_Pekerjaan', $pekerjaan)->first();

            // <-- LOGGING YANG DIMINTA -->
            Log::info("Hasil pencarian JenisPekerjaan: " . json_encode($jenisPekerjaan));

            if ($jenisPekerjaan) {
                $tugasSurat->Id_Jenis_Pekerjaan = $jenisPekerjaan->Id_Jenis_Pekerjaan;
            } else {
                // Log jika tidak ditemukan
                Log::warning("Jenis Pekerjaan DB tidak ditemukan untuk nilai: " . $pekerjaan . " dari Jenis Surat ID: " . $jenisSuratId);
                // Pertimbangkan: haruskah kita set default atau biarkan null? Sesuai DB Anda, NULL diizinkan.
                // $tugasSurat->Id_Jenis_Pekerjaan = null; // Ini default jika kolomnya nullable
            }
        } else {
            Log::error("Jenis Surat tidak ditemukan untuk ID: " . $jenisSuratId);
            // Handle error, mungkin redirect back dengan pesan error
        }

        try {
            $tugasSurat->save(); // Ia mencoba menyimpan
        } catch (\Exception $e) {
            // ... dan ia BERHASIL menangkap error database!
            Log::error("Gagal menyimpan Tugas Surat: " . $e->getMessage()); 
            return redirect()->back()->with('error', 'Terjadi kesalahan...');
    }


        // === 5. SIMPAN FILE PENDUKUNG (JIKA ADA) ===

        if ($filePendukung) { // Cukup cek $filePendukung sudah diisi atau belum

            try {
                // Simpan file ke storage
                $path = $filePendukung->store('uploads/pendukung', 'public');

                $fileArsip = new FileArsip();
                $fileArsip->Id_Tugas_Surat = $tugasSurat->Id_Tugas_Surat; // Gunakan Primary Key yang benar
                $fileArsip->Keterangan = 'Dokumen Pendukung Mahasiswa';
                $fileArsip->Path_File = $path;
                $fileArsip->Id_Penerima_Tugas_Surat = $mahasiswaId;
                $fileArsip->Id_Pemberi_Tugas_Surat = 1; // Asumsi ID 1 adalah Admin

                $fileArsip->save();

            } catch (\Exception $e) {
                 Log::error("Gagal menyimpan File Arsip: " . $e->getMessage());
                 // Pertimbangkan: haruskah pengajuan dibatalkan jika file gagal disimpan?
                 // Atau cukup berikan pesan warning?
                 return redirect()->back()->with('error', 'Data pengajuan tersimpan, tetapi gagal mengunggah file pendukung.')->withInput();
            }
        }

        // === 6. KEMBALIKAN KE HALAMAN FORM ===

        return redirect()->route('mahasiswa.pengajuan.create')
                         ->with('success', 'Pengajuan surat Anda (' . $judul . ') telah berhasil terkirim!');
    }
}