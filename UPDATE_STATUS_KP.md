# Update Status KP Mahasiswa

## Sistem Otomatis Update Status_KP

Sistem ini mengatur status KP/Magang mahasiswa secara otomatis berdasarkan durasi magang.

### Flow Status KP

```
Tidak_Sedang_Melaksanakan
    ↓ (Saat Kaprodi Approve)
Sedang_Melaksanakan
    ↓ (Saat durasi magang selesai)
Telah_Melaksanakan
```

### Cara Kerja

1. **Saat Pengajuan Dibuat**

    - Status awal mahasiswa tetap (tidak berubah)
    - Mahasiswa dengan Status_KP = `Sedang_Melaksanakan` tidak bisa ditambahkan sebagai teman magang

2. **Saat Kaprodi Menyetujui (Approve)**

    - Status berubah menjadi `Sedang_Melaksanakan`
    - File: `app/Http/Controllers/Kaprodi/PermintaanSuratController.php` method `approve()`
    - Semua mahasiswa dalam pengajuan yang sama akan ter-update

3. **Saat Durasi Magang Selesai**
    - Status berubah menjadi `Telah_Melaksanakan` secara otomatis
    - Menggunakan Artisan Command: `mahasiswa:update-status-kp`
    - **Syarat**: Acc_Koordinator = 1 DAN Acc_Dekan = 1

### Menjalankan Command Update Status

#### Manual (Run Once)```bash

php artisan mahasiswa:update-status-kp

````

#### Otomatis dengan Task Scheduler

Tambahkan di `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Update status KP setiap hari pukul 00:01
    $schedule->command('mahasiswa:update-status-kp')
             ->daily()
             ->at('00:01');
}
````

Kemudian jalankan scheduler dengan cron job:

```bash
# Linux/Mac - Tambahkan di crontab
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1

# Windows - Gunakan Task Scheduler
php artisan schedule:run
```

#### Alternatif: Jalankan Manual Berkala

Jika tidak menggunakan scheduler, jalankan command secara manual setiap hari:

```bash
php artisan mahasiswa:update-status-kp
```

### File Terkait

1. **Command**: `app/Console/Commands/UpdateStatusKPMahasiswa.php`

    - Logic update status berdasarkan Tanggal_Selesai

2. **Controller Kaprodi**: `app/Http/Controllers/Kaprodi/PermintaanSuratController.php`

    - Method `approve()` - Update status saat disetujui
    - Method `updateMahasiswaStatusKP()` - Helper function

3. **API Search**: `app/Http/Controllers/PengajuanSurat/SuratPengantarMagangController.php`

    - Method `searchMahasiswa()` - Filter mahasiswa berdasarkan Status_KP

4. **JavaScript**: `public/js/pengajuan-surat.js`
    - Autocomplete dengan visual indicator untuk mahasiswa yang sedang magang

### Testing

```bash
# Cek mahasiswa yang sedang magang
SELECT Nama_Mahasiswa, NIM, Status_KP
FROM Mahasiswa
WHERE Status_KP = 'Sedang_Melaksanakan';

# Cek surat magang yang sudah selesai durasi
SELECT sm.*, ts.Id_Tugas_Surat
FROM Surat_Magang sm
JOIN Tugas_Surat ts ON sm.Id_Tugas_Surat = ts.Id_Tugas_Surat
WHERE sm.Status = 'Success'
AND sm.Acc_Koordinator = 1
AND sm.Acc_Dekan = 1
AND sm.Tanggal_Selesai < CURDATE();# Jalankan command
php artisan mahasiswa:update-status-kp

# Verifikasi hasil
SELECT Nama_Mahasiswa, NIM, Status_KP
FROM Mahasiswa
WHERE Status_KP = 'Telah_Melaksanakan';
```

### Catatan Penting

-   Command hanya akan update mahasiswa dengan Status_KP = `Sedang_Melaksanakan`
-   **Syarat surat diproses**: Status = `Success`, Acc_Koordinator = 1, dan Acc_Dekan = 1
-   Hanya surat dengan Tanggal_Selesai sudah lewat yang diproses
-   Mahasiswa yang sudah `Telah_Melaksanakan` tidak akan diupdate lagi
-   Data mahasiswa diambil dari field JSON `Data_Mahasiswa` di tabel `Surat_Magang`

### Struktur Database

**Kolom Approval di Surat_Magang:**

-   `Acc_Koordinator` (tinyint): 0 = Belum, 1 = Sudah disetujui Kaprodi
-   `Acc_Dekan` (tinyint): 0 = Belum, 1 = Sudah disetujui Dekan

**Migration:**

```php
// File: 2025_11_24_132627_add_acc_dekan_to_surat_magang_table.php
$table->tinyInteger('Acc_Dekan')->default(0)->after('Acc_Koordinator');
```
