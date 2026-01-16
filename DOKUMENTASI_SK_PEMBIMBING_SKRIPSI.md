# Model SK Pembimbing Skripsi - Dokumentasi

## Overview

Sistem ini terdiri dari 2 tabel utama untuk mengelola SK Pembimbing Skripsi:

1. **Req_SK_Pembimbing_Skripsi** - Menyimpan request/pengajuan SK
2. **Acc_SK_Pembimbing_Skripsi** - Menyimpan approval/persetujuan SK

---

## 1. ReqSKPembimbingSkripsi Model

### Informasi Tabel

-   **Table Name**: `Req_SK_Pembimbing_Skripsi`
-   **Primary Key**: `No` (auto increment)
-   **Timestamps**: Disabled (manual datetime fields)

### Struktur Field

| Field                        | Type         | Description                              |
| ---------------------------- | ------------ | ---------------------------------------- |
| No                           | int          | Primary key (auto increment)             |
| Id_Prodi                     | int          | Foreign key ke tabel Prodi               |
| Semester                     | enum         | 'Ganjil' atau 'Genap'                    |
| Data_Pembimbing_Skripsi      | json         | Data pembimbing dalam format JSON        |
| Id_Dosen_Kaprodi             | int          | Foreign key ke tabel Dosen (Kaprodi)     |
| Data_Dosen_Wali              | int          | ID Dosen Wali                            |
| Nomor_Surat                  | varchar(100) | Nomor surat SK                           |
| Status                       | enum         | Status pengajuan (7 kemungkinan)         |
| Id_Acc_SK_Pembimbing_Skripsi | int          | Foreign key ke Acc_SK_Pembimbing_Skripsi |
| Alasan-Tolak                 | text         | Alasan jika ditolak                      |
| Tanggal-Pengajuan            | timestamp    | Tanggal pengajuan                        |
| Tanggal-Tenggat              | datetime     | Tanggal deadline                         |

### Status Enum Values

1. `Dikerjakan admin` - Sedang diproses admin
2. `Menunggu-Persetujuan-Wadek-1` - Menunggu persetujuan Wadek 1
3. `Menunggu-Persetujuan-Dekan` - Menunggu persetujuan Dekan
4. `Selesai` - Sudah selesai disetujui
5. `Ditolak-Admin` - Ditolak oleh Admin
6. `Ditolak-Wadek1` - Ditolak oleh Wadek 1
7. `Ditolak-Dekan` - Ditolak oleh Dekan

### Relasi

#### 1. prodi() - belongsTo Prodi

```php
$request->prodi; // Mendapatkan data Program Studi
```

#### 2. kaprodi() - belongsTo Dosen

```php
$request->kaprodi; // Mendapatkan data Dosen Kaprodi
```

#### 3. accSKPembimbingSkripsi() - belongsTo AccSKPembimbingSkripsi

```php
$request->accSKPembimbingSkripsi; // Mendapatkan data approval
```

### Helper Methods

#### Scopes

```php
// Filter berdasarkan status
ReqSKPembimbingSkripsi::byStatus('Selesai')->get();

// Filter berdasarkan semester
ReqSKPembimbingSkripsi::bySemester('Ganjil')->get();

// Filter berdasarkan prodi
ReqSKPembimbingSkripsi::byProdi(1)->get();
```

#### Status Checkers

```php
$request->isSelesai();    // true jika status = 'Selesai'
$request->isDitolak();    // true jika ditolak (Admin/Wadek1/Dekan)
$request->isPending();    // true jika masih dalam proses
```

#### Accessors

```php
$request->status_readable; // Mendapatkan status dalam format yang lebih readable
// Contoh: "Menunggu-Persetujuan-Dekan" => "Menunggu Persetujuan Dekan"
```

---

## 2. AccSKPembimbingSkripsi Model

### Informasi Tabel

-   **Table Name**: `Acc_SK_Pembimbing_Skripsi`
-   **Primary Key**: `No` (auto increment)
-   **Timestamps**: Disabled (manual datetime fields)

