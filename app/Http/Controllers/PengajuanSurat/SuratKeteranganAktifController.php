<?php

namespace App\Http\Controllers\PengajuanSurat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SuratKetAktif;
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

        // === 4. TENTUKAN PEMBERI & PENERIMA TUGAS ===
        $pemberi_tugas_id = $mahasiswaId; // Mahasiswa yang submit

        // Cari Admin Fakultas sebagai penerima tugas
        $adminUser = \App\Models\User::whereHas('role', function ($q) {
            $q->where('Name_Role', 'Admin Fakultas');
        })->first();

        $penerima_tugas_id = $adminUser ? $adminUser->Id_User : $pemberi_tugas_id;

        // === 5. SIMPAN KE DATABASE (SuratKetAktif) ===
        try {
            $suratKetAktif = new SuratKetAktif();
            $suratKetAktif->Id_Pemberi_Tugas = $pemberi_tugas_id;
            $suratKetAktif->Id_Penerima_Tugas = $penerima_tugas_id;
            $suratKetAktif->Status = 'Diajukan-ke-koordinator'; // Status awal
            $suratKetAktif->Tanggal_Diberikan = Carbon::now();
            $suratKetAktif->Tahun_Akademik = $dataSpesifik['tahun_akademik'] ?? null;
            $suratKetAktif->KRS = $pathDokumenPendukung;
            $suratKetAktif->is_urgent = $isUrgent;
            $suratKetAktif->urgent_reason = $isUrgent ? $urgentReason : null;
            $suratKetAktif->Deskripsi = $deskripsi;
            $suratKetAktif->save();

            Log::info("Surat Keterangan Aktif berhasil disimpan", [
                'id_no' => $suratKetAktif->id_no,
                'Id_Pemberi' => $pemberi_tugas_id,
                'dokumen_pendukung' => $pathDokumenPendukung,
            ]);

            // TODO: Kirim notifikasi ke admin fakultas
            // if ($adminUser) {
            //     Notifikasi::create([
            //         'Tipe_Notifikasi' => 'Invitation',
            //         'Pesan' => '📬 Pengajuan surat baru: Surat Keterangan Mahasiswa Aktif dari ' . Auth::user()->Name_User,
            //         'Dest_user' => $adminUser->Id_User,
            //         'Source_User' => Auth::id(),
            //         'Is_Read' => false,
            //         'Data_Tambahan' => json_encode([
            //             'id_no' => $suratKetAktif->id_no,
            //             'jenis_surat' => 'aktif',
            //             'action_url' => route('admin_fakultas.surat.kelola'),
            //         ]),
            //         'created_at' => now(),
            //     ]);
            // }

            return redirect()->route('mahasiswa.riwayat')
                ->with('success', 'Pengajuan surat berhasil dikirim! Silakan cek status di Riwayat Surat.');

        } catch (\Exception $e) {
            Log::error("Gagal menyimpan Surat Keterangan Aktif: " . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan pengajuan: ' . $e->getMessage())
                ->withInput();
        }
    }
}
