# Database Migration Summary

## Struktur Database: sistem_fakultas

Berikut adalah daftar lengkap tabel dan migrasi yang telah disesuaikan dengan database aktual.

### Urutan Migrasi (Berdasarkan Dependency)

#### 1. Tabel Dasar (Tanpa Foreign Key)

-   **0001_01_01_000001_create_cache_table.php** - Cache & cache_locks (Laravel default)
-   **0001_01_01_000002_create_jobs_table.php** - Jobs, job_batches, failed_jobs (Laravel default)
-   **2024_01_01_000001_create_roles_table.php** - Roles
-   **2024_01_01_000002_create_fakultas_table.php** - Fakultas
-   **2024_01_01_000004_create_pejabat_table.php** - Pejabat
-   **2024_01_01_000005_create_jenis_pekerjaan_table.php** - Jenis_Pekerjaan
-   **2024_01_01_000006_create_jenis_surat_table.php** - Jenis_Surat

#### 2. Tabel dengan 1 Level FK

-   **2024_01_01_000003_create_prodi_table.php** - Prodi (FK: Fakultas)
-   **0001_01_01_000000_create_users_table.php** - Users, password_reset_tokens, sessions (FK: Roles)

#### 3. Tabel dengan 2+ Level FK

-   **2024_01_01_000010_create_mahasiswa_table.php** - Mahasiswa (FK: Users, Prodi)
-   **2024_01_01_000011_create_dosen_table.php** - Dosen (FK: Users, Prodi, Pejabat)
-   **2024_01_01_000012_create_pegawai_prodi_table.php** - Pegawai_Prodi (FK: Users, Prodi)
-   **2024_01_01_000013_create_pegawai_fakultas_table.php** - Pegawai_Fakultas (FK: Users, Fakultas)
-   **2024_01_01_000020_create_tugas_table.php** - Tugas (FK: Users x2, Jenis_Pekerjaan)
-   **2024_01_01_000021_create_tugas_surat_table.php** - Tugas_Surat (FK: Users x2, Jenis_Surat, Jenis_Pekerjaan)

#### 4. Tabel dengan 3+ Level FK

-   **2025_11_14_032355_create_surat_magang_table.php** - Surat_Magang (FK: Tugas_Surat, Dosen)
-   **2025_11_19_163140_create_notifikasi_table.php** - Notifikasi (FK: Users x2)
-   **2024_01_01_000030_create_file_arsip_table.php** - File_Arsip (FK: Tugas_Surat, Users x2)
-   **2024_01_01_000031_create_penilaian_kinerja_table.php** - Penilaian_Kinerja (FK: Pegawai_Prodi, Users)
-   **2024_01_01_000032_create_surat_verifications_table.php** - surat_verifications (FK: Tugas_Surat)

---

## Detail Struktur Tabel

### 1. Roles

```
Id_Role (int PK)
Name_Role (varchar 255)
```

### 2. Fakultas

```
Id_Fakultas (int PK auto_increment)
Nama_Fakultas (varchar 255 NOT NULL)
```

### 3. Prodi

```
Id_Prodi (int PK)
Nama_Prodi (varchar 255)
Id_Fakultas (int FK → Fakultas)
```

### 4. Pejabat

```
Id_Pejabat (int PK)
Nama_Jabatan (enum: 'Kaprodi','Kajur','Dekan')
```

### 5. Users

```
Id_User (int PK, NO auto_increment) ⚠️
Username (varchar 255)
password (varchar 255)
Name_User (varchar 255)
Id_Role (int FK → Roles)
email (varchar 255)
```

**Note**: Id_User harus di-generate manual (max + 1)

### 6. Mahasiswa

```
Id_Mahasiswa (int PK)
NIM (int)
Nama_Mahasiswa (varchar 255)
Jenis_Kelamin_Mahasiswa (enum: 'L','P')
Alamat_Mahasiswa (text)
Id_User (int FK → Users)
Id_Prodi (int FK → Prodi)
```

### 7. Dosen

```
Id_Dosen (int PK auto_increment)
NIP (varchar 255)
Nama_Dosen (varchar 255)
Alamat_Dosen (text)
Id_User (int FK → Users)
Id_Prodi (int FK → Prodi)
Id_Pejabat (int FK → Pejabat)
```

### 8. Pegawai_Prodi

```
Id_Pegawai (int PK auto_increment)
NIP (varchar 255)
Nama_Pegawai (varchar 255)
Jenis_Kelamin_Pegawai (enum: 'L','P')
Alamat_Pegawai (text)
Id_User (int FK → Users)
Id_Prodi (int FK → Prodi)
```

### 9. Pegawai_Fakultas

```
Id_Pegawai (int PK, NO auto_increment) ⚠️
NIP (varchar 255)
Nama_Pegawai (varchar 255)
Jenis_Kelamin_Pegawai (enum: 'L','P')
Alamat_Pegawai (text)
Id_User (int FK → Users)
Id_Fakultas (int FK → Fakultas)
```

### 10. Jenis_Pekerjaan

```
Id_Jenis_Pekerjaan (int PK)
Jenis_Pekerjaan (enum: 'Surat','Non-Surat')
Nama_Pekerjaan (varchar 255)
```

### 11. Jenis_Surat

```
Id_Jenis_Surat (int PK)
Tipe_Surat (enum: 'Surat-Keluar','Surat-Masuk')
Nama_Surat (varchar 255)
```

### 12. Tugas

