<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth; 
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PengajuanSuratController;

// Impor Model untuk route pengajuan
use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\Prodi;
use App\Models\JenisSurat; // Pastikan ini ada

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


// =========================================================================
// AREA PENGGUNA YANG SUDAH LOGIN
// =========================================================================
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
        Route::get('/kelola-pengguna', function () {
            return view('admin.kelola_pengguna'); })->name('users.index');
        Route::get('/manajemen-surat', function () {
            return view('admin.manajemen_surat'); })->name('surat.manage');
        Route::get('/arsip-surat', function () {
            return view('admin.arsip_surat'); })->name('surat.archive');
        Route::get('/pengaturan', function () {
            return view('admin.pengaturan'); })->name('settings.index');
    });

    // FITUR DEKAN
    Route::prefix('dekan')->name('dekan.')->group(function () {
        Route::get('/persetujuan-surat', function () {
            return view('dekan.persetujuan_surat'); })->name('persetujuan.index');
        Route::get('/arsip-surat', function () {
            return view('dekan.arsip_surat'); })->name('arsip.index');
    });

    // FITUR DOSEN
    Route::prefix('dosen')->name('dosen.')->group(function () {
        Route::get('/pengajuan', function () { return view('dosen.pengajuan'); })->name('pengajuan.index');
        // PENANDA: Rute yang hilang ditambahkan di sini
        Route::get('/riwayat', function () { return view('dosen.riwayat'); })->name('riwayat.index');
        Route::get('/input-nilai', function () { return view('dosen.input_nilai'); })->name('nilai.index');
        Route::get('/bimbingan', function () { return view('dosen.bimbingan_akademik'); })->name('bimbingan.index');
    });

    // FITUR KAJUR
    Route::prefix('kajur')->name('kajur.')->group(function () {
        Route::get('/verifikasi-rps', function () {
            return view('kajur.verifikasi_rps'); })->name('rps.index');
        Route::get('/laporan', function () {
            return view('kajur.laporan_jurusan'); })->name('laporan.index');
        Route::get('/persetujuan-surat', function () {
            return view('kajur.persetujuan-surat'); })->name('persetujuan.index');
    });

    // FITUR KAPRODI
    Route::prefix('kaprodi')->name('kaprodi.')->group(function () {
        Route::get('/kurikulum', function () {
            return view('kaprodi.kurikulum'); })->name('kurikulum.index');
        Route::get('/jadwal-kuliah', function () {
            return view('kaprodi.jadwal_kuliah'); })->name('jadwal.index');
    });

    // FITUR MAHASISWA
    Route::prefix('mahasiswa')->name('mahasiswa.')->group(function () {
        Route::get('/pengajuan-surat', function () { return view('mahasiswa.pengajuan_surat'); })->name('pengajuan.create');
        Route::get('/riwayat', function () { return view('mahasiswa.riwayat'); })->name('riwayat.index');
        Route::get('/legalisir', function () { return view('mahasiswa.legalisir'); })->name('legalisir.create');
    });

});