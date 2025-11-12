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
            'file_pendukung_magang' => 'required|file|mimes:pdf|max:2048', // Max 2MB
        ], [
            'data_spesifik.dosen_pembimbing_1.required' => 'Dosen pembimbing 1 wajib dipilih',
            'data_spesifik.nama_instansi.required' => 'Nama instansi/perusahaan wajib diisi',
            'data_spesifik.alamat_instansi.required' => 'Alamat instansi wajib diisi',
            'file_pendukung_magang.required' => 'Form pengajuan KP wajib diunggah',
            'file_pendukung_magang.mimes' => 'File harus berformat PDF',
            'file_pendukung_magang.max' => 'Ukuran file maksimal 2MB',
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

        // === 3. UPLOAD FILE FORM KP ===
        $pathDokumenPendukung = null;

        try {
            $filePendukung = $request->file('file_pendukung_magang');
            $pathDokumenPendukung = $filePendukung->store('uploads/pendukung/surat-magang', 'public');

            Log::info("File Form KP uploaded", [
                'path' => $pathDokumenPendukung,
                'original_name' => $filePendukung->getClientOriginalName(),
                'size' => $filePendukung->getSize(),
            ]);
        } catch (\Exception $e) {
            Log::error("Gagal upload file Form KP: " . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal mengunggah file Form KP: ' . $e->getMessage())
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
        $tugasSurat->data_spesifik = $dataSpesifik;
        $tugasSurat->dokumen_pendukung = $pathDokumenPendukung;
        $tugasSurat->Status = 'Diterima Admin';
        $tugasSurat->Tanggal_Diberikan_Tugas_Surat = Carbon::now()->format('Y-m-d');
        $tugasSurat->Tanggal_Tenggat_Tugas_Surat = Carbon::now()->addDays(5)->format('Y-m-d'); // 5 hari untuk magang

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
