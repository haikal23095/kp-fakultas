# 📁 STRUKTUR CONTROLLER MODULAR - PENGAJUAN SURAT

## 🎯 TUJUAN REFACTORING

Memisahkan logika bisnis setiap jenis surat ke Controller terpisah agar:

-   ✅ Lebih mudah dimaintain
-   ✅ Kode lebih terorganisir
-   ✅ Mudah menambah jenis surat baru
-   ✅ Setiap Controller fokus pada 1 tugas (Single Responsibility Principle)

---

## 📂 STRUKTUR FOLDER BARU

```
app/Http/Controllers/
├── PengajuanSurat/
│   ├── SuratKeteranganAktifController.php    ← Khusus Surat Aktif
│   └── SuratPengantarMagangController.php     ← Khusus Surat Magang
└── TugasSuratController.php (untuk admin view)
```

**✅ PengajuanSuratController.php sudah dihapus** - tidak diperlukan lagi karena sudah dipecah menjadi controller modular.

---

## 🗺️ MAPPING ROUTES

| Jenis Surat                      | ID  | Route                                    | Controller                             |
| -------------------------------- | --- | ---------------------------------------- | -------------------------------------- |
| Surat Keterangan Mahasiswa Aktif | 3   | `POST /mahasiswa/pengajuan-surat/aktif`  | `SuratKeteranganAktifController@store` |
| Surat Pengantar Magang/KP        | 6   | `POST /mahasiswa/pengajuan-surat/magang` | `SuratPengantarMagangController@store` |

---

## 🔄 ALUR KERJA

### 1️⃣ User memilih jenis surat di form

```javascript
// JavaScript mendeteksi perubahan dropdown
jenisSuratSelect.addEventListener("change", function () {
    const selectedValue = this.value; // ID surat (3 atau 6)

    // Ubah action form sesuai jenis surat
    if (selectedValue === "3") {
        form.action = "/mahasiswa/pengajuan-surat/aktif";
    } else if (selectedValue === "6") {
        form.action = "/mahasiswa/pengajuan-surat/magang";
    }
});
```

### 2️⃣ User submit form

```html
<form action="/mahasiswa/pengajuan-surat/aktif" method="POST">
    <!-- Data form surat aktif -->
</form>
```

### 3️⃣ Laravel routing mengarahkan ke Controller yang sesuai

```php
// routes/web.php
Route::post('/pengajuan-surat/aktif', [SuratKeteranganAktifController::class, 'store']);
Route::post('/pengajuan-surat/magang', [SuratPengantarMagangController::class, 'store']);
```

### 4️⃣ Controller memproses sesuai logika bisnis masing-masing

**Surat Aktif:**

-   Validasi: semester, tahun akademik, KRS
-   Upload file ke: `uploads/pendukung/surat-aktif/`
-   Deadline: 3 hari

**Surat Magang:**

-   Validasi: dosen pembimbing, instansi, alamat
-   Upload file ke: `uploads/pendukung/surat-magang/`
-   Deadline: 5 hari

---

## 📋 PERBEDAAN UTAMA SETIAP CONTROLLER

### SuratKeteranganAktifController

**Validasi:**

```php
'data_spesifik.semester' => 'required|numeric|min:1|max:14',
'data_spesifik.tahun_akademik' => 'required|string',
'file_pendukung_aktif' => 'required|file|mimes:pdf|max:2048',
```

**Upload:**

```php
$path = $file->store('uploads/pendukung/surat-aktif', 'public');
```

**Deadline:** 3 hari

---

### SuratPengantarMagangController

**Validasi:**

```php
'data_spesifik.dosen_pembimbing_1' => 'required|string',
'data_spesifik.nama_instansi' => 'required|string|max:255',
'data_spesifik.alamat_instansi' => 'required|string|max:500',
'file_pendukung_magang' => 'required|file|mimes:pdf|max:2048',
```

**Upload:**

```php
$path = $file->store('uploads/pendukung/surat-magang', 'public');
```

**Deadline:** 5 hari

---

## ➕ CARA MENAMBAH JENIS SURAT BARU

### 1. Buat Controller Baru

```bash
php artisan make:controller PengajuanSurat/SuratRekomendasiController
```

### 2. Implementasi method `store()`

```php
<?php
namespace App\Http\Controllers\PengajuanSurat;

class SuratRekomendasiController extends Controller
{
    public function store(Request $request)
    {
        // Logika khusus surat rekomendasi
    }
}
```

### 3. Tambahkan Route

```php
// routes/web.php
Route::post('/pengajuan-surat/rekomendasi',
    [SuratRekomendasiController::class, 'store'])
    ->name('mahasiswa.pengajuan.rekomendasi.store');
```

### 4. Update JavaScript di Blade

```javascript
const formIdMap = {
    3: { formId: "form-surat-aktif", route: "/aktif" },
    6: { formId: "form-surat-magang", route: "/magang" },
    5: { formId: "form-surat-rekomendasi", route: "/rekomendasi" }, // ← BARU
};
```

### 5. Buat Form HTML

```html
<div id="form-surat-rekomendasi" class="dynamic-form" style="display: none;">
    <!-- Form khusus surat rekomendasi -->
</div>
```

---

## ✅ KEUNTUNGAN STRUKTUR BARU

| Aspek                  | Sebelum (1 Controller) | Sesudah (Modular)       |
| ---------------------- | ---------------------- | ----------------------- |
| **Kode per file**      | 300+ baris             | 150 baris               |
| **Maintainability**    | ❌ Sulit               | ✅ Mudah                |
| **Debugging**          | ❌ Kompleks            | ✅ Jelas                |
| **Tambah surat baru**  | ❌ Edit file besar     | ✅ Buat file baru       |
| **Testing**            | ❌ Sulit isolasi       | ✅ Mudah test per jenis |
| **Team collaboration** | ❌ Conflict prone      | ✅ Parallel work        |

---

## 🧪 TESTING

### Test Route Surat Aktif

```bash
POST /mahasiswa/pengajuan-surat/aktif
Body: {
    "Id_Jenis_Surat": 3,
    "data_spesifik": {
        "semester": 7,
        "tahun_akademik": "2024/2025"
    },
    "Deskripsi_Tugas_Surat_Aktif": "Untuk beasiswa",
    "file_pendukung_aktif": [file PDF]
}
```

### Test Route Surat Magang

```bash
POST /mahasiswa/pengajuan-surat/magang
Body: {
    "Id_Jenis_Surat": 6,
    "data_spesifik": {
        "dosen_pembimbing_1": "Dr. Budi",
        "nama_instansi": "PT Google",
        "alamat_instansi": "Jakarta"
    },
    "file_pendukung_magang": [file PDF]
}
```

---

## 📊 HASIL AKHIR

✅ 2 Controller terpisah sudah dibuat
✅ Routes sudah dikonfigurasi
✅ Form blade sudah update dengan dynamic action
✅ JavaScript sudah handle routing otomatis
✅ Validasi khusus per jenis surat
✅ Upload file ke folder terpisah
✅ Deadline berbeda per jenis surat

---

## 🔍 VERIFIKASI

Jalankan command ini untuk memastikan routes terdaftar:

```bash
php artisan route:list --name=mahasiswa.pengajuan
```

Output yang diharapkan:

```
GET  mahasiswa/pengajuan-surat → pengajuan.create
POST mahasiswa/pengajuan-surat/aktif → SuratKeteranganAktifController@store
POST mahasiswa/pengajuan-surat/magang → SuratPengantarMagangController@store
```

---

**STRUKTUR MODULAR SUDAH SIAP DIGUNAKAN!** 🚀
