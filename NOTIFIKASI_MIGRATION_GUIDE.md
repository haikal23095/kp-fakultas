# Panduan Migrasi Notifikasi

## ✅ STATUS UPDATE - SISTEM SMART REDIRECT AKTIF!

### Perubahan Terbaru (v2.0)

**SMART REDIRECT SYSTEM** - Notifikasi sekarang otomatis mendeteksi kemana harus redirect berdasarkan:

-   ✅ Role user yang login
-   ✅ Jenis surat dari notifikasi
-   ✅ Tipe notifikasi (surat, approval, rejection, invitation)
-   ✅ Data tambahan yang tersimpan

**Tidak perlu action_url di setiap notifikasi lagi!** Sistem akan otomatis menentukan redirect yang tepat.

## Perubahan yang Sudah Dilakukan

### 1. **Controller Notifikasi** (`NotifikasiController.php`)

-   ✅ Tambah method `markAsReadAndRedirect()` - Auto mark as read + smart redirect
-   ✅ Tambah method `determineRedirectUrl()` - Logic smart redirect berdasarkan role & jenis surat
-   ✅ Tambah method `deleteAll()` - Hapus semua notifikasi user
-   ✅ Update method `markAsRead()` - Khusus untuk AJAX saja

### 2. **View Notifikasi** (`notifikasi/index.blade.php`)

-   ✅ Notifikasi sekarang clickable dan otomatis mark as read
-   ✅ Tambah tombol "Hapus Semua Notifikasi"
-   ✅ Hapus tombol centang (mark as read manual)
-   ✅ Tombol hapus individual tetap ada dengan stop propagation

### 3. **Routes** (`routes/web.php`)

-   ✅ `GET /notifikasi/{id}/redirect` - markAsReadAndRedirect
-   ✅ `DELETE /notifikasi` - deleteAll

### 4. **Controller yang Sudah Diupdate**

-   ✅ `PeminjamanMobilMahasiswaController.php` - Notifikasi mobil dinas
-   ✅ `SuratKeteranganAktifController.php` - Notifikasi surat aktif
-   ✅ `SuratPengantarMagangController.php` - Notifikasi magang (accept/reject)
-   ✅ `SuratLegalisirController.php` - Notifikasi legalisir
-   ✅ `Admin_Fakultas/DetailSuratController.php` - Notifikasi rejection & forward to dean
    -   Tambah helper: `getJenisSuratSlug()` dan `getActionUrlForMahasiswa()`

## Smart Redirect Logic

### Mahasiswa (Id_Role = 6)

Otomatis redirect ke riwayat berdasarkan jenis surat:

-   `aktif` → Riwayat Surat Aktif
-   `magang` → Riwayat Magang
-   `legalisir` → Riwayat Legalisir
-   `mobil_dinas` → Riwayat Mobil Dinas
-   `tidak_beasiswa` → Riwayat Tidak Beasiswa
-   `dispensasi` → Riwayat Dispensasi
-   `berkelakuan_baik` → Riwayat Berkelakuan Baik
-   `invitation` → Riwayat Magang (untuk invitation)
-   Default → Halaman Pilih Riwayat

### Admin Fakultas (Id_Role = 7)

-   `mobil_dinas` → Halaman Mobil Dinas
-   `legalisir` → Halaman Legalisir
-   Lainnya → Halaman Kelola Surat
-   Default → Dashboard Admin

### Admin Prodi (Id_Role = 1)

-   Semua → Manajemen Surat

### Dekan (Id_Role = 2)

-   `legalisir` → Legalisir Dekan
-   `sk_dosen_wali` → SK Dosen Wali
-   Lainnya → Pending Surat

### Wadek1 (Id_Role = 8)

-   `legalisir` → Legalisir Wadek1
-   `sk` → SK Wadek1
-   Lainnya → Pending Surat

### Wadek3 (Id_Role = 9/10)

-   `berkelakuan_baik` → Kelakuan Baik
-   `dispensasi` → Kemahasiswaan
-   Lainnya → Kemahasiswaan

## Format Standar Notifikasi Baru

### Format Minimum (Tanpa action_url - Sistem akan auto-detect)

```php
\App\Models\Notifikasi::create([
    'Tipe_Notifikasi' => 'surat', // surat/approval/rejection/invitation
    'Pesan' => 'Pesan notifikasi yang jelas',
    'Dest_user' => $userId,
    'Source_User' => Auth::id(),
    'Is_Read' => false,
    'Data_Tambahan' => json_encode([
        'id_tugas_surat' => $tugasSurat->Id_Tugas_Surat,
        'jenis_surat' => 'nama_jenis', // PENTING: untuk smart redirect
    ]),
]);
```

### Format Lengkap (Dengan action_url eksplisit - Override smart redirect)

