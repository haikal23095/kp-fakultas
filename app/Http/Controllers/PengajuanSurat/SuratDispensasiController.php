<?php

namespace App\Http\Controllers\PengajuanSurat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\TugasSurat;
use App\Models\SuratDispensasi;
use App\Models\JenisSurat;
use App\Models\Pejabat;
use App\Models\Mahasiswa;
use Carbon\Carbon;

class SuratDispensasiController extends Controller
{
    /**
     * Show the form for creating a new Surat Dispensasi.
     */
    public function create()
    {
        return view('mahasiswa.pengajuan-surat.form_surat_dispensasi');
    }

    /**
     * Store a newly created Surat Dispensasi in storage.
     */
    public function store(Request $request)
    {
        // Validasi
        $validator = Validator::make($request->all(), [
            'nama_kegiatan' => 'required|string|max:255',
            'instansi_penyelenggara' => 'nullable|string|max:255',
            'tempat_pelaksanaan' => 'nullable|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'file_lampiran' => 'nullable|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
            'nama_kegiatan.required' => 'Nama kegiatan/alasan wajib diisi.',
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi.',
            'tanggal_selesai.required' => 'Tanggal selesai wajib diisi.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',
            'file_lampiran.mimes' => 'File lampiran harus berformat PDF, JPG, JPEG, atau PNG.',
            'file_lampiran.max' => 'File lampiran maksimal 2MB.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $user = Auth::user();
            $mahasiswa = Mahasiswa::where('Id_User', $user->Id_User)->with('prodi.fakultas')->first();

            if (!$mahasiswa) {
                throw new \Exception('Data mahasiswa tidak ditemukan.');
            }

            // Cari Jenis Surat Dispensasi
            $jenisSurat = JenisSurat::where('Nama_Surat', 'LIKE', '%Dispensasi%')->first();
            
            if (!$jenisSurat) {
                throw new \Exception('Jenis surat Dispensasi tidak ditemukan di database. Hubungi admin.');
            }

            // Cari Pejabat (Wakil Dekan 3)
            $pejabat = Pejabat::where(function($query) {
                $query->where('Nama_Jabatan', 'LIKE', '%Wakil Dekan III%')
                      ->orWhere('Nama_Jabatan', 'LIKE', '%Wakil Dekan 3%')
                      ->orWhere('Nama_Jabatan', 'LIKE', '%Kemahasiswaan%')
                      ->orWhere('Nama_Jabatan', 'LIKE', '%WD3%')
                      ->orWhere('Nama_Jabatan', 'LIKE', '%WD 3%');
            })->first();

            $idPejabat = $pejabat ? $pejabat->Id_Pejabat : null;

            // Upload file lampiran/bukti pendukung (opsional)
            $fileLampiranPath = null;
            if ($request->hasFile('file_lampiran')) {
                $fileLampiran = $request->file('file_lampiran');
                
                // Validate file
                if (!$fileLampiran->isValid()) {
                    throw new \Exception('File upload gagal. File tidak valid.');
                }
                
                $extension = $fileLampiran->getClientOriginalExtension();
                $fileLampiranName = 'lampiran_dispen_' . $mahasiswa->NIM . '_' . time() . '.' . $extension;
                
                // Store file to public disk (storage/app/public/surat_dispensasi/)
                $storedPath = Storage::disk('public')->putFileAs(
                    'surat_dispensasi',
                    $fileLampiran,
                    $fileLampiranName
                );
                
                if (!$storedPath) {
                    throw new \Exception('File upload gagal. Tidak bisa menyimpan ke storage.');
                }
                
                // Verify file exists using Storage facade
                if (!Storage::disk('public')->exists($storedPath)) {
                    \Log::error('File upload dispensasi: File tidak ditemukan setelah upload', [
                        'expected_path' => $storedPath,
                        'full_path' => storage_path('app/public/' . $storedPath)
                    ]);
                    throw new \Exception('File upload gagal. File tidak ditemukan setelah disimpan.');
                }
                
                \Log::info('File upload dispensasi berhasil', [
                    'filename' => $fileLampiranName,
                    'stored_path' => $storedPath,
                    'full_path' => storage_path('app/public/' . $storedPath),
                    'size' => Storage::disk('public')->size($storedPath)
                ]);
                
                $fileLampiranPath = $storedPath; // Will be 'surat_dispensasi/filename.ext'
            }

            // Insert ke Tabel Tugas_Surat
            $tugasSurat = TugasSurat::create([
                'Id_Pemberi_Tugas_Surat' => $user->Id_User,
                'Id_Jenis_Surat' => $jenisSurat->Id_Jenis_Surat,
                'Judul_Tugas_Surat' => 'Permohonan Surat Dispensasi - ' . $request->nama_kegiatan,
                'Status' => 'baru',
                'Tanggal_Pembuatan_Tugas' => Carbon::now()->toDateString(),
            ]);

            // Insert ke Tabel Surat_Dispensasi
            SuratDispensasi::create([
                'Id_Tugas_Surat' => $tugasSurat->Id_Tugas_Surat,
                'Id_User' => $user->Id_User,
                'Id_Pejabat_Wadek3' => $idPejabat,
                'nama_kegiatan' => $request->nama_kegiatan,
                'instansi_penyelenggara' => $request->instansi_penyelenggara,
                'tempat_pelaksanaan' => $request->tempat_pelaksanaan,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'file_lampiran' => $fileLampiranPath,
            ]);

            DB::commit();

            return redirect()->route('mahasiswa.pengajuan.create')->with('success', 'Surat Dispensasi berhasil diajukan! Mohon tunggu proses verifikasi dari Admin dan Wakil Dekan 3.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }
}
