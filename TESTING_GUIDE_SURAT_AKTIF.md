# PANDUAN TESTING SURAT KETERANGAN MAHASISWA AKTIF

## Status Saat Ini

Berdasarkan debug, ditemukan:

-   ✅ **User Dekan:** Dr. Budi Hartono (ID: 101) - VALID
-   ✅ **Data Dosen:** Terhubung ke Prodi Teknik Informatika (Fakultas ID: 2)
-   ✅ **Database Schema:** Migration sudah jalan, kolom lengkap
-   ⚠️ **Surat Existing:** Ada beberapa surat dengan status 'baru' yang belum diproses

## Langkah Testing

### STEP 1: Login sebagai Mahasiswa

1. Buka browser, akses aplikasi
2. Login dengan akun mahasiswa (contoh: Adi Saputra)
3. Navigasi ke: **Dashboard Mahasiswa** → **Pengajuan Surat Baru**

### STEP 2: Ajukan Surat Keterangan Aktif

1. Pilih jenis surat: **"Surat Keterangan Mahasiswa Aktif"**
2. Form akan muncul dengan data mahasiswa sudah terisi otomatis
3. Isi field required:
    - **Semester:** (contoh: 5)
    - **Tahun Akademik:** (contoh: 2024/2025)
    - **Keperluan:** (contoh: "Pengajuan beasiswa")
    - **Upload KRS:** Pilih file PDF
4. Klik **"Kirim Pengajuan"**

**Expected Result:**

-   ✅ Flash message: "Pengajuan Surat Keterangan Mahasiswa Aktif berhasil dikirim!"
-   ✅ Status surat: `'Diterima Admin'`
-   ✅ Penerima: User Admin Fakultas (bukan mahasiswa)

### STEP 3: Login sebagai Admin Fakultas

1. Logout dari mahasiswa
2. Login dengan akun: **Admin Fakultas**
3. Navigasi ke: **Dashboard Admin** → **Manajemen Surat**

**Expected Result:**

-   ✅ Tabel menampilkan surat dari mahasiswa
-   ✅ Status: "Diterima Admin" (badge warna secondary/abu-abu)
-   ✅ Kolom Pengaju: Nama mahasiswa + role (Mahasiswa)

### STEP 4: Admin Proses Surat (Opsi A - Tanpa Upload)

1. Klik **"Lihat Detail"** pada surat yang baru masuk
2. Di bagian header card, klik tombol **"Proses & Ajukan"**
3. Confirm dialog muncul, klik **OK**

**Expected Result:**

-   ✅ Flash message: "Tugas telah diajukan ke Dekan."
-   ✅ Status berubah: `'menunggu-ttd'`
-   ✅ Badge berubah warna (tidak lagi abu-abu)
-   ✅ Log file (`storage/logs/laravel.log`) muncul entry:
    ```
    Admin: Mengajukan surat ke Dekan
    id_surat: [ID]
    id_dekan: 101
    status: menunggu-ttd
    ```

### STEP 4 (Alternatif): Admin Upload Draft Final

1. Klik **"Lihat Detail"** pada surat
2. Scroll ke card **"Dokumen & Proses"**
3. Di bagian **"Upload Draft Final (PDF)"**:
    - Pilih file PDF (max 5MB)
    - Klik **"Submit & Ajukan ke Dekan"**
4. Confirm dialog, klik **OK**

**Expected Result:**

-   ✅ Flash message: "Draft final berhasil diupload dan diajukan ke Dekan."
-   ✅ Status: `'menunggu-ttd'`
-   ✅ File tersimpan di: `storage/app/public/surat_drafts/[ID]/`
-   ✅ Record baru di tabel `File_Arsip`
-   ✅ Link "Lihat Draft" muncul di card
-   ✅ Log file muncul entry dengan `draft_path`

### STEP 5: Login sebagai Dekan

1. Logout dari Admin
2. Login dengan akun: **Dekan** (Dr. Budi Hartono)
3. Navigasi ke: **Dashboard Dekan** → **Persetujuan TTE Surat**

**Expected Result (INI YANG KITA PERBAIKI):**

-   ✅ Tabel **TIDAK KOSONG** lagi!
-   ✅ Menampilkan surat yang sudah diajukan Admin
-   ✅ Kolom terisi:
    -   **Jenis Surat:** Surat Keterangan Aktif Kuliah
    -   **Pemohon:** Nama mahasiswa (Mahasiswa)
    -   **Tanggal Diajukan:** Tanggal pengajuan
    -   **Aksi:** Tombol "Lihat Detail" dan "Setujui (TTE)"

### STEP 6: Dekan Lihat Detail

1. Klik **"Lihat Detail"** pada salah satu surat
2. Halaman detail Dekan terbuka

**Expected Result:**

-   ✅ 3 card: Informasi Surat, Pemohon, Dokumen
-   ✅ Tombol **"Setujui & Tandatangani"** tersedia
-   ✅ Tombol **"Tolak Surat"** tersedia
-   ✅ Dokumen pendukung bisa didownload

### STEP 7: Dekan Approve Surat

1. Di halaman detail, klik **"Setujui & Tandatangani"**
2. Confirm dialog, klik **OK**

**Expected Result:**