### Struktur Field

| Field                     | Type         | Description                          |
| ------------------------- | ------------ | ------------------------------------ |
| No                        | int          | Primary key (auto increment)         |
| Semester                  | enum         | 'Ganjil' atau 'Genap'                |
| Tahun_Akademik            | varchar(100) | Tahun akademik (misal: 2024/2025)    |
| Data_Pembimbing_Skripsi   | json         | Data pembimbing dalam format JSON    |
| Nomor_Surat               | varchar(100) | Nomor surat SK yang disetujui        |
| Status                    | enum         | Status approval (5 kemungkinan)      |
| Alasan_Tolak              | text         | Alasan jika ditolak                  |
| QR_Code                   | varchar(255) | Path ke file QR Code                 |
| Tanggal_Persetujuan_Dekan | timestamp    | Tanggal disetujui Dekan              |
| Id_Dekan                  | int          | Foreign key ke tabel Pejabat (Dekan) |
| Tanggal_Pengajuan         | datetime     | Tanggal pengajuan                    |
| Tanggal_Tenggat           | datetime     | Tanggal deadline                     |

### Status Enum Values

1. `Menunggu-Persetujuan-Wadek-1` - Menunggu persetujuan Wadek 1
2. `Menunggu-Persetujuan-Dekan` - Menunggu persetujuan Dekan
3. `Selesai` - Sudah selesai disetujui
4. `Ditolak-Wadek1` - Ditolak oleh Wadek 1
5. `Ditolak-Dekan` - Ditolak oleh Dekan

### Relasi

#### 1. dekan() - belongsTo Pejabat

```php
$approval->dekan; // Mendapatkan data Dekan yang menyetujui
```

#### 2. reqSKPembimbingSkripsi() - hasMany ReqSKPembimbingSkripsi

```php
$approval->reqSKPembimbingSkripsi; // Mendapatkan semua request yang terkait
```

### Helper Methods

#### Scopes

```php
// Filter berdasarkan status
AccSKPembimbingSkripsi::byStatus('Selesai')->get();

// Filter berdasarkan semester
AccSKPembimbingSkripsi::bySemester('Ganjil')->get();

// Filter berdasarkan tahun akademik
AccSKPembimbingSkripsi::byTahunAkademik('2024/2025')->get();
```

#### Status Checkers

```php
$approval->isSelesai();    // true jika status = 'Selesai'
$approval->isDitolak();    // true jika ditolak (Wadek1/Dekan)
$approval->isPending();    // true jika masih dalam proses
```

#### Accessors

```php
$approval->status_readable; // Mendapatkan status dalam format yang lebih readable
```

---

## Relasi Antar Tabel

### Diagram Relasi

```
┌──────────────────────────────┐
│   Req_SK_Pembimbing_Skripsi  │
├──────────────────────────────┤
│ No (PK)                      │
│ Id_Prodi (FK -> Prodi)       │──────┐
│ Id_Dosen_Kaprodi (FK)        │──┐   │
│ Id_Acc_SK_Pembimbing_Skr...  │  │   │
│ Data_Pembimbing_Skripsi      │  │   │
│ Status                       │  │   │
│ ...                          │  │   │
└──────────────────────────────┘  │   │
         │                        │   │
         │ belongsTo              │   │
         ▼                        │   │
┌──────────────────────────────┐  │   │
│  Acc_SK_Pembimbing_Skripsi   │  │   │
├──────────────────────────────┤  │   │
│ No (PK)                      │  │   │
│ Id_Dekan (FK -> Pejabat)     │  │   │
│ Data_Pembimbing_Skripsi      │  │   │
│ QR_Code                      │  │   │
│ Status                       │  │   │
│ ...                          │  │   │
└──────────────────────────────┘  │   │
                                  │   │
         ┌────────────────────────┘   │
         ▼                            │
    ┌─────────┐                       │
    │  Dosen  │                       │
    ├─────────┤                       │
    │Id_Dosen │                       │
    └─────────┘                       │
                                      │
         ┌────────────────────────────┘
         ▼
    ┌─────────┐
    │  Prodi  │
    ├─────────┤
    │Id_Prodi │
    └─────────┘
```

