# DOKUMENTASI PERBAIKAN ALUR SURAT KETERANGAN MAHASISWA AKTIF

## Masalah yang Diperbaiki

**Symptom:** Data surat yang sudah diproses Admin tidak muncul di dashboard Dekan

**Root Cause:** Status mismatch antara Admin Controller dan Dekan Controller

-   Admin mengubah status ke `'Diajukan ke Dekan'`
-   Dekan mencari status `'menunggu-ttd'`
-   Akibatnya: Query Dekan tidak menemukan data

## Solusi yang Diimplementasikan

### 1. Perbaikan Status Workflow (CRITICAL FIX)

**File:** `app/Http/Controllers/Admin/DetailSuratController.php`

**Perubahan:**

-   Line 113: Ubah status dari `'Diajukan ke Dekan'` → `'menunggu-ttd'`
-   Line 173: Ubah status dari `'Diajukan ke Dekan'` → `'menunggu-ttd'`

**Reason:** Status `'menunggu-ttd'` lebih sesuai dengan workflow TTE dan sesuai dengan query Dekan.

### 2. Penambahan Logging untuk Debug

**File:** `app/Http/Controllers/Admin/DetailSuratController.php`

**Lokasi:**

-   Line 107-116: Log saat Admin ajukan tanpa upload
-   Line 155-166: Log saat Admin upload draft final

**Benefit:** Memudahkan tracking alur data di `storage/logs/laravel.log`

### 3. Perbaikan Query Dekan (ENHANCEMENT)

**File:** `app/Http/Controllers/Dekan/PersetujuanSuratController.php`

**Perubahan:**

-   Line 61-66: Tambah kondisi `->orWhere('Id_Penerima_Tugas_Surat', $user->Id_User)`
-   Line 69-75: Tambah logging untuk debug query

**Reason:** Memastikan surat yang langsung ditujukan ke Dekan (tanpa filter fakultas) tetap muncul.

### 4. Peningkatan UI/UX

**File:** `resources/views/dekan/persetujuan_surat.blade.php`

**Perubahan:**

-   Line 7-20: Tambah flash messages (success/error)
-   Line 40-44: Tambah pesan informatif saat data kosong

**Benefit:** User mendapat feedback yang lebih jelas.

## Alur Data yang Benar

```
┌─────────────┐
│  MAHASISWA  │ Submit form + upload KRS
└──────┬──────┘
       │ Status: 'Diterima Admin'
       │ Id_Penerima: Admin Fakultas
       v
┌─────────────┐
│    ADMIN    │ Validasi & buat draft
└──────┬──────┘
       │ Klik "Proses & Ajukan" ATAU
       │ Upload draft final + submit
       │
       │ [DIPERBAIKI]
       │ Status: 'menunggu-ttd'
       │ Id_Penerima: User Dekan
       v
┌─────────────┐
│    DEKAN    │ Query: WHERE Status = 'menunggu-ttd'
└──────┬──────┘     AND Id_Penerima = Dekan.Id_User
       │
       │ Approve: Status → 'Telah Ditandatangani Dekan'
       │ Reject:  Status → 'Ditolak'
       v
    [SELESAI]
```

## Status ENUM Values

Setelah migration yang sudah dijalankan, kolom `Status` di tabel `Tugas_Surat` memiliki nilai:

```sql
enum(
  'baru',
  'Diterima Admin',      -- Mahasiswa → Admin
  'Proses',
  'Diajukan ke Dekan',   -- [DEPRECATED, tidak dipakai lagi]
  'menunggu-ttd',        -- [ACTIVE] Admin → Dekan
  'Telah Ditandatangani Dekan',
  'Ditolak',
  'Selesai',
  'Terlambat'
)
```

## Testing Checklist

### Prerequisite

```bash
# Pastikan migration sudah dijalankan
php artisan migrate

# Jalankan debug script
php debug_surat_flow.php
```

### Test Case 1: Admin Proses Tanpa Upload

1. ✅ Login sebagai Admin Fakultas
2. ✅ Buka halaman Manajemen Surat
3. ✅ Klik detail pada surat dengan status "Diterima Admin"
4. ✅ Klik tombol "Proses & Ajukan" (tanpa upload)
5. ✅ Verifikasi:
    - Status berubah ke `'menunggu-ttd'`
    - `Id_Penerima_Tugas_Surat` berubah ke ID Dekan
    - Log muncul di `storage/logs/laravel.log`

### Test Case 2: Admin Proses Dengan Upload Draft

1. ✅ Login sebagai Admin Fakultas
2. ✅ Buka detail surat status "Diterima Admin"
3. ✅ Upload file PDF di form "Upload Draft Final"
4. ✅ Klik "Submit & Ajukan ke Dekan"
5. ✅ Verifikasi:
    - Status berubah ke `'menunggu-ttd'`
    - File tersimpan di `storage/app/public/surat_drafts/`
    - Record di tabel `File_Arsip` bertambah
    - Log muncul di `storage/logs/laravel.log`

### Test Case 3: Dekan Melihat Surat

