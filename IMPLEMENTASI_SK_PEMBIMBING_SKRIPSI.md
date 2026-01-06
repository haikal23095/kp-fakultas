# Implementasi SK Pembimbing Skripsi

## Overview

Fitur ini memungkinkan Kaprodi untuk mengajukan Surat Keputusan Pembimbing Skripsi yang berisi daftar mahasiswa beserta dosen pembimbing 1 dan pembimbing 2.

## File yang Dibuat/Dimodifikasi

### 1. Models

-   **SKPembimbingSkripsi.php** (`app/Models/SKPembimbingSkripsi.php`)

    -   Model untuk tabel `Req_SK_Pembimbing_Skripsi`
    -   Menyimpan data pengajuan SK Pembimbing Skripsi

-   **AccSKPembimbingSkripsi.php** (`app/Models/AccSKPembimbingSkripsi.php`)
    -   Model untuk tabel `Acc_SK_Pembimbing_Skripsi`
    -   Menyimpan SK yang sudah disetujui Dekan

### 2. Controller

-   **SKController.php** (`app/Http/Controllers/Kaprodi/SKController.php`)
    -   Method `createPembimbingSkripsi()`: Menampilkan form pengajuan
    -   Method `storePembimbingSkripsi()`: Menyimpan data pengajuan ke database

### 3. Views

-   **create.blade.php** (`resources/views/kaprodi/sk/pembimbing-skripsi/create.blade.php`)
    -   Form interaktif untuk input data mahasiswa dan pembimbing
    -   Validasi client-side untuk memastikan data lengkap

### 4. Routes

-   **web.php** (`routes/web.php`)
    -   GET `/kaprodi/sk/pembimbing-skripsi/create`: Tampilkan form
    -   POST `/kaprodi/sk/pembimbing-skripsi`: Simpan data

### 5. Migration

-   **create_req_sk_pembimbing_skripsi_table.php**
    -   Membuat tabel `Req_SK_Pembimbing_Skripsi`
    -   Membuat tabel `Acc_SK_Pembimbing_Skripsi`

## Struktur Data

### Data yang Diperlukan

1. **Identitas Pengajuan:**

    - Program Studi
    - Semester (Ganjil/Genap)
    - Tahun Akademik

2. **Daftar Penetapan Pembimbing (per Mahasiswa):**
    - Nama Mahasiswa (dropdown)
    - NPM (auto-fill dari mahasiswa)
    - Judul Skripsi (textarea)
    - Dosen Pembimbing 1 (dropdown)
    - Dosen Pembimbing 2 (dropdown)

### Format Data JSON (Data_Pembimbing)

```json
[
    {
        "id_mahasiswa": 1,
        "nama_mahasiswa": "John Doe",
        "npm": "200101010101",
        "judul_skripsi": "Implementasi Machine Learning...",
        "pembimbing_1": {
            "id_dosen": 5,
            "nama_dosen": "Dr. Jane Smith",
            "nip": "198501012010011001"
        },
        "pembimbing_2": {
            "id_dosen": 8,
            "nama_dosen": "Prof. Robert Brown",
            "nip": "197801012005011002"
        }
    }
]
```

## Fitur Form

### Validasi

1. **Server-side:**

    - Semua field wajib diisi
    - Judul skripsi maksimal 500 karakter
    - Mahasiswa dan dosen harus ada di database

2. **Client-side:**
    - Minimal 1 mahasiswa harus diinput
    - Pembimbing 1 dan Pembimbing 2 tidak boleh sama
    - NPM auto-fill saat memilih mahasiswa
    - Prevent double submission

### User Experience

-   Tambah baris baru dengan tombol "Tambah Mahasiswa"
-   Hapus baris dengan tombol trash (merah)
-   Dropdown dengan data real-time dari database
-   Auto-numbering untuk nomor urut
-   Loading state saat submit

## Flow Proses

```
Kaprodi → Form Input → Validasi → Simpan ke DB → Redirect ke Index
                                      ↓
                              Status: "Dikerjakan admin"
                                      ↓
                              (Menunggu proses Admin Fakultas)
```

## Cara Menggunakan

1. Login sebagai Kaprodi
2. Buka menu "Ajukan SK"
3. Klik card "SK Pembimbing Skripsi"
4. Isi identitas pengajuan (Prodi, Semester, Tahun Akademik)
5. Klik "Tambah Mahasiswa" untuk setiap mahasiswa yang akan ditambahkan
6. Pilih mahasiswa, isi judul skripsi, pilih pembimbing 1 dan 2
7. Klik "Ajukan SK"
8. SK akan masuk ke sistem dengan status "Dikerjakan admin"

## Next Steps (Belum Implementasi)

1. Halaman index/list SK Pembimbing Skripsi yang sudah diajukan
2. Detail view SK Pembimbing Skripsi
3. Download PDF SK Pembimbing Skripsi
4. Proses approval dari Admin Fakultas
5. Proses approval dari Dekan
6. Generate QR Code untuk verifikasi

## Dependencies

-   Laravel 10+
-   Bootstrap 5
-   jQuery (untuk dynamic form)
-   Font Awesome (icons)

## Database Tables

### Req_SK_Pembimbing_Skripsi

-   No (PK)
-   Id_Prodi (FK)
-   Semester
-   Tahun_Akademik
-   Data_Pembimbing (JSON)
-   Nomor_Surat
-   Id_Acc_SK_Pembimbing_Skripsi (FK)
-   Tanggal-Pengajuan
-   Tanggal-Tenggat
-   Id_Dosen_Kaprodi (FK)
-   Status
-   Alasan-Tolak

### Acc_SK_Pembimbing_Skripsi

-   No (PK)
-   Id_Prodi (FK)
-   Semester
-   Tahun_Akademik
-   Data_Pembimbing (JSON)
-   Nomor_Surat
-   Tanggal-Persetujuan-Dekan
-   QR_Code
