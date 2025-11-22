# Testing Guide: QR Code Digital Signature

Panduan lengkap setup dan testing fitur QR Code Digital Signature pada sistem surat fakultas.

---

## 🔧 Setup Requirements

### 1. Python Requirements

QR Code generation menggunakan **Python script** (bukan PHP library). Install dependencies:

```bash
pip install qrcode[pil]
```

**Verifikasi instalasi:**

```bash
python -c "import qrcode; print('✅ QR Code library OK')"
```

> **Note:** Script ini menggunakan `qrcode` library dengan Pillow (PIL) untuk generate gambar PNG.

---

### 2. File Structure

#### **File Baru yang Ditambahkan:**

```
kp-fakultas/
├── generate_qr.py                              # Python script untuk generate QR code
├── app/
│   ├── Helpers/
│   │   └── QrCodeHelper.php                    # Helper Laravel untuk call Python script
│   └── Models/
│       └── SuratVerification.php               # Model untuk tabel surat_verifications
├── storage/
│   └── app/
│       └── public/
│           └── qr-codes/                        # Folder untuk simpan QR code images (auto-created)
└── resources/
    └── views/
        └── mahasiswa/
            └── pdf/
                └── surat_dengan_qr.blade.php   # View PDF dengan QR code
```

#### **File yang Diubah:**

1. **`app/Http/Controllers/Dekan/DetailSuratController.php`**

    - Added method `approve()` - Generate QR saat dekan approve surat
    - Added method `reject()` - Handle rejection dengan notifikasi

2. **`app/Http/Controllers/Mahasiswa/RiwayatSuratController.php`**

    - Added method `downloadSurat()` - Download PDF dengan QR code

3. **`app/Models/TugasSurat.php`**

    - Added relation `verification()` ke SuratVerification

4. **`composer.json`**
    - Removed `simplesoftwareio/simple-qrcode` (tidak jadi dipakai karena dependency issue)

---

### 3. Database Changes

#### **Tabel Baru: `surat_verifications`**

```sql
CREATE TABLE surat_verifications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_tugas_surat INT UNSIGNED NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    signed_by VARCHAR(255) NOT NULL,
    signed_by_user_id INT UNSIGNED,
    signed_at TIMESTAMP NOT NULL,
    qr_path TEXT NULL,                          -- Simpan URL ke file QR code PNG
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (id_tugas_surat) REFERENCES tugas_surat(Id_Tugas_Surat) ON DELETE CASCADE
);
```

#### **Kolom Baru di `tugas_surat`:**

```sql
ALTER TABLE tugas_surat
ADD COLUMN qr_image_path TEXT NULL,             -- URL ke QR code image
ADD COLUMN signature_qr_data TEXT NULL;         -- JSON data signature (backup)
```

---

### 4. Setup Commands

Jalankan command berikut setelah pull project:

```bash
# 1. Install Python library
pip install qrcode[pil]

# 2. Buat symlink storage (jika belum ada)
php artisan storage:link

# 3. Buat folder untuk QR codes
mkdir storage/app/public/qr-codes

# 4. Dump autoload (untuk load helper baru)
composer dump-autoload

# 5. (Optional) Regenerate QR untuk surat yang sudah ada
php regenerate_qr_codes.php
```

---

## 🔄 Workflow QR Code Generation

### Flow saat Dekan Approve Surat:

```
1. Dekan klik "Setujui" di halaman detail surat
   ↓
2. Controller: DetailSuratController@approve()
   ↓
3. Generate token unik (64 karakter)
   ↓
4. Create record di tabel surat_verifications
   ↓
5. Generate verify URL: http://localhost/verify-surat/{token}
   ↓
6. Call QrCodeHelper::generate($verifyUrl, 10)
   ↓
7. QrCodeHelper execute Python script:
   python generate_qr.py "http://..." "storage/app/public/qr-codes/qr_xxx.png" 10
   ↓
8. Python generate QR code PNG dan save ke storage
   ↓
9. Return public URL: http://localhost/storage/qr-codes/qr_xxx.png
   ↓
10. Save URL ke database:
    - surat_verifications.qr_path
    - tugas_surat.qr_image_path
    ↓
11. Update status surat jadi "Selesai"
    ↓
12. Kirim notifikasi ke mahasiswa & admin
```

### Flow saat Mahasiswa Download Surat:

```
1. Mahasiswa klik "Download PDF" di riwayat surat
   ↓
2. Controller: RiwayatSuratController@downloadSurat()
   ↓
3. Load TugasSurat dengan relasi verification
   ↓
4. Check status = "Selesai"
   ↓
5. Ambil QR code URL dari verification->qr_path
   ↓
6. Render view: mahasiswa.pdf.surat_dengan_qr
   ↓
7. View load QR code image dari URL lokal
   ↓
8. Display PDF dengan QR code di browser
```

---

## 🧪 Testing Steps

### Test 1: Generate QR Code dengan Python (Manual Test)

```bash
# Test generate QR code langsung dengan Python script
python generate_qr.py "https://example.com/verify/test123" "test_qr.png" 10

# Output: test_qr.png
# Check file exists:
ls test_qr.png
```

✅ **Expected Result:** File `test_qr.png` terbuat (ukuran ~1-2 KB)

---

### Test 2: Test QrCodeHelper dari Laravel

