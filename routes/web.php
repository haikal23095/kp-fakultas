<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Redirect root ke login
Route::get('/', function () {
    return redirect('/login');
});

// Routes untuk autentikasi (hanya untuk guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Logout (bisa diakses siapa saja yang login)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Routes yang memerlukan autentikasi
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

    // Dashboard berdasarkan role
    Route::get('/dashboard/admin', [AuthController::class, 'dashboardAdmin'])->name('dashboard.admin');
    Route::get('/dashboard/dekan', [AuthController::class, 'dashboardDekan'])->name('dashboard.dekan');
    Route::get('/dashboard/kajur', [AuthController::class, 'dashboardKajur'])->name('dashboard.kajur');
    Route::get('/dashboard/kaprodi', [AuthController::class, 'dashboardKaprodi'])->name('dashboard.kaprodi');
    Route::get('/dashboard/dosen', [AuthController::class, 'dashboardDosen'])->name('dashboard.dosen');
    Route::get('/dashboard/mahasiswa', [AuthController::class, 'dashboardMahasiswa'])->name('dashboard.mahasiswa');
    Route::get('/dashboard/default', [AuthController::class, 'dashboardDefault'])->name('dashboard.default');
});
