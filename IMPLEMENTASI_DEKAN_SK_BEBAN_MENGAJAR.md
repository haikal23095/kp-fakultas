# Implementasi Halaman SK Beban Mengajar untuk Dekan

## Deskripsi

Implementasi halaman baru untuk Dekan yang menampilkan daftar SK Beban Mengajar dari tabel `Acc_SK_Beban_Mengajar` dengan fitur preview dokumen dan penandatanganan menggunakan QR Code.

## Tanggal Implementasi

6 Januari 2025

## File yang Dibuat/Dimodifikasi

### 1. Controller Baru

**File:** `app/Http/Controllers/Dekan/SKBebanMengajarController.php`

**Fitur:**

-   `index()` - Menampilkan daftar SK dengan filter status dan semester
-   `detail($id)` - Mengambil detail SK dalam format JSON untuk preview
-   `approve($id)` - Menyetujui dan menandatangani SK dengan QR Code
-   `reject($id)` - Menolak SK dengan pilihan target (Admin Fakultas atau Wadek 1)
-   `history()` - Menampilkan history SK yang sudah diproses

**Proses Approval:**

1. Validasi status SK (harus `Menunggu-Persetujuan-Dekan`)
2. Generate QR Code dengan data:
    - Nomor Surat
    - Tahun Ajaran dan Semester
    - Nama dan NIP Dekan
    - Tanggal penandatanganan
3. Update status SK menjadi `Selesai`
4. Update `Id_Dekan`, `Tanggal_Persetujuan_Dekan`, dan `QR_Code`
5. Update semua Req_SK_Beban_Mengajar terkait dengan Nomor_Surat dan status
6. Kirim notifikasi ke Admin Fakultas

**Proses Rejection:**

1. Validasi status SK
2. Pilih target penolakan: `admin` atau `wadek1`
3. Update status sesuai target:
    - `Ditolak-Dekan-Ke-Admin`
    - `Ditolak-Dekan-Ke-Wadek1`
4. Update Alasan_Penolakan
5. Update semua Req_SK_Beban_Mengajar terkait
6. Kirim notifikasi ke target yang dipilih

### 2. Routes

**File:** `routes/web.php`

**Routes Ditambahkan:**

```php
Route::get('/persetujuan-surat/sk-beban-mengajar', [SKBebanMengajarController::class, 'index'])
    ->name('sk.beban-mengajar.index');
Route::get('/sk-beban-mengajar/history', [SKBebanMengajarController::class, 'history'])
    ->name('sk.beban-mengajar.history');
Route::get('/sk-beban-mengajar/{id}', [SKBebanMengajarController::class, 'detail'])
    ->name('sk.beban-mengajar.detail');
Route::post('/sk-beban-mengajar/{id}/approve', [SKBebanMengajarController::class, 'approve'])
    ->name('sk.beban-mengajar.approve');
Route::post('/sk-beban-mengajar/{id}/reject', [SKBebanMengajarController::class, 'reject'])
    ->name('sk.beban-mengajar.reject');
```

### 3. View Baru

**File:** `resources/views/dekan/sk/beban-mengajar/index.blade.php`

**Fitur:**

-   Tabel daftar SK dengan pagination
-   Filter berdasarkan status dan semester
-   Badge count jumlah dosen per SK
-   Badge status dengan color coding
-   Tombol aksi:
    -   **Lihat Detail (👁️)** - Preview dokumen lengkap dalam modal
    -   **Setujui dan Tandatangani (✓)** - Approve dengan QR Code (hanya untuk status Menunggu-Persetujuan-Dekan)
    -   **Tolak (✗)** - Reject dengan modal pilihan target

**Preview Document:**

-   Header kop surat Fakultas Ilmu Komputer
-   Nomor surat dan judul SK
-   Bagian "Menimbang" dan "Mengingat"
-   Bagian "Memutuskan"
-   Lampiran per prodi dengan tabel dosen dan mata kuliah
-   Area tanda tangan dengan QR Code (jika sudah ditandatangani)
-   Styling menggunakan Times New Roman, format resmi

**Modal Reject:**

-   Input pilihan target: Admin Fakultas atau Wadek 1
-   Textarea untuk alasan penolakan
-   Alert warning tentang konsekuensi penolakan
-   Validasi input sebelum submit