```php
\App\Models\Notifikasi::create([
    'Tipe_Notifikasi' => 'surat',
    'Pesan' => 'Pesan notifikasi yang jelas',
    'Dest_user' => $userId,
    'Source_User' => Auth::id(),
    'Is_Read' => false,
    'Data_Tambahan' => json_encode([
        'id_tugas_surat' => $tugasSurat->Id_Tugas_Surat,
        'jenis_surat' => 'nama_jenis',
        'action_url' => route('route.name'), // Optional: override smart redirect
    ]),
]);
```

## Action URL Berdasarkan Role

### Mahasiswa (Id_Role = 6)

-   Surat Aktif: `route('mahasiswa.riwayat.aktif')`
-   Magang: `route('mahasiswa.riwayat.magang')`
-   Legalisir: `route('mahasiswa.riwayat.legalisir')`
-   Mobil Dinas: `route('mahasiswa.riwayat.mobil_dinas')`
-   Tidak Beasiswa: `route('mahasiswa.riwayat.tidak_beasiswa')`
-   Dispensasi: `route('mahasiswa.riwayat.dispensasi')`
-   Berkelakuan Baik: `route('mahasiswa.riwayat.berkelakuan_baik')`

### Admin Fakultas (Id_Role = 7)

-   Kelola Surat: `route('admin_fakultas.surat.kelola')`
-   Mobil Dinas: `route('admin_fakultas.surat.mobil_dinas')`
-   Legalisir: `route('admin_fakultas.legalisir.index')`

### Admin Prodi (Id_Role = 1)

-   Manajemen Surat: `route('admin_prodi.surat.manage')`

### Dekan (Id_Role = 2)

-   Pending Surat: `route('dekan.surat.pending')`
-   Legalisir: `route('dekan.legalisir.index')`
-   SK Dosen Wali: `route('dekan.sk_dosen_wali.index')`

### Wadek1 (Id_Role = 8)

-   Pending Surat: `route('wadek1.surat.pending')`
-   Legalisir: `route('wadek1.legalisir.index')`
-   SK: `route('wadek1.sk.index')`

### Wadek3 (Id_Role = ?)

-   Kelakuan Baik: `route('wadek3.kelakuan_baik.index')`
-   Kemahasiswaan: `route('wadek3.kemahasiswaan.index')`

## TODO: Update Notifikasi di Controller Lainnya

Berikut controller yang perlu diupdate untuk menambahkan `action_url`:

1. ✅ `PeminjamanMobilMahasiswaController.php` - DONE
2. ✅ `SuratKeteranganAktifController.php` - DONE
3. `SuratPengantarMagangController.php`
4. `SuratLegalisirController.php`
5. `SuratKelakuanBaikController.php`
6. `Admin_Fakultas/DetailSuratController.php`
7. `Admin_Fakultas/SKController.php`
8. `Dekan/DetailSuratController.php`
9. `Dekan/LegalisirController.php`
10. `Wadek1/LegalisirController.php`
11. `Wadek3/KelakuanBaikController.php`
12. Dan semua controller lain yang membuat notifikasi

## Cara Update Existing Notifikasi

### Before:

```php
Notifikasi::create([
    'Tipe_Notifikasi' => 'Accepted',
    'Pesan' => 'Pesan notifikasi',
    'Dest_user' => $userId,
    'Source_User' => Auth::id(),
    'Is_Read' => false,
]);
```

### After:

```php
Notifikasi::create([
    'Tipe_Notifikasi' => 'approval', // gunakan lowercase: surat, approval, rejection, invitation
    'Pesan' => 'Pesan notifikasi',
    'Dest_user' => $userId,
    'Source_User' => Auth::id(),
    'Is_Read' => false,
    'Data_Tambahan' => json_encode([
        'id_tugas_surat' => $tugasSurat->Id_Tugas_Surat,
        'jenis_surat' => 'nama_jenis',
        'action_url' => route('appropriate.route.based.on.role'),
    ]),
]);
```

## Testing Checklist

-   [ ] Mahasiswa mengajukan surat → Admin menerima notifikasi dengan link yang benar
-   [ ] Admin approve surat → Mahasiswa menerima notifikasi dengan link ke riwayat
-   [ ] Klik notifikasi otomatis mark as read dan redirect ke halaman yang tepat
-   [ ] Tombol "Hapus Semua" berfungsi dengan baik
-   [ ] Tombol hapus individual tidak trigger redirect
-   [ ] Notifikasi invitation tetap bisa accept/reject tanpa redirect
-   [ ] Semua role dapat akses notifikasi dengan layout yang sesuai

## Catatan Penting

-   **Jangan** hardcode URL, selalu gunakan `route()`
-   **Pastikan** route sudah ada sebelum digunakan
-   **Test** setiap perubahan dengan berbagai role
-   **Data_Tambahan** harus dalam format JSON valid
