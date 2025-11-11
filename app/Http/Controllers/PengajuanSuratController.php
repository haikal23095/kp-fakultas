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


        // === 4. UPLOAD FILE PENDUKUNG TERLEBIH DAHULU ===
        $pathDokumenPendukung = null;

        if ($filePendukung) {
            try {
                // Simpan file ke storage/app/public/uploads/pendukung
                $pathDokumenPendukung = $filePendukung->store('uploads/pendukung', 'public');
                Log::info("File uploaded successfully", ['path' => $pathDokumenPendukung]);
            } catch (\Exception $e) {
                Log::error("Gagal upload file: " . $e->getMessage());
                return redirect()->back()->with('error', 'Gagal mengunggah file pendukung: ' . $e->getMessage())->withInput();
            }
        }

        // === 5. SIMPAN KE TABEL Tugas_Surat ===

        $tugasSurat = new TugasSurat();
        $tugasSurat->Id_Penerima_Tugas_Surat = $mahasiswaId;
        $tugasSurat->Id_Jenis_Surat = $jenisSuratId;
        $tugasSurat->Judul_Tugas_Surat = $judul;
        $tugasSurat->Deskripsi_Tugas_Surat = $deskripsi;
        $tugasSurat->data_spesifik = $dataSpesifik;
        $tugasSurat->dokumen_pendukung = $pathDokumenPendukung; // Simpan path file ke kolom dokumen_pendukung

            // ===== Perbaikan: pastikan pemberi dan penerima tugas diisi dengan benar =====
            // Pemberi tugas = user yang sedang login (mahasiswa)
            $pemberi_tugas_id = auth()->user()->Id_User ?? auth()->id();

            // Cari admin fakultas berdasarkan relasi role (jika ada)
            $adminUser = \App\Models\User::whereHas('role', function($q) {
                $q->where('Name_Role', 'Admin Fakultas');
            })->first();

            // Fallback: jika tidak ditemukan, coba cari user dengan Id_Role = 1 (legacy)
            $penerima_tugas_id = $adminUser ? $adminUser->Id_User : (\DB::table('Users')->where('Id_Role', 1)->value('Id_User') ?? $pemberi_tugas_id);

            $tugasSurat->Id_Pemberi_Tugas_Surat = $pemberi_tugas_id;
            $tugasSurat->Id_Penerima_Tugas_Surat = $penerima_tugas_id;

            // Status awal sesuai rule bisnis
            $tugasSurat->Status = 'Diterima Admin';
            $tugasSurat->Tanggal_Diberikan_Tugas_Surat = Carbon::now();
            // Atur tenggat oleh sistem: maksimal 3 hari dari sekarang
            $tugasSurat->Tanggal_Tenggat_Tugas_Surat = Carbon::now()->addDays(3);

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
            $tugasSurat->save();
            Log::info("Tugas Surat saved successfully", [
                'Id_Tugas_Surat' => $tugasSurat->Id_Tugas_Surat,
                'dokumen_pendukung' => $tugasSurat->dokumen_pendukung,
            ]);
        } catch (\Exception $e) {
            Log::error("Gagal menyimpan Tugas Surat: " . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan pengajuan: ' . $e->getMessage())->withInput();
        }

        // === 6. KEMBALIKAN KE HALAMAN FORM ===

        return redirect()->route('mahasiswa.pengajuan.create')
            ->with('success', 'Pengajuan surat Anda (' . $judul . ') telah berhasil terkirim!');
    }
}