# TESTING GUIDE - DIGITAL SIGNATURE QR CODE SYSTEM

## Sistem Surat dengan QR Code Verifikasi

---

## 📋 RINGKASAN FITUR YANG SUDAH DIIMPLEMENTASIKAN

### ✅ 1. DEKAN APPROVAL - QR CODE GENERATION

**File:** `app/Http/Controllers/Dekan/DetailSuratController.php`

**Perubahan:**

-   Status surat setelah approve: `'Selesai'` (bukan 'Telah Ditandatangani Dekan')
-   Generate QR Code 300x300 PNG
-   Simpan ke `storage/app/public/qr_codes/{token}.png`
-   Create record di tabel `surat_verifications`
-   Token unik 64 karakter untuk keamanan

**Flow Logic:**

```
Dekan Approve → Generate Token → Create Verification Record → Generate QR Image →
Update Status = 'Selesai' → Set Tanggal_Diselesaikan
```

---

### ✅ 2. MAHASISWA DASHBOARD - RIWAYAT SURAT

**File:** `app/Http/Controllers/Mahasiswa/RiwayatSuratController.php`
**View:** `resources/views/mahasiswa/riwayat.blade.php`

**Fitur:**

-   Query semua surat mahasiswa dengan eager loading (jenisSurat, verification, suratMagang)
-   Tampilan tabel dengan status badge (color-coded)
-   Tombol "Download" hanya muncul jika status = 'Selesai'
-   Tombol "Verifikasi" untuk cek QR Code di public page
-   DataTables untuk sorting dan filtering

---

### ✅ 3. DOWNLOAD PDF DENGAN QR CODE

**File:** `app/Http/Controllers/Mahasiswa/RiwayatSuratController.php` → `downloadSurat()`
**View:** `resources/views/mahasiswa/pdf/surat_dengan_qr.blade.php`

**Fitur:**

-   Template PDF format resmi dengan Kop Surat
-   QR Code embedded di bagian tanda tangan (120x120 px)
-   Data mahasiswa (Nama, NIM, Prodi)
-   Info digital signature (token, timestamp)
-   Badge "✓ DIGITAL SIGNATURE" dengan gradient hijau
-   Footer dengan URL verifikasi dan token truncated

**Security:**

-   Validasi: Hanya mahasiswa pemilik surat yang bisa download
-   Validasi: Hanya surat dengan status 'Selesai' yang bisa didownload

---

### ✅ 4. PUBLIC VERIFICATION PAGE

**File:** `app/Http/Controllers/SuratVerificationController.php`
**View:** `resources/views/public/surat-verification.blade.php`
**Route:** `/verify-surat/{token}` (PUBLIC, no auth)

**Fitur:**

-   Scan QR Code → Redirect ke halaman verifikasi
-   Tampilkan: Jenis Surat, Pengaju, NIM, Penandatangan, Timestamp
-   Design: Gradient background, animated icons
-   API Endpoint: `/api/verify-surat/{token}` (JSON response)

---

## 🧪 CARA TESTING END-TO-END

### STEP 1: MAHASISWA SUBMIT SURAT

```
1. Login sebagai Mahasiswa
   URL: http://127.0.0.1:8000/login

2. Klik "Buat Pengajuan Baru" di dashboard
   URL: http://127.0.0.1:8000/mahasiswa/pengajuan-surat

3. Pilih jenis surat (contoh: Surat Keterangan Aktif)
4. Isi form dan submit
5. Lihat status di dashboard: Badge "Baru" (biru)
```

---

### STEP 2: ADMIN PROSES SURAT

```
1. Logout mahasiswa
2. Login sebagai Admin Fakultas

3. Buka Manajemen Surat
   URL: http://127.0.0.1:8000/admin/manajemen-surat

4. Tab "Perlu Diproses" → Pilih surat
5. Klik "Proses Surat"
   URL: http://127.0.0.1:8000/admin/surat/detail/{id}

6. Klik "Ajukan ke Dekan"
7. Status berubah: "menunggu-ttd"
```

---

### STEP 3: DEKAN APPROVE & GENERATE QR