Buat file `test_qr_helper.php` di root project:

```php
<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$testUrl = "http://127.0.0.1:8000/verify-surat/TEST123";
$qrUrl = \App\Helpers\QrCodeHelper::generate($testUrl, 10);

echo "QR URL: {$qrUrl}\n";
echo file_exists(public_path(parse_url($qrUrl, PHP_URL_PATH))) ? "✅ File exists\n" : "❌ File not found\n";
```

Jalankan:

```bash
php test_qr_helper.php
```

✅ **Expected Output:**

```
QR URL: http://localhost/storage/qr-codes/qr_xxxxxxxxxxxxxxxx.png
✅ File exists
```

---

### Test 3: Dekan Approve Surat (Full Flow)

1. **Login sebagai Dekan**

    - URL: `http://127.0.0.1:8000/login`
    - Email: dekan@example.com

2. **Buka Dashboard Dekan**

    - Klik menu "Surat Masuk"
    - Cari surat dengan status "Menunggu Tanda Tangan"

3. **Approve Surat**

    - Klik tombol "Detail"
    - Klik tombol "✅ Setujui"
    - Lihat notifikasi sukses

4. **Verify Database**
    ```php
    // Check surat_verifications table
    php check_verifications.php
    ```

✅ **Expected:**

-   Status surat berubah jadi "Selesai"
-   Record baru di `surat_verifications` dengan qr_path berisi URL
-   File QR code ada di `storage/app/public/qr-codes/`
-   Mahasiswa dapat notifikasi

---

### Test 4: Mahasiswa Download Surat dengan QR

1. **Login sebagai Mahasiswa** (pembuat surat)

    - URL: `http://127.0.0.1:8000/login`

2. **Buka Riwayat Surat**

    - Klik menu "Riwayat Surat"
    - Cari surat yang statusnya "Selesai"

3. **Download PDF**

    - Klik tombol "📄 Download PDF"
    - PDF terbuka di browser baru

4. **Verify QR Code**
    - QR code muncul di bagian tanda tangan
    - Ada badge "✓ DIGITAL SIGNATURE"
    - Scan QR dengan HP → redirect ke verify URL

✅ **Expected:**

-   PDF menampilkan QR code (150x150px)
-   QR code bisa di-scan
-   QR berisi URL: `http://127.0.0.1:8000/verify-surat/{token}`

---

### Test 5: Verify QR Code URL

Scan QR code atau buka URL manual:

```
http://127.0.0.1:8000/verify-surat/{token}
```

✅ **Expected:**

-   Halaman verifikasi menampilkan:
    -   ✅ Dokumen Valid
    -   Nomor Surat
    -   Ditandatangani oleh: [Nama Dekan]
    -   Tanggal: [DD MMM YYYY]
    -   Status: Verified

---

## 🐛 Troubleshooting

### Error: "python: command not found"

**Solusi:**

```bash
# Pastikan Python terinstall
python --version

# Atau gunakan python3
python3 --version
```

Update `app/Helpers/QrCodeHelper.php` line 31:

```php
// Ganti "python" jadi "python3" kalau perlu
$command = "python3 \"{$scriptPath}\" {$dataEscaped} {$pathEscaped} {$boxSize}";
```

---

### Error: "No module named 'qrcode'"

**Solusi:**

```bash
pip install qrcode[pil]

# Atau dengan pip3
pip3 install qrcode[pil]
```

---

### Error: "QR Code not displaying in PDF"

**Debugging:**

1. Check file exists:

    ```bash
    ls storage/app/public/qr-codes/
    ```

2. Check symlink:

    ```bash
    php artisan storage:link
    ```

3. Check database qr_path:

    ```php
    php check_verifications.php
    ```

4. Regenerate QR codes:
    ```bash
    php regenerate_qr_codes.php
    ```

---

### Error: "Permission denied" saat generate QR

**Solusi:**

```bash
# Beri permission write ke folder storage
chmod -R 775 storage/app/public/qr-codes

# Atau di Windows (PowerShell as Admin):
icacls storage\app\public\qr-codes /grant Users:F /T
```

---

## 📝 Helper Scripts

Project ini include beberapa helper scripts untuk testing:

1. **`check_verifications.php`** - Check isi tabel surat_verifications
2. **`regenerate_qr_codes.php`** - Regenerate semua QR code yang ada
3. **`test_qr_helper.php`** - Test QrCodeHelper Laravel
4. **`generate_qr.py`** - Python script untuk generate QR (main script)

---

## 🎯 Key Points

✅ **QR Code menggunakan Python** (bukan PHP library)  
✅ **QR disimpan sebagai PNG** di storage (bukan Google Charts API)  
✅ **High error correction** (ERROR_CORRECT_H = 30% recovery)  
✅ **Unique token** (64 karakter) untuk setiap surat  
✅ **Verification URL** bisa di-scan dari HP  
✅ **Auto-cleanup** - QR code file akan tetap ada meskipun data dihapus (manual cleanup if needed)

---

## 📚 References

-   Python QR Code Library: https://github.com/lincolnloop/python-qrcode
-   Laravel Storage: https://laravel.com/docs/filesystem
-   QR Code Spec: https://www.qrcode.com/en/about/standards.html

---

**Last Updated:** November 22, 2025  
**Author:** Development Team  
**Branch:** sultan-fix
