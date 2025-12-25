<?php

use Illuminate\Support\Facades\Route;
// Admin Prodi - Preview Surat Magang standalone
Route::get('/admin-prodi/surat/preview-magang/{id_no}', [\App\Http\Controllers\Admin_Prodi\ManajemenSuratController::class, 'previewMagang'])
    ->name('admin_prodi.surat.preview_magang');
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin_Prodi\DetailSuratController;
use App\Http\Controllers\Admin_Prodi\ManajemenSuratController;
use App\Http\Controllers\Admin_Fakultas\DetailSuratController as FakultasDetailSuratController;
use App\Http\Controllers\Admin_Fakultas\ManajemenSuratController as FakultasManajemenSuratController;
use App\Http\Controllers\Admin_Fakultas\SuratMagangController as FakultasSuratMagangController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\SuratVerificationController;
use App\Http\Controllers\QrCodeVerificationController;

// Import Controller untuk Pengajuan Surat (Modular)
use App\Http\Controllers\PengajuanSurat\SuratKeteranganAktifController;
use App\Http\Controllers\PengajuanSurat\SuratPengantarMagangController;
use App\Http\Controllers\PengajuanSurat\SuratLegalisirController;
use App\Http\Controllers\Admin_Fakultas\SuratLegalisirController as FakultasSuratLegalisirController;

// Impor Model untuk route pengajuan
use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\Prodi;
use App\Models\JenisSurat; // Pastikan ini ada
use App\Models\TugasSurat;
use App\Models\Role;
use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect root ke halaman login
Route::get('/', function () {
    return redirect()->route('login');
});

// ============================================================
// PUBLIC ROUTES (Tanpa Auth - untuk Verifikasi QR Code)
// ============================================================
Route::get('/verify-surat/{id}', [QrCodeVerificationController::class, 'verify'])->name('surat.verify.id');
Route::get('/verify/{token}', [QrCodeVerificationController::class, 'verifyByToken'])->name('surat.verify');

