# DOKUMENTASI IMPLEMENTASI SURAT IZIN KEGIATAN MALAM

## 📋 OVERVIEW

Fitur baru "Surat Izin Kegiatan Malam" telah berhasil diimplementasikan sesuai dengan standar coding dan pattern yang digunakan di project ini.

## ✅ FILES YANG SUDAH DIBUAT

### 1. Database Migration

**File:** `database/migrations/2025_12_29_100000_create_surat_izin_kegiatan_malams_table.php`

-   ✅ Tabel: `surat_izin_kegiatan_malams`
-   ✅ Foreign Keys: `id_tugas_surat`, `id_user`, `id_pejabat`
-   ✅ Columns: nama_kegiatan, waktu_mulai, waktu_selesai, lokasi_kegiatan, jumlah_peserta, alasan, nomor_surat

### 2. Model

**File:** `app/Models/SuratIzinKegiatanMalam.php`

-   ✅ Menggunakan snake_case untuk nama tabel sesuai Laravel convention
-   ✅ Relasi ke TugasSurat, User, dan Pejabat sudah dibuat
-   ✅ Cast datetime untuk kolom waktu_mulai dan waktu_selesai

### 3. Controller

**File:** `app/Http/Controllers/PengajuanSurat/SuratIzinKegiatanMalamController.php`

-   ✅ Method `create()` - Form pengajuan mahasiswa
-   ✅ Method `store()` - Simpan pengajuan dengan DB transaction
-   ✅ Method `indexAdmin()` - Daftar pengajuan untuk admin
-   ✅ Method `downloadSurat()` - Download surat yang sudah selesai
-   ✅ Validasi lengkap untuk semua field
-   ✅ Otomatis mencari Pejabat (Wakil Dekan 3) menggunakan LIKE query

### 4. Views - Mahasiswa

**File:** `resources/views/mahasiswa/pengajuan-surat/form_izin_malam.blade.php`

-   ✅ Form lengkap dengan datetime-local input
-   ✅ Validasi client-side untuk waktu mulai/selesai
-   ✅ Alert informasi yang jelas
-   ✅ Responsive design mengikuti pattern yang ada

### 5. Views - Admin Fakultas

**File:** `resources/views/admin_fakultas/surat_izin_kegiatan_malam/index.blade.php`

-   ✅ Table listing dengan DataTables
-   ✅ Tampilan waktu kegiatan yang rapi
-   ✅ Badge status dengan warna sesuai status
-   ✅ Button aksi untuk detail

### 6. Update Model TugasSurat

**File:** `app/Models/TugasSurat.php`

-   ✅ Ditambahkan relasi `suratIzinKegiatanMalam()`

### 7. Update RiwayatSuratController

**File:** `app/Http/Controllers/Mahasiswa/RiwayatSuratController.php`

-   ✅ Ditambahkan `$countIzinKegiatanMalam` di method `index()`
-   ✅ Ditambahkan method `riwayatIzinKegiatanMalam()`

### 8. Update JenisSuratSeeder

**File:** `database/seeders/JenisSuratSeeder.php`

-   ✅ Ditambahkan ID 12: 'Surat Izin Kegiatan Malam'

### 9. Routes Documentation

**File:** `ROUTES_SURAT_IZIN_KEGIATAN_MALAM.md`

-   ✅ Dokumentasi lengkap routes yang perlu ditambahkan
-   ✅ Contoh penempatan dalam web.php

## 🎯 BUSINESS RULES YANG DIIMPLEMENTASIKAN

1. **Fungsi:** Izin bagi mahasiswa/ormawa untuk berkegiatan di kampus di luar jam operasional
2. **Aktor:** Mahasiswa (Input) → Admin (Verifikasi) → Wakil Dekan 3 (Tanda Tangan)
3. **Logic Pejabat:** Sistem otomatis mencari `id_pejabat` untuk Wakil Dekan 3 menggunakan WHERE LIKE
4. **Validasi:**
    - Nama kegiatan wajib diisi (max 255 karakter)
    - Waktu mulai dan selesai wajib diisi (datetime)
    - Waktu selesai harus setelah waktu mulai
    - Lokasi kegiatan wajib diisi (max 255 karakter)
    - Jumlah peserta minimal 1 orang
    - Alasan kegiatan wajib diisi (text)

## 📝 LANGKAH SELANJUTNYA

### A. Jalankan Migration