```
Id_Tugas (int PK auto_increment)
Id_Pemberi_Tugas (int FK → Users)
Id_Penerima_Tugas (int FK → Users)
Id_Jenis_Pekerjaan (int FK → Jenis_Pekerjaan)
Judul_Tugas (varchar 255)
Deskripsi_Tugas (text)
Tanggal_Diberikan_Tugas (date)
Tanggal_Tenggat_Tugas (date)
Status (enum: 'Dikerjakan','Selesai','Terlambat')
Tanggal_Diselesaikan (date)
File_Laporan (text)
```

### 13. Tugas_Surat

```
Id_Tugas_Surat (int PK, NO auto_increment) ⚠️
Id_Pemberi_Tugas_Surat (int FK → Users)
Id_Penerima_Tugas_Surat (int FK → Users)
Id_Jenis_Surat (int FK → Jenis_Surat)
Id_Jenis_Pekerjaan (int FK → Jenis_Pekerjaan)
Judul_Tugas_Surat (varchar 255)
Nomor_Surat (varchar 255)
Status (enum: 'baru','Diterima Admin','Proses','Diajukan ke Dekan',
       'menunggu-ttd','Telah Ditandatangani Dekan','Ditolak','Selesai','Terlambat')
data_spesifik (json)
signature_qr_data (text)
qr_image_path (varchar 255)
Tanggal_Diberikan_Tugas_Surat (date)
Tanggal_Tenggat_Tugas_Surat (date)
Tanggal_Diselesaikan (date)
```

**Note**: Id_Tugas_Surat harus di-generate manual (max + 1)

### 14. Surat_Magang

```
id_no (int PK auto_increment)
Id_Tugas_Surat (int FK → Tugas_Surat, NOT NULL)
Data_Mahasiswa (json)
Data_Dosen_pembiming (json)
Dokumen_Proposal (varchar 500)
Surat_Pengantar_Magang (varchar 500)
Nama_Instansi (varchar 255)
Alamat_Instansi (text)
Tanggal_Mulai (date NOT NULL)
Tanggal_Selesai (date NOT NULL)
Foto_ttd (varchar 500)
Nama_Koordinator (int FK → Dosen.Id_Dosen, NOT NULL)
Acc_Koordinator (tinyint(1) default 0, NOT NULL)
Status (enum: 'Diajukan-ke-koordinator','Dikerjakan-admin',
       'Diajukan-ke-dekan','Success','Ditolak', NOT NULL)
Komentar (text)
```

### 15. Notifikasi

```
Id_Notifikasi (bigint PK auto_increment)
Tipe_Notifikasi (enum: 'Rejected','Accepted','Caution','Error', NOT NULL)
Pesan (text NOT NULL)
Dest_user (int FK → Users, NOT NULL)
Source_User (int FK → Users, NOT NULL)
```

### 16. File_Arsip

```
Id_File_Arsip (int PK)
Id_Tugas_Surat (int FK → Tugas_Surat)
Id_Pemberi_Tugas_Surat (int FK → Users)
Id_Penerima_Tugas_Surat (int FK → Users)
Keterangan (text)
```

### 17. Penilaian_Kinerja

```
Id_Penilaian (int PK)
Id_Pegawai (int FK → Pegawai_Prodi)
Id_Penilai (int FK → Users)
Skor (enum: '1','2','3','4','5')
Komentar (text)
Tanggal_Penilaian (date)
```

### 18. surat_verifications

```
id (bigint unsigned PK auto_increment)
id_tugas_surat (int unsigned FK → Tugas_Surat, NOT NULL)
token (varchar 64 UNIQUE, NOT NULL)
signed_by (varchar 255, NOT NULL)
signed_by_user_id (int unsigned)
signed_at (timestamp, NOT NULL)
qr_path (varchar 255)
created_at (timestamp)
updated_at (timestamp)
```

---

## Important Notes

### ⚠️ Non-Auto-Increment Primary Keys

Beberapa tabel menggunakan PK yang **TIDAK auto-increment**:

1. **Users.Id_User** - Generate manual: `max(Id_User) + 1`
2. **Tugas_Surat.Id_Tugas_Surat** - Generate manual: `max(Id_Tugas_Surat) + 1`
3. **Pegawai_Fakultas.Id_Pegawai** - Generate manual: `max(Id_Pegawai) + 1`

### 🔑 Foreign Key Constraints

Semua foreign key menggunakan:

-   `onDelete('cascade')` - Hapus child records saat parent dihapus
-   `onUpdate('cascade')` - Update child records saat parent ID berubah

### 📋 Enum Values

Pastikan nilai enum di migration **persis sama** dengan database:

-   Case-sensitive
-   Spasi dan karakter khusus harus match
-   Contoh: `'Surat-Keluar'` bukan `'Surat Keluar'`

### 🚫 Migrasi yang Dihapus

File berikut telah dihapus karena duplikat/obsolete:

-   `2025_11_19_163133_create_notifikasi_table.php` (duplikat)
-   `2025_11_14_033455_add_nama_instansi_to_surat_magang_table.php` (sudah included di create)
-   `2025_11_14_032435_migrate_existing_magang_data_to_surat_magang.php` (tidak perlu)
-   `2025_11_15_125628_add_default_value_to_acc_koordinator_in_surat_magang_table.php` (sudah included)

---

## Cara Menggunakan

### Fresh Install (Database Baru)

```bash
php artisan migrate:fresh
```

### Update Existing (Production)

⚠️ **JANGAN gunakan migrate:fresh di production!**

Untuk production, buat migration baru untuk ALTER TABLE jika ada perubahan.

### Rollback

```bash
php artisan migrate:rollback
php artisan migrate:rollback --step=5
```

---

**Last Updated**: 2025-11-20
**Database**: sistem_fakultas
**Total Tables**: 24 (custom) + 5 (Laravel default)