### Flow Pengajuan

1. Admin/Kaprodi membuat **Request** di tabel `Req_SK_Pembimbing_Skripsi`
    - Status: `Dikerjakan admin`
2. Admin memproses dan forward ke Wadek 1
    - Status: `Menunggu-Persetujuan-Wadek-1`
3. Wadek 1 menyetujui dan forward ke Dekan
    - Status: `Menunggu-Persetujuan-Dekan`
    - Data dipindahkan ke tabel `Acc_SK_Pembimbing_Skripsi`
4. Dekan menyetujui dan finalisasi
    - Status di Req: `Selesai`
    - Status di Acc: `Selesai`
    - QR Code digenerate dan disimpan

### Catatan Penting

-   **One Request → One Approval**: Setiap request memiliki satu approval (belongsTo)
-   **One Approval → Many Requests**: Satu approval bisa dikaitkan dengan banyak request (hasMany)
-   **Data JSON**: Field `Data_Pembimbing_Skripsi` menyimpan array pembimbing dalam format JSON
-   **Status Flow**: Request memiliki lebih banyak status (7) dibanding Approval (5)

---

## Contoh Usage

### 1. Membuat Request Baru

```php
$request = ReqSKPembimbingSkripsi::create([
    'Id_Prodi' => 1,
    'Semester' => 'Ganjil',
    'Data_Pembimbing_Skripsi' => [
        ['nama_dosen' => 'Dr. Ahmad', 'nip' => '123456'],
        ['nama_dosen' => 'Dr. Budi', 'nip' => '789012'],
    ],
    'Id_Dosen_Kaprodi' => 5,
    'Status' => 'Dikerjakan admin',
    'Tanggal-Pengajuan' => now(),
]);
```

### 2. Mendapatkan Request dengan Relasi

```php
$request = ReqSKPembimbingSkripsi::with(['prodi', 'kaprodi', 'accSKPembimbingSkripsi'])
    ->find(1);

echo $request->prodi->Nama_Prodi;
echo $request->kaprodi->Nama_Dosen;
```

### 3. Filter Request

```php
// Semua request yang selesai
$selesai = ReqSKPembimbingSkripsi::byStatus('Selesai')->get();

// Request semester ganjil prodi tertentu
$requests = ReqSKPembimbingSkripsi::bySemester('Ganjil')
    ->byProdi(1)
    ->get();
```

### 4. Membuat Approval

```php
$approval = AccSKPembimbingSkripsi::create([
    'Semester' => 'Ganjil',
    'Tahun_Akademik' => '2024/2025',
    'Data_Pembimbing_Skripsi' => [
        ['nama_dosen' => 'Dr. Ahmad', 'nip' => '123456'],
    ],
    'Nomor_Surat' => 'SK/123/FT/2024',
    'Status' => 'Menunggu-Persetujuan-Wadek-1',
    'Id_Dekan' => 2,
]);

// Link request ke approval
$request->update([
    'Id_Acc_SK_Pembimbing_Skripsi' => $approval->No
]);
```

### 5. Check Status

```php
if ($request->isSelesai()) {
    // Download SK
} elseif ($request->isDitolak()) {
    // Tampilkan alasan penolakan
    echo $request->{'Alasan-Tolak'};
} else {
    // Masih dalam proses
    echo $request->status_readable;
}
```

---

## File Locations

-   **ReqSKPembimbingSkripsi Model**: `app/Models/ReqSKPembimbingSkripsi.php`
-   **AccSKPembimbingSkripsi Model**: `app/Models/AccSKPembimbingSkripsi.php`
-   **Test Script**: `test_sk_pembimbing_models.php`