```bash
php artisan migrate
```

### B. Jalankan Seeder (Update Jenis Surat)

```bash
php artisan db:seed --class=JenisSuratSeeder
```

### C. Tambahkan Routes ke web.php

Buka file `ROUTES_SURAT_IZIN_KEGIATAN_MALAM.md` dan copy-paste routes yang diperlukan ke `routes/web.php` sesuai dengan group middleware yang sesuai.

**Routes yang perlu ditambahkan:**

#### Untuk MAHASISWA (di dalam middleware mahasiswa):

```php
// Form Pengajuan
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
```

#### Untuk ADMIN FAKULTAS (di dalam middleware admin fakultas):

```php
Route::get('/surat/izin-kegiatan-malam', [\App\Http\Controllers\PengajuanSurat\SuratIzinKegiatanMalamController::class, 'indexAdmin'])
    ->name('surat.izin_malam.index');
```

### D. (OPTIONAL) Tambahkan Card di Halaman Riwayat Mahasiswa

Edit file `resources/views/mahasiswa/riwayat.blade.php` untuk menambahkan card "Izin Kegiatan Malam":

```blade
{{-- Card Izin Kegiatan Malam --}}
<div class="col-md-4 mb-4">
    <a href="{{ route('mahasiswa.riwayat.izin_kegiatan_malam') }}" class="text-decoration-none">
        <div class="card card-jenis-surat position-relative">
            <span class="badge bg-info badge-count">{{ $countIzinKegiatanMalam ?? 0 }} Surat</span>
            <div class="card-body">
                <div class="card-icon teal"><i class="fas fa-moon"></i></div>
                <h5>Izin Kegiatan Malam</h5>
                <p>Riwayat izin kegiatan di luar jam operasional</p>
            </div>
        </div>
    </a>
</div>
```

### E. (OPTIONAL) Tambahkan Menu di Sidebar/Navigation

Edit sidebar mahasiswa untuk menambahkan link ke form pengajuan izin kegiatan malam.

### F. (OPTIONAL) Buat View PDF untuk Download Surat

Buat file `resources/views/mahasiswa/pdf/surat_izin_kegiatan_malam.blade.php` untuk template PDF surat yang sudah ditandatangani.

## ✨ KEUNGGULAN IMPLEMENTASI

1. ✅ **Mengikuti Pattern Existing:** Semua file dibuat mengikuti struktur dan pattern yang sudah ada
2. ✅ **No Hardcoded IDs:** Pejabat dicari secara dinamis menggunakan LIKE query
3. ✅ **Database Transaction:** Menggunakan DB::transaction untuk data integrity
4. ✅ **Comprehensive Validation:** Validasi server-side dan client-side
5. ✅ **Responsive Design:** Mengikuti Bootstrap dan style yang sudah ada
6. ✅ **Clean Code:** Komentar yang jelas, naming convention yang konsisten
7. ✅ **Safety First:** TIDAK ada file referensi yang diubah/dihapus, hanya menambahkan relasi baru

## 🔍 TESTING CHECKLIST

Setelah setup selesai, lakukan testing berikut:

-   [ ] Akses form pengajuan sebagai mahasiswa
-   [ ] Submit form dengan data valid
-   [ ] Cek apakah data tersimpan di database (Tugas_Surat dan surat_izin_kegiatan_malams)
-   [ ] Cek apakah counter di halaman riwayat update
-   [ ] Akses halaman admin untuk melihat daftar pengajuan
-   [ ] Validasi error handling (waktu selesai sebelum waktu mulai, field kosong, dll)
-   [ ] Test download surat setelah status = selesai

## 📞 TROUBLESHOOTING

**Error: Class not found**

-   Pastikan sudah run `composer dump-autoload`

**Error: Table doesn't exist**

-   Run migration: `php artisan migrate`

**Error: Foreign key constraint fails**

-   Pastikan tabel Tugas_Surat, Users, dan Pejabat sudah ada
-   Run seeder untuk Jenis_Surat: `php artisan db:seed --class=JenisSuratSeeder`

**ID Pejabat null**

-   Pastikan ada data pejabat dengan jabatan yang mengandung kata "Wakil Dekan 3" atau "WD3" atau "Kemahasiswaan"

## 🎉 SELESAI!

Fitur "Surat Izin Kegiatan Malam" sudah siap digunakan setelah menyelesaikan langkah-langkah di atas.