```
1. Logout admin
2. Login sebagai Dekan

3. Buka Persetujuan Surat
   URL: http://127.0.0.1:8000/dekan/persetujuan-surat

4. Klik surat dengan status "menunggu-ttd"
5. Klik tombol "Setujui & Tanda Tangan"

6. ✅ HASIL:
   - Alert success: "Surat berhasil ditandatangani..."
   - Status berubah: "Selesai"
   - QR Code tersimpan di: storage/app/public/qr_codes/{token}.png
   - Record baru di tabel surat_verifications
```

**Verifikasi QR Generation:**

```sql
-- Cek database
SELECT * FROM surat_verifications ORDER BY signed_at DESC LIMIT 1;

-- Kolom yang harus terisi:
-- id_tugas_surat, token, signed_by, signed_by_user_id, signed_at, qr_path
```

---

### STEP 4: MAHASISWA DOWNLOAD SURAT

```
1. Logout dekan
2. Login kembali sebagai Mahasiswa (yang submit surat tadi)

3. Buka Riwayat Surat
   URL: http://127.0.0.1:8000/mahasiswa/riwayat

4. Lihat tabel → Status: Badge "Selesai" (hijau)
5. Tombol "Download" dan "Verifikasi" muncul

6. Klik tombol "Download" (hijau)
   URL: http://127.0.0.1:8000/mahasiswa/surat/download/{id}

7. ✅ HASIL:
   - PDF terbuka di browser baru
   - QR Code muncul di bagian tanda tangan
   - Badge "✓ DIGITAL SIGNATURE" visible
   - Footer berisi token verifikasi
```

---

### STEP 5: VERIFIKASI QR CODE

```
1. Buka PDF yang didownload
2. Scan QR Code menggunakan smartphone/QR scanner

3. Browser akan redirect ke:
   URL: http://127.0.0.1:8000/verify-surat/{token}

4. ✅ HASIL VALID:
   - Background gradient hijau
   - Icon check circle animated
   - Tampil data: Jenis Surat, Pengaju + NIM, Penandatangan, Timestamp
   - Badge "VALID DOCUMENT"

5. Test Invalid Token:
   URL: http://127.0.0.1:8000/verify-surat/TOKENPALSU12345

   ✅ HASIL INVALID:
   - Background gradient merah
   - Icon times circle animated
   - Pesan error: "Dokumen tidak ditemukan"
```

---

## 🗄️ DATABASE VERIFICATION

### Tabel: `surat_verifications`

```sql
-- Cek semua verifikasi
SELECT
    sv.id,
    sv.token,
    ts.Judul_Tugas_Surat,
    ts.Status,
    sv.signed_by,
    sv.signed_at,
    sv.qr_path
FROM surat_verifications sv
JOIN Tugas_Surat ts ON sv.id_tugas_surat = ts.Id_Tugas_Surat
ORDER BY sv.signed_at DESC;
```

### Expected Output:

```
| id | token        | Judul_Tugas_Surat | Status  | signed_by    | signed_at           | qr_path                        |
|----|--------------|-------------------|---------|--------------|---------------------|--------------------------------|
| 1  | abc123def... | Surat Aktif...    | Selesai | Prof. Dr...  | 2025-11-18 15:30:00 | qr_codes/abc123def456.png     |
```

---

## 📁 FILE STRUCTURE YANG DIBUAT/DIMODIFIKASI

### 🆕 New Files:

```
app/Http/Controllers/Mahasiswa/RiwayatSuratController.php
app/Models/SuratVerification.php
app/Http/Controllers/SuratVerificationController.php
resources/views/mahasiswa/riwayat.blade.php (updated)
resources/views/mahasiswa/pdf/surat_dengan_qr.blade.php
resources/views/public/surat-verification.blade.php
database/migrations/2025_11_18_155455_create_surat_verifications_table.php
```

### ✏️ Modified Files:

```
app/Http/Controllers/Dekan/DetailSuratController.php
  - Line 143: Status = 'Selesai' (bukan 'Telah Ditandatangani Dekan')
  - Line 77-142: QR Code generation logic

app/Models/TugasSurat.php
  - Added: verification() relation

routes/web.php
  - Added: mahasiswa.riwayat route
  - Added: mahasiswa.surat.download route
  - Already exists: surat.verify (public route)

resources/views/dashboard/mahasiswa.blade.php
  - Updated: Link "Lihat Semua Riwayat" → route('mahasiswa.riwayat')
```

