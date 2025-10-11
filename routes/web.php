<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
// Controller Dekan
use App\Http\Controllers\Dekan\PersetujuanController;
use App\Http\Controllers\Dekan\ArsipController;
// Controller Mahasiswa
use App\Http\Controllers\Mahasiswa\PengajuanController;
use App\Http\Controllers\Mahasiswa\RiwayatController;
use App\Http\Controllers\Mahasiswa\LegalisirController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect root ke halaman login
Route::get('/', function () {
    return redirect('/login');
});

// Routes untuk autentikasi (hanya untuk guest/pengguna belum login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Route untuk logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// =========================================================================
// AREA KHUSUS PENGGUNA YANG SUDAH LOGIN
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

    // --------------------------------------------------------------------
    // FITUR DEKANAT
    // --------------------------------------------------------------------
    Route::prefix('dekan')->name('dekan.')->group(function () {
        Route::get('/persetujuan-surat', [PersetujuanController::class, 'index'])->name('persetujuan.index');
        Route::get('/arsip-surat', [ArsipController::class, 'index'])->name('arsip.index');
    });

    // --------------------------------------------------------------------
    // FITUR MAHASISWA
    // --------------------------------------------------------------------
    Route::prefix('mahasiswa')->name('mahasiswa.')->group(function () {
        Route::get('/pengajuan/baru', [PengajuanController::class, 'create'])->name('pengajuan.create');
        Route::post('/pengajuan', [PengajuanController::class, 'store'])->name('pengajuan.store');
        Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat.index');
        Route::get('/legalisir', [LegalisirController::class, 'create'])->name('legalisir.create');
    });

});