// Routes untuk autentikasi
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Route untuk logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// AREA PENGGUNA YANG SUDAH LOGIN
Route::middleware('auth')->group(function () {

    // DASHBOARD UTAMA & ROLE
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard/admin_prodi', [AuthController::class, 'dashboardAdmin'])->name('dashboard.admin_prodi');
    Route::get('/dashboard/admin-fakultas', [AuthController::class, 'dashboardAdminFakultas'])->name('dashboard.admin_fakultas');
    Route::get('/dashboard/dekan', [AuthController::class, 'dashboardDekan'])->name('dashboard.dekan');
    Route::get('/dashboard/kajur', [AuthController::class, 'dashboardKajur'])->name('dashboard.kajur');
    Route::get('/dashboard/kaprodi', [AuthController::class, 'dashboardKaprodi'])->name('dashboard.kaprodi');
    Route::get('/dashboard/dosen', [AuthController::class, 'dashboardDosen'])->name('dashboard.dosen');
    Route::get('/dashboard/mahasiswa', [AuthController::class, 'dashboardMahasiswa'])->name('dashboard.mahasiswa');
    Route::get('/dashboard/default', [AuthController::class, 'dashboardDefault'])->name('dashboard.default');

    // PROFILE ROUTES (Available for all authenticated users)
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // NOTIFIKASI ROUTES (Available for all authenticated users)
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::get('/notifikasi/recent', [NotifikasiController::class, 'getRecent'])->name('notifikasi.recent');
    Route::post('/notifikasi/{id}/read', [NotifikasiController::class, 'markAsRead'])->name('notifikasi.markRead');
    Route::post('/notifikasi/mark-all-read', [NotifikasiController::class, 'markAllAsRead'])->name('notifikasi.markAllRead');
    Route::delete('/notifikasi/{id}', [NotifikasiController::class, 'destroy'])->name('notifikasi.delete');

    // FITUR ADMIN PRODI
    Route::prefix('admin-prodi')->name('admin_prodi.')->group(function () {

        Route::get('/manajemen-surat', [ManajemenSuratController::class, 'index'])
            ->name('surat.manage');

        // Preview dokumen pendukung (PDF) di browser
        Route::get('/surat/{id}/preview', [ManajemenSuratController::class, 'previewDokumen'])->name('surat.preview');

        // Detail surat (lihat detail berdasarkan Id_Tugas_Surat)
        Route::get('/surat/{id}/detail', [DetailSuratController::class, 'show'])->name('surat.detail');
        // Download dokumen pendukung (admin)
        Route::get('/surat/{id}/download', [DetailSuratController::class, 'downloadPendukung'])->name('surat.download');
        // Proses upload draft final / ajukan ke Dekan
        Route::post('/surat/{id}/process-draft', [DetailSuratController::class, 'processDraft'])->name('surat.process_draft');

        // Route: Tolak Surat
        Route::post('/surat/{id}/reject', [DetailSuratController::class, 'reject'])->name('surat.reject');

        // Route: update status tugas (hanya admin)
        Route::post('/manajemen-surat/{id}/update-status', [ManajemenSuratController::class, 'updateStatus'])
            ->name('surat.updateStatus');

        // Route: Tambah nomor surat dan teruskan ke Dekan
        Route::post('/surat/{id}/add-nomor', [ManajemenSuratController::class, 'addNomorSurat'])
            ->name('surat.add_nomor');

        // ... (route /arsip-surat) ...
        Route::get('/arsip-surat', [ManajemenSuratController::class, 'archive'])
            ->name('surat.archive');

        Route::get('/pengaturan', function () {
            return view('admin_prodi.pengaturan');
        })->name('settings.index');
    });

    // FITUR ADMIN FAKULTAS
    Route::prefix('admin-fakultas')->name('admin_fakultas.')->group(function () {

        // DEBUG ROUTE - HAPUS SETELAH TESTING
        Route::get('/debug-surat', function() {
            $user = Auth::user()->load(['pegawaiFakultas.fakultas']);
            $fakultasId = $user->pegawaiFakultas?->Id_Fakultas;
            
            $allSurat = \App\Models\TugasSurat::with(['jenisSurat', 'pemberiTugas.mahasiswa.prodi.fakultas'])
                ->where(function ($q) use ($fakultasId) {
                    $q->whereHas('pemberiTugas.mahasiswa.prodi.fakultas', function ($subQ) use ($fakultasId) {
                        $subQ->where('Id_Fakultas', $fakultasId);
                    })
                    ->orWhereHas('pemberiTugas.dosen.prodi.fakultas', function ($subQ) use ($fakultasId) {
                        $subQ->where('Id_Fakultas', $fakultasId);
                    })
                    ->orWhereHas('pemberiTugas.pegawai.prodi.fakultas', function ($subQ) use ($fakultasId) {
                        $subQ->where('Id_Fakultas', $fakultasId);
                    });
                })
                ->get();
            
            $grouped = $allSurat->groupBy('Id_Jenis_Surat');
            
            $result = "<h2>Debug Surat - Fakultas ID: {$fakultasId}</h2>";
            $result .= "<p>Total Surat: " . $allSurat->count() . "</p>";
            $result .= "<h3>Group by Id_Jenis_Surat:</h3><ul>";
            
            foreach($grouped as $jenisId => $surats) {
                $namaJenis = $surats->first()->jenisSurat->Nama_Surat ?? 'Unknown';
                $result .= "<li>Id_Jenis_Surat {$jenisId} ({$namaJenis}): " . $surats->count() . " surat</li>";
            }
            $result .= "</ul>";
            
            return $result;
        })->name('debug.surat');

        Route::get('/manajemen-surat', [FakultasManajemenSuratController::class, 'index'])
            ->name('surat.manage');

        // Route untuk list per jenis surat
        Route::get('/surat-aktif', [FakultasManajemenSuratController::class, 'listAktif'])
            ->name('surat.aktif');
        Route::get('/surat-magang-list', [FakultasManajemenSuratController::class, 'listMagang'])
            ->name('surat.magang');

        // Surat Magang Routes
        Route::get('/surat-magang', [FakultasSuratMagangController::class, 'index'])
            ->name('surat_magang.index');
        Route::get('/surat-magang/{id}', [FakultasSuratMagangController::class, 'show'])
            ->name('surat_magang.show');
        Route::get('/surat-magang/{id}/download', [FakultasSuratMagangController::class, 'downloadProposal'])
            ->name('surat_magang.download');
        Route::post('/surat-magang/{id}/assign', [FakultasSuratMagangController::class, 'assignNomorSurat'])
            ->name('surat_magang.assign');

        // Detail surat (lihat detail berdasarkan Id_Tugas_Surat)
        Route::get('/surat/{id}/detail', [FakultasDetailSuratController::class, 'show'])->name('surat.detail');
        // Download dokumen pendukung (admin fakultas)
        Route::get('/surat/{id}/download', [FakultasDetailSuratController::class, 'downloadPendukung'])->name('surat.download');
        // Preview dokumen pendukung (admin fakultas)
        Route::get('/surat/{id}/preview', [FakultasDetailSuratController::class, 'previewPendukung'])->name('surat.preview');

        // Route: Tolak Surat
        Route::post('/surat/{id}/reject', [FakultasDetailSuratController::class, 'reject'])->name('surat.reject');
        // Route: Teruskan ke Dekan (setelah beri nomor)
        Route::post('/surat/{id}/forward', [FakultasDetailSuratController::class, 'forwardToDean'])->name('surat.forward');

        // Route: Legalisir - Tandai Sudah Bayar
        Route::get('/surat-legalisir', [FakultasSuratLegalisirController::class, 'index'])->name('surat_legalisir.index');
        Route::get('/input', [FakultasSuratLegalisirController::class, 'create'])->name('surat_legalisir.create');
        Route::post('/store', [FakultasSuratLegalisirController::class, 'store'])->name('surat_legalisir.store');
        Route::post('/surat-legalisir/{id}/verifikasi', [FakultasSuratLegalisirController::class, 'verifikasi'])->name('surat_legalisir.verifikasi');
        Route::post('/surat-legalisir/{id}/bayar', [FakultasSuratLegalisirController::class, 'konfirmasiPembayaran'])->name('surat_legalisir.bayar');
        Route::post('/surat-legalisir/{id}/progress', [FakultasSuratLegalisirController::class, 'updateProgress'])->name('surat_legalisir.progress');

        // Arsip surat
        Route::get('/arsip-surat', [FakultasManajemenSuratController::class, 'archive'])
            ->name('surat.archive');

        Route::get('/pengaturan', function () {
            return view('admin_fakultas.pengaturan');
        })->name('settings.index');
    });

    // FITUR DEKAN
    Route::prefix('dekan')->name('dekan.')->group(function () {
        Route::get('/persetujuan-surat', [App\Http\Controllers\Dekan\PersetujuanSuratController::class, 'index'])->name('persetujuan.index');
        Route::get('/surat/{id}/detail', [App\Http\Controllers\Dekan\DetailSuratController::class, 'show'])->name('surat.detail');
        Route::get('/surat/{id}/preview', [App\Http\Controllers\Dekan\DetailSuratController::class, 'previewDraft'])->name('surat.preview');
        Route::get('/surat/{id}/download', [App\Http\Controllers\Dekan\DetailSuratController::class, 'downloadPendukung'])->name('surat.download');
        Route::post('/surat/{id}/approve', [App\Http\Controllers\Dekan\DetailSuratController::class, 'approve'])->name('surat.approve');
        Route::post('/surat/{id}/reject', [App\Http\Controllers\Dekan\DetailSuratController::class, 'reject'])->name('surat.reject');

        // Routes untuk Surat Magang
        Route::get('/surat-magang', [App\Http\Controllers\Dekan\SuratMagangController::class, 'index'])->name('surat_magang.index');
        Route::get('/surat-magang/{id}', [App\Http\Controllers\Dekan\SuratMagangController::class, 'show'])->name('surat_magang.show');
        Route::post('/surat-magang/{id}/approve', [App\Http\Controllers\Dekan\SuratMagangController::class, 'approve'])->name('surat_magang.approve');
        Route::post('/surat-magang/{id}/reject', [App\Http\Controllers\Dekan\SuratMagangController::class, 'reject'])->name('surat_magang.reject');
        Route::get('/surat-magang/{id}/download', [App\Http\Controllers\Dekan\SuratMagangController::class, 'download'])->name('surat_magang.download');

        Route::get('/arsip-surat', function () {
            return view('dekan.arsip_surat');
        })->name('arsip.index');
    });

    // FITUR DOSEN
    Route::prefix('dosen')->name('dosen.')->group(function () {
        Route::get('/pengajuan', function () {
            return view('dosen.pengajuan');
        })->name('pengajuan.index');
        // PENANDA: Rute yang hilang ditambahkan di sini
        Route::get('/riwayat', function () {
            return view('dosen.riwayat');
        })->name('riwayat.index');
        Route::get('/input-nilai', function () {
            return view('dosen.input_nilai');
        })->name('nilai.index');
        Route::get('/bimbingan', function () {
            return view('dosen.bimbingan_akademik');
        })->name('bimbingan.index');
    });

    // FITUR KAJUR
    Route::prefix('kajur')->name('kajur.')->group(function () {
        Route::get('/verifikasi-rps', function () {
            return view('kajur.verifikasi_rps');
        })->name('rps.index');
        Route::get('/laporan', function () {
            return view('kajur.laporan_jurusan');
        })->name('laporan.index');
        Route::get('/persetujuan-surat', function () {
            return view('kajur.persetujuan-surat');
        })->name('persetujuan.index');
    });

    // FITUR KAPRODI
    Route::prefix('kaprodi')->name('kaprodi.')->group(function () {
        Route::get('/permintaan-kp', [\App\Http\Controllers\Kaprodi\PermintaanSuratController::class, 'index'])
            ->name('surat.index');
        Route::get('/permintaan-kp/{id}', [\App\Http\Controllers\Kaprodi\PermintaanSuratController::class, 'show'])
            ->name('surat.show');
        Route::post('/set-redirect', [\App\Http\Controllers\Kaprodi\PermintaanSuratController::class, 'setRedirect'])
            ->name('set-redirect');
        Route::get('/history-pengajuan', [\App\Http\Controllers\Kaprodi\PermintaanSuratController::class, 'history'])
            ->name('history-pengajuan');
        Route::post('/permintaan-kp/{id}/approve', [\App\Http\Controllers\Kaprodi\PermintaanSuratController::class, 'approve'])
            ->name('surat.approve');
        Route::post('/permintaan-kp/{id}/reject', [\App\Http\Controllers\Kaprodi\PermintaanSuratController::class, 'reject'])
            ->name('surat.reject');
        Route::get('/permintaan-kp/{id}/download-proposal', [\App\Http\Controllers\Kaprodi\PermintaanSuratController::class, 'downloadProposal'])
            ->name('surat.download');

        Route::get('/kurikulum', function () {
            return view('kaprodi.kurikulum');
        })->name('kurikulum.index');
        Route::get('/jadwal-kuliah', function () {
            return view('kaprodi.jadwal_kuliah');
        })->name('jadwal.index');
    });

    // FITUR MAHASISWA
    Route::prefix('mahasiswa')->name('mahasiswa.')->group(function () {

        // --- ROUTE GET: HALAMAN PILIHAN JENIS SURAT (CARD VIEW) ---
        Route::get('/pengajuan-surat', function () {
            // Definisikan surat apa saja yang boleh diajukan Mahasiswa
            $namaSuratMahasiswa = [
                'Surat Keterangan Aktif Kuliah',
                'Surat Rekomendasi',
                'Surat Pengantar KP/Magang'
            ];

            // Ambil dari DB HANYA surat-surat yang ada di daftar statis
            $jenis_surats = JenisSurat::whereIn('Nama_Surat', $namaSuratMahasiswa)
                ->orderBy('Nama_Surat', 'asc')
                ->get();

            return view('mahasiswa.pilih_jenis_surat', [
                'jenis_surats' => $jenis_surats
            ]);
        })->name('pengajuan.create');

        // --- ROUTE GET: FORM SURAT KETERANGAN AKTIF ---
        Route::get('/pengajuan-surat/aktif', function () {
            $user = Auth::user();
            $mahasiswa = Mahasiswa::where('Id_User', $user->Id_User)->first();

            $prodi = null;
            if ($mahasiswa && $mahasiswa->Id_Prodi) {
                $prodi = Prodi::find($mahasiswa->Id_Prodi);
            }

            // Ambil ID jenis surat
            $jenisSurat = JenisSurat::where('Nama_Surat', 'Surat Keterangan Aktif Kuliah')->first();

            return view('mahasiswa.form_surat_aktif', [
                'mahasiswa' => $mahasiswa,
                'prodi' => $prodi,
                'jenisSurat' => $jenisSurat
            ]);
        })->name('pengajuan.aktif.form');

        // --- ROUTE GET & POST: SURAT PENGANTAR MAGANG ---
        Route::get('/pengajuan-surat/magang', function () {
            $user = Auth::user();
            $mahasiswa = Mahasiswa::where('Id_User', $user->Id_User)->first();

            $prodi = null;
            $jurusan = null;
            if ($mahasiswa && $mahasiswa->Id_Prodi) {
                $prodi = Prodi::with('jurusan')->find($mahasiswa->Id_Prodi);
                if ($prodi && $prodi->jurusan) {
                    $jurusan = $prodi->jurusan;
                }
            }

            // Filter dosen berdasarkan prodi mahasiswa
            $dosens = Dosen::query()
                ->when($mahasiswa && $mahasiswa->Id_Prodi, function ($query) use ($mahasiswa) {
                    return $query->where('Id_Prodi', $mahasiswa->Id_Prodi);
                })
                ->orderBy('Nama_Dosen', 'asc')
                ->get();

            // Ambil Kaprodi
            $kaprodi = null;
            $kaprodiName = null;
            $kaprodiNIP = null;

            if ($mahasiswa && $mahasiswa->Id_Prodi) {
                $kaprodiUser = \App\Models\User::where('Id_Role', 4)
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

            $jenisSurat = JenisSurat::where('Nama_Surat', 'Surat Pengantar KP/Magang')->first();

            return view('mahasiswa.form_surat_magang', [
                'mahasiswa' => $mahasiswa,
                'prodi' => $prodi,
                'jurusan' => $jurusan,
                'dosens' => $dosens,
                'kaprodi' => $kaprodi,
                'kaprodiName' => $kaprodiName,
                'kaprodiNIP' => $kaprodiNIP,
                'jenisSurat' => $jenisSurat
            ]);
        })->name('pengajuan.magang.form');

        Route::post('/pengajuan-surat/magang', [SuratPengantarMagangController::class, 'store'])->name('pengajuan.magang.store');

        // --- ROUTE API: SEARCH MAHASISWA (AUTOCOMPLETE) ---
        Route::get('/api/mahasiswa/search', [SuratPengantarMagangController::class, 'searchMahasiswa'])->name('api.mahasiswa.search');

        // --- ROUTE: INVITATION ACTIONS ---
        Route::post('/invitation/{id}/accept', [SuratPengantarMagangController::class, 'acceptInvitation'])->name('invitation.accept');
        Route::post('/invitation/{id}/reject', [SuratPengantarMagangController::class, 'rejectInvitation'])->name('invitation.reject');

        // --- ROUTE GET: FORM SURAT REKOMENDASI ---
        Route::get('/pengajuan-surat/rekomendasi', function () {
            $user = Auth::user();
            $mahasiswa = Mahasiswa::where('Id_User', $user->Id_User)->first();

            $prodi = null;
            if ($mahasiswa && $mahasiswa->Id_Prodi) {
                $prodi = Prodi::find($mahasiswa->Id_Prodi);
            }

            return view('mahasiswa.form_surat_rekomendasi', [
                'mahasiswa' => $mahasiswa,
                'prodi' => $prodi
            ]);
        })->name('pengajuan.rekomendasi.form');

        // --- ROUTE POST: MENYIMPAN PENGAJUAN SURAT (MODULAR) ---
        // Route untuk Surat Keterangan Mahasiswa Aktif
        Route::post('/pengajuan-surat/aktif/store', [SuratKeteranganAktifController::class, 'store'])
            ->name('pengajuan.aktif.store');

        // Route untuk Surat Pengantar Magang/KP
        Route::post('/pengajuan-surat/magang/store', [SuratPengantarMagangController::class, 'store'])
            ->name('pengajuan.magang.store');

        // API untuk mendapatkan daftar mahasiswa satu prodi (untuk autocomplete)
        Route::get('/api/mahasiswa/search', [SuratPengantarMagangController::class, 'searchMahasiswa'])
            ->name('api.mahasiswa.search');

        // Riwayat Surat Mahasiswa
        Route::get('/riwayat', [\App\Http\Controllers\Mahasiswa\RiwayatSuratController::class, 'index'])
            ->name('riwayat');

        // Riwayat per Jenis Surat
        Route::get('/riwayat/aktif', [\App\Http\Controllers\Mahasiswa\RiwayatSuratController::class, 'riwayatAktif'])
            ->name('riwayat.aktif');
        Route::get('/riwayat/magang', [\App\Http\Controllers\Mahasiswa\RiwayatSuratController::class, 'riwayatMagang'])
            ->name('riwayat.magang');
        Route::get('/riwayat/legalisir', [\App\Http\Controllers\Mahasiswa\RiwayatSuratController::class, 'riwayatLegalisir'])
            ->name('riwayat.legalisir');

        // Download Surat dengan QR Code
        Route::get('/surat/download/{id}', [\App\Http\Controllers\Mahasiswa\RiwayatSuratController::class, 'downloadSurat'])
            ->name('surat.download');

        // Download Surat Pengantar (Signed by Kaprodi)
        Route::get('/surat/download-pengantar/{id}', [\App\Http\Controllers\Mahasiswa\RiwayatSuratController::class, 'downloadPengantar'])
            ->name('surat.download_pengantar');

        // --- AJAKAN MAGANG ROUTES ---
        Route::get('/ajakan-magang', [\App\Http\Controllers\AjakanMagangController::class, 'index'])
            ->name('ajakan-magang');
        Route::post('/ajakan-magang/{id}/accept', [\App\Http\Controllers\AjakanMagangController::class, 'accept'])
            ->name('ajakan-magang.accept');
        Route::post('/ajakan-magang/{id}/reject', [\App\Http\Controllers\AjakanMagangController::class, 'reject'])
            ->name('ajakan-magang.reject');

        // --- ROUTE SURAT LEGALISIR ---
        Route::get('/pengajuan-surat/legalisir', [SuratLegalisirController::class, 'create'])->name('pengajuan.legalisir.create');
        Route::post('/pengajuan-surat/legalisir', [SuratLegalisirController::class, 'store'])->name('pengajuan.legalisir.store');

        // Rute lainnya
        // Route::get('/legalisir', function () {
        //     return view('mahasiswa.legalisir');
        // })->name('legalisir.create');
    });

});
