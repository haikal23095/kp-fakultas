<?php

namespace App\Http\Controllers\PengajuanSurat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TugasSurat;
use App\Models\SuratMagang;
use App\Models\JenisSurat;
use App\Models\JenisPekerjaan;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SuratPengantarMagangController extends Controller
{
    /**
     * Menyimpan pengajuan Surat Pengantar Magang/KP
     */
    public function store(Request $request)
    {
        // Check if files were uploaded (handle PHP upload limits)
        if (!$request->hasFile('file_pendukung_magang')) {
            return redirect()->back()
                ->with('error', 'File Proposal tidak ditemukan. Pastikan ukuran file tidak melebihi 2MB dan format adalah PDF.')
                ->withInput();
        }

        if (!$request->hasFile('file_tanda_tangan')) {
            return redirect()->back()
                ->with('error', 'File Tanda Tangan tidak ditemukan. Pastikan ukuran file tidak melebihi 1MB dan format adalah JPG/PNG.')
                ->withInput();
        }

        // === 1. VALIDASI DATA ===
        $validator = Validator::make($request->all(), [
            'Id_Jenis_Surat' => 'required|numeric',
            'data_spesifik.dosen_pembimbing_1' => 'required|string',
            'data_spesifik.nama_instansi' => 'required|string|max:255',
            'data_spesifik.alamat_instansi' => 'required|string|max:500',
            'data_spesifik.judul_penelitian' => 'nullable|string|max:255',
            'data_spesifik.tanggal_mulai' => 'required|date',
            'data_spesifik.tanggal_selesai' => 'required|date|after_or_equal:data_spesifik.tanggal_mulai',
            'file_pendukung_magang' => 'required|file|mimes:pdf|max:2048', // Max 2MB
            'file_tanda_tangan' => 'required|file|image|mimes:jpg,jpeg,png|max:1024', // Satu file TTD untuk semua
            // [BARU] Support multiple mahasiswa
            'mahasiswa' => 'required|array|min:1|max:5', // Minimal 1, maksimal 5 mahasiswa
            'mahasiswa.*.nama' => 'required|string|max:255',
            'mahasiswa.*.nim' => 'required|numeric',
            'mahasiswa.*.jurusan' => 'required|string|max:255',
            'mahasiswa.*.angkatan' => 'required|integer|min:2000|max:' . (date('Y') + 1),
        ], [
            'data_spesifik.dosen_pembimbing_1.required' => 'Dosen pembimbing wajib dipilih',
            'data_spesifik.nama_instansi.required' => 'Nama instansi/perusahaan wajib diisi',
            'data_spesifik.alamat_instansi.required' => 'Alamat instansi wajib diisi',
            'data_spesifik.tanggal_mulai.required' => 'Tanggal mulai magang wajib diisi.',
            'data_spesifik.tanggal_selesai.required' => 'Tanggal selesai magang wajib diisi.',
            'data_spesifik.tanggal_selesai.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai.',
            'file_pendukung_magang.required' => 'Proposal wajib diunggah.',
            'file_pendukung_magang.mimes' => 'Proposal harus berformat PDF.',
            'file_pendukung_magang.max' => 'Ukuran proposal maksimal 2MB.',
            'file_tanda_tangan.required' => 'Foto tanda tangan wajib diunggah.',
            'file_tanda_tangan.image' => 'File tanda tangan harus berupa gambar.',
            'file_tanda_tangan.mimes' => 'Format tanda tangan harus JPG, JPEG, atau PNG.',
            'file_tanda_tangan.max' => 'Ukuran file tanda tangan maksimal 1MB.',
            'mahasiswa.required' => 'Minimal harus ada 1 mahasiswa.',
            'mahasiswa.*.nama.required' => 'Nama mahasiswa wajib diisi.',
            'mahasiswa.*.nim.required' => 'NIM mahasiswa wajib diisi.',
            'mahasiswa.*.jurusan.required' => 'Jurusan mahasiswa wajib diisi.',
            'mahasiswa.*.angkatan.required' => 'Angkatan mahasiswa wajib diisi.',
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
        $dataMahasiswaArray = [];

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

        // Upload Tanda Tangan (satu file untuk semua mahasiswa)
        try {
            $fileTandaTangan = $request->file('file_tanda_tangan');
            $pathTandaTangan = $fileTandaTangan->store('uploads/tanda-tangan', 'public');

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

        // Build array data mahasiswa (tanpa file tanda tangan per mahasiswa)
        $mahasiswaData = $request->input('mahasiswa');
        foreach ($mahasiswaData as $mhs) {
            $dataMahasiswaArray[] = [
                'nama' => $mhs['nama'],
                'nim' => $mhs['nim'],
                'jurusan' => $mhs['jurusan'],
                'angkatan' => $mhs['angkatan'],
            ];
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

        // === 5.1 AMBIL DATA KOORDINATOR KP SESUAI PRODI MAHASISWA ===
        $idDosenKoordinator = null;
        $mahasiswa = \App\Models\Mahasiswa::where('Id_User', $mahasiswaId)->first();

        if ($mahasiswa && $mahasiswa->Id_Prodi) {
            // Cari User dengan role Kaprodi (Id_Role = 4) sesuai prodi mahasiswa
            $kaprodiUser = \App\Models\User::where('Id_Role', 4)
                ->where(function ($query) use ($mahasiswa) {
                    // Cek apakah dia Dosen di prodi ini
                    $query->whereHas('dosen', function ($q) use ($mahasiswa) {
                        $q->where('Id_Prodi', $mahasiswa->Id_Prodi);
                    })
                        // ATAU Pegawai di prodi ini
                        ->orWhereHas('pegawai', function ($q) use ($mahasiswa) {
                        $q->where('Id_Prodi', $mahasiswa->Id_Prodi);
                    });
                })
                ->with(['dosen', 'pegawai'])
                ->first();

            if ($kaprodiUser && $kaprodiUser->dosen) {
                // Ambil Id_Dosen untuk disimpan sebagai Nama_Koordinator
                $idDosenKoordinator = $kaprodiUser->dosen->Id_Dosen;
            }
        }

        // === 6. SIMPAN KE DATABASE (NORMALISASI) ===
        DB::beginTransaction();

        try {
            // 6.1 Simpan ke tabel Tugas_Surat (data umum)
            // ID akan di-generate otomatis karena AUTO_INCREMENT
            $tugasSurat = new TugasSurat();
            $tugasSurat->Id_Pemberi_Tugas_Surat = $pemberi_tugas_id;
            $tugasSurat->Id_Penerima_Tugas_Surat = $penerima_tugas_id;
            $tugasSurat->Id_Jenis_Surat = $jenisSuratId;
            $tugasSurat->Judul_Tugas_Surat = $judul;
            $tugasSurat->Tanggal_Diberikan_Tugas_Surat = Carbon::now()->format('Y-m-d');
            $tugasSurat->Tanggal_Tenggat_Tugas_Surat = Carbon::now()->addDays(5)->format('Y-m-d');

            // Set status default 'baru' untuk surat yang baru diajukan
            $tugasSurat->Status = 'baru';

            // === 7. SET ID JENIS PEKERJAAN ===
            if ($jenisSurat->Jenis_Pekerjaan) {
                $jenisPekerjaan = JenisPekerjaan::where('Jenis_Pekerjaan', $jenisSurat->Jenis_Pekerjaan)->first();
                if ($jenisPekerjaan) {
                    $tugasSurat->Id_Jenis_Pekerjaan = $jenisPekerjaan->Id_Jenis_Pekerjaan;
                }
            }

            $tugasSurat->save();

            // 6.2 Simpan ke tabel Surat_Magang (data spesifik magang)
            $suratMagang = new SuratMagang();
            $suratMagang->Id_Tugas_Surat = $tugasSurat->Id_Tugas_Surat;

            // Nama Instansi
            $suratMagang->Nama_Instansi = $instansi;

            // Alamat Instansi
            $suratMagang->Alamat_Instansi = $dataSpesifik['alamat_instansi'] ?? null;

            // Tanggal Mulai dan Selesai Magang
            $suratMagang->Tanggal_Mulai = $dataSpesifik['tanggal_mulai'] ?? null;
            $suratMagang->Tanggal_Selesai = $dataSpesifik['tanggal_selesai'] ?? null;

            // Foto Tanda Tangan (satu file untuk semua mahasiswa)
            $suratMagang->Foto_ttd = $pathTandaTangan;

            // Data Mahasiswa (Array of JSON) - tanpa path tanda tangan
            $suratMagang->Data_Mahasiswa = $dataMahasiswaArray;

            // Data Dosen Pembimbing (JSON)
            $dataDosenPembimbing = [
                'dosen_pembimbing_1' => $dataSpesifik['dosen_pembimbing_1'] ?? null,
                'dosen_pembimbing_2' => $dataSpesifik['dosen_pembimbing_2'] ?? null,
            ];
            $suratMagang->Data_Dosen_pembiming = $dataDosenPembimbing;

            // Dokumen Proposal
            $suratMagang->Dokumen_Proposal = $pathDokumenPendukung;

            // Surat Pengantar Magang akan diisi setelah instansi approve (null dulu)
            $suratMagang->Surat_Pengantar_Magang = null;

            // Nama Koordinator adalah ID Dosen Kaprodi (relasi ke tabel Dosen)
            // Simpan Id_Dosen Kaprodi jika ada
            if ($idDosenKoordinator) {
                $suratMagang->Nama_Koordinator = $idDosenKoordinator;
            }

            // Set Status awal (default: Diajukan-ke-koordinator)
            $suratMagang->Status = 'Diajukan-ke-koordinator';
            $suratMagang->Acc_Koordinator = 0; // Belum di-acc

            $suratMagang->save();

            DB::commit();

            Log::info("Surat Pengantar Magang berhasil disimpan (NORMALISASI)", [
                'Id_Tugas_Surat' => $tugasSurat->Id_Tugas_Surat,
                'id_no_surat_magang' => $suratMagang->id_no,
                'Id_Pemberi' => $pemberi_tugas_id,
                'Nama_Instansi' => $instansi,
                'Tanggal_Mulai' => $suratMagang->Tanggal_Mulai,
                'Tanggal_Selesai' => $suratMagang->Tanggal_Selesai,
                'Foto_TTD' => $pathTandaTangan,
                'jumlah_mahasiswa' => count($dataMahasiswaArray),
                'dokumen_proposal' => $pathDokumenPendukung,
            ]);
            
            // Kirim notifikasi ke admin fakultas
            if ($adminUser) {
                Notifikasi::create([
                    'Tipe_Notifikasi' => 'Invitation',
                    'Pesan' => '📬 Pengajuan surat baru: Surat Pengantar Magang/KP ke ' . $instansi . ' dari ' . Auth::user()->Name_User,
                    'Dest_user' => $adminUser->Id_User,
                    'Source_User' => Auth::id(),
                    'Is_Read' => false,
                    'created_at' => now(),
                ]);
            }

            return redirect()->route('mahasiswa.riwayat')
                ->with('success', '✅ Pengajuan Surat Pengantar Magang/KP ke ' . $instansi . ' berhasil dikirim! Anda dapat memantau status pengajuan di halaman ini. Nomor pengajuan: #' . $tugasSurat->Id_Tugas_Surat);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal menyimpan Surat Pengantar Magang: " . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan pengajuan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * API untuk mencari mahasiswa berdasarkan nama atau NIM dalam satu prodi
     */
    public function searchMahasiswa(Request $request)
    {
        $query = $request->get('q', '');
        $currentUser = Auth::user();

        // Ambil data mahasiswa yang sedang login
        $currentMahasiswa = \App\Models\Mahasiswa::where('Id_User', $currentUser->Id_User)->first();

        if (!$currentMahasiswa) {
            return response()->json([]);
        }

        // Ambil mahasiswa satu prodi (kecuali diri sendiri)
        $mahasiswaList = \App\Models\Mahasiswa::query()
            ->where('Id_Prodi', $currentMahasiswa->Id_Prodi)
            ->where('Id_Mahasiswa', '!=', $currentMahasiswa->Id_Mahasiswa)
            ->where(function ($q) use ($query) {
                $q->where('Nama_Mahasiswa', 'LIKE', '%' . $query . '%')
                    ->orWhere('NIM', 'LIKE', '%' . $query . '%');
            })
            ->with('prodi') // Load relasi prodi
            ->limit(10)
            ->get();

        // Format response
        $results = $mahasiswaList->map(function ($mahasiswa) {
            return [
                'id' => $mahasiswa->Id_Mahasiswa,
                'nama' => $mahasiswa->Nama_Mahasiswa,
                'nim' => $mahasiswa->NIM,
                'jurusan' => $mahasiswa->prodi->Nama_Prodi ?? 'Tidak Diketahui',
                'angkatan' => $mahasiswa->Angkatan,
                'label' => $mahasiswa->Nama_Mahasiswa . ' - ' . $mahasiswa->NIM,
            ];
        });

        return response()->json($results);
    }
}