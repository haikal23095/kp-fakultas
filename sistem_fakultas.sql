-- phpMyAdmin SQL Dump
-- version 4.4.15.10
-- https://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 07 Okt 2025 pada 04.32
-- Versi Server: 8.4.6
-- PHP Version: 8.2.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sistem_fakultas`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache`
--

CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache_locks`
--

CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `Dosen`
--

CREATE TABLE IF NOT EXISTS `Dosen` (
  `Id_Dosen` int NOT NULL,
  `NIP` varchar(255) DEFAULT NULL,
  `Nama_Dosen` varchar(255) DEFAULT NULL,
  `Alamat_Dosen` text,
  `Id_User` int DEFAULT NULL,
  `Id_Prodi` int DEFAULT NULL,
  `Id_Pejabat` int DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=93 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `Dosen`
--

INSERT INTO `Dosen` (`Id_Dosen`, `NIP`, `Nama_Dosen`, `Alamat_Dosen`, `Id_User`, `Id_Prodi`, `Id_Pejabat`) VALUES
(23, '198503152010121001', 'Dr. Budi Hartono, S.Kom., M.Kom.', 'Jl. Gebang Putih No. 10, Surabaya', 101, 1, 1),
(24, '198801202012112002', 'Rina Permatasari, S.T., M.Sc.', 'Jl. Keputih Tegal No. 5, Surabaya', 102, 1, 2),
(25, '197911102005011003', 'Prof. Dr. Ir. Iwan Santoso', 'Jl. Arief Rahman Hakim No. 100, Surabaya', 103, 1, 3),
(26, '199005052015032001', 'Fitriani, S.Kom., M.Cs.', 'Jl. Medokan Semampir Indah No. 23, Surabaya', 104, 1, NULL),
(27, '198208172008011005', 'Agus Wibowo, S.Kom., Ph.D.', 'Jl. Semolowaru Utara V No. 1, Surabaya', 105, 1, NULL),
(28, '198604222011021004', 'Teguh Prasetyo, S.T., M.T.', 'Perumahan ITS Blok C No. 12, Surabaya', 106, 1, NULL),
(29, '199109012016032003', 'Dewi Lestari, S.Kom., M.Kom.', 'Jl. Nginden Jangkungan III No. 30, Surabaya', 107, 1, NULL),
(30, '198007252006041002', 'Hendro Susilo, B.Eng., M.Eng.', 'Jl. Klampis Ngasem No. 8, Surabaya', 108, 1, NULL),
(31, '198902142014052002', 'Yulia Citra, S.Si., M.Sc.', 'Jl. Manyar Kertoarjo No. 77, Surabaya', 109, 1, NULL),
(32, '197806302003121001', 'Dr. Bambang Purnomo, M.T.', 'Jl. Menur Pumpungan No. 62, Surabaya', 110, 1, NULL),
(33, '198702112013011002', 'Ahmad Fauzi, S.Kom., M.M.S.I.', 'Jl. Baratajaya No. 15, Surabaya', 111, 2, 3),
(34, '199008212015032004', 'Siti Nurhaliza, S.SI., M.Kom.', 'Jl. Rungkut Madya No. 9, Surabaya', 112, 2, NULL),
(35, '198405192009101001', 'Donny Setiawan, M.Sc.', 'Apartemen Puncak Kertajaya, Surabaya', 113, 2, NULL),
(36, '198903122014022003', 'Linda Wati, S.Kom., M.T.', 'Jl. Panjang Jiwo Permai No. 4, Surabaya', 114, 2, NULL),
(37, '198112012007011004', 'Dr. Eko Nugroho, M.Kom.', 'Jl. Kutisari Indah Barat No. 11, Surabaya', 115, 2, NULL),
(38, '198510282010031003', 'Cahyo Purnomo, S.T., M.Sc.', 'Jl. Wonorejo Permai, Surabaya', 116, 2, NULL),
(39, '199201152017032005', 'Anisa Rahmawati, S.Kom., M.Kom.', 'Jl. Sidosermo PDK, Surabaya', 117, 2, NULL),
(40, '198309092008111002', 'Fajar Sidik, S.Kom., M.M.', 'Jl. Tenggilis Mejoyo, Surabaya', 118, 2, NULL),
(41, '198806182013052004', 'Maya Anggraini, S.Kom., M.Sc.', 'Jl. Kendangsari, Surabaya', 119, 2, NULL),
(42, '197904252005021002', 'Prof. Dr. Ir. Surya Dharma', 'Jl. Jemur Andayani No. 50, Surabaya', 120, 2, NULL),
(63, '198009172006041007', 'Dr. Ir. Heru Santoso, M.Eng.', 'Jl. Teknik Kimia, Kampus ITS, Surabaya', 141, 3, 2),
(64, '198501102010032009', 'Anita Sari, S.T., M.T.', 'Jl. Teknik Elektro, Kampus ITS, Surabaya', 142, 3, 3),
(65, '197803222003121006', 'Prof. Dr. Ir. Taufik, M.Sc., Ph.D.', 'Jl. Kemenangan No. 1, Surabaya', 143, 3, NULL),
(66, '198905152014021007', 'Rendra Setiawan, S.T., M.T.', 'Jl. Gebang Lor No. 70, Surabaya', 144, 3, NULL),
(67, '199106062016032009', 'Diana Anggraini, S.T., M.Eng.', 'Jl. Deles, Surabaya', 145, 3, NULL),
(68, '198210122008011009', 'Dr. Bayu Adhi, M.T.', 'Jl. Mulyorejo Tengah, Surabaya', 146, 3, NULL),
(69, '198711302013012007', 'Siska Amelia, S.T., M.T.', 'Jl. Kalijudan, Surabaya', 147, 3, NULL),
(70, '198402282009101005', 'Galih Prakoso, S.T., Ph.D.', 'Jl. Babatan Pantai, Surabaya', 148, 3, NULL),
(71, '197512252001121003', 'Prof. Dr. Ir. Rini Nur, M.Sc.', 'Jl. Laguna Indah, Surabaya', 149, 3, NULL),
(72, '198808202012111004', 'Faisal Rahman, S.T., M.T.', 'Jl. Kejawan Putih Tambak, Surabaya', 150, 3, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
--

CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint unsigned NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `Fakultas`
--

CREATE TABLE IF NOT EXISTS `Fakultas` (
  `Id_Fakultas` int NOT NULL,
  `Nama_Fakultas` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `Fakultas`
--

INSERT INTO `Fakultas` (`Id_Fakultas`, `Nama_Fakultas`) VALUES
(1, 'Fisib'),
(2, 'Teknik'),
(3, 'FEB'),
(4, 'FKIS'),
(5, 'FP'),
(6, 'FKIP');

-- --------------------------------------------------------

--
-- Struktur dari tabel `File_Arsip`
--

CREATE TABLE IF NOT EXISTS `File_Arsip` (
  `Id_File_Arsip` int NOT NULL,
  `Id_Tugas_Surat` int DEFAULT NULL,
  `Id_Pemberi_Tugas_Surat` int DEFAULT NULL,
  `Id_Penerima_Tugas_Surat` int DEFAULT NULL,
  `Keterangan` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `File_Arsip`
--

INSERT INTO `File_Arsip` (`Id_File_Arsip`, `Id_Tugas_Surat`, `Id_Pemberi_Tugas_Surat`, `Id_Penerima_Tugas_Surat`, `Keterangan`) VALUES
(1, 1, 101, 301, 'Surat Tugas asli telah diarsipkan di Lemari B, Rak 2. File digital tersimpan di server fakultas.'),
(2, 3, 201, 304, 'Surat Keterangan Aktif Kuliah telah diserahkan kepada mahasiswa yang bersangkutan. Salinan digital diarsipkan.'),
(3, 7, 121, 306, 'Surat undangan telah dikirim melalui email kepada dosen tamu. Arsip digital tersimpan.'),
(4, 10, 151, 302, 'Surat Keputusan Cuti telah diserahkan kepada dosen yang bersangkutan dan salinannya diarsipkan di bagian kepegawaian.');

-- --------------------------------------------------------

--
-- Struktur dari tabel `Jenis_Pekerjaan`
--

CREATE TABLE IF NOT EXISTS `Jenis_Pekerjaan` (
  `Id_Jenis_Pekerjaan` int NOT NULL,
  `Jenis_Pekerjaan` enum('Surat','Non-Surat') DEFAULT NULL,
  `Nama_Pekerjaan` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `Jenis_Pekerjaan`
--

INSERT INTO `Jenis_Pekerjaan` (`Id_Jenis_Pekerjaan`, `Jenis_Pekerjaan`, `Nama_Pekerjaan`) VALUES
(1, 'Surat', 'Membuat Surat Tugas'),
(2, 'Surat', 'Menerbitkan Surat Peringatan'),
(3, 'Surat', 'Membuat Surat Undangan Rapat'),
(4, 'Surat', 'Memproses Surat Permohonan Cuti'),
(5, 'Surat', 'Membuat Surat Keterangan Aktif Bekerja'),
(6, 'Surat', 'Menerbitkan Surat Keputusan (SK)'),
(7, 'Surat', 'Membuat Surat Rekomendasi'),
(8, 'Surat', 'Mengurus Surat Perintah Perjalanan Dinas (SPPD)'),
(9, 'Non-Surat', 'Mengarsipkan Dokumen Fisik'),
(10, 'Non-Surat', 'Melakukan Entri Data Nilai Mahasiswa'),
(11, 'Non-Surat', 'Menyiapkan Materi Ajar'),
(12, 'Non-Surat', 'Mengelola Inventaris Fakultas'),
(13, 'Non-Surat', 'Memperbarui Konten Website Fakultas'),
(14, 'Non-Surat', 'Menyusun Jadwal Rapat Internal'),
(15, 'Non-Surat', 'Memberikan Dukungan IT'),
(16, 'Non-Surat', 'Melakukan Rekonsiliasi Keuangan Harian');

-- --------------------------------------------------------

--
-- Struktur dari tabel `Jenis_Surat`
--

CREATE TABLE IF NOT EXISTS `Jenis_Surat` (
  `Id_Jenis_Surat` int NOT NULL,
  `Tipe_Surat` enum('Surat-Keluar','Surat-Masuk') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `Nama_Surat` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `Jenis_Surat`
--

INSERT INTO `Jenis_Surat` (`Id_Jenis_Surat`, `Tipe_Surat`, `Nama_Surat`) VALUES
(1, 'Surat-Keluar', 'Surat Keputusan Dekan'),
(2, 'Surat-Keluar', 'Surat Tugas Dosen'),
(3, 'Surat-Keluar', 'Surat Keterangan Aktif Kuliah'),
(4, 'Surat-Keluar', 'Surat Undangan Rapat'),
(5, 'Surat-Keluar', 'Surat Rekomendasi'),
(6, 'Surat-Keluar', 'Surat Pengantar Penelitian'),
(7, 'Surat-Keluar', 'Surat Perintah Perjalanan Dinas (SPPD)'),
(8, 'Surat-Keluar', 'Surat Peringatan'),
(9, 'Surat-Masuk', 'Surat Permohonan Izin Penelitian'),
(10, 'Surat-Masuk', 'Surat Edaran Rektorat'),
(11, 'Surat-Masuk', 'Surat Penawaran Kerjasama'),
(12, 'Surat-Masuk', 'Surat Lamaran Kerja');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jobs`
--

CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint unsigned NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `job_batches`
--

CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `Mahasiswa`
--

CREATE TABLE IF NOT EXISTS `Mahasiswa` (
  `Id_Mahasiswa` int NOT NULL,
  `NIM` int DEFAULT NULL,
  `Nama_Mahasiswa` varchar(255) DEFAULT NULL,
  `Jenis_Kelamin_Mahasiswa` enum('L','P') DEFAULT NULL,
  `Alamat_Mahasiswa` text,
  `Id_User` int DEFAULT NULL,
  `Id_Prodi` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `Mahasiswa`
--

INSERT INTO `Mahasiswa` (`Id_Mahasiswa`, `NIM`, `Nama_Mahasiswa`, `Jenis_Kelamin_Mahasiswa`, `Alamat_Mahasiswa`, `Id_User`, `Id_Prodi`) VALUES
(1, 1121001, 'Adi Saputra', 'L', 'Jl. Merdeka No. 1, Jakarta', 201, 1),
(2, 1121002, 'Bunga Citra Lestari', 'P', 'Jl. Sudirman No. 2, Bandung', 202, 1),
(3, 1121003, 'Candra Wijaya', 'L', 'Jl. Pahlawan No. 3, Surabaya', 203, 1),
(4, 1121004, 'Dewi Anggraini', 'P', 'Jl. Diponegoro No. 4, Yogyakarta', 204, 1),
(5, 1121005, 'Eko Prasetyo', 'L', 'Jl. Gajah Mada No. 5, Semarang', 205, 1),
(6, 1121006, 'Fitriana Rahmawati', 'P', 'Jl. Asia Afrika No. 6, Medan', 206, 1),
(7, 1121007, 'Guntur Hidayat', 'L', 'Jl. Imam Bonjol No. 7, Makassar', 207, 1),
(8, 1121008, 'Herlina Sari', 'P', 'Jl. Gatot Subroto No. 8, Palembang', 208, 1),
(9, 1121009, 'Irfan Maulana', 'L', 'Jl. Siliwangi No. 9, Denpasar', 209, 1),
(10, 1121010, 'Jasmine Putri', 'P', 'Jl. Veteran No. 10, Malang', 210, 1),
(11, 1122011, 'Kevin Sanjaya', 'L', 'Jl. Thamrin No. 11, Jakarta', 211, 2),
(12, 1122012, 'Lia Kartika', 'P', 'Jl. Braga No. 12, Bandung', 212, 2),
(13, 1122013, 'Muhammad Rizki', 'L', 'Jl. Tunjungan No. 13, Surabaya', 213, 2),
(14, 1122014, 'Nadia Paramita', 'P', 'Jl. Malioboro No. 14, Yogyakarta', 214, 2),
(15, 1122015, 'Oscar Maulana', 'L', 'Jl. Pemuda No. 15, Semarang', 215, 2),
(16, 1122016, 'Putri Ayu', 'P', 'Jl. Kesawan No. 16, Medan', 216, 2),
(17, 1122017, 'Qori Akbar', 'L', 'Jl. Losari No. 17, Makassar', 217, 2),
(18, 1122018, 'Rahmat Hidayat', 'L', 'Jl. Ampera No. 18, Palembang', 218, 2),
(19, 1122019, 'Siska Wulandari', 'P', 'Jl. Legian No. 19, Denpasar', 219, 2),
(20, 1122020, 'Taufik Ramadhan', 'L', 'Jl. Ijen No. 20, Malang', 220, 2),
(41, 1125041, 'Olivia Jensen', 'P', 'Jl. Dago No. 41, Bandung', 241, 3),
(42, 1125042, 'Pandu Wijaya', 'L', 'Jl. Ngagel No. 42, Surabaya', 242, 3),
(43, 1125043, 'Ratih Kumala', 'P', 'Jl. Monjali No. 43, Yogyakarta', 243, 3),
(44, 1125044, 'Sakti Mahendra', 'L', 'Jl. Gajah Mungkur No. 44, Semarang', 244, 3),
(45, 1125045, 'Tiara Ananda', 'P', 'Jl. Gatot Subroto No. 45, Medan', 245, 3),
(46, 1125046, 'Umar Abdullah', 'L', 'Jl. Sultan Hasanuddin No. 46, Makassar', 246, 3),
(47, 1125047, 'Vania Mariska', 'P', 'Jl. R. Sukamto No. 47, Palembang', 247, 3),
(48, 1125048, 'Wahyu Setiawan', 'L', 'Jl. Teuku Umar No. 48, Denpasar', 248, 3),
(49, 1125049, 'Yasmine Wildblood', 'P', 'Jl. Kawi No. 49, Malang', 249, 3),
(50, 1125050, 'Zacky Ramadhan', 'L', 'Jl. Panglima Polim No. 50, Jakarta', 250, 3);

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(4, '0001_01_01_000001_create_cache_table', 2),
(5, '0001_01_01_000002_create_jobs_table', 2);

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_reset_tokens`
--

CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `Pegawai`
--

CREATE TABLE IF NOT EXISTS `Pegawai` (
  `Id_Pegawai` int NOT NULL,
  `NIP` varchar(255) DEFAULT NULL,
  `Nama_Pegawai` varchar(255) DEFAULT NULL,
  `Jenis_Kelamin_Pegawai` enum('L','P') DEFAULT NULL,
  `Alamat_Pegawai` text,
  `Id_User` int DEFAULT NULL,
  `Id_Prodi` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `Pegawai`
--

INSERT INTO `Pegawai` (`Id_Pegawai`, `NIP`, `Nama_Pegawai`, `Jenis_Kelamin_Pegawai`, `Alamat_Pegawai`, `Id_User`, `Id_Prodi`) VALUES
(1, '199501102020122001', 'Sri Wahyuni', 'P', 'Jl. Administrasi Fakultas No. 1, Surabaya', 301, 1),
(2, '199203152018031002', 'Joko Susanto', 'L', 'Jl. Tata Usaha Gedung A, Surabaya', 302, 2),
(5, '199708172021032005', 'Siti Aminah', 'P', 'Jl. Keuangan Kampus, Surabaya', 305, 3);

-- --------------------------------------------------------

--
-- Struktur dari tabel `Pejabat`
--

CREATE TABLE IF NOT EXISTS `Pejabat` (
  `Id_Pejabat` int NOT NULL,
  `Nama_Jabatan` enum('Kaprodi','Kajur','Dekan') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `Pejabat`
--

INSERT INTO `Pejabat` (`Id_Pejabat`, `Nama_Jabatan`) VALUES
(1, 'Dekan'),
(2, 'Kajur'),
(3, 'Kaprodi');

-- --------------------------------------------------------

--
-- Struktur dari tabel `Penilaian_Kinerja`
--

CREATE TABLE IF NOT EXISTS `Penilaian_Kinerja` (
  `Id_Penilaian` int NOT NULL,
  `Id_Pegawai` int DEFAULT NULL,
  `Id_Penilai` int DEFAULT NULL,
  `Skor` enum('1','2','3','4','5') DEFAULT NULL,
  `Komentar` text,
  `Tanggal_Penilaian` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `Penilaian_Kinerja`
--

INSERT INTO `Penilaian_Kinerja` (`Id_Penilaian`, `Id_Pegawai`, `Id_Penilai`, `Skor`, `Komentar`, `Tanggal_Penilaian`) VALUES
(1, 301, 101, '5', 'Kinerja sangat baik selama periode ini. Sangat proaktif dalam menyelesaikan tugas administrasi prodi dan selalu teliti.', '2025-09-10'),
(2, 302, 103, '4', 'Secara umum kinerjanya baik dan responsif. Perlu peningkatan dalam hal inisiatif untuk tugas-tugas baru di luar rutinitas.', '2025-09-10'),
(3, 303, 121, '4', 'Pekerjaan selalu selesai tepat waktu. Kemampuan komunikasi dengan mahasiswa dan dosen sangat baik dan membantu.', '2025-09-11'),
(4, 304, 151, '5', 'Sangat responsif dan solutif dalam menangani masalah teknis di prodi kami, terutama dalam pengelolaan website. Sangat membantu sekali.', '2025-09-11'),
(5, 306, 103, '3', 'Pekerjaan rutin terselesaikan dengan baik, namun perlu meningkatkan ketelitian dalam penyusunan laporan dan pengarsipan dokumen penting.', '2025-09-12');

-- --------------------------------------------------------

--
-- Struktur dari tabel `Prodi`
--

CREATE TABLE IF NOT EXISTS `Prodi` (
  `Id_Prodi` int NOT NULL,
  `Nama_Prodi` varchar(255) DEFAULT NULL,
  `Id_Fakultas` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `Prodi`
--

INSERT INTO `Prodi` (`Id_Prodi`, `Nama_Prodi`, `Id_Fakultas`) VALUES
(1, 'Teknik Informatika', 2),
(2, 'Sistem Informasi', 2),
(3, 'Teknik Elektro', 2),
(4, 'Teknik Mekatronika', 2),
(5, 'Teknik Mesin', 2),
(6, 'Teknik Industri', 2);

-- --------------------------------------------------------

--
-- Struktur dari tabel `Roles`
--

CREATE TABLE IF NOT EXISTS `Roles` (
  `Id_Role` int NOT NULL,
  `Name_Role` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `Roles`
--

INSERT INTO `Roles` (`Id_Role`, `Name_Role`) VALUES
(1, 'Pegawai'),
(2, 'Dekan'),
(3, 'Kajur'),
(4, 'Kaprodi'),
(5, 'Dosen'),
(6, 'Mahasiswa');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sessions`
--

CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('aq12REYNH4cAfj9MdRGRflS6Umso5P224Q6sWiL1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSE1VZlBwTG5LWlZ6UzNYMTRPZk5ybmJGQlpxM0FXeXhHNkc1Mk1aTiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9sb2dpbiI7fX0=', 1759483973),
('gIfk00GMzUWPgBlvb7VJI8oqNNYHzYWB12Q3dFT7', 141, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZUxYNzUwQzFseFhUYllaaXBLM1Z1YllVa0JpZWlBMWxQRnNCemxCTSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kYXNoYm9hcmQva2FqdXIiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxNDE7fQ==', 1759738166),
('QZpYjHZu09LmFV1FDCXQeZVQA13Nvc3qx08Cg3gk', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWHhiR1AxajB6UlNpM2YwMUZ2WnNHbDhseWFYdlBFNE1wYThxRFBhYiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1759474203),
('rnBqZ3cKL6nS52SwG2EzPg0G9kfcy6jWLZegFNdr', 142, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiMTJBak55UFRnVnV1b0lxTVpHVzJWY0NsU0FNdTRjRzRPQVJ4SVZoMSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzk6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kYXNoYm9hcmQva2Fwcm9kaSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6MzoidXJsIjthOjE6e3M6ODoiaW50ZW5kZWQiO3M6Mzc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kYXNoYm9hcmQva2FqdXIiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxNDI7fQ==', 1759738210),
('XSB6a9shhKN4HtWCzZUfPONQkrFa3MYStCiXhSdK', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQjdxY0dVSUFPWFZLM1RPNnc3Q3ViWlJ1ajZQaFN3U0pjcWMzUmNmQiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1759045461);

-- --------------------------------------------------------

--
-- Struktur dari tabel `Tugas`
--

CREATE TABLE IF NOT EXISTS `Tugas` (
  `Id_Tugas` int DEFAULT NULL,
  `Id_Pemberi_Tugas` int DEFAULT NULL,
  `Id_Penerima_Tugas` int DEFAULT NULL,
  `Id_Jenis_Pekerjaan` int DEFAULT NULL,
  `Judul_Tugas` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `Deskripsi_Tugas` text,
  `Tanggal_Diberikan_Tugas` date DEFAULT NULL,
  `Tanggal_Tenggat_Tugas` date DEFAULT NULL,
  `Status` enum('Dikerjakan','Selesai','Terlambat') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `Tanggal_Diselesaikan` date DEFAULT NULL,
  `File_Laporan` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `Tugas`
--

INSERT INTO `Tugas` (`Id_Tugas`, `Id_Pemberi_Tugas`, `Id_Penerima_Tugas`, `Id_Jenis_Pekerjaan`, `Judul_Tugas`, `Deskripsi_Tugas`, `Tanggal_Diberikan_Tugas`, `Tanggal_Tenggat_Tugas`, `Status`, `Tanggal_Diselesaikan`, `File_Laporan`) VALUES
(1, 101, 301, 10, 'Entri Nilai Mata Kuliah Dasar Pemrograman', 'Mohon untuk segera melakukan entri nilai akhir untuk mata kuliah Dasar Pemrograman kelas A dan B ke dalam sistem akademik.', '2025-09-12', '2025-09-19', 'Dikerjakan', NULL, NULL),
(2, 103, 302, 12, 'Update Data Inventaris Laboratorium', 'Lakukan pengecekan dan update data inventaris untuk seluruh perangkat komputer di Laboratorium 1 dan 2. Laporkan jika ada kerusakan.', '2025-09-08', '2025-09-15', 'Selesai', '2025-09-12', '/laporan/inventaris/2025/Laporan_Inventaris_Lab_Sept2025.xlsx'),
(3, 151, 304, 13, 'Posting Berita Prestasi Mahasiswa', 'Tolong unggah berita mengenai kemenangan tim mahasiswa DKV dalam kompetisi desain tingkat nasional. Materi berita dan foto sudah dikirim via email.', '2025-09-11', '2025-09-12', 'Selesai', '2025-09-11', NULL),
(4, 121, 305, 14, 'Penjadwalan Rapat Koordinasi Dosen', 'Mohon atur jadwal dan kirim undangan untuk rapat koordinasi seluruh dosen prodi Manajemen. Target pelaksanaan minggu depan.', '2025-09-12', '2025-09-16', 'Dikerjakan', NULL, NULL),
(5, 141, 307, 9, 'Pengarsipan Berkas Akademik Mahasiswa', 'Lakukan pengarsipan berkas fisik (KRS, KHS, dll.) untuk mahasiswa angkatan 2023. Pastikan semua tersimpan di lemari arsip yang sesuai.', '2025-09-10', '2025-09-24', 'Dikerjakan', NULL, NULL),
(6, 118, 304, 15, 'Perbaikan Proyektor di Ruang Kelas', 'Proyektor di ruang R-301 tidak dapat terhubung ke laptop. Mohon segera diperiksa sebelum perkuliahan jam 10 pagi ini.', '2025-09-12', '2025-09-12', 'Selesai', '2025-09-12', NULL),
(7, 131, 301, 16, 'Rekonsiliasi Keuangan Bulan Agustus', 'Lakukan rekonsiliasi pengeluaran dan pemasukan dana prodi Akuntansi untuk bulan Agustus 2025. Laporan ditunggu paling lambat akhir minggu ini.', '2025-09-12', '2025-09-15', 'Dikerjakan', NULL, NULL),
(8, 103, 306, 11, 'Persiapan Materi untuk Akreditasi', 'Tolong siapkan dan cetak semua borang dan dokumen pendukung untuk visitasi akreditasi. Pastikan semua sudah dijilid rapi.', '2025-09-12', '2025-09-22', 'Dikerjakan', NULL, NULL),
(9, 114, 307, 9, 'Arsipkan Soal Ujian Semester Lalu', 'Arsipkan semua berkas soal dan jawaban UAS semester Ganjil 2024/2025 ke dalam lemari arsip sesuai kode mata kuliah.', '2025-09-05', '2025-09-12', 'Selesai', '2025-09-11', '/laporan/arsip/2025/Checklist_Arsip_UAS_Ganjil_2024.txt'),
(10, 111, 304, 13, 'Update Informasi Kurikulum di Website', 'Mohon perbarui halaman kurikulum di website prodi Sistem Informasi dengan struktur mata kuliah terbaru yang telah disetujui.', '2025-09-12', '2025-09-15', 'Dikerjakan', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `Tugas_Surat`
--

CREATE TABLE IF NOT EXISTS `Tugas_Surat` (
  `Id_Tugas_Surat` int NOT NULL,
  `Id_Pemberi_Tugas_Surat` int DEFAULT NULL,
  `Id_Penerima_Tugas_Surat` int DEFAULT NULL,
  `Id_Jenis_Surat` int DEFAULT NULL,
  `Id_Jenis_Pekerjaan` int DEFAULT NULL,
  `Judul_Tugas_Surat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `Nomor_Surat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `Deskripsi_Tugas_Surat` text,
  `Tanggal_Diberikan_Tugas_Surat` date DEFAULT NULL,
  `Tanggal_Tenggat_Tugas_Surat` date DEFAULT NULL,
  `Status` enum('Dikerjakan','Selesai','Terlambat') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `File_Surat` text,
  `Tanggal_Diselesaikan` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `Tugas_Surat`
--

INSERT INTO `Tugas_Surat` (`Id_Tugas_Surat`, `Id_Pemberi_Tugas_Surat`, `Id_Penerima_Tugas_Surat`, `Id_Jenis_Surat`, `Id_Jenis_Pekerjaan`, `Judul_Tugas_Surat`, `Nomor_Surat`, `Deskripsi_Tugas_Surat`, `Tanggal_Diberikan_Tugas_Surat`, `Tanggal_Tenggat_Tugas_Surat`, `Status`, `File_Surat`, `Tanggal_Diselesaikan`) VALUES
(1, 101, 301, 2, 1, 'Pembuatan Surat Tugas Panitia Seminar', 'ST/001/FTI/IX/2025', 'Tolong buatkan draf Surat Tugas untuk panitia Seminar Nasional Teknologi 2025. Daftar nama terlampir di email.', '2025-09-12', '2025-09-16', 'Selesai', '/arsip/surat_tugas/2025/ST_Panitia_Seminar_001.pdf', '2025-09-15'),
(2, 103, 302, 1, 6, 'Penerbitan SK Ketua Laboratorium', 'SK/025/DEKAN-FTI/IX/2025', 'Mohon dipersiapkan Surat Keputusan (SK) untuk pengangkatan Ketua Laboratorium Jaringan Komputer periode 2025-2029.', '2025-09-15', '2025-09-19', 'Dikerjakan', NULL, NULL),
(3, 201, 304, 3, 5, 'Permohonan Surat Keterangan Aktif Kuliah', 'SKAK/150/KEMAHASISWAAN/IX/2025', 'Mahasiswa atas nama Adi Saputra (NIM: 1121001) mengajukan permohonan Surat Keterangan Aktif Kuliah untuk keperluan beasiswa.', '2025-09-10', '2025-09-12', 'Selesai', '/arsip/surat_keterangan/2025/SKAK_AdiSaputra_1121001.pdf', '2025-09-11'),
(4, 141, 306, 7, 8, 'Pengurusan SPPD untuk Konferensi Internasional', 'SPPD/042/TE/IX/2025', 'Tolong proses administrasi SPPD untuk Sdr. Rendra Setiawan, S.T., M.T. untuk menghadiri konferensi ICITEE di Kuala Lumpur.', '2025-09-11', '2025-09-20', 'Dikerjakan', NULL, NULL),
(5, 111, 305, 5, 7, 'Pembuatan Surat Rekomendasi Beasiswa LPDP', 'SR/088/SI/IX/2025', 'Draf surat rekomendasi untuk mahasiswa Siska Wulandari (NIM: 1122019) untuk pendaftaran beasiswa LPDP. Poin-poin penting sudah dikirim via email.', '2025-09-12', '2025-09-17', 'Dikerjakan', NULL, NULL),
(6, 113, 307, 8, 2, 'Penerbitan Surat Peringatan Akademik', 'SP/015/AKD/IX/2025', 'Mohon dibuatkan Surat Peringatan (SP-1) untuk mahasiswa atas nama Kevin Sanjaya (NIM: 1122011) karena jumlah absensi melebihi batas.', '2025-09-12', '2025-09-15', 'Dikerjakan', NULL, NULL),
(7, 121, 306, 4, 3, 'Surat Undangan Dosen Tamu', 'UND/055/MANAJ/IX/2025', 'Tolong siapkan surat undangan resmi untuk Bapak Dr. Antonius sebagai dosen tamu dalam mata kuliah Manajemen Strategis.', '2025-09-10', '2025-09-11', 'Selesai', '/arsip/undangan/2025/UND_DosenTamu_Antonius.pdf', '2025-09-11'),
(8, 251, 301, 6, 7, 'Permohonan Surat Pengantar Magang', 'SP/210/KEMAHASISWAAN/IX/2025', 'Mahasiswa Amanda Manopo (NIM: 1126051) dari prodi DKV memohon dibuatkan surat pengantar untuk keperluan magang di PT. Kreatif Media.', '2025-09-12', '2025-09-17', 'Dikerjakan', NULL, NULL),
(9, 103, 303, 11, 1, 'Penyusunan Surat Balasan Kerjasama', 'SB/010/DEKAN-FTI/IX/2025', 'Mohon siapkan draf surat balasan untuk tawaran kerjasama dari Universitas ABC. Pada prinsipnya kita menerima tawaran tersebut.', '2025-09-12', '2025-09-18', 'Dikerjakan', NULL, NULL),
(10, 151, 302, 1, 4, 'Proses Pengajuan Surat Cuti Dosen', 'SC/005/DKV/IX/2025', 'Tolong proses pengajuan cuti melahirkan dari dosen Clara Bella, S.Sn., M.Ds. sesuai dengan prosedur yang berlaku.', '2025-09-11', '2025-09-16', 'Selesai', '/arsip/cuti/2025/SC_ClaraBella.pdf', '2025-09-12');

-- --------------------------------------------------------

--
-- Struktur dari tabel `Users`
--

CREATE TABLE IF NOT EXISTS `Users` (
  `Id_User` int NOT NULL,
  `Username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `Name_User` varchar(255) DEFAULT NULL,
  `Id_Role` int DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `Users`
--

INSERT INTO `Users` (`Id_User`, `Username`, `password`, `Name_User`, `Id_Role`, `email`) VALUES
(101, 'budi_hartono', '$2y$12$7uQ8p9ZEjz7zKgA28jsuu.LUv8U9wdU9Rr17dHrh1mSmQD8Jaa3Jq', 'Dr. Budi Hartono, S.Kom., M.Kom.', 2, 'budi.hartono@fakultas.ac.id'),
(102, 'rina_permatasari', '$2y$12$VM0tE3mRNmFmjf7V69WiQ.5OvUuGrwfTi3EMnVac6dYAJRpiOduQi', 'Rina Permatasari, S.T., M.Sc.', 3, 'rina.permatasari@fakultas.ac.id'),
(103, 'iwan_santoso', '$2y$12$GW53Yc6WTZYLL1HJooRZ1ueP2Q3QTyi0rqrWaxfjgpzPGnI1IfBdm', 'Prof. Dr. Ir. Iwan Santoso', 4, 'iwan.santoso@fakultas.ac.id'),
(104, 'fitriani_kom', '$2y$12$R3H8ACGH0dbS0x1s/M4JaOLfi7oLMCTnqBkyjl7NidzUtYC20Ki0i', 'Fitriani, S.Kom., M.Cs.', 5, 'fitriani.kom@fakultas.ac.id'),
(105, 'agus_wibowo', '$2y$12$.WLgOu/uHaJsu1yd/jd9KubFVf/3.0OJVrGGrk2V3.ai1GGt9j/6a', 'Agus Wibowo, S.Kom., Ph.D.', 5, 'agus.wibowo@fakultas.ac.id'),
(106, 'teguh_prasetyo', '$2y$12$PhGbehDzq1EMoJbYg6jROOgn6K9TPSR1H03wHKZFIr5T0CorHjZI2', 'Teguh Prasetyo, S.T., M.T.', 5, 'teguh.prasetyo@fakultas.ac.id'),
(107, 'dewi_lestari', '$2y$12$vEPsVB6pAbU.gkWbvA3JAOHOcqyXvd3eyMZPnlfHe1CPvhrO1Psi.', 'Dewi Lestari, S.Kom., M.Kom.', 5, 'dewi.lestari@fakultas.ac.id'),
(108, 'hendro_susilo', '$2y$12$OLh9LsVt3NyohtGoUfgtQeFd3sXn0fMgfOHsh1F97KVVR59D8Qe3e', 'Hendro Susilo, B.Eng., M.Eng.', 5, 'hendro.susilo@fakultas.ac.id'),
(109, 'yulia_citra', '$2y$12$Cd4SrcX0ulpEHo5kCMXerO9z6kQR1QO8Fd0JMraOP2NGadr6JdKPm', 'Yulia Citra, S.Si., M.Sc.', 5, 'yulia.citra@fakultas.ac.id'),
(110, 'bambang_purnomo', '$2y$12$Q65uskxOMlvJ8o2qoflprus8eJowZi4awB/qyuO4pgwfOu2GFPkEe', 'Dr. Bambang Purnomo, M.T.', 5, 'bambang.purnomo@fakultas.ac.id'),
(111, 'ahmad_fauzi', '$2y$12$h2zBi678ql0ySUfZueFTxe017ZMUAERHbSASG8VHxD3fWTqz501LC', 'Ahmad Fauzi, S.Kom., M.M.S.I.', 4, 'ahmad.fauzi@fakultas.ac.id'),
(112, 'siti_nurhaliza', '$2y$12$jmTPhrRpEEw2VTyAf8EOvOJ4PI4hJ9fxj8ufH7ucP9L4YgF1qDRvm', 'Siti Nurhaliza, S.SI., M.Kom.', 5, 'siti.nurhaliza@fakultas.ac.id'),
(113, 'donny_setiawan', '$2y$12$rM.7YeGYSg9sSvKh/WCXHeGPbkI9gvTst7LAIT7z5v7DPoN6MwEIi', 'Donny Setiawan, M.Sc.', 5, 'donny.setiawan@fakultas.ac.id'),
(114, 'linda_wati', '$2y$12$FjzkL9EBdYCh47vBIqLa3ulqLbnG/wGFWjDoplhi.TbtmYN7jzpZu', 'Linda Wati, S.Kom., M.T.', 5, 'linda.wati@fakultas.ac.id'),
(115, 'eko_nugroho', '$2y$12$DVG6zNwbByqc7f4ncnrvWOrrZn330wTNPR0fpIW6jEeB49f7ZfKva', 'Dr. Eko Nugroho, M.Kom.', 5, 'eko.nugroho@fakultas.ac.id'),
(116, 'cahyo_purnomo', '$2y$12$UP18Yllz2rO89l9QeLd2YuephwLNLzTzvyR3DERhwxEc22cmaXyIm', 'Cahyo Purnomo, S.T., M.Sc.', 5, 'cahyo.purnomo@fakultas.ac.id'),
(117, 'anisa_rahmawati', '$2y$12$x6XaiTMfqKAQoKg5NQna1.tgxRxyT3WI21HUWLkbkxhWaleI8kIA.', 'Anisa Rahmawati, S.Kom., M.Kom.', 5, 'anisa.rahmawati@fakultas.ac.id'),
(118, 'fajar_sidik', '$2y$12$sUtVgE00QZBhzOY3iE3DzeW2xubQBqMMNRa80HkdupUYnIMiSuR8G', 'Fajar Sidik, S.Kom., M.M.', 5, 'fajar.sidik@fakultas.ac.id'),
(119, 'maya_anggraini', '$2y$12$8L/6bA56RCYBbeVFxbl2dOjINDUeejgkzXjY93vpVVMLoEAyWt.ZS', 'Maya Anggraini, S.Kom., M.Sc.', 5, 'maya.anggraini@fakultas.ac.id'),
(120, 'surya_dharma', '$2y$12$94L5er5IfeXxe0.EwKhpSOTPwHT6aybASYXxYc.aeZtiZLDzIg7ja', 'Prof. Dr. Ir. Surya Dharma', 5, 'surya.dharma@fakultas.ac.id'),
(121, 'hendra_wijaya', '$2y$12$VnxlQI7PqbReF5Vv6SeIo.R3zH36vI7aeukJ6OF/AUcmKNGFsAeZm', 'Dr. Hendra Wijaya, S.E., M.M.', 5, 'hendra.wijaya@fakultas.ac.id'),
(122, 'amelia_santoso', '$2y$12$oGHklWqJW/.tm1HTvEG1Luu5zbrAgMWtwa5Tub4EkxvNXwpFufoNa', 'Dr. Amelia Santoso, S.E., M.SM.', 5, 'amelia.santoso@fakultas.ac.id'),
(123, 'sutrisno_mm', '$2y$12$6sHh3NmIIMcXjYCD.nqq3uVPiHSJKqRpFpcQzUbB5yvgm4YGB.IHq', 'Prof. Dr. Sutrisno, M.M.', 5, 'sutrisno.mm@fakultas.ac.id'),
(124, 'diana_puspita', '$2y$12$r8YEBhi2QTo7A6.kUNh2Su0YIsZ0vD6xm4E22/5URM6tfFcnAF7He', 'Diana Puspita, S.E., M.B.A.', 5, 'diana.puspita@fakultas.ac.id'),
(125, 'rizky_maulana', '$2y$12$HShSpY2EQj44ktb4fEf3rO9zAQRXioDC5xqJKep9JUoCIVjOFXl7S', 'Rizky Maulana, S.M., M.M.', 5, 'rizky.maulana@fakultas.ac.id'),
(126, 'putri_wulandari', '$2y$12$KV30oCHnn9yV7O5M0ETP7.g.Oo..3tOAj0YwZNyuqt7bkgpLVHdkm', 'Putri Wulandari, S.E., M.M.', 5, 'putri.wulandari@fakultas.ac.id'),
(127, 'baskoro_adiwijaya', '$2y$12$OVgJ.hTAGaliEifdwdVJyeU6hxYiz30Y1RCORINtUd31Bed9bwTKS', 'Baskoro Adiwijaya, S.E., M.M.', 5, 'baskoro.adiwijaya@fakultas.ac.id'),
(128, 'antonius_mm', '$2y$12$wXm7Dk2tRbRvycMbKwYZVeSFubPye3BWdeqFyehpGdHWORZQ1timi', 'Dr. Antonius, M.M.', 5, 'antonius.mm@fakultas.ac.id'),
(129, 'jessica_tanoe', '$2y$12$PE4WmAiw57j.cuf44zbml.daKIeCtfxI3zBloNxC5nJkrSJVeuc.S', 'Jessica Tanoe, S.E., M.Sc.', 5, 'jessica.tanoe@fakultas.ac.id'),
(130, 'hermanto_mba', '$2y$12$5PxVcSeetk7HtWpkgeVOg.q/1TAJVwNFlsavNJ67IO9rnhrBvAes2', 'Prof. Dr. Hermanto, M.B.A.', 5, 'hermanto.mba@fakultas.ac.id'),
(131, 'liana_susanti', '$2y$12$WTf5xrvsci7/4EDlRhopgueKTwwEey07ASOuqACDKq3f1EmDASJRu', 'Dr. Liana Susanti, S.E., M.Ak., Ak.', 5, 'liana.susanti@fakultas.ac.id'),
(132, 'dedi_setiadi', '$2y$12$0KCTCTn7NHqm2tiT3IPIfuJHyHtby1Z24fw7GpOqQPU2fCkkkQXdq', 'Dedi Setiadi, S.Ak., M.Ak.', 5, 'dedi.setiadi@fakultas.ac.id'),
(133, 'kartika_dewi', '$2y$12$0tjOyCFJvGfiJno3kpdfme6eoeIxbCiIEy9o11a8M0NbjxtkU1bVq', 'Dr. Kartika Dewi, S.E., M.Si., Ak., CA.', 5, 'kartika.dewi@fakultas.ac.id'),
(134, 'andi_prasetyo', '$2y$12$3Og5fXUSlrBRj8q/JNV5vOYspjrpsLlMPlC/pwZJ2gFMj1.2kzy.K', 'Andi Prasetyo, S.Ak., M.Ak.', 5, 'andi.prasetyo@fakultas.ac.id'),
(135, 'evelyn_wijaya', '$2y$12$oVJ7SrD9tBPX2xkyPUmPsencyky2JiUsDynpCtYZEFdblzDk6TkZG', 'Evelyn Wijaya, S.E., M.Ak.', 5, 'evelyn.wijaya@fakultas.ac.id'),
(136, 'yohanes_hartanto', '$2y$12$EdAoeuECp7CsN4jIUzMCxuhKV154SGrZkfcsLVRa9wOy4zQUM6PDe', 'Yohanes Hartanto, S.Ak., M.Ak.', 5, 'yohanes.hartanto@fakultas.ac.id'),
(137, 'cindy_aurelia', '$2y$12$Pyjn0fTIxp9n2UHxwBhD/.c1TNk5Imc0JaF6za1jXAVrgIgdncFyy', 'Cindy Aurelia, S.E., M.Ak.', 5, 'cindy.aurelia@fakultas.ac.id'),
(138, 'edi_sukamto', '$2y$12$mUpLv0qYZvLPNDfO3H0tlO8puM3T4lhfcN3jdKfkwDNS9WYFNanie', 'Prof. Dr. Edi Sukamto, M.Si., Ak.', 5, 'edi.sukamto@fakultas.ac.id'),
(139, 'rahman_hakim', '$2y$12$jqbCtb8/5hb38FUmT9M/weLf40CrRcs1oFn4jlqGlDN5VFU/8GMCi', 'Rahman Hakim, S.Ak., M.Ak.', 5, 'rahman.hakim@fakultas.ac.id'),
(140, 'putri_amelia', '$2y$12$.u0Y4JqbpRUZz9pk1zO6KOunBbBMLNZRz5X2E.HOZE7sXUY5KC4u6', 'Putri Amelia, S.E., M.Ak.', 5, 'putri.amelia@fakultas.ac.id'),
(141, 'heru_santoso', '$2y$12$kjjSucdTy0AWbAest6iPp.76czaQZRn/6XP2.6yTyY9O/I0tnvsNO', 'Dr. Ir. Heru Santoso, M.Eng.', 3, 'heru.santoso@fakultas.ac.id'),
(142, 'anita_sari', '$2y$12$NEwRPLNofB4UVd9xM.ojfukNxoXH.HwbjjSFHZTMSJwsI67Gkf/.O', 'Anita Sari, S.T., M.T.', 4, 'anita.sari@fakultas.ac.id'),
(143, 'taufik_phd', '$2y$12$D7OiyZECIOQZRZJ46P3l..sUeAQbT0hlZAv8rBe1jGl8UKZ3Ikblq', 'Prof. Dr. Ir. Taufik, M.Sc., Ph.D.', 5, 'taufik.phd@fakultas.ac.id'),
(144, 'rendra_setiawan', '$2y$12$OW12Qg9wjgQu5CX4RBT4z.oLep.UCRGVuaqteYJ/RE5AKUsIyGBe2', 'Rendra Setiawan, S.T., M.T.', 5, 'rendra.setiawan@fakultas.ac.id'),
(145, 'diana_anggraini', '$2y$12$cf73P7CLsmsoV3Ubyu42ou0EB6cftciFNsBOh7TD3N4vE9UtY3FDG', 'Diana Anggraini, S.T., M.Eng.', 5, 'diana.anggraini@fakultas.ac.id'),
(146, 'bayu_adhi', '$2y$12$IkN3FZaiRUnJdM/uI.oroOEg80XomgIQyIJueHkPrU5Lm4nT4ln3y', 'Dr. Bayu Adhi, M.T.', 5, 'bayu.adhi@fakultas.ac.id'),
(147, 'siska_amelia', '$2y$12$jtfhuoDxE/Mk2nyqCpArEuksxz9eHNTnyc6QtGiRk8wP23FRFEAg6', 'Siska Amelia, S.T., M.T.', 5, 'siska.amelia@fakultas.ac.id'),
(148, 'galih_prakoso', '$2y$12$maEKyNsludan2jvJUIrn4.RLACrXumjbHtE106XqYUExhPojxb1Ai', 'Galih Prakoso, S.T., Ph.D.', 5, 'galih.prakoso@fakultas.ac.id'),
(149, 'rini_nur', '$2y$12$V8Wrt2NDrxRCbb3aPx/3i.fG7/WRy518nXk/eySqh5RTd/35Fi8O.', 'Prof. Dr. Ir. Rini Nur, M.Sc.', 5, 'rini.nur@fakultas.ac.id'),
(150, 'faisal_rahman', '$2y$12$yqWT7Tly9.GPL12EikUIjujT.4Gn1wX.icPDGATo3D7g3L71gq8da', 'Faisal Rahman, S.T., M.T.', 5, 'faisal.rahman@fakultas.ac.id'),
(151, 'adi_nugroho', '$2y$12$uuqTnfZLKMRjOWFmJHJb8OO5Qnkkgs2z0V8BfQ0J6bkcMofDHR02S', 'Adi Nugroho, S.Ds., M.Sn.', 5, 'adi.nugroho@fakultas.ac.id'),
(152, 'clara_bella', '$2y$12$C8vZSNFYuu6XXfwUsTNkLef2T7T.bVQPMgfwZqTtcgmJ9Uil/G6iW', 'Clara Bella, S.Sn., M.Ds.', 5, 'clara.bella@fakultas.ac.id'),
(153, 'deni_kreatif', '$2y$12$4peR1aFOHb/88vNsUPY9J.o4j74tuJHYTZA8e2hcfbxzxR9KvBvLe', 'Dr. Deni Kreatif, M.Sn.', 5, 'deni.kreatif@fakultas.ac.id'),
(154, 'vina_alvionita', '$2y$12$b6vOc58XfT5Leiq1dGKJnO7ufz7uNeJpEpSXL3felP/YPz.eIyiqq', 'Vina Alvionita, S.Ds., M.A.', 5, 'vina.alvionita@fakultas.ac.id'),
(155, 'reza_priyambada', '$2y$12$GJolSZWZOE4ILqHrWpgBkepmGqij41WQGKSOPK5sNNimnKYsSYgTm', 'Reza Priyambada, S.Sn., M.Ds.', 5, 'reza.priyambada@fakultas.ac.id'),
(156, 'sari_indah', '$2y$12$RbpMCBa.pcQEfosvH8RMN.ugfmKhOq.pvEpTAq51eQ0n/7u6RNV1O', 'Sari Indah, S.Ds., M.Sn.', 5, 'sari.indah@fakultas.ac.id'),
(157, 'leo_firmansyah', '$2y$12$txW6JyPxSgmBmDz6Y.WPS.Hyl8Xi7NOOKSTNE5FMOYwA.gywwzqSm', 'Leo Firmansyah, S.Sn.', 5, 'leo.firmansyah@fakultas.ac.id'),
(158, 'gita_permata', '$2y$12$ytOrQQ7AIpLcHSpz1U0ATuDpptwkoaEjgg.H0XGIcj9YUm2MWkqp6', 'Gita Permata, S.Ds., M.A.', 5, 'gita.permata@fakultas.ac.id'),
(159, 'bambang_subekti', '$2y$12$dsFMKrOghEpDAyVS8yEEReSI0p4679C8eQ5A/E7N4cYNGxfJ9CWIi', 'Dr. Bambang Subekti, M.Sn.', 5, 'bambang.subekti@fakultas.ac.id'),
(160, 'citra_kirana', '$2y$12$BkeiU3g7kZWkHTrYgktJyub3N2t1u4PuD4WsJCVvW4ZofjdwM5K..', 'Prof. Dr. Citra Kirana, M.Hum.', 5, 'citra.kirana@fakultas.ac.id'),
(161, 'agung_laksana', '$2y$12$.K.iCFMKmddiT6yxverGo.4.viv2Jth1YLuiV7iVZIJ9Hh2RP7MeO', 'Dr. Agung Laksana, S.I.Kom., M.Si.', 4, 'agung.laksana@fakultas.ac.id'),
(162, 'riana_fitri', '$2y$12$7HW/fKW/6dv4d1gg3IIvieH1tKcntOQQY3DSXHN6JXDR5a42XiNSe', 'Riana Fitri, S.Sos., M.I.Kom.', 5, 'riana.fitri@fakultas.ac.id'),
(163, 'dedi_mulyana', '$2y$12$23SxbhQVvw5rRgUqDD94fuDlR88Ia4EPze5LSUb5tAEyGmBWWGwhS', 'Prof. Dr. Dedi Mulyana, M.A.', 5, 'dedi.mulyana@fakultas.ac.id'),
(164, 'fahmi_idris', '$2y$12$t9DW3UWqfH98k77Zl9ABluU2JGQaEnZ1WfDOlvS2Ncj8itsDJw84.', 'Fahmi Idris, S.I.Kom., M.Si.', 5, 'fahmi.idris@fakultas.ac.id'),
(165, 'nadia_utami', '$2y$12$0fu7d1tvP0iA5H77ZtRcceDkXIifc/yva0qR.p1xsX4d9d8TN4tnS', 'Nadia Utami, S.Sos., M.I.Kom.', 5, 'nadia.utami@fakultas.ac.id'),
(166, 'gilang_pratama', '$2y$12$AhAzWfjN82D.PO3OpKsiDuvsiQDC5GzlvqE1tR9Llq94dQQdIPSFy', 'Dr. Gilang Pratama, M.Si.', 5, 'gilang.pratama@fakultas.ac.id'),
(167, 'indah_cahyani', '$2y$12$9VMPlZmvvCDyacM7kIy03enDBw3FG9AWMqrfULJ0OKMnDWu.up.yO', 'Indah Cahyani, S.I.Kom., M.A.', 5, 'indah.cahyani@fakultas.ac.id'),
(168, 'yudi_candra', '$2y$12$r.1hl9rxiuJkMbtp1st8P.2RnD8xEh9UTTJfZWlTxJMmtwpL1tByy', 'Yudi Candra, S.Sos., M.Si.', 5, 'yudi.candra@fakultas.ac.id'),
(169, 'shinta_bella', '$2y$12$MuuewNCejqOY1hGWg3cn3.Vz5.PZo22GwLnX3h86efyDxhhJv81HK', 'Shinta Bella, S.I.Kom., M.Med.Kom.', 5, 'shinta.bella@fakultas.ac.id'),
(170, 'widodo_ms', '$2y$12$JufMmIJZTU0XJgqOHo7k1OJotkqsqI2lDGkzVDRRTOh9ekq0zjQP2', 'Prof. Dr. Widodo, M.S.', 5, 'widodo.ms@fakultas.ac.id'),
(201, 'mahasiswa1', '$2y$12$/roC3CpDOzVylgBlGV4lAeWoWNsRWZQkU0vXYG0zPRKH9CieKihyS', 'Adi Saputra', 6, 'adi.saputra@student.ac.id'),
(202, 'mahasiswa2', '$2y$12$KXuYU0wD1I6fL.bePeGUTuOqy9.WmkPdprMSV6c3DclTfVIM1dB2y', 'Bunga Citra Lestari', 6, 'bunga.citra@student.ac.id'),
(203, 'mahasiswa3', '$2y$12$A.sCl4sptMZpluxGx5A0bugtRwSQaINxiRWnR2QG9YlACVoZEvSua', 'Candra Wijaya', 6, 'candra.wijaya@student.ac.id'),
(204, 'mahasiswa4', '$2y$12$YUy/TMNgobM.0TgMSeQlw.E/TKUANZ5CzhW/iQVN40tTuxaMr8xf2', 'Dewi Anggraini', 6, 'dewi.anggraini@student.ac.id'),
(205, 'mahasiswa5', '$2y$12$ZaAST.XhP3uiOD7rM1NyxuBrDK.5B09cLKXXXVQ2eaNaTxratKdre', 'Eko Prasetyo', 6, 'eko.prasetyo@student.ac.id'),
(206, 'mahasiswa6', '$2y$12$Qji6X6ZKO9TBhZpFu/Capeqt6HstHXZkTHzVliYRlRnJDirwnr60u', 'Fitriana Rahmawati', 6, 'fitriana.rahmawati@student.ac.id'),
(207, 'mahasiswa7', '$2y$12$JinN/zV7o2zdizIDT3eq3.Qhc70obatE3FERwphy8PXWKgqi2YTse', 'Guntur Hidayat', 6, 'guntur.hidayat@student.ac.id'),
(208, 'mahasiswa8', '$2y$12$3s8sPol71QmxaKTioctuGulB5MMpJeKJRu4IyFQT0ZnxA22ojE.s.', 'Herlina Sari', 6, 'herlina.sari@student.ac.id'),
(209, 'mahasiswa9', '$2y$12$BJ4xVGtji1qJYOKeJrqihuftXAdCexBrnnohPt/I94ZyIZpeIWOD6', 'Irfan Maulana', 6, 'irfan.maulana@student.ac.id'),
(210, 'mahasiswa10', '$2y$12$SgOGlhtSyQL/htp6E/588.QuJkmbpGBNNAR/4nEGOJ2O4S3NpvVeK', 'Jasmine Putri', 6, 'jasmine.putri@student.ac.id'),
(211, 'mahasiswa11', '$2y$12$7IY8vDk2ve4PJ1fkY2DAx.OFlOGvQ161kugEfJTYSFsl.kfmcKtq.', 'Kevin Sanjaya', 6, 'kevin.sanjaya@student.ac.id'),
(212, 'mahasiswa12', '$2y$12$TIX4E63GarN.lO7uptN8aux5gaF5vuKA8SOddk/R8JbYrejcGe78K', 'Lia Kartika', 6, 'lia.kartika@student.ac.id'),
(213, 'mahasiswa13', '$2y$12$1x2a9R1WKA2Yq.K3cj32o.3ir2DB2VxQP.UsunEJTrQBQ/D1YXvbq', 'Muhammad Rizki', 6, 'muhammad.rizki@student.ac.id'),
(214, 'mahasiswa14', '$2y$12$L6BrPIe8OfK6w5abuojsQ.Mzt7VpQ3RBhcr2bVaR2ZuZ/cNLKHNpi', 'Nadia Paramita', 6, 'nadia.paramita@student.ac.id'),
(215, 'mahasiswa15', '$2y$12$oi9gKqEvsKhKhIBMcfl/6eGyLGvAxdJHCaooHfNmZpouzvws5Wa8O', 'Oscar Maulana', 6, 'oscar.maulana@student.ac.id'),
(216, 'mahasiswa16', '$2y$12$EqmMPy29B6.CQgnRPWnLQ.m9RV7LJE0Eht4MLbr.GAVZ3dtEntYu.', 'Putri Ayu', 6, 'putri.ayu@student.ac.id'),
(217, 'mahasiswa17', '$2y$12$uTMoN/P82YDq0XQXPsfa6.bYpeZCFM/dTpzUjhc.OtxsQjQMOOAf6', 'Qori Akbar', 6, 'qori.akbar@student.ac.id'),
(218, 'mahasiswa18', '$2y$12$F7jcoljopBHmNrLInBSTlun9YqRWtad1A6NbHMwPebdX.xyLubOI2', 'Rahmat Hidayat', 6, 'rahmat.hidayat@student.ac.id'),
(219, 'mahasiswa19', '$2y$12$CswuL.nxsQ0M3WRlLDGXd.36.5EFbMw8QQ9uFBMQa/MFweK9oGHYq', 'Siska Wulandari', 6, 'siska.wulandari@student.ac.id'),
(220, 'mahasiswa20', '$2y$12$Cnn8Bnev2uCYQ9EtitJWee3xmEZnrzmoxlasMvWk2UiIjNN66SOfm', 'Taufik Ramadhan', 6, 'taufik.ramadhan@student.ac.id'),
(221, 'mahasiswa21', '$2y$12$uUjqPOHLNzTymrzCPfoy5eVHg9OxM6rK/aFdjzCL84fAnQSuzZ9cO', 'Utari Dewi', 6, 'utari.dewi@student.ac.id'),
(222, 'mahasiswa22', '$2y$12$akCKH0YLsT1/7gCZGKwnpuG9KbinQu918cBoxBciIHYXzoG.hvObm', 'Vino Bastian', 6, 'vino.bastian@student.ac.id'),
(223, 'mahasiswa23', '$2y$12$.uJz5OMgYsHNiComIckFLuVMmwdyxNj4A9b6CBfP2Vrsd4ufcXv9i', 'Winda Lestari', 6, 'winda.lestari@student.ac.id'),
(224, 'mahasiswa24', '$2y$12$QmyAPh4OnPK0Cmg..RD2Tu9mDjr0LkTCFzyhUtgaC773fVQr02NyO', 'Xavier Nugraha', 6, 'xavier.nugraha@student.ac.id'),
(225, 'mahasiswa25', '$2y$12$r8RyGEPi22gXwYkX6UqOSujeek.VUsYzIjWnJ8jOrszVq1LA.6PM2', 'Yulia Puspita', 6, 'yulia.puspita@student.ac.id'),
(226, 'mahasiswa26', '$2y$12$EgLvAugPE3FO40cEQdRaWeWVmVXMYrrZm1y.amfgV/erMebEsNtzy', 'Zainal Abidin', 6, 'zainal.abidin@student.ac.id'),
(227, 'mahasiswa27', '$2y$12$Vzq2ozkiEz0QjUTBd9GV/.BnKUVymQVL94ardzE31PN12reuqVSie', 'Andika Pratama', 6, 'andika.pratama@student.ac.id'),
(228, 'mahasiswa28', '$2y$12$y.arU7Q0L9NY4yhtZiDtQeBYUMMDY3Xa9Hrg72o5fUKeOsETzm/J2', 'Bella Saphira', 6, 'bella.saphira@student.ac.id'),
(229, 'mahasiswa29', '$2y$12$UQRXGIGRE.263fmZBsOH2eX0w.F6TNX2A6U4egIsM2S1JY2xMlV8e', 'Citra Adinda', 6, 'citra.adinda@student.ac.id'),
(230, 'mahasiswa30', '$2y$12$B1iuXNexqjaVyIzi5l9mjelZl1r4vZ0w1iVlMiOWB.U8U601u.0TG', 'Dika Wahyudi', 6, 'dika.wahyudi@student.ac.id'),
(231, 'mahasiswa31', '$2y$12$TvtQjaPxfGeO.xZzG/BYS.ddj5SfDnkwJVADeC13ZNDhJkhHbNlO.', 'Erika Putri', 6, 'erika.putri@student.ac.id'),
(232, 'mahasiswa32', '$2y$12$gwNxd0bWZp6w.8.CNtFhku5Isfhe91kvB1DO0PMxM6B3WSVT9Rp4K', 'Fajar Nugraha', 6, 'fajar.nugraha@student.ac.id'),
(233, 'mahasiswa33', '$2y$12$2ohZyn.od1Y1KS2knwVlQe4rhmRYTpH7yXhYdaWPYSOkclVS0qqUq', 'Gita Amelia', 6, 'gita.amelia@student.ac.id'),
(234, 'mahasiswa34', '$2y$12$AN9bA8cW4BIa7l/KVwuLN.RV0oHhpgZxRxkqB.zNeJ1YJ2kmEzoaW', 'Hafiz Alfarizi', 6, 'hafiz.alfarizi@student.ac.id'),
(235, 'mahasiswa35', '$2y$12$DOZPx5Y90u9M3knV0SBYDufcCJse..9DZ9kN5ZzW/GYqfYm9lSYgW', 'Indah Permatasari', 6, 'indah.permatasari@student.ac.id'),
(236, 'mahasiswa36', '$2y$12$VnWxbDbhv.vIIy0ARR4gj.pL/PwqmKQVGbCR8YV1Fpr2iXn5s9HDy', 'Joko Susilo', 6, 'joko.susilo@student.ac.id'),
(237, 'mahasiswa37', '$2y$12$TznZ18S9J7IDs2SAA/8mCe.KwxcLdvTqYKKYXA50/MxVVL3fRf6nK', 'Kartika Sari', 6, 'kartika.sari@student.ac.id'),
(238, 'mahasiswa38', '$2y$12$eHf291LoXRRuJb35UutrD.N/5K70vBCJVhGsRQ4tDvff7JRwsJzze', 'Lutfi Hakim', 6, 'lutfi.hakim@student.ac.id'),
(239, 'mahasiswa39', '$2y$12$bCnGAFdUSRXrPqf74iSswefnDJhXLiEVI.f.TUFOc6BeSe5ofo8kC', 'Mega Utami', 6, 'mega.utami@student.ac.id'),
(240, 'mahasiswa40', '$2y$12$XnxpN595L3gm4Ozkih24YOPI76TlHv3UJqJfMdiHOZAwOpgbEm3zS', 'Nanda Pratama', 6, 'nanda.pratama@student.ac.id'),
(241, 'mahasiswa41', '$2y$12$y5QIFwr11SQtkc0sNv0ALeLYPIxxlI1XWVHdGdmXU.Z3pcV5do1Ly', 'Olivia Jensen', 6, 'olivia.jensen@student.ac.id'),
(242, 'mahasiswa42', '$2y$12$JE5zskfqditq4gdGMRiYmuqvvxzHnVcn0/ViDCrnAo88kRCKxAnlG', 'Pandu Wijaya', 6, 'pandu.wijaya@student.ac.id'),
(243, 'mahasiswa43', '$2y$12$m/br5IQIS2U8Z5.d4wzTM.MikyzuQXXL4qCs2L8MaY4drG9rxjOau', 'Ratih Kumala', 6, 'ratih.kumala@student.ac.id'),
(244, 'mahasiswa44', '$2y$12$A1x.Qst7fVeY8XJw5R3ylOGBiDg.Go0OdukhOXOMimRCqpGxhG.p6', 'Sakti Mahendra', 6, 'sakti.mahendra@student.ac.id'),
(245, 'mahasiswa45', '$2y$12$Td3ElpvNEOBKBeKzufRZlOScPpUBGi91UJuWcnqbdHimP5yW/UJc.', 'Tiara Ananda', 6, 'tiara.ananda@student.ac.id'),
(246, 'mahasiswa46', '$2y$12$W/YW79Sc8OwpbfjnR1fls.7e2QRD8HiW9BLbszQazPJ8DZGCKT2nW', 'Umar Abdullah', 6, 'umar.abdullah@student.ac.id'),
(247, 'mahasiswa47', '$2y$12$cxjr8.4n1mpUanlT.lKbDebuLuvT25Rry1VvxGHFDF9ijd79nJHnm', 'Vania Mariska', 6, 'vania.mariska@student.ac.id'),
(248, 'mahasiswa48', '$2y$12$aCHaGZRyOZYXetP7R4C6D.v4hY4hLclFL1Evy8VdfNpOrAUatpAC6', 'Wahyu Setiawan', 6, 'wahyu.setiawan@student.ac.id'),
(249, 'mahasiswa49', '$2y$12$od6x5XRvuR42sMSVpKJcneB7UgeYC7yGhnoR6ru2itHz4YmDnNXxq', 'Yasmine Wildblood', 6, 'yasmine.wildblood@student.ac.id'),
(250, 'mahasiswa50', '$2y$12$ZWhRiFBpu9LsHAC73jAOu.5uR1psHu96A0vKcNh5vmaOaNaT8sbJu', 'Zacky Ramadhan', 6, 'zacky.ramadhan@student.ac.id'),
(251, 'mahasiswa51', '$2y$12$irq538./zweZlfczsTAacuRmtDcj6En4o0K80Y2f1WdBY5IFTIlim', 'Amanda Manopo', 6, 'amanda.manopo@student.ac.id'),
(252, 'mahasiswa52', '$2y$12$GTyDTv09uQgX86Wf9ce3fOXM6QPk7VDMRLPcn2d6OR9XijNJVIiYO', 'Bagas Aditya', 6, 'bagas.aditya@student.ac.id'),
(253, 'mahasiswa53', '$2y$12$nV0Hjd8HWKWinUD.YeC3Q.0lgf/b0AXfC0wXkKr1lhTVQrz/oX2sK', 'Cindy Gulla', 6, 'cindy.gulla@student.ac.id'),
(254, 'mahasiswa54', '$2y$12$8rlSKxFYoikDx0nE9tZQa.mW/PbisTvQHn3IpCuwhRjT3wXbMhWyG', 'Dimas Anggara', 6, 'dimas.anggara@student.ac.id'),
(255, 'mahasiswa55', '$2y$12$2zrGf6wDfbp3SSLvallpEuHtLsrsakq9DJ2VJeTC4.3fkzenpcZt6', 'Elina Joerg', 6, 'elina.joerg@student.ac.id'),
(256, 'mahasiswa56', '$2y$12$X93UI32Q9sV7Bbu0Luqdq.9QE0H9qpGrtYLS3TWuenn/QGlmvu/Qu', 'Farhan Jamil', 6, 'farhan.jamil@student.ac.id'),
(257, 'mahasiswa57', '$2y$12$DjEpoEdImqineG3BwdaYe.ga3KIMPzecp2BLpwtie4joNPx.7cboe', 'Gisella Anastasia', 6, 'gisella.anastasia@student.ac.id'),
(258, 'mahasiswa58', '$2y$12$Cpnpx4l21trC9z36hPUituDHw/zQw2XknMXE2toSXP7kBtlTjnJUC', 'Harris Vriza', 6, 'harris.vriza@student.ac.id'),
(259, 'mahasiswa59', '$2y$12$OCrVQD4iPddMuSvCqe9HYetkavB7Aye2GwouBPCRj5nd7quFsm3NW', 'Irish Bella', 6, 'irish.bella@student.ac.id'),
(260, 'mahasiswa60', '$2y$12$wrwiuEgGyyD.bAmP4n2iA.HwR3lPDdxI0NcDwDuQy/KuGM3lJlle2', 'Jeffri Nichol', 6, 'jeffri.nichol@student.ac.id'),
(261, 'mahasiswa61', '$2y$12$Tel8BQxYo7ESDvo3JlGQfemDoeRJcws1lNG8GI5U63nC3a1mpeRf6', 'Kesha Ratuliu', 6, 'kesha.ratuliu@student.ac.id'),
(262, 'mahasiswa62', '$2y$12$dCfQ1OL.QtmA3DVIKW3Y3Ol.e0FTAUEx/turw2xJgAyXCNhzBJoN2', 'Laura Basuki', 6, 'laura.basuki@student.ac.id'),
(263, 'mahasiswa63', '$2y$12$U3M2sIEhs5tYToQeQBzf5e11cZzTFMaiVm.BFz8DIl/SmE4Pnjm3a', 'Marcell Darwin', 6, 'marcell.darwin@student.ac.id'),
(264, 'mahasiswa64', '$2y$12$uYfBQvr8Zt2maO8RnJqib.a2yFVUgmcBHFvwxAroEh3xA.Ukb9Bf6', 'Natasha Wilona', 6, 'natasha.wilona@student.ac.id'),
(265, 'mahasiswa65', '$2y$12$DFWe9UoLmOCt9Mh9as4Vremcr97heEsgkKW40SHALdqvCtfI1o2zC', 'Omar Daniel', 6, 'omar.daniel@student.ac.id'),
(266, 'mahasiswa66', '$2y$12$pWuhCXOqfOYHqSWI7Vp4P.jrtePDC2thW0RJ1ACWurL6kWG9j7JRO', 'Pamela Bowie', 6, 'pamela.bowie@student.ac.id'),
(267, 'mahasiswa67', '$2y$12$PX2f0csHEm.nf1V57vStB.fE4vEYYhq8LKfFV5NM8Q10yQE5ndE4G', 'Randy Martin', 6, 'randy.martin@student.ac.id'),
(268, 'mahasiswa68', '$2y$12$tAbQskOFl7z4khvWtPPv/eeaIzzJ1sjGqsUEdFImsNc2XSiuXy.te', 'Salshabilla Adriani', 6, 'salshabilla.adriani@student.ac.id'),
(269, 'mahasiswa69', '$2y$12$KC7O49gPraqch7kGY0qRVOwgWNydh7jNZv5tsdM6CdokswX7dnRH.', 'Teuku Rassya', 6, 'teuku.rassya@student.ac.id'),
(270, 'mahasiswa70', '$2y$12$eRyZdUGZH.IZbFthKDe7Pen8PZR0hkMcg3hXttBToM2aKlE2i4qa2', 'Valerie Thomas', 6, 'valerie.thomas@student.ac.id'),
(301, 'sri_wahyuni', '$2y$12$vYzuo8yCTD1ft9G6XWs0aukN.pHo2AU6Zs96qqirsYl/mHjyQuEUu', 'Sri Wahyuni', 1, 'sri.wahyuni@fakultas.ac.id'),
(302, 'joko_susanto', '$2y$12$o6lSdnHGHIuQDnqSTA7z0O0htb4Q5JWisEYRSW2TH987U4A18bSV.', 'Joko Susanto', 1, 'joko.susanto@fakultas.ac.id'),
(303, 'endang_lestari', '$2y$12$WWW7Yfu9YbrKwRYx.QFwsuDnW7dvIsx6R9tl4IPj6Ntcb16ASGCsO', 'Endang Lestari', 1, 'endang.lestari@fakultas.ac.id'),
(304, 'agung_santoso', '$2y$12$Wmi5qcdHCK0jhQFQp5BfWeJQBKt796kLpQ7fAilZShMAgB5Pzm.4q', 'Agung Santoso', 1, 'agung.santoso@fakultas.ac.id'),
(305, 'siti_aminah', '$2y$12$EtcKz2lTmyCLqss850CpweKdoRxKH0EM8wq1oW7qrZISpdQC5UxVa', 'Siti Aminah', 1, 'siti.aminah@fakultas.ac.id'),
(306, 'bambang_irawan', '$2y$12$617sUvUF8FVID5NyTc1IQ.RsimvVlGml0Pj2cXVYyFx5fd2O5KJLG', 'Bambang Irawan', 1, 'bambang.irawan@fakultas.ac.id'),
(307, 'dewi_sartika', '$2y$12$vbyK8WrszhLmjgDtn5yus.eOGQHA9TO.oMe7rboTL2LcQOQ6kwK7i', 'Dewi Sartika', 1, 'dewi.sartika@fakultas.ac.id');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `Dosen`
--
ALTER TABLE `Dosen`
  ADD PRIMARY KEY (`Id_Dosen`),
  ADD KEY `Id_User` (`Id_User`),
  ADD KEY `fk_dosen_pejabat` (`Id_Pejabat`),
  ADD KEY `fk_dosen_prodi` (`Id_Prodi`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `Fakultas`
--
ALTER TABLE `Fakultas`
  ADD PRIMARY KEY (`Id_Fakultas`);

--
-- Indexes for table `File_Arsip`
--
ALTER TABLE `File_Arsip`
  ADD PRIMARY KEY (`Id_File_Arsip`),
  ADD KEY `Id_Tugas_Surat` (`Id_Tugas_Surat`),
  ADD KEY `Id_User` (`Id_Pemberi_Tugas_Surat`),
  ADD KEY `arsip_penerima` (`Id_Penerima_Tugas_Surat`);

--
-- Indexes for table `Jenis_Pekerjaan`
--
ALTER TABLE `Jenis_Pekerjaan`
  ADD PRIMARY KEY (`Id_Jenis_Pekerjaan`);

--
-- Indexes for table `Jenis_Surat`
--
ALTER TABLE `Jenis_Surat`
  ADD PRIMARY KEY (`Id_Jenis_Surat`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Mahasiswa`
--
ALTER TABLE `Mahasiswa`
  ADD PRIMARY KEY (`Id_Mahasiswa`),
  ADD KEY `fk_users` (`Id_User`),
  ADD KEY `fk_prodi` (`Id_Prodi`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `Pegawai`
--
ALTER TABLE `Pegawai`
  ADD PRIMARY KEY (`Id_Pegawai`),
  ADD KEY `Id_User` (`Id_User`),
  ADD KEY `fk_pegawai_prodi` (`Id_Prodi`);

--
-- Indexes for table `Pejabat`
--
ALTER TABLE `Pejabat`
  ADD PRIMARY KEY (`Id_Pejabat`);

--
-- Indexes for table `Penilaian_Kinerja`
--
ALTER TABLE `Penilaian_Kinerja`
  ADD PRIMARY KEY (`Id_Penilaian`),
  ADD KEY `Id_Pegawai` (`Id_Pegawai`),
  ADD KEY `Id_Penilai` (`Id_Penilai`);

--
-- Indexes for table `Prodi`
--
ALTER TABLE `Prodi`
  ADD PRIMARY KEY (`Id_Prodi`),
  ADD KEY `prodi_fakultas` (`Id_Fakultas`);

--
-- Indexes for table `Roles`
--
ALTER TABLE `Roles`
  ADD PRIMARY KEY (`Id_Role`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `Tugas`
--
ALTER TABLE `Tugas`
  ADD KEY `Id_Jenis_Pekerjaan` (`Id_Jenis_Pekerjaan`),
  ADD KEY `Id_Pemberi_Tugas` (`Id_Pemberi_Tugas`),
  ADD KEY `Id_Penerima_Tugas` (`Id_Penerima_Tugas`);

--
-- Indexes for table `Tugas_Surat`
--
ALTER TABLE `Tugas_Surat`
  ADD PRIMARY KEY (`Id_Tugas_Surat`),
  ADD KEY `Id_Jenis_Pekerjaan` (`Id_Jenis_Pekerjaan`),
  ADD KEY `Id_Pemberi_Tugas_Surat` (`Id_Pemberi_Tugas_Surat`),
  ADD KEY `Id_Penerima_Tugas_Surat` (`Id_Penerima_Tugas_Surat`),
  ADD KEY `fk_jenis_surat` (`Id_Jenis_Surat`);

--
-- Indexes for table `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`Id_User`),
  ADD KEY `Users_ibfk_1` (`Id_Role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Dosen`
--
ALTER TABLE `Dosen`
  MODIFY `Id_Dosen` int NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=93;
--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `Fakultas`
--
ALTER TABLE `Fakultas`
  MODIFY `Id_Fakultas` int NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `Dosen`
--
ALTER TABLE `Dosen`
  ADD CONSTRAINT `Dosen_ibfk_1` FOREIGN KEY (`Id_User`) REFERENCES `Users` (`Id_User`),
  ADD CONSTRAINT `fk_dosen_pejabat` FOREIGN KEY (`Id_Pejabat`) REFERENCES `Pejabat` (`Id_Pejabat`),
  ADD CONSTRAINT `fk_dosen_prodi` FOREIGN KEY (`Id_Prodi`) REFERENCES `Prodi` (`Id_Prodi`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Ketidakleluasaan untuk tabel `File_Arsip`
--
ALTER TABLE `File_Arsip`
  ADD CONSTRAINT `arsip_penerima` FOREIGN KEY (`Id_Penerima_Tugas_Surat`) REFERENCES `Users` (`Id_User`),
  ADD CONSTRAINT `File_Arsip_ibfk_1` FOREIGN KEY (`Id_Tugas_Surat`) REFERENCES `Tugas_Surat` (`Id_Tugas_Surat`),
  ADD CONSTRAINT `File_Arsip_ibfk_2` FOREIGN KEY (`Id_Pemberi_Tugas_Surat`) REFERENCES `Users` (`Id_User`);

--
-- Ketidakleluasaan untuk tabel `Mahasiswa`
--
ALTER TABLE `Mahasiswa`
  ADD CONSTRAINT `fk_prodi` FOREIGN KEY (`Id_Prodi`) REFERENCES `Prodi` (`Id_Prodi`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_users` FOREIGN KEY (`Id_User`) REFERENCES `Users` (`Id_User`);

--
-- Ketidakleluasaan untuk tabel `Pegawai`
--
ALTER TABLE `Pegawai`
  ADD CONSTRAINT `fk_pegawai_prodi` FOREIGN KEY (`Id_Prodi`) REFERENCES `Prodi` (`Id_Prodi`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `Pegawai_ibfk_1` FOREIGN KEY (`Id_User`) REFERENCES `Users` (`Id_User`);

--
-- Ketidakleluasaan untuk tabel `Penilaian_Kinerja`
--
ALTER TABLE `Penilaian_Kinerja`
  ADD CONSTRAINT `Penilaian_Kinerja_ibfk_1` FOREIGN KEY (`Id_Pegawai`) REFERENCES `Users` (`Id_User`),
  ADD CONSTRAINT `Penilaian_Kinerja_ibfk_2` FOREIGN KEY (`Id_Penilai`) REFERENCES `Users` (`Id_User`);

--
-- Ketidakleluasaan untuk tabel `Prodi`
--
ALTER TABLE `Prodi`
  ADD CONSTRAINT `prodi_fakultas` FOREIGN KEY (`Id_Fakultas`) REFERENCES `Fakultas` (`Id_Fakultas`);

--
-- Ketidakleluasaan untuk tabel `Tugas`
--
ALTER TABLE `Tugas`
  ADD CONSTRAINT `Tugas_ibfk_1` FOREIGN KEY (`Id_Jenis_Pekerjaan`) REFERENCES `Jenis_Pekerjaan` (`Id_Jenis_Pekerjaan`),
  ADD CONSTRAINT `Tugas_ibfk_2` FOREIGN KEY (`Id_Pemberi_Tugas`) REFERENCES `Users` (`Id_User`),
  ADD CONSTRAINT `Tugas_ibfk_3` FOREIGN KEY (`Id_Penerima_Tugas`) REFERENCES `Users` (`Id_User`);

--
-- Ketidakleluasaan untuk tabel `Tugas_Surat`
--
ALTER TABLE `Tugas_Surat`
  ADD CONSTRAINT `fk_jenis_surat` FOREIGN KEY (`Id_Jenis_Surat`) REFERENCES `Jenis_Surat` (`Id_Jenis_Surat`),
  ADD CONSTRAINT `Tugas_Surat_ibfk_1` FOREIGN KEY (`Id_Jenis_Pekerjaan`) REFERENCES `Jenis_Pekerjaan` (`Id_Jenis_Pekerjaan`),
  ADD CONSTRAINT `Tugas_Surat_ibfk_2` FOREIGN KEY (`Id_Pemberi_Tugas_Surat`) REFERENCES `Users` (`Id_User`),
  ADD CONSTRAINT `Tugas_Surat_ibfk_3` FOREIGN KEY (`Id_Penerima_Tugas_Surat`) REFERENCES `Users` (`Id_User`);

--
-- Ketidakleluasaan untuk tabel `Users`
--
ALTER TABLE `Users`
  ADD CONSTRAINT `Users_ibfk_1` FOREIGN KEY (`Id_Role`) REFERENCES `Roles` (`Id_Role`) ON DELETE RESTRICT ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