### 4. Update Dashboard

**File:** `resources/views/dekan/sk/index.blade.php`

**Perubahan:**

-   Card SK Beban Mengajar diaktifkan (remove `disabled` class)
-   Link menuju `route('dekan.sk.beban-mengajar.index')`
-   Badge count menampilkan jumlah SK yang menunggu
-   Tampilan total SK

### 5. Update Controller Dashboard

**File:** `app/Http/Controllers/Dekan/PersetujuanSuratController.php`

**Perubahan:**

-   Import `AccSKBebanMengajar` model
-   Tambah counting untuk SK Beban Mengajar:
    ```php
    $skBebanMengajarCount = AccSKBebanMengajar::where('Status', 'Menunggu-Persetujuan-Dekan')->count();
    $skBebanMengajarTotal = AccSKBebanMengajar::count();
    ```
-   Pass variable ke view

## Flow Approval SK Beban Mengajar

### Alur Lengkap:

1. **Kaprodi** → Mengajukan request SK Beban Mengajar (`Req_SK_Beban_Mengajar`)

    - Status: `Belum-Diproses`

2. **Admin Fakultas** → Menggabungkan beberapa request menjadi 1 SK (`Acc_SK_Beban_Mengajar`)

    - Status: `Menunggu-Persetujuan-Wadek-1`
    - Menggabungkan Data_Beban_Mengajar dari semua request
    - Menambahkan Id_Prodi dan Nama_Prodi ke setiap entry

3. **Wadek 1** → Mereview dan menyetujui/menolak

    - **Approve:** Status → `Menunggu-Persetujuan-Dekan` (tidak ada notifikasi ke Dekan)
    - **Reject:**
        - Ke Admin: Status → `Ditolak-Wadek1-Ke-Admin`
        - Ke Kaprodi: Status → `Ditolak-Wadek1-Ke-Kaprodi` (kirim ke semua Kaprodi terkait)

4. **Dekan** → Menandatangani dengan QR Code (IMPLEMENTASI BARU)
    - **Approve:**
        - Status → `Selesai`
        - Generate QR Code dan simpan
        - Update Tanggal_Persetujuan_Dekan dan Id_Dekan
        - Update Nomor_Surat di semua Req terkait
        - Kirim notifikasi ke Admin Fakultas
    - **Reject:**
        - Ke Admin: Status → `Ditolak-Dekan-Ke-Admin`
        - Ke Wadek 1: Status → `Ditolak-Dekan-Ke-Wadek1`
        - Kirim notifikasi ke target yang dipilih

## Status SK yang Ditangani

### Status yang Ditampilkan di Dekan:

-   `Menunggu-Persetujuan-Dekan` - Badge warning (kuning)
-   `Selesai` - Badge success (hijau)
-   `Ditolak-Dekan-Ke-Admin` - Badge danger (merah)
-   `Ditolak-Dekan-Ke-Wadek1` - Badge danger (merah)

### Filter Status:

-   Semua Status
-   Menunggu Persetujuan
-   Selesai
-   Ditolak ke Admin
-   Ditolak ke Wadek 1

## Kolom Database yang Digunakan

### Tabel: Acc_SK_Beban_Mengajar

-   `Id_Acc_SK_Beban_Mengajar` (Primary Key)
-   `Nomor_Surat`
-   `Tahun_Ajaran`
-   `Semester`
-   `Data_Beban_Mengajar` (JSON)
-   `Status` (ENUM)
-   `Id_Dekan` (Foreign Key → Dosen.Id_Dosen)
-   `Tanggal_Pengajuan` (timestamp)
-   `Tanggal_Persetujuan_Dekan` (timestamp)
-   `QR_Code` (TEXT - base64 encoded)
-   `Alasan_Penolakan` (TEXT)

### Tabel: Req_SK_Beban_Mengajar

-   `Id_Acc_SK_Beban_Mengajar` (Foreign Key, nullable)
-   `Nomor_Surat` (nullable)
-   `Status` (ENUM)

## Notifikasi yang Dikirim

### Saat Approve:

**Target:** Admin Fakultas

