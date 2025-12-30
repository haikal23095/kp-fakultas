# Update Status KP Mahasiswa - Implementasi

## Fitur yang Ditambahkan

### 1. Update Status Saat Dekan Menyetujui

Ketika Dekan mengklik tombol **"SETUJUI & TTD"**, sistem akan:

-   Mengubah status surat menjadi "Success"
-   Generate QR Code untuk tanda tangan digital Dekan
-   **Update Status_KP mahasiswa menjadi "Sedang_Melaksanakan"** (BARU)
-   Mengirim notifikasi ke mahasiswa

**File yang dimodifikasi:**

-   `app/Http/Controllers/Dekan/SuratMagangController.php` (method `approve`)

### 2. Update Status Otomatis Saat Tanggal Selesai Tercapai

Sistem akan secara otomatis mengubah Status_KP mahasiswa menjadi **"Telah_Melaksanakan"** ketika:

-   Status surat = "Success"
-   Acc_Dekan = 1 (sudah disetujui)
-   Tanggal_Selesai sudah lewat/tercapai
-   Status_KP mahasiswa masih "Sedang_Melaksanakan"

**File yang dibuat:**

-   `app/Console/Commands/UpdateStatusMahasiswaKP.php` (Command untuk cek dan update status)
-   `routes/console.php` (Schedule untuk menjalankan command setiap hari)

## Cara Kerja

### Flow Status KP Mahasiswa:

```
Belum_Melaksanakan
    ↓
(Dekan klik SETUJUI & TTD)
    ↓
Sedang_Melaksanakan
    ↓
(Tanggal_Selesai tercapai - cron job otomatis)
    ↓
Telah_Melaksanakan
```

## Testing Command Manual

Untuk menguji command secara manual tanpa menunggu scheduler:

```bash
php artisan mahasiswa:update-status-kp
```

Command akan:

-   Mengecek semua surat magang yang sudah disetujui
-   Memeriksa tanggal selesai magang
-   Update status mahasiswa yang tanggal magangnya sudah selesai
-   Menampilkan log mahasiswa yang diupdate

## Setup Scheduler (Untuk Production)

Agar command berjalan otomatis setiap hari, tambahkan cron job di server:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

Atau untuk Windows Task Scheduler:

```
Program/script: C:\php\php.exe
Add arguments: artisan schedule:run
Start in: D:\Project-KP\kp-fakultas
```

Schedule akan menjalankan command `mahasiswa:update-status-kp` setiap hari secara otomatis.

## Catatan Penting

1. **Status_KP di Tabel Mahasiswa** harus memiliki nilai yang valid:

    - Belum_Melaksanakan
    - Sedang_Melaksanakan
    - Telah_Melaksanakan

2. **Data_Mahasiswa** di tabel Surat_Magang harus berisi array dengan key `nim` yang valid.

3. Command hanya akan update mahasiswa yang status KP-nya "Sedang_Melaksanakan" untuk menghindari update berulang.

4. Scheduler Laravel harus diaktifkan di server production dengan cron job.

## Testing

### Test Approve Dekan:

1. Login sebagai Dekan
2. Buka surat magang yang menunggu persetujuan
3. Klik tombol "SETUJUI & TTD"
4. Cek tabel Mahasiswa - Status_KP harus berubah menjadi "Sedang_Melaksanakan"

### Test Update Otomatis:

1. Pastikan ada surat magang dengan Tanggal_Selesai yang sudah lewat
2. Jalankan command: `php artisan mahasiswa:update-status-kp`
3. Cek tabel Mahasiswa - Status_KP harus berubah menjadi "Telah_Melaksanakan"
