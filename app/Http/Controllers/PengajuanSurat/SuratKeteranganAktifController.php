<?php

namespace App\Http\Controllers\PengajuanSurat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SuratKetAktif;
use App\Models\Mahasiswa;
use App\Models\Prodi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SuratKeteranganAktifController extends Controller
{
    /**
     * Tampilkan form pengajuan Surat Keterangan Mahasiswa Aktif
     */
    public function create()
    {
        $user = Auth::user();
        $mahasiswa = Mahasiswa::where('Id_User', $user->Id_User)->first();

        $prodi = null;
        if ($mahasiswa && $mahasiswa->Id_Prodi) {
            $prodi = Prodi::find($mahasiswa->Id_Prodi);
        }

        // Hardcode jenis surat untuk Surat Keterangan Aktif (ID: 1)
        $jenisSurat = (object) [
            'Id_Jenis_Surat' => 1,
            'Nama_Surat' => 'Surat Keterangan Aktif'
        ];

        return view('mahasiswa.pengajuan.form_surat_aktif', [
            'mahasiswa' => $mahasiswa,
            'prodi' => $prodi,
            'jenisSurat' => $jenisSurat,
        ]);
    }

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
            // Untuk pengajuan oleh mahasiswa, initial status adalah Dikerjakan-admin
            $suratKetAktif->Status = 'Dikerjakan-admin';
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
