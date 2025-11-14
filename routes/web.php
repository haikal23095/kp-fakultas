<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DetailSuratController;
use App\Http\Controllers\Admin\ManajemenSuratController;

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
    Route::get('/dashboard/admin', [AuthController::class, 'dashboardAdmin'])->name('dashboard.admin');
    Route::get('/dashboard/dekan', [AuthController::class, 'dashboardDekan'])->name('dashboard.dekan');
    Route::get('/dashboard/kajur', [AuthController::class, 'dashboardKajur'])->name('dashboard.kajur');
    Route::get('/dashboard/kaprodi', [AuthController::class, 'dashboardKaprodi'])->name('dashboard.kaprodi');
    Route::get('/dashboard/dosen', [AuthController::class, 'dashboardDosen'])->name('dashboard.dosen');
    Route::get('/dashboard/mahasiswa', [AuthController::class, 'dashboardMahasiswa'])->name('dashboard.mahasiswa');
    Route::get('/dashboard/default', [AuthController::class, 'dashboardDefault'])->name('dashboard.default');

    // FITUR ADMIN
    Route::prefix('admin')->name('admin.')->group(function () {

        Route::get('/manajemen-surat', [ManajemenSuratController::class, 'index'])
            ->name('surat.manage');

        // Detail surat (lihat detail berdasarkan Id_Tugas_Surat)
        Route::get('/surat/{id}/detail', [DetailSuratController::class, 'show'])->name('surat.detail');
        // Download dokumen pendukung (admin)
        Route::get('/surat/{id}/download', [DetailSuratController::class, 'downloadPendukung'])->name('surat.download');
        // Proses upload draft final / ajukan ke Dekan
        Route::post('/surat/{id}/process-draft', [DetailSuratController::class, 'processDraft'])->name('surat.process_draft');

        // Route: update status tugas (hanya admin)
        Route::post('/manajemen-surat/{id}/update-status', [ManajemenSuratController::class, 'updateStatus'])
            ->name('surat.updateStatus');

        // ... (route /arsip-surat) ...
        Route::get('/arsip-surat', [ManajemenSuratController::class, 'archive'])
            ->name('surat.archive');

        Route::get('/pengaturan', function () {
            return view('admin.pengaturan');
        })->name('settings.index');
    });

    // FITUR DEKAN
    Route::prefix('dekan')->name('dekan.')->group(function () {
        Route::get('/persetujuan-surat', function () {
            return view('dekan.persetujuan_surat');
        })->name('persetujuan.index');
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
        Route::get('/kurikulum', function () {
            return view('kaprodi.kurikulum');
        })->name('kurikulum.index');
        Route::get('/jadwal-kuliah', function () {
            return view('kaprodi.jadwal_kuliah');
        })->name('jadwal.index');
    });

    // FITUR MAHASISWA
    Route::prefix('mahasiswa')->name('mahasiswa.')->group(function () {

        // --- ROUTE GET: FORM PENGAJUAN SURAT ---
        Route::get('/pengajuan-surat', function () {

            $user = Auth::user();
            $mahasiswa = Mahasiswa::where('Id_User', $user->Id_User)->first();

            $prodi = null;
            if ($mahasiswa && $mahasiswa->Id_Prodi) {
                $prodi = Prodi::find($mahasiswa->Id_Prodi);
            }

            // Filter dosen berdasarkan prodi mahasiswa
            // Jika mahasiswa punya prodi, tampilkan dosen dari prodi tersebut
            // Jika tidak, tampilkan semua dosen (fallback)
            $dosens = Dosen::query()
                ->when($mahasiswa && $mahasiswa->Id_Prodi, function ($query) use ($mahasiswa) {
                    return $query->where('Id_Prodi', $mahasiswa->Id_Prodi);
                })
                ->orderBy('Nama_Dosen', 'asc')
                ->get();

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

            return view('mahasiswa.pengajuan_surat', [
                'mahasiswa' => $mahasiswa,
                'prodi' => $prodi,
                'dosens' => $dosens,
                'jenis_surats' => $jenis_surats
            ]);

        })->name('pengajuan.create');

        // --- ROUTE POST: MENYIMPAN PENGAJUAN SURAT (MODULAR) ---
        // Route untuk Surat Keterangan Mahasiswa Aktif
        Route::post('/pengajuan-surat/aktif', [SuratKeteranganAktifController::class, 'store'])
            ->name('pengajuan.aktif.store');

        // Route untuk Surat Pengantar Magang/KP
        Route::post('/pengajuan-surat/magang', [SuratPengantarMagangController::class, 'store'])
            ->name('pengajuan.magang.store');

        // API untuk mendapatkan daftar mahasiswa satu prodi (untuk autocomplete)
        Route::get('/api/mahasiswa/search', [SuratPengantarMagangController::class, 'searchMahasiswa'])
            ->name('api.mahasiswa.search');

        // Rute lainnya
        Route::get('/riwayat', function () {
            return view('mahasiswa.riwayat');
        })->name('riwayat.index');
        Route::get('/legalisir', function () {
            return view('mahasiswa.legalisir');
        })->name('legalisir.create');
    });

});
