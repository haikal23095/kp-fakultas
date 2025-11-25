<?php

namespace App\Http\Controllers\PengajuanSurat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TugasSurat;
use App\Models\SuratMagang;
use App\Models\JenisSurat;
use App\Models\JenisPekerjaan;
use App\Models\Mahasiswa;
use App\Models\Prodi;
use App\Models\User;
use App\Models\SuratMagangDraft;
use App\Models\SuratMagangInvitation;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SuratPengantarMagangController extends Controller
{
    /**
     * Menampilkan form pengajuan surat magang dan AUTO-CREATE draft
     */
    public function create()
    {
        try {
            \Log::info('[DRAFT] Form magang dibuka, auto-creating draft...');

            $user = Auth::user();
            $mahasiswa = Mahasiswa::where('Id_User', $user->Id_User)->first();

            if (!$mahasiswa) {
                return redirect()->back()->with('error', 'Data mahasiswa tidak ditemukan');
            }

            $prodi = null;
            if ($mahasiswa && $mahasiswa->Id_Prodi) {
                $prodi = Prodi::find($mahasiswa->Id_Prodi);
            }

            // Filter dosen berdasarkan prodi mahasiswa
            $dosens = \App\Models\Dosen::query()
                ->when($mahasiswa && $mahasiswa->Id_Prodi, function ($query) use ($mahasiswa) {
                    return $query->where('Id_Prodi', $mahasiswa->Id_Prodi);
                })
                ->orderBy('Nama_Dosen', 'asc')
                ->get();

            // Ambil Kaprodi (user dengan role 4) sesuai prodi mahasiswa
            $kaprodi = null;
            $kaprodiName = null;
            $kaprodiNIP = null;

            if ($mahasiswa && $mahasiswa->Id_Prodi) {
                $kaprodiUser = User::where('Id_Role', 4)
                    ->where(function ($query) use ($mahasiswa) {
                        $query->whereHas('dosen', function ($q) use ($mahasiswa) {
                            $q->where('Id_Prodi', $mahasiswa->Id_Prodi);
                        })
                            ->orWhereHas('pegawai', function ($q) use ($mahasiswa) {
                                $q->where('Id_Prodi', $mahasiswa->Id_Prodi);
                            });
                    })
                    ->with(['dosen', 'pegawai'])
                    ->first();

                if ($kaprodiUser) {
                    $kaprodi = $kaprodiUser;
                    if ($kaprodiUser->dosen) {
                        $kaprodiName = $kaprodiUser->dosen->Nama_Dosen;
                        $kaprodiNIP = $kaprodiUser->dosen->NIP;
                    } elseif ($kaprodiUser->pegawai) {
                        $kaprodiName = $kaprodiUser->pegawai->Nama_Pegawai;
                        $kaprodiNIP = $kaprodiUser->pegawai->NIP;
                    }
                }
            }

            // Ambil ID jenis surat
            $jenisSurat = JenisSurat::where('Nama_Surat', 'Surat Pengantar KP/Magang')->first();

            if (!$jenisSurat) {
                return redirect()->back()->with('error', 'Jenis surat tidak ditemukan');
            }

            // AUTO-CREATE DRAFT saat buka form
            \Log::info('[DRAFT] Creating draft for mahasiswa', [
                'mahasiswa_id' => $mahasiswa->Id_Mahasiswa,
                'jenis_surat_id' => $jenisSurat->Id_Jenis_Surat
            ]);

            // Check apakah sudah ada draft untuk mahasiswa ini dan jenis surat ini
            $existingDraft = SuratMagangDraft::where('Id_Mahasiswa_Pembuat', $mahasiswa->Id_Mahasiswa)
                ->where('Id_Jenis_Surat', $jenisSurat->Id_Jenis_Surat)
                ->first();

            if ($existingDraft) {
                \Log::info('[DRAFT] Draft already exists', ['draft_id' => $existingDraft->id_draft]);
            } else {
                $draft = SuratMagangDraft::create([
                    'Id_Mahasiswa_Pembuat' => $mahasiswa->Id_Mahasiswa,
                    'Id_Jenis_Surat' => $jenisSurat->Id_Jenis_Surat,
                    'Data_Mahasiswa_Confirmed' => [
                        [
                            'id' => $mahasiswa->Id_Mahasiswa,
                            'nama' => $mahasiswa->Nama_Mahasiswa,
                            'nim' => $mahasiswa->NIM,
                            'jurusan' => $prodi ? $prodi->Nama_Prodi : '-',
                            'angkatan' => $mahasiswa->Angkatan
                        ]
                    ],
                    'Data_Mahasiswa_Pending' => [],
                    'Nama_Instansi' => null,
                    'Alamat_Instansi' => null,
                    'Judul_Penelitian' => null,
                    'Tanggal_Mulai' => null,
                    'Tanggal_Selesai' => null,
                    'Dosen_Pembimbing_1' => null,
                    'Dosen_Pembimbing_2' => null
                ]);

                \Log::info('[DRAFT] Draft created successfully', [
                    'draft_id' => $draft->id_draft,
                    'confirmed_count' => count($draft->Data_Mahasiswa_Confirmed)
                ]);
            }

            return view('mahasiswa.form_surat_magang', [
                'mahasiswa' => $mahasiswa,
                'prodi' => $prodi,
                'dosens' => $dosens,
                'kaprodi' => $kaprodi,
                'kaprodiName' => $kaprodiName,
                'kaprodiNIP' => $kaprodiNIP,
                'jenisSurat' => $jenisSurat
            ]);

        } catch (\Exception $e) {
            \Log::error('[DRAFT] Error in create method', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

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

            // CATATAN: Status sudah dipindah ke tabel Surat_Magang, tidak perlu di-set di sini

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

            return redirect()->route('mahasiswa.pengajuan.magang.form')
                ->with('success', 'Pengajuan Surat Pengantar Magang/KP ke ' . $instansi . ' berhasil dikirim! Nomor pengajuan: #' . $tugasSurat->Id_Tugas_Surat);

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

        // Format response dengan Status_KP
        $results = $mahasiswaList->map(function ($mahasiswa) {
            $statusKP = $mahasiswa->Status_KP ?? 'Tidak_Sedang_Melaksanakan';
            $isAvailable = in_array($statusKP, ['Tidak_Sedang_Melaksanakan', 'Telah_Melaksanakan']);

            return [
                'id' => $mahasiswa->Id_Mahasiswa,
                'nama' => $mahasiswa->Nama_Mahasiswa,
                'nim' => $mahasiswa->NIM,
                'jurusan' => $mahasiswa->prodi->Nama_Prodi ?? 'Tidak Diketahui',
                'angkatan' => $mahasiswa->Angkatan,
                'status_kp' => $statusKP,
                'is_available' => $isAvailable,
                'label' => $mahasiswa->Nama_Mahasiswa . ' - ' . $mahasiswa->NIM,
            ];
        });

        return response()->json($results);
    }

    /**
     * Save draft dan kirim invitation ke mahasiswa yang ditambahkan
     */
    public function saveDraft(Request $request)
    {
        \Log::info('[DRAFT] saveDraft called', [
            'Id_Jenis_Surat' => $request->Id_Jenis_Surat,
            'nama_instansi' => $request->nama_instansi,
            'mahasiswa_id_tambahan' => $request->mahasiswa_id_tambahan
        ]);

        DB::beginTransaction();

        try {
            $mahasiswa = Auth::user()->mahasiswa;

            if (!$mahasiswa) {
                throw new \Exception('Mahasiswa not found for current user');
            }

            \Log::info('[DRAFT] Creating/updating draft for mahasiswa', [
                'Id_Mahasiswa' => $mahasiswa->Id_Mahasiswa
            ]);

            // Get or create draft
            $draft = SuratMagangDraft::firstOrCreate(
                [
                    'Id_Mahasiswa_Pembuat' => $mahasiswa->Id_Mahasiswa,
                    'Id_Jenis_Surat' => $request->Id_Jenis_Surat
                ],
                [
                    'Data_Mahasiswa_Confirmed' => [
                        [
                            'id' => $mahasiswa->Id_Mahasiswa,
                            'nama' => $mahasiswa->Nama_Mahasiswa,
                            'nim' => $mahasiswa->NIM,
                            'angkatan' => $mahasiswa->Angkatan,
                            'status' => 'confirmed'
                        ]
                    ],
                    'Data_Mahasiswa_Pending' => []
                ]
            );

            \Log::info('[DRAFT] Draft found/created', ['id_draft' => $draft->id_draft]);

            // Update draft data
            $draft->Nama_Instansi = $request->nama_instansi;
            $draft->Alamat_Instansi = $request->alamat_instansi;
            $draft->Tanggal_Mulai = $request->tanggal_mulai;
            $draft->Tanggal_Selesai = $request->tanggal_selesai;
            $draft->Judul_Penelitian = $request->judul_penelitian;
            $draft->Dosen_Pembimbing_1 = $request->dosen_pembimbing_1;
            $draft->Dosen_Pembimbing_2 = $request->dosen_pembimbing_2;
            $draft->save();

            \Log::info('[DRAFT] Draft updated', [
                'id_draft' => $draft->id_draft,
                'Nama_Instansi' => $draft->Nama_Instansi
            ]);

            // Handle mahasiswa baru yang ditambahkan
            if ($request->has('mahasiswa_id_tambahan')) {
                $mahasiswaId = $request->mahasiswa_id_tambahan;

                \Log::info('[DRAFT] Adding mahasiswa to invitation', [
                    'mahasiswa_id' => $mahasiswaId
                ]);

                // Cek apakah sudah ada invitation
                $existingInvitation = SuratMagangInvitation::where('id_draft', $draft->id_draft)
                    ->where('Id_Mahasiswa_Diundang', $mahasiswaId)
                    ->first();

                if (!$existingInvitation) {
                    \Log::info('[DRAFT] Creating new invitation');

                    // Create invitation
                    $invitation = SuratMagangInvitation::create([
                        'id_draft' => $draft->id_draft,
                        'Id_Mahasiswa_Diundang' => $mahasiswaId,
                        'Id_Mahasiswa_Pengundang' => $mahasiswa->Id_Mahasiswa,
                        'status' => 'pending'
                    ]);

                    \Log::info('[DRAFT] Invitation created', [
                        'id_invitation' => $invitation->id_invitation
                    ]);

                    // Get mahasiswa data
                    $mahasiswaDiundang = Mahasiswa::find($mahasiswaId);

                    // Update Data_Mahasiswa_Pending
                    $pendingList = is_array($draft->Data_Mahasiswa_Pending) ? $draft->Data_Mahasiswa_Pending : [];
                    $pendingList[] = [
                        'id' => $mahasiswaDiundang->Id_Mahasiswa,
                        'nama' => $mahasiswaDiundang->Nama_Mahasiswa,
                        'nim' => $mahasiswaDiundang->NIM,
                        'angkatan' => $mahasiswaDiundang->Angkatan,
                        'status' => 'pending',
                        'invitation_id' => $invitation->id_invitation
                    ];
                    $draft->Data_Mahasiswa_Pending = $pendingList;
                    $draft->save();

                    // Create notification
                    Notifikasi::create([
                        'Tipe_Notifikasi' => 'Invitation',
                        'Pesan' => $mahasiswa->Nama_Mahasiswa . ' mengundang Anda untuk bergabung dalam pengajuan magang ke ' . ($request->nama_instansi ?? 'instansi tertentu'),
                        'Dest_User' => $mahasiswaDiundang->Id_User,
                        'Source_User' => Auth::user()->Id_User,
                        'Is_Read' => false,
                        'Data_Tambahan' => [
                            'invitation_id' => $invitation->id_invitation,
                            'draft_id' => $draft->id_draft
                        ]
                    ]);

                    \Log::info('[DRAFT] Notification created for invitation');
                } else {
                    \Log::info('[DRAFT] Invitation already exists, skipping');
                }
            }

            DB::commit();

            \Log::info('[DRAFT] Transaction committed successfully', [
                'draft_id' => $draft->id_draft
            ]);

            return response()->json([
                'success' => true,
                'draft_id' => $draft->id_draft,
                'message' => 'Draft berhasil disimpan'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('[DRAFT] Error saving draft', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => $e->getMessage(),
                'success' => false
            ], 500);
        }
    }

    /**
     * Load draft untuk mahasiswa yang login
     */
    public function loadDraft(Request $request)
    {
        try {
            $mahasiswa = Auth::user()->mahasiswa;

            $draft = SuratMagangDraft::with(['invitations.mahasiswaDiundang'])
                ->where('Id_Mahasiswa_Pembuat', $mahasiswa->Id_Mahasiswa)
                ->latest()
                ->first();

            if (!$draft) {
                return response()->json(['draft' => null]);
            }

            return response()->json([
                'draft' => $draft,
                'mahasiswa_confirmed' => is_array($draft->Data_Mahasiswa_Confirmed) ? $draft->Data_Mahasiswa_Confirmed : [],
                'mahasiswa_pending' => is_array($draft->Data_Mahasiswa_Pending) ? $draft->Data_Mahasiswa_Pending : [],
                'invitations' => $draft->invitations
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Accept invitation
     */
    public function acceptInvitation($invitationId)
    {
        DB::beginTransaction();

        try {
            $invitation = SuratMagangInvitation::findOrFail($invitationId);
            $mahasiswa = Auth::user()->mahasiswa;

            // Validasi: hanya yang diundang yang bisa accept
            if ($invitation->Id_Mahasiswa_Diundang != $mahasiswa->Id_Mahasiswa) {
                abort(403, 'Anda tidak memiliki akses');
            }

            // Update invitation status
            $invitation->status = 'accepted';
            $invitation->responded_at = now();
            $invitation->save();

            // Update draft: move dari pending ke confirmed
            $draft = $invitation->draft;
            $pendingList = is_array($draft->Data_Mahasiswa_Pending) ? $draft->Data_Mahasiswa_Pending : [];
            $confirmedList = is_array($draft->Data_Mahasiswa_Confirmed) ? $draft->Data_Mahasiswa_Confirmed : [];

            // Find and move mahasiswa
            foreach ($pendingList as $key => $mhs) {
                if ($mhs['id'] == $mahasiswa->Id_Mahasiswa) {
                    $mhs['status'] = 'confirmed';
                    $confirmedList[] = $mhs;
                    unset($pendingList[$key]);
                    break;
                }
            }

            $draft->Data_Mahasiswa_Pending = array_values($pendingList);
            $draft->Data_Mahasiswa_Confirmed = $confirmedList;
            $draft->save();

            // Notify pembuat
            Notifikasi::create([
                'Tipe_Notifikasi' => 'Accepted',
                'Pesan' => $mahasiswa->Nama_Mahasiswa . ' menerima undangan magang Anda',
                'Dest_User' => $invitation->mahasiswaPengundang->Id_User,
                'Source_User' => Auth::user()->Id_User,
                'Is_Read' => false
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Undangan berhasil diterima!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Reject invitation
     */
    public function rejectInvitation(Request $request, $invitationId)
    {
        $request->validate([
            'keterangan' => 'required|min:5'
        ]);

        DB::beginTransaction();

        try {
            $invitation = SuratMagangInvitation::findOrFail($invitationId);
            $mahasiswa = Auth::user()->mahasiswa;

            // Validasi
            if ($invitation->Id_Mahasiswa_Diundang != $mahasiswa->Id_Mahasiswa) {
                abort(403);
            }

            $invitation->status = 'rejected';
            $invitation->keterangan = $request->keterangan;
            $invitation->responded_at = now();
            $invitation->save();

            // Update draft: remove dari pending
            $draft = $invitation->draft;
            $pendingList = is_array($draft->Data_Mahasiswa_Pending) ? $draft->Data_Mahasiswa_Pending : [];

            foreach ($pendingList as $key => $mhs) {
                if ($mhs['id'] == $mahasiswa->Id_Mahasiswa) {
                    unset($pendingList[$key]);
                    break;
                }
            }

            $draft->Data_Mahasiswa_Pending = array_values($pendingList);
            $draft->save();

            // Notify pembuat
            Notifikasi::create([
                'Tipe_Notifikasi' => 'Rejected',
                'Pesan' => $mahasiswa->Nama_Mahasiswa . ' menolak undangan magang: ' . $request->keterangan,
                'Dest_User' => $invitation->mahasiswaPengundang->Id_User,
                'Source_User' => Auth::user()->Id_User,
                'Is_Read' => false
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Undangan ditolak');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Delete draft saat user keluar dari form
     */
    public function deleteDraft(Request $request)
    {
        try {
            $mahasiswa = Auth::user()->mahasiswa;

            if ($request->has('draft_id')) {
                $deleted = SuratMagangDraft::where('id_draft', $request->draft_id)
                    ->where('Id_Mahasiswa_Pembuat', $mahasiswa->Id_Mahasiswa)
                    ->delete();
            } else {
                // Hapus semua draft mahasiswa ini untuk jenis surat ini
                $deleted = SuratMagangDraft::where('Id_Mahasiswa_Pembuat', $mahasiswa->Id_Mahasiswa)
                    ->where('Id_Jenis_Surat', $request->Id_Jenis_Surat)
                    ->delete();
            }

            \Log::info('[DRAFT] Draft deleted on exit', ['deleted_count' => $deleted]);

            return response()->json([
                'success' => true,
                'message' => 'Draft berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            \Log::error('[DRAFT] Error deleting draft', ['error' => $e->getMessage()]);
            return response()->json([
                'error' => $e->getMessage(),
                'success' => false
            ], 500);
        }
    }
}