1. ✅ Login sebagai Dekan
2. ✅ Buka halaman "Persetujuan TTE Surat"
3. ✅ Verifikasi:
    - Surat yang sudah diproses Admin MUNCUL di tabel
    - Kolom menampilkan: Jenis Surat, Pemohon, Tanggal, Aksi
    - Tombol "Lihat Detail" dan "Setujui (TTE)" tersedia

### Test Case 4: Dekan Approve/Reject

1. ✅ Klik "Lihat Detail" pada salah satu surat
2. ✅ Klik "Setujui" atau "Tolak dengan Komentar"
3. ✅ Verifikasi:
    - Status berubah ke `'Telah Ditandatangani Dekan'` atau `'Ditolak'`
    - Surat hilang dari daftar antrian
    - Flash message sukses muncul

## Troubleshooting

### Problem: Data masih tidak muncul di Dekan

**Solusi 1: Cek User Dekan**

```bash
php artisan tinker
```

```php
// Cari user Dekan
$dekan = \App\Models\User::whereHas('role', function($q) {
    $q->whereRaw("LOWER(TRIM(Name_Role)) = 'dekan'");
})->first();

echo "Dekan ID: " . ($dekan ? $dekan->Id_User : 'TIDAK ADA');
```

**Jika NULL:** Tambahkan user dengan role Dekan di database.

**Solusi 2: Cek Data Dosen**

```php
// Cek apakah Dekan punya record di tabel Dosen
$dosen = \App\Models\Dosen::where('Id_User', $dekan->Id_User)->first();
echo "Dosen: " . ($dosen ? "Ada (Id_Prodi: {$dosen->Id_Prodi})" : 'TIDAK ADA');
```

**Jika NULL:** Tambahkan record Dekan ke tabel Dosen dengan `Id_Prodi` yang valid.

**Solusi 3: Cek Status Surat**

```php
// Cek surat dengan status menunggu-ttd
$surat = \App\Models\TugasSurat::where('Status', 'menunggu-ttd')->get();
echo "Jumlah surat menunggu-ttd: " . $surat->count();
```

**Jika 0:** Admin belum memproses surat. Kembali ke halaman Admin.

**Solusi 4: Cek Id_Penerima**

```php
// Cek apakah surat ditujukan ke Dekan
$surat = \App\Models\TugasSurat::where('Status', 'menunggu-ttd')
    ->where('Id_Penerima_Tugas_Surat', $dekan->Id_User)
    ->get();
echo "Surat ke Dekan: " . $surat->count();
```

**Jika 0:** Ada masalah di Admin Controller saat set penerima.

### Problem: Error saat Admin submit

**Error:** "Call to a member function ... on null"

**Solusi:**

-   Cek apakah ada user dengan role 'Dekan'
-   Pastikan query `whereRaw("LOWER(TRIM(Name_Role)) = 'dekan'")` menemukan data
-   Cek tabel `Role`: pastikan ada record dengan `Name_Role = 'Dekan'`

## Database Requirements

### Tabel `User`

-   Harus ada user dengan `Id_Role` yang mengarah ke role 'Dekan'

### Tabel `Role`

-   Harus ada record: `Name_Role = 'Dekan'` (exact match, case-sensitive)

### Tabel `Dosen`

-   User Dekan harus punya record di tabel ini
-   Kolom `Id_Prodi` harus diisi (untuk filter fakultas)

### Tabel `Prodi`

-   Record prodi Dekan harus ada
-   Kolom `Id_Fakultas` harus diisi

### Tabel `Tugas_Surat`

-   Kolom `Status` harus ENUM dengan value `'menunggu-ttd'`
-   Kolom `data_spesifik` (JSON) harus ada (sudah di-migrate)

## Log Files

Cek log untuk debug:

```bash
# Windows PowerShell
Get-Content storage\logs\laravel.log -Tail 50

# Atau buka di editor
code storage/logs/laravel.log
```

Cari keyword:

-   `Admin: Mengajukan surat ke Dekan` → Admin berhasil proses
-   `Admin: Dekan tidak ditemukan` → Masalah di data Dekan
-   `Dekan Query Debug` → Info query Dekan

## Rollback Plan

Jika perlu rollback ke status lama:

```sql
-- Ubah semua 'menunggu-ttd' kembali ke 'Diajukan ke Dekan'
UPDATE Tugas_Surat
SET Status = 'Diajukan ke Dekan'
WHERE Status = 'menunggu-ttd';
```

Kemudian revert controller ke commit sebelumnya.

## Next Steps (Future Enhancement)

1. ✅ Implementasi QR Code TTE (sudah ada plan di `TTE_INTEGRATION_PLAN.md`)
2. ⚠️ Notifikasi Email/WA saat status berubah
3. ⚠️ Dashboard statistik surat per fakultas
4. ⚠️ Export laporan PDF untuk Admin/Dekan
5. ⚠️ Tracking history perubahan status

## Contact & Support

Jika masih ada masalah, provide informasi berikut:

1. Output dari `php debug_surat_flow.php`
2. Screenshot halaman Dekan
3. Last 50 lines dari `storage/logs/laravel.log`
4. Screenshot tabel `Tugas_Surat` dari database

---

**Last Updated:** November 18, 2025  
**Version:** 1.0  
**Status:** ✅ PRODUCTION READY
