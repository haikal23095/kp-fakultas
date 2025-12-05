# Sistem Manajemen Surat Fakultas Teknik - Universitas Trunojoyo Madura

![Laravel](https://img.shields.io/badge/Laravel-12.x-red.svg)
![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)
![License](https://img.shields.io/badge/License-MIT-green.svg)

Sistem informasi berbasis web untuk mengelola proses pengajuan, verifikasi, dan penerbitan surat-surat administratif di Fakultas Teknik Universitas Trunojoyo Madura. Sistem ini mendukung workflow multi-level approval dengan integrasi QR Code untuk validasi dokumen digital.

## 📋 Daftar Isi

-   [Fitur Utama](#-fitur-utama)
-   [Teknologi](#-teknologi)
-   [Persyaratan Sistem](#-persyaratan-sistem)
-   [Instalasi](#-instalasi)
-   [Konfigurasi](#-konfigurasi)
-   [Struktur Database](#-struktur-database)
-   [Pengguna & Role](#-pengguna--role)
-   [Workflow Surat](#-workflow-surat)
-   [Dokumentasi](#-dokumentasi)
-   [Kontribusi](#-kontribusi)
-   [Lisensi](#-lisensi)

## 🎯 Fitur Utama

### 1. Manajemen Multi-Role

-   **Mahasiswa**: Pengajuan surat, tracking status, undangan magang kolaboratif
-   **Kaprodi**: Verifikasi dan approval surat dari mahasiswa
-   **Dekan**: Approval final dengan tanda tangan digital + QR Code
-   **Admin Fakultas**: Monitoring semua surat, manajemen data master

### 2. Jenis Surat yang Didukung

-   ✅ **Surat Keterangan Mahasiswa Aktif**: Verifikasi status aktif kuliah
-   ✅ **Surat Pengantar Magang/KP**: Dengan sistem ajakan kolaboratif multi-mahasiswa (max 5 orang)
-   🔄 **Surat Pengantar Tugas Akhir**: (Coming Soon)
-   🔄 **Surat Rekomendasi**: (Coming Soon)

### 3. Workflow Digital

```
Mahasiswa → Pengajuan Surat
    ↓
Verifikasi Dokumen (Upload KRS/Proposal)
    ↓
Approval Kaprodi → QR Code Kaprodi
    ↓
Approval Dekan → QR Code Dekan
    ↓
Download Surat Resmi (PDF dengan QR Code)
```

### 4. Fitur Khusus Surat Magang

-   **Sistem Ajakan Kolaboratif**: Mahasiswa dapat mengundang teman (max 5 orang)
-   **Invitation System**: Mahasiswa yang diundang dapat menerima/menolak
-   **Status Tracking**: Draft → Pending Approval → Diajukan → Approved → Success
-   **Multi-Upload**: Proposal + Tanda Tangan Digital
-   **No WhatsApp per Mahasiswa**: Kontak langsung dalam JSON `Data_Mahasiswa`

### 5. QR Code Integration

-   **Endroid QR Code v6.0**: Generate QR Code untuk setiap approval
-   **Dual QR System**: QR Kaprodi + QR Dekan pada surat final
-   **Validasi Digital**: QR berisi informasi timestamp dan identitas penandatangan

### 6. Dashboard & Monitoring

-   **Statistik Real-time**: Total surat, status pending, approved, rejected
-   **Timeline Tracking**: History perjalanan surat dari pengajuan hingga selesai
-   **Notifikasi**: Alert untuk surat baru, approval, dan perubahan status

## 🛠 Teknologi

### Backend

-   **Laravel 12.x**: PHP Framework
-   **PHP 8.2+**: Server-side language
-   **MySQL**: Database management

### Frontend

-   **Bootstrap 5**: UI Framework
-   **jQuery**: DOM manipulation & AJAX
-   **Font Awesome**: Icon library
-   **SB Admin 2**: Admin template

### Libraries

-   **Endroid QR Code v6.0**: QR Code generation
-   **Dompdf**: PDF generation
-   **Laravel Tinker**: Interactive shell

## 📦 Persyaratan Sistem

-   PHP >= 8.2
-   Composer
-   MySQL >= 8.0
-   Node.js & NPM (untuk asset compilation)
-   Web Server (Apache/Nginx)

## 🚀 Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/haikal23095/kp-fakultas.git
cd kp-fakultas
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

### 3. Environment Setup

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Database Configuration

Edit file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kp_fakultas
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Database Migration & Seeding

```bash
# Run migrations
php artisan migrate

# Seed data (opsional)
php artisan db:seed
```

### 6. Storage Link

```bash
# Create symbolic link untuk storage
php artisan storage:link
```

### 7. Run Development Server

```bash
# Start Laravel development server
php artisan serve

# Compile assets (terminal terpisah)
npm run dev
```

Akses aplikasi di: `http://localhost:8000`

## ⚙️ Konfigurasi

### Upload Limits

Edit di `.env`:

```env
# Maximum file upload size
UPLOAD_MAX_FILESIZE=2M
POST_MAX_SIZE=2M
```

### QR Code Settings

Konfigurasi di controller:

```php
$qrCode = new QrCode(
    data: $content,
    size: 200,      // Ukuran QR Code
    margin: 10      // Margin
);
```

### Email Configuration (untuk notifikasi)

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@unijoyo.ac.id
MAIL_FROM_NAME="${APP_NAME}"
```

## 🗄 Struktur Database

### Tabel Utama

#### `users`

User authentication dan profil dasar

```sql
- Id_User (PK)
- Name_User
- Email_User
- Password_User
- Role (1=Admin, 2=Mahasiswa, 3=Dekan, 4=Kaprodi, 5=Dosen)
```

#### `mahasiswa`

Data lengkap mahasiswa

```sql
- Id_Mahasiswa (PK)
- Id_User (FK)
- Nama_Mahasiswa
- NIM
- Id_Prodi (FK)
- Angkatan
- Status_KP (enum: Belum_Mengambil, Sedang_Melaksanakan, Selesai)
```

#### `tugas_surat`

Master record untuk setiap pengajuan surat

```sql
- Id_Tugas_Surat (PK)
- Id_Jenis_Surat (FK)
- Id_Pemberi_Tugas_Surat (FK -> users)
- Id_Penerima_Tugas_Surat (FK -> users)
- Status (baru, dalam_proses, selesai, dibatalkan)
- Judul_Tugas_Surat
- Tanggal_Diberikan_Tugas_Surat
```

#### `surat_magang`

Detail surat pengantar magang/KP

```sql
- id_no (PK)
- Id_Tugas_Surat (FK)
- Data_Mahasiswa (JSON) - [{nama, nim, jurusan, angkatan, no_wa}]
- Data_Dosen_pembiming (JSON) - {dosen_pembimbing_1, dosen_pembimbing_2}
- Judul_Penelitian
- Nama_Instansi
- Alamat_Instansi
- Tanggal_Mulai
- Tanggal_Selesai
- Dokumen_Proposal
- Foto_ttd
- Status (Draft, Diajukan-ke-koordinator, Ditolak, Success)
- Nama_Koordinator (FK -> dosen)
- Nama_Dekan (FK -> dosen)
- Nip_Dekan
- Qr_code (Kaprodi)
- Qr_code_dekan (Dekan)
- Acc_Koordinator (boolean)
- Acc_Dekan (boolean)
```

#### `surat_magang_invitations`

Sistem ajakan magang kolaboratif

```sql
- id (PK)
- id_surat_magang (FK)
- id_mahasiswa_pengundang (FK)
- id_mahasiswa_diundang (FK)
- status (pending, accepted, rejected)
- keterangan
- invited_at
- responded_at
```

#### `surat_aktif`

Detail surat keterangan mahasiswa aktif

```sql
- id_no (PK)
- Id_Tugas_Surat (FK)
- Nama_Mahasiswa
- NIM
- Program_Studi
- Semester
- Tahun_Akademik
- File_KRS
- Status (Pending, Disetujui, Ditolak)
```

## 👥 Pengguna & Role

### 1. Mahasiswa

**Hak Akses:**

-   Mengajukan surat baru
-   Upload dokumen pendukung (KRS, Proposal, TTD)
-   Mengundang teman untuk magang kolaboratif
-   Tracking status surat
-   Download surat yang sudah disetujui
-   Menerima/menolak ajakan magang

**Menu:**

-   Dashboard (statistik surat pribadi)
-   Pengajuan Surat Baru
-   Riwayat Surat Aktif
-   Riwayat Surat Magang
-   Ajakan Magang (invitation list)

### 2. Kaprodi (Koordinator KP/TA)

**Hak Akses:**

-   Melihat semua pengajuan surat dari mahasiswa prodinya
-   Approve/reject surat
-   Generate QR Code untuk approval
-   Memberikan catatan/komentar

**Menu:**

-   Dashboard (statistik surat prodi)
-   Daftar Surat Aktif
-   Daftar Surat Magang
-   Riwayat Approval

### 3. Dekan

**Hak Akses:**

-   Melihat semua surat yang sudah di-approve Kaprodi
-   Final approval dengan QR Code Dekan
-   Reject surat (dengan alasan)
-   Monitoring semua surat fakultas

**Menu:**

-   Dashboard (statistik fakultas)
-   Daftar Surat Menunggu Approval
-   Daftar Surat Magang
-   Riwayat Approval

### 4. Admin Fakultas

**Hak Akses:**

-   Full access monitoring
-   Manajemen data master (mahasiswa, dosen, prodi)
-   Export data & laporan
-   Konfigurasi sistem

**Menu:**

-   Dashboard (statistik lengkap)
-   Manajemen Surat
-   Manajemen User
-   Laporan

## 📊 Workflow Surat

### Surat Keterangan Mahasiswa Aktif

```
1. Mahasiswa → Upload KRS + Isi form (semester, tahun akademik, keperluan)
2. Sistem → Status: Pending
3. Kaprodi → Review → Approve/Reject
4. Mahasiswa → Download surat (jika approved)
```

### Surat Pengantar Magang/KP

```
1. Mahasiswa (Pembuat) → Isi form + Upload Proposal + TTD
   ├─ Opsional: Ajak teman (max 5 orang total)
   └─ Isi No WhatsApp tiap mahasiswa

2. Sistem → Status: Draft (jika ada ajakan) / Diajukan (jika solo)

3. Mahasiswa yang Diundang → Terima/Tolak ajakan
   └─ Jika semua terima → Status: Diajukan-ke-koordinator

4. Kaprodi → Review → Approve/Reject
   └─ Generate QR Code Kaprodi

5. Dekan → Final Approval
   ├─ Generate QR Code Dekan
   └─ Status: Success

6. Mahasiswa → Download Surat Pengantar (dengan 2 QR Code)
```

### Status Surat Magang

-   **Draft**: Ada ajakan yang belum direspons
-   **Diajukan-ke-koordinator**: Menunggu approval Kaprodi
-   **Ditolak**: Rejected oleh Kaprodi/Dekan
-   **Success**: Approved oleh Dekan, siap download

## 📖 Dokumentasi

### Struktur Folder

```
kp-fakultas/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/           # Admin controllers
│   │   │   ├── Dekan/           # Dekan controllers
│   │   │   ├── Kaprodi/         # Kaprodi controllers
│   │   │   ├── Mahasiswa/       # Mahasiswa controllers
│   │   │   └── PengajuanSurat/  # Pengajuan surat controllers
│   │   └── Middleware/
│   └── Models/                   # Eloquent models
├── database/
│   ├── migrations/              # Database migrations
│   └── seeders/                 # Database seeders
├── public/
│   ├── css/                     # Custom CSS
│   ├── js/                      # Custom JavaScript
│   │   └── pengajuan-surat.js  # Form handling & autocomplete
│   └── images/                  # Assets
├── resources/
│   └── views/
│       ├── admin/
│       ├── dekan/
│       ├── kaprodi/
│       ├── mahasiswa/
│       │   ├── pdf/            # PDF templates
│       │   │   ├── surat_pengantar.blade.php
│       │   │   └── surat_dengan_qr.blade.php
│       │   └── pengajuan_surat.blade.php
│       └── layouts/
└── routes/
    └── web.php                  # Route definitions
```

### API Endpoints

#### Mahasiswa Search (Autocomplete)

```
GET /mahasiswa/api/mahasiswa/search?q={query}
Response: [
  {
    "id": 1,
    "nama": "Adi Saputra",
    "nim": "1121001",
    "angkatan": "2022",
    "jurusan": "Teknik Informatika",
    "status_kp": "Belum_Mengambil"
  }
]
```

### JSON Format Data_Mahasiswa

```json
[
    {
        "nama": "Adi Saputra",
        "nim": "1121001",
        "program-studi": "Teknik Informatika",
        "jurusan": "Teknik Informatika",
        "angkatan": "2022",
        "no_wa": "081234567890"
    }
]
```

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter SuratMagangTest

# Run with coverage
php artisan test --coverage
```

## 🔧 Troubleshooting

### Error: "Class 'Endroid\QrCode\QrCode' not found"

```bash
composer require endroid/qr-code:^6.0
```

### Error: Upload file failed

-   Cek `php.ini`: `upload_max_filesize` dan `post_max_size`
-   Cek permission folder `storage/app/public`

### Error: QR Code tidak muncul di PDF

-   Pastikan `storage/app/public/qrcodes` exists
-   Jalankan: `php artisan storage:link`

### Error: Migration failed

```bash
# Reset database (WARNING: data akan hilang)
php artisan migrate:fresh

# Atau rollback step by step
php artisan migrate:rollback --step=1
```

## 🤝 Kontribusi

Kontribusi sangat diterima! Silakan ikuti langkah berikut:

1. Fork repository ini
2. Buat branch fitur baru (`git checkout -b feature/AmazingFeature`)
3. Commit perubahan (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

### Code Style

-   Follow PSR-12 PHP Standards
-   Use Laravel best practices
-   Write descriptive commit messages
-   Add comments for complex logic

## 📄 Lisensi

Distributed under the MIT License. See `LICENSE` for more information.

## 👨‍💻 Developer

Developed by **Tim Pengembang Fakultas Teknik UTM**

-   GitHub: [@haikal23095](https://github.com/haikal23095)
-   Email: support@ft.trunojoyo.ac.id

## 🙏 Acknowledgments

-   [Laravel Framework](https://laravel.com/)
-   [Endroid QR Code](https://github.com/endroid/qr-code)
-   [SB Admin 2 Bootstrap Template](https://startbootstrap.com/theme/sb-admin-2)
-   [Universitas Trunojoyo Madura](https://www.trunojoyo.ac.id/)

---

**Universitas Trunojoyo Madura - Fakultas Teknik**  
_Sistem Manajemen Surat Digital - 2025_