---

## 🔐 SECURITY FEATURES

1. **Token Unik**: 64 karakter random string per dokumen
2. **Route Protection**: Download surat hanya bisa oleh mahasiswa pemilik
3. **Status Validation**: Download hanya untuk surat status 'Selesai'
4. **Timestamp**: Setiap tanda tangan tercatat dengan timestamp akurat
5. **Public Verification**: QR dapat diverifikasi siapa saja tanpa login
6. **Token Truncation**: Di PDF hanya ditampilkan sebagian token (first 16 + last 8 char)

---

## 🎨 UI/UX HIGHLIGHTS

### Mahasiswa Riwayat View:

-   Color-coded status badges
-   DataTables untuk search/sort
-   Info card menjelaskan arti setiap status
-   Button disabled untuk surat yang belum selesai

### PDF Template:

-   Professional letterhead (Kop Surat)
-   QR Code dengan border + badge
-   Footer informasi digital signature
-   Print-friendly CSS

### Public Verification Page:

-   Gradient background (green/red)
-   Animated icons (fade-in effect)
-   Responsive design
-   Security badge indicators

---

## ⚠️ TROUBLESHOOTING

### Issue 1: QR Code tidak muncul di PDF

**Check:**

```bash
# Verifikasi file QR exists
ls storage/app/public/qr_codes/

# Cek symbolic link
ls -la public/storage

# Jika belum ada, jalankan:
php artisan storage:link
```

### Issue 2: Route 404 Not Found

**Solution:**

```bash
# Clear route cache
php artisan route:clear
php artisan route:cache

# Verify routes
php artisan route:list --path=mahasiswa
```

### Issue 3: Download PDF error

**Check:**

-   Apakah status surat = 'Selesai'?
-   Apakah mahasiswa yang login adalah pemilik surat?
-   Cek error log: `storage/logs/laravel.log`

### Issue 4: QR Scanner tidak redirect

**Check:**

-   Apakah URL di QR Code benar? (harus full URL dengan domain)
-   Test manual: Copy URL dari footer PDF, paste di browser
-   Verifikasi token di database

---

## 📊 METRICS TO TRACK

1. **Jumlah surat dengan QR**:

    ```sql
    SELECT COUNT(*) FROM surat_verifications;
    ```

2. **Rata-rata waktu proses surat**:

    ```sql
    SELECT AVG(TIMESTAMPDIFF(HOUR,
        Tanggal_Diberikan_Tugas_Surat,
        Tanggal_Diselesaikan
    )) as avg_hours
    FROM Tugas_Surat
    WHERE Status = 'Selesai';
    ```

3. **Surat selesai per hari**:
    ```sql
    SELECT DATE(Tanggal_Diselesaikan) as tanggal, COUNT(*) as total
    FROM Tugas_Surat
    WHERE Status = 'Selesai'
    GROUP BY DATE(Tanggal_Diselesaikan);
    ```

---

## ✅ CHECKLIST DEPLOYMENT

-   [ ] Run migration: `php artisan migrate`
-   [ ] Storage link: `php artisan storage:link`
-   [ ] QR library installed: `composer show simplesoftwareio/simple-qrcode`
-   [ ] Folder writable: `chmod -R 775 storage/app/public/qr_codes`
-   [ ] Test route list: `php artisan route:list`
-   [ ] Clear all cache: `php artisan optimize:clear`
-   [ ] Test end-to-end workflow (all 5 steps above)
-   [ ] Test QR scan dengan smartphone
-   [ ] Backup database sebelum deploy production

---

## 🚀 NEXT ENHANCEMENTS (OPTIONAL)

1. **Email Notification**: Kirim email ke mahasiswa saat surat selesai
2. **Download Counter**: Track berapa kali surat didownload
3. **QR Expiry**: QR Code expire setelah X bulan
4. **Batch QR Generation**: Generate QR untuk multiple surat sekaligus
5. **Admin Report**: Dashboard analytics untuk admin
6. **Mobile App**: API untuk mobile app verification
7. **Watermark**: Tambah watermark "ASLI" di background PDF
8. **Digital Archive**: Auto-archive ke Google Drive/OneDrive

---

**Created:** November 18, 2025
**Version:** 1.0.0
**Status:** ✅ Production Ready