-   ✅ Redirect ke halaman Persetujuan TTE
-   ✅ Flash message: "Surat berhasil ditandatangani..."
-   ✅ Surat **HILANG** dari tabel antrian
-   ✅ Di database:
    -   Status: `'Telah Ditandatangani Dekan'`
    -   Kolom `signature_qr_data` terisi (untuk future QR implementation)

### STEP 7 (Alternatif): Dekan Reject Surat

1. Klik **"Tolak Surat"**
2. Modal popup muncul
3. Isi **"Alasan Penolakan"** (contoh: "Data tidak lengkap")
4. Klik **"Tolak Surat"** di modal

**Expected Result:**

-   ✅ Modal tertutup
-   ✅ Flash message: "Surat telah ditolak."
-   ✅ Surat hilang dari antrian
-   ✅ Di database:
    -   Status: `'Ditolak'`
    -   Kolom `data_spesifik` berisi JSON:
        ```json
        {
            "komentar_penolakan": "Data tidak lengkap",
            "rejected_by": "Dr. Budi Hartono, S.Kom., M.Kom.",
            "rejected_by_id": 101,
            "rejected_at": "2025-11-18T10:30:00+07:00"
        }
        ```

## Verifikasi Database

Setelah testing, cek database untuk memastikan:

```bash
php artisan tinker
```

```php
// Cek surat terbaru
$surat = \App\Models\TugasSurat::orderBy('Id_Tugas_Surat', 'desc')->first();
echo "Status: " . $surat->Status . "\n";
echo "Penerima: " . $surat->Id_Penerima_Tugas_Surat . "\n";

// Jika di-reject, cek data_spesifik
if ($surat->Status === 'Ditolak') {
    $data = json_decode($surat->data_spesifik, true);
    print_r($data);
}

// Jika di-approve, cek signature_qr_data (optional, untuk future)
if ($surat->Status === 'Telah Ditandatangani Dekan') {
    echo "QR Data: " . ($surat->signature_qr_data ?? 'NULL') . "\n";
}
```

## Troubleshooting

### Problem: Surat tidak muncul di Dekan setelah Admin proses

**Diagnosis:**

```bash
php debug_surat_flow.php
```

**Cek:**

1. Apakah status berubah ke `'menunggu-ttd'`?
    - Jika TIDAK: Admin Controller error
2. Apakah `Id_Penerima_Tugas_Surat = 101` (ID Dekan)?
    - Jika TIDAK: Query Admin tidak menemukan Dekan
3. Apakah log muncul di `storage/logs/laravel.log`?
    - Jika TIDAK: Controller tidak eksekusi code yang benar

**Solusi:**

-   Cek role name di database: Harus **exact match** `'Dekan'`
-   Cek query: `LOWER(TRIM(Name_Role)) = 'dekan'` case-insensitive

### Problem: Error "Dekan tidak ditemukan"

**Diagnosis:**

```php
// Di tinker
$dekan = \App\Models\User::whereHas('role', function($q) {
    $q->whereRaw("LOWER(TRIM(Name_Role)) = 'dekan'");
})->first();

if (!$dekan) {
    echo "MASALAH: Dekan tidak ada!\n";

    // Cek semua role
    $roles = \App\Models\Role::all();
    foreach ($roles as $r) {
        echo "Role: [{$r->Name_Role}] (ID: {$r->Id_Role})\n";
    }
}
```

**Solusi:**

-   Pastikan ada role dengan `Name_Role = 'Dekan'` (case-sensitive di database)
-   Pastikan ada user dengan `Id_Role` yang sesuai

### Problem: Filter fakultas tidak bekerja

Ini jarang terjadi karena query sudah diperbaiki dengan tambahan:

```php
->orWhere('Id_Penerima_Tugas_Surat', $user->Id_User)
```

Tapi jika tetap tidak muncul, cek:

```php
// Cek prodi mahasiswa
$mahasiswa = \App\Models\Mahasiswa::where('Id_User', 201)->first(); // 201 = Adi Saputra
echo "Prodi Mahasiswa: " . $mahasiswa->Id_Prodi . "\n";

// Cek fakultas prodi
$prodi = \App\Models\Prodi::find($mahasiswa->Id_Prodi);
echo "Fakultas: " . $prodi->Id_Fakultas . "\n";

// Cek fakultas Dekan
$dekanDosen = \App\Models\Dosen::where('Id_User', 101)->first();
$prodiDekan = \App\Models\Prodi::find($dekanDosen->Id_Prodi);
echo "Fakultas Dekan: " . $prodiDekan->Id_Fakultas . "\n";

// Harusnya sama!
```

## Success Criteria

Testing dianggap **BERHASIL** jika:

-   ✅ Mahasiswa bisa submit surat
-   ✅ Admin bisa lihat surat di Manajemen Surat
-   ✅ Admin bisa proses & ajukan ke Dekan
-   ✅ **Dekan bisa MELIHAT surat di Persetujuan TTE** ← KEY FIX
-   ✅ Dekan bisa approve/reject
-   ✅ Status tersimpan dengan benar di database
-   ✅ Log files mencatat semua aksi

## Next Action

Setelah testing sukses:

1. Hapus/comment debug log di controller (optional, tidak mengganggu production)
2. Hapus file debug script (optional):
    - `debug_surat_flow.php`
    - `check_recent_surat.php`
3. Update dokumentasi user manual (jika ada)
4. Commit & push ke branch `sultan-fix`

---

**Prepared by:** GitHub Copilot  
**Date:** November 18, 2025  
**Status:** Ready for Testing
