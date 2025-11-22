<?php

namespace App\Http\Controllers\PengajuanSurat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TugasSurat;
use App\Models\SuratKetAktif;
use App\Models\JenisSurat;
use App\Models\JenisPekerjaan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SuratKeteranganAktifController extends Controller
{
    /**
     * Menyimpan pengajuan Surat Keterangan Mahasiswa Aktif
     */
    public function store(Request $request)
    {
        // === 1. VALIDASI DATA ===
        $validator = Validator::make($request->all(), [
            'Id_Jenis_Surat' => 'required|numeric',
            'data_spesifik.semester' => 'required|numeric|min:1|max:14',
            'data_spesifik.tahun_akademik' => 'required|string',
            'Deskripsi_Tugas_Surat_Aktif' => 'required|string|max:500',
            'file_pendukung_aktif' => 'required|file|mimes:pdf|max:2048', // Max 2MB
        ], [
            'data_spesifik.semester.required' => 'Semester wajib diisi',
            'data_spesifik.tahun_akademik.required' => 'Tahun akademik wajib diisi',
            'Deskripsi_Tugas_Surat_Aktif.required' => 'Keperluan surat wajib diisi',
            'file_pendukung_aktif.required' => 'File KRS wajib diunggah',
            'file_pendukung_aktif.mimes' => 'File harus berformat PDF',
            'file_pendukung_aktif.max' => 'Ukuran file maksimal 2MB',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // === 2. INISIALISASI VARIABEL ===
        $mahasiswaId = Auth::id();
        $jenisSuratId = $request->input('Id_Jenis_Surat');
        $dataSpesifik = $request->input('data_spesifik');
        $deskripsi = $request->input('Deskripsi_Tugas_Surat_Aktif');
        $isUrgent = $request->has('is_urgent');
        $urgentReason = $request->input('urgent_reason');

        // === 3. UPLOAD FILE KRS ===
        $pathDokumenPendukung = null;

        try {
            $filePendukung = $request->file('file_pendukung_aktif');
            $pathDokumenPendukung = $filePendukung->store('uploads/pendukung/surat-aktif', 'public');

            Log::info("File KRS uploaded", [
                'path' => $pathDokumenPendukung,
                'original_name' => $filePendukung->getClientOriginalName(),
                'size' => $filePendukung->getSize(),
            ]);
        } catch (\Exception $e) {
            Log::error("Gagal upload file KRS: " . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal mengunggah file KRS: ' . $e->getMessage())
                ->withInput();
        }

        // === 4. AMBIL DATA JENIS SURAT ===
        $jenisSurat = JenisSurat::find($jenisSuratId);

        if (!$jenisSurat) {
            Log::error("Jenis Surat tidak ditemukan", ['Id_Jenis_Surat' => $jenisSuratId]);
            return redirect()->back()
                ->with('error', 'Jenis surat tidak valid')
                ->withInput();
        }

        $judul = "Pengajuan " . $jenisSurat->Nama_Surat;
        
        // Append deskripsi ke judul agar admin tahu keperluannya (karena tidak ada kolom deskripsi di DB)
        if (!empty($deskripsi)) {
            $judul .= " - " . \Illuminate\Support\Str::limit($deskripsi, 150);
        }

        // === 5. TENTUKAN PEMBERI & PENERIMA TUGAS ===
        $pemberi_tugas_id = $mahasiswaId; // Mahasiswa yang submit

        // Cari Admin Fakultas sebagai penerima tugas
        $adminUser = \App\Models\User::whereHas('role', function ($q) {
            $q->where('Name_Role', 'Admin Fakultas');
        })->first();

        $penerima_tugas_id = $adminUser ? $adminUser->Id_User : $pemberi_tugas_id;

        // === 6. SIMPAN KE DATABASE (Tugas_Surat) ===
        $tugasSurat = new TugasSurat();
        $tugasSurat->Id_Pemberi_Tugas_Surat = $pemberi_tugas_id;
        $tugasSurat->Id_Penerima_Tugas_Surat = $penerima_tugas_id;
        $tugasSurat->Id_Jenis_Surat = $jenisSuratId;
        $tugasSurat->Judul_Tugas_Surat = $judul;
        
        // Set status default 'baru' untuk surat yang baru diajukan
        $tugasSurat->Status = 'baru';
        
        $tugasSurat->Tanggal_Diberikan_Tugas_Surat = Carbon::now();
        $tugasSurat->Tanggal_Tenggat_Tugas_Surat = Carbon::now()->addDays(3);

        // === 7. SET ID JENIS PEKERJAAN ===
        if ($jenisSurat->Jenis_Pekerjaan) {
            $jenisPekerjaan = JenisPekerjaan::where('Jenis_Pekerjaan', $jenisSurat->Jenis_Pekerjaan)->first();

            if ($jenisPekerjaan) {
                $tugasSurat->Id_Jenis_Pekerjaan = $jenisPekerjaan->Id_Jenis_Pekerjaan;
            } else {
                Log::warning("Jenis Pekerjaan tidak ditemukan", [
                    'Jenis_Pekerjaan' => $jenisSurat->Jenis_Pekerjaan
                ]);
            }
        }

        // === 8. SIMPAN KE DATABASE ===
        try {
            // 1. Simpan TugasSurat
            $tugasSurat->save();

            // 2. Simpan SuratKetAktif
            $suratKetAktif = new SuratKetAktif();
            $suratKetAktif->Id_Tugas_Surat = $tugasSurat->Id_Tugas_Surat;
            $suratKetAktif->Tahun_Akademik = $dataSpesifik['tahun_akademik'] ?? null;
            $suratKetAktif->KRS = $pathDokumenPendukung;
            $suratKetAktif->is_urgent = $isUrgent;
            $suratKetAktif->urgent_reason = $isUrgent ? $urgentReason : null;
            // Semester dan Deskripsi tidak disimpan karena tidak ada kolomnya di DB
            $suratKetAktif->save();

            Log::info("Surat Keterangan Aktif berhasil disimpan", [
                'Id_Tugas_Surat' => $tugasSurat->Id_Tugas_Surat,
                'Id_Pemberi' => $pemberi_tugas_id,
                'dokumen_pendukung' => $pathDokumenPendukung,
            ]);
            
            // Kirim notifikasi ke admin fakultas
            if ($adminUser) {
                Notifikasi::create([
                    'Tipe_Notifikasi' => 'Invitation',
                    'Pesan' => '📬 Pengajuan surat baru: Surat Keterangan Mahasiswa Aktif dari ' . Auth::user()->Name_User,
                    'Dest_user' => $adminUser->Id_User,
                    'Source_User' => Auth::id(),
                    'Is_Read' => false,
                    'created_at' => now(),
                ]);
            }

            return redirect()->route('mahasiswa.riwayat')
                ->with('success', '✅ Pengajuan Surat Keterangan Mahasiswa Aktif berhasil dikirim! Nomor pengajuan: #' . $tugasSurat->Id_Tugas_Surat . '. Silakan pantau status pengajuan Anda di halaman ini.');

        } catch (\Exception $e) {
            Log::error("Gagal menyimpan Surat Keterangan Aktif: " . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan pengajuan: ' . $e->getMessage())
                ->withInput();
        }
    }
}
