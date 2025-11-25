<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin_Prodi\DetailSuratController;
use App\Http\Controllers\Admin_Prodi\ManajemenSuratController;
use App\Http\Controllers\Admin_Fakultas\DetailSuratController as FakultasDetailSuratController;
use App\Http\Controllers\Admin_Fakultas\ManajemenSuratController as FakultasManajemenSuratController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotifikasiController;

// Import Controller untuk Pengajuan Surat (Modular)
use App\Http\Controllers\PengajuanSurat\SuratKeteranganAktifController;
use App\Http\Controllers\PengajuanSurat\SuratPengantarMagangController;

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
// TODO: Implement SuratVerificationController
// Route::get('/verify-surat/{token}', [SuratVerificationController::class, 'verify'])->name('surat.verify');
// Route::get('/api/verify-surat/{token}', [SuratVerificationController::class, 'verifyApi'])->name('surat.verify.api');

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

    // FITUR ADMIN
    Route::prefix('admin')->name('admin_prodi.')->group(function () {

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

        // ... (route /arsip-surat) ...
        Route::get('/arsip-surat', [ManajemenSuratController::class, 'archive'])
            ->name('surat.archive');

        Route::get('/pengaturan', function () {
            return view('admin_prodi.pengaturan');
        })->name('settings.index');
    });

    // FITUR ADMIN FAKULTAS
    Route::prefix('admin-fakultas')->name('admin_fakultas.')->group(function () {

        Route::get('/manajemen-surat', [FakultasManajemenSuratController::class, 'index'])
            ->name('surat.manage');

        // Detail surat (lihat detail berdasarkan Id_Tugas_Surat)
        Route::get('/surat/{id}/detail', [FakultasDetailSuratController::class, 'show'])->name('surat.detail');
        // Download dokumen pendukung (admin fakultas)
        Route::get('/surat/{id}/download', [FakultasDetailSuratController::class, 'downloadPendukung'])->name('surat.download');

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
        Route::get('/permintaan-surat', [\App\Http\Controllers\Kaprodi\PermintaanSuratController::class, 'index'])
            ->name('surat.index');
        Route::post('/permintaan-surat/{id}/approve', [\App\Http\Controllers\Kaprodi\PermintaanSuratController::class, 'approve'])
            ->name('surat.approve');
        Route::post('/permintaan-surat/{id}/reject', [\App\Http\Controllers\Kaprodi\PermintaanSuratController::class, 'reject'])
            ->name('surat.reject');
        Route::get('/permintaan-surat/{id}/download-proposal', [\App\Http\Controllers\Kaprodi\PermintaanSuratController::class, 'downloadProposal'])
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

        // --- ROUTE GET: FORM SURAT PENGANTAR MAGANG ---
        Route::get('/pengajuan-surat/magang', [SuratPengantarMagangController::class, 'create'])->name('pengajuan.magang.form');

        // --- ROUTE POST: SUBMIT FORM SURAT MAGANG/KP ---
        Route::post('/pengajuan-surat/magang', [SuratPengantarMagangController::class, 'store'])->name('pengajuan.magang.store');

        // --- ROUTE API: SEARCH MAHASISWA (AUTOCOMPLETE) ---
        Route::get('/api/mahasiswa/search', [SuratPengantarMagangController::class, 'searchMahasiswa'])->name('api.mahasiswa.search');

        // --- ROUTE API: DRAFT MANAGEMENT ---
        Route::post('/api/draft/save', [SuratPengantarMagangController::class, 'saveDraft'])->name('api.draft.save');
        Route::get('/api/draft/load', [SuratPengantarMagangController::class, 'loadDraft'])->name('api.draft.load');
        Route::post('/api/draft/delete', [SuratPengantarMagangController::class, 'deleteDraft'])->name('api.draft.delete');

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

        // Download Surat dengan QR Code
        Route::get('/surat/download/{id}', [\App\Http\Controllers\Mahasiswa\RiwayatSuratController::class, 'downloadSurat'])
            ->name('surat.download');

        // Rute lainnya
        Route::get('/legalisir', function () {
            return view('mahasiswa.legalisir');
        })->name('legalisir.create');
    });

});
