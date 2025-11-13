<?php

namespace App\Http\Controllers\PengajuanSurat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TugasSurat;
use App\Models\JenisSurat;
use App\Models\JenisPekerjaan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB; // <--- ditambahkan

class SuratPengantarMagangController extends Controller
{
    /**
     * Menyimpan pengajuan Surat Pengantar Magang/KP
     */
    public function store(Request $request)
    {
        // === 1. VALIDASI DATA ===
        $validator = Validator::make($request->all(), [
            'Id_Jenis_Surat' => 'required|numeric',
            'data_spesifik.dosen_pembimbing_1' => 'required|string',
            'data_spesifik.nama_instansi' => 'required|string|max:255',
            'data_spesifik.alamat_instansi' => 'required|string|max:500',
            'data_spesifik.judul_penelitian' => 'nullable|string|max:255', // [BARU]
            'data_spesifik.tanggal_mulai' => 'required|date', // [BARU]
            'data_spesifik.tanggal_selesai' => 'required|date|after_or_equal:data_spesifik.tanggal_mulai', // [BARU]
            'file_pendukung_magang' => 'required|file|mimes:pdf|max:2048', // Max 2MB
            'file_tanda_tangan' => 'required|file|image|mimes:jpg,jpeg,png|max:1024', // [BARU] Max 1MB
        ], [
            'data_spesifik.dosen_pembimbing_1.required' => 'Dosen pembimbing wajib dipilih',
            'data_spesifik.nama_instansi.required' => 'Nama instansi/perusahaan wajib diisi',
            'data_spesifik.alamat_instansi.required' => 'Alamat instansi wajib diisi',
            'data_spesifik.tanggal_mulai.required' => 'Tanggal mulai magang wajib diisi.',
            'data_spesifik.tanggal_selesai.required' => 'Tanggal selesai magang wajib diisi.',
            'data_spesifik.tanggal_selesai.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai.',
            'file_pendukung_magang.required' => 'Proposal wajib diunggah.', // [DIUBAH]
            'file_pendukung_magang.mimes' => 'Proposal harus berformat PDF.', // [DIUBAH]
            'file_pendukung_magang.max' => 'Ukuran proposal maksimal 2MB.', // [DIUBAH]
            'file_tanda_tangan.required' => 'Foto tanda tangan wajib diunggah.', // [BARU]
            'file_tanda_tangan.image' => 'File tanda tangan harus berupa gambar.', // [BARU]
            'file_tanda_tangan.mimes' => 'Format tanda tangan harus JPG, JPEG, atau PNG.', // [BARU]
            'file_tanda_tangan.max' => 'Ukuran file tanda tangan maksimal 1MB.', // [BARU]
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

        $instansi = $dataSpesifik['nama_instansi'] ?? 'Instansi Tujuan';
        $deskripsi = "Pengajuan surat pengantar magang/KP ke " . $instansi;

        // === 3. UPLOAD FILE PROPOSAL & TANDA TANGAN ===
        $pathDokumenPendukung = null;
        $pathTandaTangan = null;

        // Upload Proposal (file_pendukung_magang)
        try {
            $filePendukung = $request->file('file_pendukung_magang');
            $pathDokumenPendukung = $filePendukung->store('uploads/pendukung/surat-magang', 'public');

            Log::info("File Proposal KP/Magang uploaded", [
                'path' => $pathDokumenPendukung,
                'original_name' => $filePendukung->getClientOriginalName(),
                'size' => $filePendukung->getSize(),
            ]);
        } catch (\Exception $e) {
            Log::error("Gagal upload file Proposal: " . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal mengunggah file Proposal: ' . $e->getMessage())
                ->withInput();
        }

        // [BARU] Upload Tanda Tangan (file_tanda_tangan)
        try {
            $fileTandaTangan = $request->file('file_tanda_tangan');
            $pathTandaTangan = $fileTandaTangan->store('uploads/tanda-tangan', 'public');
            
            // Simpan path tanda tangan ke dalam JSON data_spesifik
            $dataSpesifik['path_tanda_tangan'] = $pathTandaTangan;

            Log::info("File Tanda Tangan uploaded", [
                'path' => $pathTandaTangan,
                'original_name' => $fileTandaTangan->getClientOriginalName(),
                'size' => $fileTandaTangan->getSize(),
            ]);
        } catch (\Exception $e) {
            Log::error("Gagal upload file Tanda Tangan: " . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal mengunggah file Tanda Tangan: ' . $e->getMessage())
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

        // === 5. TENTUKAN PEMBERI & PENERIMA TUGAS ===
        $pemberi_tugas_id = $mahasiswaId; // Mahasiswa yang submit

        // Cari Admin Fakultas sebagai penerima tugas
        $adminUser = \App\Models\User::whereHas('role', function ($q) {
            $q->where('Name_Role', 'Admin Fakultas');
        })->first();

        $penerima_tugas_id = $adminUser ? $adminUser->Id_User : $pemberi_tugas_id;

        // === 6. SIMPAN KE DATABASE ===
        $tugasSurat = new TugasSurat();
        $tugasSurat->Id_Pemberi_Tugas_Surat = $pemberi_tugas_id;
        $tugasSurat->Id_Penerima_Tugas_Surat = $penerima_tugas_id;
        $tugasSurat->Id_Jenis_Surat = $jenisSuratId;
        $tugasSurat->Judul_Tugas_Surat = $judul;
        $tugasSurat->Deskripsi_Tugas_Surat = $deskripsi;
        
    // [DIUBAH] $dataSpesifik sekarang sudah berisi path_tanda_tangan
    // Simpan flag workflow admin ke dalam JSON supaya tidak perlu mengubah ENUM di DB
    $dataSpesifik['admin_status'] = 'Diterima Admin';

    // Pastikan data_spesifik disimpan sebagai JSON string (model mungkin sudah cast, tapi aman untuk encode)
    $tugasSurat->data_spesifik = is_array($dataSpesifik) ? json_encode($dataSpesifik, JSON_UNESCAPED_UNICODE) : $dataSpesifik;

    $tugasSurat->dokumen_pendukung = $pathDokumenPendukung;

    // Ambil daftar nilai ENUM riil dari kolom Status di DB untuk menghindari warning 1265
    try {
        $col = DB::select("SHOW COLUMNS FROM `Tugas_Surat` LIKE 'Status'");
        $allowedStatuses = [];
        if (!empty($col) && isset($col[0]->Type)) {
            // Type contoh: "enum('Dikerjakan','Selesai','Terlambat')"
            if (preg_match("/^enum\\((.*)\\)$/i", $col[0]->Type, $matches)) {
                $vals = str_getcsv($matches[1], ',', "'");
                $allowedStatuses = array_map(function($v){ return $v; }, $vals);
            }
        }
    } catch (\Exception $ex) {
        Log::warning("Gagal ambil enum Status dari DB: " . $ex->getMessage());
        $allowedStatuses = ['Dikerjakan','Selesai','Terlambat'];
    }

    // Tentukan status yang aman untuk disimpan (cari dari request dulu, atau fallback ke nilai pertama/ 'Dikerjakan')
    $incomingStatus = $request->input('Status', null);
    if ($incomingStatus && in_array($incomingStatus, $allowedStatuses, true)) {
        $saveStatus = $incomingStatus;
    } elseif (in_array('Dikerjakan', $allowedStatuses, true)) {
        $saveStatus = 'Dikerjakan';
    } else {
        $saveStatus = $allowedStatuses[0] ?? null;
    }
    $tugasSurat->Status = $saveStatus;
    // Simpan sebagai Carbon instance (model sudah melakukan cast ke date)
    $tugasSurat->Tanggal_Diberikan_Tugas_Surat = Carbon::now();
    $tugasSurat->Tanggal_Tenggat_Tugas_Surat = Carbon::now()->addDays(5); // 5 hari untuk magang

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
            $tugasSurat->save();

            Log::info("Surat Pengantar Magang berhasil disimpan", [
                'Id_Tugas_Surat' => $tugasSurat->Id_Tugas_Surat,
                'Id_Pemberi' => $pemberi_tugas_id,
                'Instansi' => $instansi,
                'dokumen_pendukung' => $tugasSurat->dokumen_pendukung,
                'tanda_tangan' => $dataSpesifik['path_tanda_tangan'] // [BARU]
            ]);

            return redirect()->route('mahasiswa.pengajuan.create')
                ->with('success', 'Pengajuan Surat Pengantar Magang/KP ke ' . $instansi . ' berhasil dikirim! Nomor pengajuan: #' . $tugasSurat->Id_Tugas_Surat);

        } catch (\Exception $e) {
            Log::error("Gagal menyimpan Surat Pengantar Magang: " . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan pengajuan: ' . $e->getMessage())
                ->withInput();
        }
    }
}