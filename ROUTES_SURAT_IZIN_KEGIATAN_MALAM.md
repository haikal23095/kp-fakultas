# ROUTES UNTUK SURAT IZIN KEGIATAN MALAM

# Tambahkan baris berikut ke file routes/web.php

## 1. Routes untuk MAHASISWA (di dalam prefix 'mahasiswa')

```php
// Form Pengajuan Izin Kegiatan Malam
Route::get('/pengajuan/izin-kegiatan-malam', [\App\Http\Controllers\PengajuanSurat\SuratIzinKegiatanMalamController::class, 'create'])
    ->name('pengajuan.izin_malam.create');

Route::post('/pengajuan/izin-kegiatan-malam', [\App\Http\Controllers\PengajuanSurat\SuratIzinKegiatanMalamController::class, 'store'])
    ->name('pengajuan.izin_malam.store');

// Riwayat Izin Kegiatan Malam
Route::get('/riwayat/izin-kegiatan-malam', [\App\Http\Controllers\Mahasiswa\RiwayatSuratController::class, 'riwayatIzinKegiatanMalam'])
    ->name('riwayat.izin_kegiatan_malam');

// Download Surat Izin Kegiatan Malam
Route::get('/surat/izin-kegiatan-malam/{id}/download', [\App\Http\Controllers\PengajuanSurat\SuratIzinKegiatanMalamController::class, 'downloadSurat'])
    ->name('surat.izin_malam.download');
```

## 2. Routes untuk ADMIN FAKULTAS (di dalam prefix 'admin-fakultas')

```php
// Manajemen Surat Izin Kegiatan Malam
Route::get('/surat/izin-kegiatan-malam', [\App\Http\Controllers\PengajuanSurat\SuratIzinKegiatanMalamController::class, 'indexAdmin'])
    ->name('surat.izin_malam.index');
```

## CONTOH PENEMPATAN DALAM WEB.PHP:

```php
// ============================================================================
// MAHASISWA ROUTES
// ============================================================================
Route::middleware(['auth', 'checkRole:Mahasiswa'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {

    // ... existing routes ...

    // Surat Izin Kegiatan Malam
    Route::get('/pengajuan/izin-kegiatan-malam', [\App\Http\Controllers\PengajuanSurat\SuratIzinKegiatanMalamController::class, 'create'])
        ->name('pengajuan.izin_malam.create');
    Route::post('/pengajuan/izin-kegiatan-malam', [\App\Http\Controllers\PengajuanSurat\SuratIzinKegiatanMalamController::class, 'store'])
        ->name('pengajuan.izin_malam.store');

    // Riwayat
    Route::get('/riwayat/izin-kegiatan-malam', [\App\Http\Controllers\Mahasiswa\RiwayatSuratController::class, 'riwayatIzinKegiatanMalam'])
        ->name('riwayat.izin_kegiatan_malam');

    // Download
    Route::get('/surat/izin-kegiatan-malam/{id}/download', [\App\Http\Controllers\PengajuanSurat\SuratIzinKegiatanMalamController::class, 'downloadSurat'])
        ->name('surat.izin_malam.download');
});

// ============================================================================
// ADMIN FAKULTAS ROUTES
// ============================================================================
Route::middleware(['auth', 'checkRole:Admin Fakultas'])->prefix('admin-fakultas')->name('admin_fakultas.')->group(function () {

    // ... existing routes ...

    // Surat Izin Kegiatan Malam
    Route::get('/surat/izin-kegiatan-malam', [\App\Http\Controllers\PengajuanSurat\SuratIzinKegiatanMalamController::class, 'indexAdmin'])
        ->name('surat.izin_malam.index');
});
```

## CATATAN PENTING:

1. Pastikan routes ditempatkan di dalam group middleware yang sesuai dengan role user
2. Untuk route download, parameter {id} adalah Id_Tugas_Surat
3. Sesuaikan prefix dan name sesuai dengan struktur routes yang sudah ada di web.php