```
Judul: SK Beban Mengajar Disetujui Dekan
Isi: SK Beban Mengajar dengan nomor {Nomor_Surat} telah disetujui dan ditandatangani oleh Dekan.
Link: route('admin_fakultas.sk.beban-mengajar')
Tipe: Accepted
```

### Saat Reject ke Admin:

**Target:** Admin Fakultas

```
Judul: SK Beban Mengajar Ditolak Dekan
Isi: SK Beban Mengajar Tahun Ajaran {Tahun_Ajaran} Semester {Semester} ditolak oleh Dekan. Alasan: {alasan}
Link: route('admin_fakultas.sk.beban-mengajar')
Tipe: Rejected
```

### Saat Reject ke Wadek 1:

**Target:** Wadek 1

```
Judul: SK Beban Mengajar Ditolak Dekan
Isi: SK Beban Mengajar Tahun Ajaran {Tahun_Ajaran} Semester {Semester} ditolak oleh Dekan. Alasan: {alasan}
Link: route('wadek1.sk.beban-mengajar.index')
Tipe: Rejected
```

## Fitur QR Code

### Data yang Dienkripsi dalam QR Code:

```
SK Beban Mengajar
Nomor: {Nomor_Surat}
Tahun Ajaran: {Tahun_Ajaran}
Semester: {Semester}
Ditandatangani oleh: {Nama_Dekan}
NIP: {NIP_Dekan}
Tanggal: {dd-mm-yyyy HH:ii:ss}
```

### Implementasi:

-   Menggunakan `QrCodeHelper::generateQrCode($data)`
-   QR Code disimpan sebagai base64 encoded string
-   Ditampilkan di dokumen preview dan dokumen final
-   Ukuran: 100x100 pixels

## Testing Checklist

### Functional Testing:

-   [ ] Card SK Beban Mengajar di dashboard bisa diklik
-   [ ] Halaman list menampilkan data dari Acc_SK_Beban_Mengajar
-   [ ] Filter status berfungsi dengan baik
-   [ ] Filter semester berfungsi dengan baik
-   [ ] Pagination berfungsi
-   [ ] Preview modal menampilkan dokumen lengkap
-   [ ] Preview menampilkan lampiran per prodi
-   [ ] Tombol approve berfungsi dan generate QR Code
-   [ ] QR Code muncul di preview setelah approve
-   [ ] Status berubah menjadi Selesai setelah approve
-   [ ] Notifikasi terkirim ke Admin Fakultas setelah approve
-   [ ] Modal reject muncul dengan benar
-   [ ] Reject ke Admin berfungsi
-   [ ] Reject ke Wadek 1 berfungsi
-   [ ] Notifikasi terkirim sesuai target reject
-   [ ] History menampilkan SK yang sudah diproses

### UI/UX Testing:

-   [ ] Badge count menampilkan angka yang benar
-   [ ] Warna badge status sesuai (warning/success/danger)
-   [ ] Modal responsive di berbagai ukuran layar
-   [ ] Preview document styling sesuai format resmi
-   [ ] Tombol disabled untuk SK yang sudah diproses
-   [ ] Alert success/error muncul dengan benar

### Security Testing:

-   [ ] Hanya Dekan yang bisa mengakses halaman ini
-   [ ] Validasi status sebelum approve/reject
-   [ ] CSRF protection berfungsi
-   [ ] Input validation untuk alasan penolakan
-   [ ] Authorization check di controller

## Catatan Tambahan

### Dependencies:

-   QrCodeHelper (sudah ada)
-   Bootstrap 5
-   Font Awesome
-   Laravel Pagination

### Browser Compatibility:

-   Chrome/Edge (latest)
-   Firefox (latest)
-   Safari (latest)

### Known Limitations:

-   History belum diimplementasikan view-nya (ada controller method tapi belum ada view)
-   QR Code hanya generate saat approve, tidak ada preview sebelumnya

## Next Steps (Opsional)

1. Implementasi view history SK Beban Mengajar
2. Tambahkan fitur download PDF SK yang sudah ditandatangani
3. Tambahkan fitur cetak SK
4. Implementasi audit log untuk tracking perubahan status
5. Tambahkan filter tahun ajaran di list SK
