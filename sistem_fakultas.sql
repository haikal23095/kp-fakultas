-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 14, 2026 at 09:08 AM
-- Server version: 8.0.45-0ubuntu0.22.04.1
-- PHP Version: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
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
-- Table structure for table `Acc_SK_Beban_Mengajar`
--

CREATE TABLE `Acc_SK_Beban_Mengajar` (
  `No` int NOT NULL,
  `Semester` enum('Ganjil','Genap') DEFAULT NULL,
  `Tahun_Akademik` varchar(100) DEFAULT NULL,
  `Data_Beban_Mengajar` json DEFAULT NULL,
  `Nomor_Surat` varchar(100) DEFAULT NULL,
  `Status` enum('Menunggu-Persetujuan-Wadek-1','Menunggu-Persetujuan-Dekan','Selesai','Ditolak-Wadek1','Ditolak-Dekan') DEFAULT NULL,
  `Alasan-Tolak` text,
  `QR_Code` varchar(255) DEFAULT NULL,
  `Tanggal-Persetujuan-Dekan` timestamp NULL DEFAULT NULL,
  `Id_Dekan` int DEFAULT NULL,
  `Tanggal-Pengajuan` timestamp NULL DEFAULT NULL,
  `Tanggal-Tenggat` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Acc_SK_Beban_Mengajar`
--

INSERT INTO `Acc_SK_Beban_Mengajar` (`No`, `Semester`, `Tahun_Akademik`, `Data_Beban_Mengajar`, `Nomor_Surat`, `Status`, `Alasan-Tolak`, `QR_Code`, `Tanggal-Persetujuan-Dekan`, `Id_Dekan`, `Tanggal-Pengajuan`, `Tanggal-Tenggat`) VALUES
(8, 'Genap', '2025/2026', '[{\"nip\": \"199005052015032001\", \"sks\": \"3\", \"kelas\": \"IF 3A\", \"Id_Prodi\": 1, \"id_dosen\": \"26\", \"Nama_Prodi\": \"Teknik Informatika\", \"nama_dosen\": \"Fitriani, S.Kom., M.Cs.\", \"id_mata_kuliah\": \"7\", \"nama_mata_kuliah\": \"Basis Data I\"}, {\"nip\": \"198208172008011005\", \"sks\": \"3\", \"kelas\": \"IF 5A\", \"Id_Prodi\": 1, \"id_dosen\": \"27\", \"Nama_Prodi\": \"Teknik Informatika\", \"nama_dosen\": \"Agus Wibowo, S.Kom., Ph.D.\", \"id_mata_kuliah\": \"1\", \"nama_mata_kuliah\": \"Pembelajaran Mesin\"}]', '320/UN46.3.4/HK.04/2026', 'Menunggu-Persetujuan-Wadek-1', NULL, NULL, NULL, NULL, '2026-01-13 02:09:59', '2026-01-16 02:09:59'),
(9, 'Genap', '2025/2026', '[{\"nip\": \"199008212015032004\", \"sks\": \"4\", \"kelas\": \"IF 1A\", \"Id_Prodi\": 2, \"id_dosen\": \"34\", \"Nama_Prodi\": \"Sistem Informasi\", \"nama_dosen\": \"Siti Nurhaliza, S.SI., M.Kom.\", \"id_mata_kuliah\": \"12\", \"nama_mata_kuliah\": \"Pengantar Teknologi Informasi\"}]', '320/UN46.3.4/HK.04/2026', 'Selesai', NULL, 'D:\\Project-KP\\kp-fakultas\\storage\\app/public/qr-codes/qr_d0HcbwfQjTiDX514.png', '2026-01-23 18:11:25', 23, '2026-01-13 02:54:37', '2026-01-16 02:54:37'),
(10, 'Genap', '2025/2026', '[{\"nip\": \"197911102005011003\", \"sks\": \"3\", \"kelas\": \"IF 5A\", \"Id_Prodi\": 1, \"id_dosen\": \"25\", \"Nama_Prodi\": \"Teknik Informatika\", \"nama_dosen\": \"Prof. Dr. Ir. Iwan Santoso\", \"id_mata_kuliah\": \"1\", \"nama_mata_kuliah\": \"Pembelajaran Mesin\"}, {\"nip\": \"199005052015032001\", \"sks\": \"3\", \"kelas\": \"IF 3A\", \"Id_Prodi\": 1, \"id_dosen\": \"26\", \"Nama_Prodi\": \"Teknik Informatika\", \"nama_dosen\": \"Fitriani, S.Kom., M.Cs.\", \"id_mata_kuliah\": \"7\", \"nama_mata_kuliah\": \"Basis Data I\"}]', '320/UN46.3.4/HK.04/2026', 'Selesai', NULL, 'D:\\Project-KP\\kp-fakultas\\storage\\app/public/qr-codes/qr_oA50E2YdJMdJKt2z.png', '2026-01-23 22:21:27', 23, '2026-01-23 22:12:08', '2026-01-27 06:12:08'),
(11, 'Genap', '2025/2026', '[{\"nip\": \"198604222011021004\", \"sks\": \"3\", \"kelas\": \"IF 5B\", \"Id_Prodi\": 1, \"id_dosen\": \"28\", \"Nama_Prodi\": \"Teknik Informatika\", \"nama_dosen\": \"Teguh Prasetyo, S.T., M.T.\", \"id_mata_kuliah\": \"19\", \"nama_mata_kuliah\": \"Pembelajaran Mesin\"}, {\"nip\": \"197911102005011003\", \"sks\": \"3\", \"kelas\": \"IF 3A\", \"Id_Prodi\": 1, \"id_dosen\": \"25\", \"Nama_Prodi\": \"Teknik Informatika\", \"nama_dosen\": \"Prof. Dr. Ir. Iwan Santoso\", \"id_mata_kuliah\": \"7\", \"nama_mata_kuliah\": \"Basis Data I\"}]', '320/UN46.3.4/HK.04/2026', 'Ditolak-Wadek1', 'kelas dirubah!', NULL, NULL, NULL, '2026-01-23 22:33:42', '2026-01-27 06:33:42'),
(12, 'Genap', '2025/2026', '[{\"nip\": \"198604222011021004\", \"sks\": \"3\", \"kelas\": \"IF 3B\", \"Id_Prodi\": 1, \"id_dosen\": \"28\", \"Nama_Prodi\": \"Teknik Informatika\", \"nama_dosen\": \"Teguh Prasetyo, S.T., M.T.\", \"id_mata_kuliah\": \"17\", \"nama_mata_kuliah\": \"Basis Data I\"}, {\"nip\": \"197911102005011003\", \"sks\": \"3\", \"kelas\": \"IF 3A\", \"Id_Prodi\": 1, \"id_dosen\": \"25\", \"Nama_Prodi\": \"Teknik Informatika\", \"nama_dosen\": \"Prof. Dr. Ir. Iwan Santoso\", \"id_mata_kuliah\": \"7\", \"nama_mata_kuliah\": \"Basis Data I\"}]', '320/UN46.3.4/HK.04/2026', 'Selesai', NULL, 'D:\\Project-KP\\kp-fakultas\\storage\\app/public/qr-codes/qr_JbNihUR8DJeFbMGX.png', '2026-01-23 23:03:20', 23, '2026-01-23 22:44:58', '2026-01-27 06:44:58');

-- --------------------------------------------------------

--
-- Table structure for table `Acc_SK_Dosen_Wali`
--

CREATE TABLE `Acc_SK_Dosen_Wali` (
  `No` int NOT NULL,
  `Semester` enum('Ganjil','Genap') NOT NULL,
  `Tahun_Akademik` varchar(12) NOT NULL,
  `Data_Dosen_Wali` json NOT NULL,
  `Nomor_Surat` varchar(100) NOT NULL,
  `Status` enum('Menunggu-Persetujuan-Wadek-1','Menunggu-Persetujuan-Dekan','Selesai','Ditolak-Wadek1','Ditolak-Dekan') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `Alasan-Tolak` text,
  `QR_Code` varchar(255) DEFAULT NULL,
  `Tanggal-Persetujuan-Dekan` timestamp NULL DEFAULT NULL,
  `Tanggal-Pengajuan` timestamp NOT NULL,
  `Tanggal-Tenggat` datetime NOT NULL,
  `Id_Dekan` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Acc_SK_Dosen_Wali`
--

INSERT INTO `Acc_SK_Dosen_Wali` (`No`, `Semester`, `Tahun_Akademik`, `Data_Dosen_Wali`, `Nomor_Surat`, `Status`, `Alasan-Tolak`, `QR_Code`, `Tanggal-Persetujuan-Dekan`, `Tanggal-Pengajuan`, `Tanggal-Tenggat`, `Id_Dekan`) VALUES
(18, 'Genap', '2025/2026', '[{\"nip\": \"197911102005011003\", \"prodi\": \"Teknik Informatika\", \"Id_Prodi\": 1, \"id_dosen\": \"25\", \"nama_dosen\": \"Prof. Dr. Ir. Iwan Santoso\", \"jumlah_anak_wali\": \"23\"}]', '320/UN46.3.4/HK.04/2025', 'Menunggu-Persetujuan-Wadek-1', NULL, NULL, NULL, '2026-01-04 11:50:49', '2026-01-07 11:45:48', NULL),
(19, 'Ganjil', '2025/2026', '[{\"nip\": \"198604222011021004\", \"prodi\": \"Teknik Informatika\", \"Id_Prodi\": 1, \"id_dosen\": \"28\", \"nama_dosen\": \"Teguh Prasetyo, S.T., M.T.\", \"jumlah_anak_wali\": \"10\"}]', '320/UN46.3.4/HK.04/2026', 'Selesai', NULL, 'qr-codes/qr_R9Rco5zEMV3MW7CL.png', '2026-02-06 07:44:28', '2026-02-06 07:36:38', '2026-02-09 15:35:34', 23);

-- --------------------------------------------------------

--
-- Table structure for table `Acc_SK_Pembimbing_Skripsi`
--

CREATE TABLE `Acc_SK_Pembimbing_Skripsi` (
  `No` int NOT NULL,
  `Semester` enum('Ganjil','Genap') DEFAULT NULL,
  `Tahun_Akademik` varchar(100) DEFAULT NULL,
  `Data_Pembimbing_Skripsi` json DEFAULT NULL,
  `Nomor_Surat` varchar(100) DEFAULT NULL,
  `Status` enum('Menunggu-Persetujuan-Wadek-1','Menunggu-Persetujuan-Dekan','Selesai','Ditolak-Wadek1','Ditolak-Dekan') DEFAULT NULL,
  `Alasan_Tolak` text,
  `QR_Code` varchar(255) DEFAULT NULL,
  `Tanggal_Persetujuan_Dekan` timestamp NULL DEFAULT NULL,
  `Id_Dekan` int DEFAULT NULL,
  `Tanggal-Pengajuan` datetime DEFAULT NULL,
  `Tanggal-Tenggat` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Acc_SK_Pembimbing_Skripsi`
--

INSERT INTO `Acc_SK_Pembimbing_Skripsi` (`No`, `Semester`, `Tahun_Akademik`, `Data_Pembimbing_Skripsi`, `Nomor_Surat`, `Status`, `Alasan_Tolak`, `QR_Code`, `Tanggal_Persetujuan_Dekan`, `Id_Dekan`, `Tanggal-Pengajuan`, `Tanggal-Tenggat`) VALUES
(7, 'Genap', '2025/2026', '\"[{\\\"nim\\\":1121001,\\\"id_mahasiswa\\\":\\\"1\\\",\\\"pembimbing_1\\\":{\\\"nip\\\":\\\"199005052015032001\\\",\\\"id_dosen\\\":\\\"26\\\",\\\"nama_dosen\\\":\\\"Fitriani, S.Kom., M.Cs.\\\"},\\\"pembimbing_2\\\":{\\\"nip\\\":\\\"198801202012112002\\\",\\\"id_dosen\\\":\\\"24\\\",\\\"nama_dosen\\\":\\\"Rina Permatasari, S.T., M.Sc.\\\"},\\\"judul_skripsi\\\":\\\"klasifikasi hewa ternak\\\",\\\"nama_mahasiswa\\\":\\\"Adi Saputra\\\",\\\"prodi_data\\\":{\\\"nama_prodi\\\":\\\"Teknik Informatika\\\",\\\"jurusan\\\":{\\\"Nama_Jurusan\\\":\\\"Teknik Informatika\\\"}}}]\"', '320/UN46.3.4/HK.04/2026', 'Selesai', NULL, 'qr-codes/sk_pembimbing_skripsi_dcOlh734oVASR4mk.png', '2026-01-23 19:48:16', 23, '2026-01-24 02:33:15', '2026-01-27 02:33:15');

-- --------------------------------------------------------

--
-- Table structure for table `Acc_SK_Penguji_Skripsi`
--

CREATE TABLE `Acc_SK_Penguji_Skripsi` (
  `No` int NOT NULL,
  `Semester` enum('Ganjil','Genap') DEFAULT NULL,
  `Tahun_Akademik` varchar(100) DEFAULT NULL,
  `Data_Penguji_Skripsi` json DEFAULT NULL,
  `Nomor_Surat` varchar(100) DEFAULT NULL,
  `Status` enum('Menunggu-Persetujuan-Wadek-1','Menunggu-Persetujuan-Dekan','Selesai','Ditolak-Wadek1','Ditolak-Dekan') DEFAULT NULL,
  `Alasan-Tolak` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `QR_Code` varchar(255) DEFAULT NULL,
  `Tanggal_Persetujuan_Dekan` timestamp NULL DEFAULT NULL,
  `Id_Dekan` int DEFAULT NULL,
  `Tanggal-Pengajuan` datetime DEFAULT NULL,
  `Tanggal-Tenggat` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Acc_SK_Penguji_Skripsi`
--

INSERT INTO `Acc_SK_Penguji_Skripsi` (`No`, `Semester`, `Tahun_Akademik`, `Data_Penguji_Skripsi`, `Nomor_Surat`, `Status`, `Alasan-Tolak`, `QR_Code`, `Tanggal_Persetujuan_Dekan`, `Id_Dekan`, `Tanggal-Pengajuan`, `Tanggal-Tenggat`) VALUES
(16, 'Genap', '2025/2026', '[{\"nim\": 1121003, \"prodi_data\": {\"jurusan\": {\"Nama_Jurusan\": \"Teknik Informatika\"}, \"nama_prodi\": \"Teknik Informatika\"}, \"mahasiswa_id\": \"3\", \"penguji_1_id\": \"26\", \"penguji_2_id\": \"25\", \"penguji_3_id\": \"23\", \"judul_skripsi\": \"Klasifikasi Penyakit daun jagung\", \"nama_mahasiswa\": \"Candra Wijaya\", \"nama_penguji_1\": \"Fitriani, S.Kom., M.Cs.\", \"nama_penguji_2\": \"Prof. Dr. Ir. Iwan Santoso\", \"nama_penguji_3\": \"Dr. Budi Hartono, S.Kom., M.Kom.\"}, {\"nim\": 1121001, \"prodi_data\": {\"jurusan\": {\"Nama_Jurusan\": \"Teknik Informatika\"}, \"nama_prodi\": \"Teknik Informatika\"}, \"mahasiswa_id\": \"1\", \"penguji_1_id\": \"27\", \"penguji_2_id\": \"26\", \"penguji_3_id\": \"23\", \"judul_skripsi\": \"Klasifikasi Pneyakit bronkitis\", \"nama_mahasiswa\": \"Adi Saputra\", \"nama_penguji_1\": \"Agus Wibowo, S.Kom., Ph.D.\", \"nama_penguji_2\": \"Fitriani, S.Kom., M.Cs.\", \"nama_penguji_3\": \"Dr. Budi Hartono, S.Kom., M.Kom.\"}]', '320/UN46.3.4/HK.04/2026', 'Menunggu-Persetujuan-Dekan', NULL, NULL, NULL, 23, '2026-01-13 08:28:29', '2026-01-16 08:31:12');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Dosen`
--

CREATE TABLE `Dosen` (
  `Id_Dosen` int NOT NULL,
  `NIP` varchar(255) DEFAULT NULL,
  `Nama_Dosen` varchar(255) DEFAULT NULL,
  `Jenis_Kelamin_Dosen` enum('L','P') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `Alamat_Dosen` text,
  `Id_User` int DEFAULT NULL,
  `Id_Prodi` int DEFAULT NULL,
  `Id_Fakultas` int NOT NULL,
  `Id_Pejabat` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Dosen`
--

INSERT INTO `Dosen` (`Id_Dosen`, `NIP`, `Nama_Dosen`, `Jenis_Kelamin_Dosen`, `Alamat_Dosen`, `Id_User`, `Id_Prodi`, `Id_Fakultas`, `Id_Pejabat`) VALUES
(7, '198808202012111004', 'Firmansyah Dani', 'L', 'Jl tempuran raya no 11 Surabaya', 171, 4, 2, 4),
(23, '198503152010121001', 'Dr. Budi Hartono, S.Kom., M.Kom.', 'L', 'Jl. Gebang Putih No. 10, Surabaya', 101, 1, 2, 1),
(24, '198801202012112002', 'Rina Permatasari, S.T., M.Sc.', 'P', 'Jl. Keputih Tegal No. 5, Surabaya', 102, 1, 2, 2),
(25, '197911102005011003', 'Prof. Dr. Ir. Iwan Santoso', 'L', 'Jl. Arief Rahman Hakim No. 100, Surabaya', 103, 1, 2, 3),
(26, '199005052015032001', 'Fitriani, S.Kom., M.Cs.', 'P', 'Jl. Medokan Semampir Indah No. 23, Surabaya', 104, 1, 2, 6),
(27, '198208172008011005', 'Agus Wibowo, S.Kom., Ph.D.', 'L', 'Jl. Semolowaru Utara V No. 1, Surabaya', 105, 1, 2, 5),
(28, '198604222011021004', 'Teguh Prasetyo, S.T., M.T.', 'L', 'Perumahan ITS Blok C No. 12, Surabaya', 106, 1, 2, NULL),
(29, '199109012016032003', 'Dewi Lestari, S.Kom., M.Kom.', 'P', 'Jl. Nginden Jangkungan III No. 30, Surabaya', 107, 4, 2, NULL),
(30, '198007252006041002', 'Hendro Susilo, B.Eng., M.Eng.', 'L', 'Jl. Klampis Ngasem No. 8, Surabaya', 108, 4, 2, 3),
(31, '198902142014052002', 'Yulia Citra, S.Si., M.Sc.', 'P', 'Jl. Manyar Kertoarjo No. 77, Surabaya', 109, 4, 2, NULL),
(32, '197806302003121001', 'Dr. Bambang Purnomo, M.T.', 'L', 'Jl. Menur Pumpungan No. 62, Surabaya', 110, 4, 2, NULL),
(33, '198702112013011002', 'Ahmad Fauzi, S.Kom., M.M.S.I.', 'L', 'Jl. Baratajaya No. 15, Surabaya', 111, 2, 2, 3),
(34, '199008212015032004', 'Siti Nurhaliza, S.SI., M.Kom.', 'P', 'Jl. Rungkut Madya No. 9, Surabaya', 112, 2, 2, NULL),
(35, '198405192009101001', 'Donny Setiawan, M.Sc.', 'L', 'Apartemen Puncak Kertajaya, Surabaya', 113, 2, 2, NULL),
(36, '198903122014022003', 'Linda Wati, S.Kom., M.T.', 'P', 'Jl. Panjang Jiwo Permai No. 4, Surabaya', 114, 2, 2, NULL),
(37, '198112012007011004', 'Dr. Eko Nugroho, M.Kom.', 'L', 'Jl. Kutisari Indah Barat No. 11, Surabaya', 115, 2, 2, NULL),
(38, '198510282010031003', 'Cahyo Purnomo, S.T., M.Sc.', 'L', 'Jl. Wonorejo Permai, Surabaya', 116, 2, 2, NULL),
(39, '199201152017032005', 'Anisa Rahmawati, S.Kom., M.Kom.', 'P', 'Jl. Sidosermo PDK, Surabaya', 117, 2, 2, NULL),
(40, '198309092008111002', 'Fajar Sidik, S.Kom., M.M.', 'L', 'Jl. Tenggilis Mejoyo, Surabaya', 118, 2, 2, NULL),
(41, '198806182013052004', 'Maya Anggraini, S.Kom., M.Sc.', 'P', 'Jl. Kendangsari, Surabaya', 119, 2, 2, NULL),
(42, '197904252005021002', 'Prof. Dr. Ir. Surya Dharma', 'L', 'Jl. Jemur Andayani No. 50, Surabaya', 120, 2, 2, NULL),
(63, '198009172006041007', 'Dr. Ir. Heru Santoso, M.Eng.', 'L', 'Jl. Teknik Kimia, Kampus ITS, Surabaya', 141, 3, 2, 2),
(64, '198501102010032009', 'Anita Sari, S.T., M.T.', 'P', 'Jl. Teknik Elektro, Kampus ITS, Surabaya', 142, 3, 2, 3),
(65, '197803222003121006', 'Prof. Dr. Ir. Taufik, M.Sc., Ph.D.', 'L', 'Jl. Kemenangan No. 1, Surabaya', 143, 3, 2, NULL),
(66, '198905152014021007', 'Rendra Setiawan, S.T., M.T.', 'L', 'Jl. Gebang Lor No. 70, Surabaya', 144, 3, 2, NULL),
(67, '199106062016032009', 'Diana Anggraini, S.T., M.Eng.', 'P', 'Jl. Deles, Surabaya', 145, 3, 2, NULL),
(68, '198210122008011009', 'Dr. Bayu Adhi, M.T.', 'L', 'Jl. Mulyorejo Tengah, Surabaya', 146, 5, 2, 2),
(69, '198711302013012007', 'Siska Amelia, S.T., M.T.', 'P', 'Jl. Kalijudan, Surabaya', 147, 5, 2, 3),
(70, '198402282009101005', 'Galih Prakoso, S.T., Ph.D.', 'L', 'Jl. Babatan Pantai, Surabaya', 148, 5, 2, NULL),
(71, '197512252001121003', 'Prof. Dr. Ir. Rini Nur, M.Sc.', 'P', 'Jl. Laguna Indah, Surabaya', 149, 6, 2, 3),
(72, '198808202012111004', 'Faisal Rahman, S.T., M.T.', 'L', 'Jl. Kejawan Putih Tambak, Surabaya', 150, 6, 2, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Fakultas`
--

CREATE TABLE `Fakultas` (
  `Id_Fakultas` int NOT NULL,
  `Nama_Fakultas` varchar(255) NOT NULL,
  `Id_Dekan` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Fakultas`
--

INSERT INTO `Fakultas` (`Id_Fakultas`, `Nama_Fakultas`, `Id_Dekan`) VALUES
(2, 'Teknik', 23);

-- --------------------------------------------------------

--
-- Table structure for table `File_Arsip`
--

CREATE TABLE `File_Arsip` (
  `Id_File_Arsip` int NOT NULL,
  `Id_Tugas_Surat` int UNSIGNED DEFAULT NULL,
  `Id_Pemberi_Tugas_Surat` int DEFAULT NULL,
  `Id_Penerima_Tugas_Surat` int DEFAULT NULL,
  `Keterangan` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Jurusan`
--

CREATE TABLE `Jurusan` (
  `Id_Jurusan` int NOT NULL,
  `Nama_Jurusan` varchar(255) DEFAULT NULL,
  `Id_Fakultas` int DEFAULT NULL,
  `Id_Kajur` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Jurusan`
--

INSERT INTO `Jurusan` (`Id_Jurusan`, `Nama_Jurusan`, `Id_Fakultas`, `Id_Kajur`) VALUES
(1, 'Teknik Informatika', 2, 24),
(2, 'Teknik Elektro', 2, 63),
(3, 'Teknik Industri dan Mesin', 2, 68);

-- --------------------------------------------------------

--
-- Table structure for table `Kendaraan`
--

CREATE TABLE `Kendaraan` (
  `id` bigint UNSIGNED NOT NULL,
  `nama_kendaraan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `plat_nomor` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kapasitas` int NOT NULL,
  `status_kendaraan` enum('Tersedia','Maintenance') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Tersedia',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `Kendaraan`
--

INSERT INTO `Kendaraan` (`id`, `nama_kendaraan`, `plat_nomor`, `kapasitas`, `status_kendaraan`, `created_at`, `updated_at`) VALUES
(1, 'Toyota Avanza', 'N 1234 AB', 7, 'Tersedia', '2026-01-06 23:59:43', '2026-01-06 23:59:43'),
(2, 'Toyota Innova', 'N 5678 CD', 8, 'Tersedia', '2026-01-06 23:59:43', '2026-01-06 23:59:43'),
(3, 'Mitsubishi L300', 'L 9012 EF', 12, 'Tersedia', '2026-01-06 23:59:43', '2026-01-07 02:42:57');

-- --------------------------------------------------------

--
-- Table structure for table `Mahasiswa`
--

CREATE TABLE `Mahasiswa` (
  `Id_Mahasiswa` int NOT NULL,
  `NIM` int NOT NULL,
  `Nama_Mahasiswa` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `Jenis_Kelamin_Mahasiswa` enum('L','P') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `Alamat_Mahasiswa` text,
  `Status_KP` enum('Sedang_Melaksanakan','Tidak_Sedang_Melaksanakan','Telah_Melaksanakan','') NOT NULL DEFAULT 'Tidak_Sedang_Melaksanakan',
  `Angkatan` int DEFAULT NULL,
  `Id_User` int DEFAULT NULL,
  `Id_Prodi` int NOT NULL,
  `Id_Fakultas` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Mahasiswa`
--

INSERT INTO `Mahasiswa` (`Id_Mahasiswa`, `NIM`, `Nama_Mahasiswa`, `Jenis_Kelamin_Mahasiswa`, `Alamat_Mahasiswa`, `Status_KP`, `Angkatan`, `Id_User`, `Id_Prodi`, `Id_Fakultas`) VALUES
(1, 1121001, 'Adi Saputra', 'L', 'Jl. Merdeka No. 1, Jakarta', 'Tidak_Sedang_Melaksanakan', 2022, 201, 1, 2),
(2, 1121002, 'Bunga Citra Lestari', 'P', 'Jl. Sudirman No. 2, Bandung', 'Tidak_Sedang_Melaksanakan', 2021, 202, 1, 2),
(3, 1121003, 'Candra Wijaya', 'L', 'Jl. Pahlawan No. 3, Surabaya', 'Sedang_Melaksanakan', 2024, 203, 1, 2),
(4, 1121004, 'Dewi Anggraini', 'P', 'Jl. Diponegoro No. 4, Yogyakarta', 'Tidak_Sedang_Melaksanakan', 2024, 204, 1, 2),
(5, 1121005, 'Eko Prasetyo', 'L', 'Jl. Gajah Mada No. 5, Semarang', 'Tidak_Sedang_Melaksanakan', 2021, 205, 1, 2),
(6, 1121006, 'Fitriana Rahmawati', 'P', 'Jl. Asia Afrika No. 6, Medan', 'Tidak_Sedang_Melaksanakan', 2023, 206, 1, 2),
(7, 1121007, 'Guntur Hidayat', 'L', 'Jl. Imam Bonjol No. 7, Makassar', 'Tidak_Sedang_Melaksanakan', 2021, 207, 1, 2),
(8, 1121008, 'Herlina Sari', 'P', 'Jl. Gatot Subroto No. 8, Palembang', 'Tidak_Sedang_Melaksanakan', 2021, 208, 1, 2),
(9, 1121009, 'Irfan Maulana', 'L', 'Jl. Siliwangi No. 9, Denpasar', 'Tidak_Sedang_Melaksanakan', 2024, 209, 1, 2),
(10, 1121010, 'Jasmine Putri', 'P', 'Jl. Veteran No. 10, Malang', 'Tidak_Sedang_Melaksanakan', 2024, 210, 1, 2),
(11, 1122011, 'Kevin Sanjaya', 'L', 'Jl. Thamrin No. 11, Jakarta', 'Tidak_Sedang_Melaksanakan', 2022, 211, 2, 2),
(12, 1122012, 'Lia Kartika', 'P', 'Jl. Braga No. 12, Bandung', 'Tidak_Sedang_Melaksanakan', 2024, 212, 2, 2),
(13, 1122013, 'Muhammad Rizki', 'L', 'Jl. Tunjungan No. 13, Surabaya', 'Tidak_Sedang_Melaksanakan', 2024, 213, 2, 2),
(14, 1122014, 'Nadia Paramita', 'P', 'Jl. Malioboro No. 14, Yogyakarta', 'Tidak_Sedang_Melaksanakan', 2024, 214, 2, 2),
(15, 1122015, 'Oscar Maulana', 'L', 'Jl. Pemuda No. 15, Semarang', 'Tidak_Sedang_Melaksanakan', 2022, 215, 2, 2),
(16, 1122016, 'Putri Ayu', 'P', 'Jl. Kesawan No. 16, Medan', 'Tidak_Sedang_Melaksanakan', 2023, 216, 2, 2),
(17, 1122017, 'Qori Akbar', 'L', 'Jl. Losari No. 17, Makassar', 'Tidak_Sedang_Melaksanakan', 2022, 217, 2, 2),
(18, 1122018, 'Rahmat Hidayat', 'L', 'Jl. Ampera No. 18, Palembang', 'Tidak_Sedang_Melaksanakan', 2022, 218, 2, 2),
(19, 1122019, 'Siska Wulandari', 'P', 'Jl. Legian No. 19, Denpasar', 'Tidak_Sedang_Melaksanakan', 2024, 219, 2, 2),
(20, 1122020, 'Taufik Ramadhan', 'L', 'Jl. Ijen No. 20, Malang', 'Tidak_Sedang_Melaksanakan', 2023, 220, 2, 2),
(41, 1125041, 'Olivia Jensen', 'P', 'Jl. Dago No. 41, Bandung', 'Tidak_Sedang_Melaksanakan', 2023, 241, 3, 2),
(42, 1125042, 'Pandu Wijaya', 'L', 'Jl. Ngagel No. 42, Surabaya', 'Tidak_Sedang_Melaksanakan', 2023, 242, 3, 2),
(43, 1125043, 'Ratih Kumala', 'P', 'Jl. Monjali No. 43, Yogyakarta', 'Tidak_Sedang_Melaksanakan', 2023, 243, 3, 2),
(44, 1125044, 'Sakti Mahendra', 'L', 'Jl. Gajah Mungkur No. 44, Semarang', 'Tidak_Sedang_Melaksanakan', 2022, 244, 3, 2),
(45, 1125045, 'Tiara Ananda', 'P', 'Jl. Gatot Subroto No. 45, Medan', 'Tidak_Sedang_Melaksanakan', 2021, 245, 3, 2),
(46, 1125046, 'Umar Abdullah', 'L', 'Jl. Sultan Hasanuddin No. 46, Makassar', 'Tidak_Sedang_Melaksanakan', 2021, 246, 3, 2),
(47, 1125047, 'Vania Mariska', 'P', 'Jl. R. Sukamto No. 47, Palembang', 'Tidak_Sedang_Melaksanakan', 2023, 247, 3, 2),
(48, 1125048, 'Wahyu Setiawan', 'L', 'Jl. Teuku Umar No. 48, Denpasar', 'Tidak_Sedang_Melaksanakan', 2023, 248, 3, 2),
(49, 1125049, 'Yasmine Wildblood', 'P', 'Jl. Kawi No. 49, Malang', 'Tidak_Sedang_Melaksanakan', 2024, 249, 3, 2),
(50, 1125050, 'Zacky Ramadhan', 'L', 'Jl. Panglima Polim No. 50, Jakarta', 'Tidak_Sedang_Melaksanakan', 2021, 250, 3, 2);

-- --------------------------------------------------------

--
-- Table structure for table `Matakuliah`
--

CREATE TABLE `Matakuliah` (
  `Nomor` int NOT NULL,
  `Nama_Matakuliah` varchar(100) DEFAULT NULL,
  `Kelas` varchar(100) DEFAULT NULL,
  `SKS` int DEFAULT NULL,
  `Id_Prodi` int DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Matakuliah`
--

INSERT INTO `Matakuliah` (`Nomor`, `Nama_Matakuliah`, `Kelas`, `SKS`, `Id_Prodi`) VALUES
(1, 'Pembelajaran Mesin', 'IF 5A', 3, 1),
(3, 'Teori Komputasi', 'IF 3A', 2, 1),
(5, 'Proyek Perangkat Lunak', 'IF 5A', 3, 1),
(7, 'Basis Data I', 'IF 3A', 3, 1),
(9, 'Struktur Data (P)', 'IF 2A', 4, 1),
(11, 'Skripsi', NULL, 6, 1),
(12, 'Pengantar Teknologi Informasi', 'IF 1A', 4, 2),
(17, 'Basis Data I', 'IF 3B', 3, 1),
(18, 'Basis Data I', 'IF 3C', 3, 1),
(19, 'Pembelajaran Mesin', 'IF 5B', 3, 1),
(20, 'Pembelajaran Mesin', 'IF 5C', 3, 1),
(21, 'Pembelajaran Mesin', 'IF 5D', 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(4, '0001_01_01_000001_create_cache_table', 2),
(5, '0001_01_01_000002_create_jobs_table', 2),
(6, '2025_11_14_032355_create_surat_magang_table', 3),
(7, '2025_11_14_032435_migrate_existing_magang_data_to_surat_magang', 4),
(8, '2025_11_14_033455_add_nama_instansi_to_surat_magang_table', 4),
(9, '2025_11_15_150500_add_data_spesifik_and_tte_columns_to_tugas_surat', 5),
(10, '2025_11_15_151000_update_status_enum_tugas_surat', 5),
(11, '2025_11_18_155455_create_surat_verifications_table', 5),
(12, '2025_11_20_000000_add_is_read_to_notifikasi_table', 6),
(13, '2025_11_24_135907_create_surat_magang_draft_table', 7),
(14, '2025_11_24_135917_create_surat_magang_invitations_table', 8),
(15, '2025_11_24_142218_add_data_tambahan_to_notifikasi_table', 9),
(16, '2025_11_25_095119_drop_surat_magang_draft_table', 10),
(17, '2025_11_25_102744_add_judul_penelitian_to_surat_magang_table', 11),
(18, '2025_11_25_103505_add_draft_status_to_surat_magang_table', 12),
(19, '2025_11_25_104200_recreate_surat_magang_invitations_table', 13),
(20, '2025_11_26_185610_add_qr_code_to_surat_magang_table', 14),
(21, '2025_12_05_014609_add_qr_code_dekan_to_surat_magang_table', 15),
(22, '2025_12_05_032529_add_nip_dekan_to_surat_magang_table', 16),
(24, '2025_12_26_000001_create_sk_dosen_wali_table', 18),
(26, '2025_12_28_000001_create_surat_tidak_beasiswa_table', 19),
(27, '2025_12_29_000001_create_surat_kelakuan_baik_table', 20),
(28, '2025_12_23_000001_create_surat_legalisir_table', 21),
(29, '2025_12_31_040354_add_qr_code_column_to_acc_sk_dosen_wali_table', 22),
(30, '2026_01_02_145217_add_file_columns_to_surat_legalisir_table', 23),
(32, '2026_01_04_091006_create_surat_dispensasi_table', 24),
(34, '2026_01_07_000001_create_kendaraan_table', 25),
(35, '2026_01_07_000002_create_surat_peminjaman_mobil_table', 26),
(36, '2026_01_07_041924_add_qr_code_and_catatan_wadek2_to_surat_peminjaman_mobil_table', 27),
(37, '2026_01_08_104500_create_surat_peminjaman_ruang_table', 28),
(38, '2026_02_03_135812_add_missing_shared_columns_to_letter_tables', 29),
(39, '2026_02_03_140519_add_letter_type_to_surat_verifications_table', 30),
(40, '2026_02_03_140825_add_missing_shared_columns_to_aktif_and_magang', 31),
(41, '2026_02_03_141043_add_missing_shared_columns_to_peminjaman_mobil', 32),
(42, '2026_02_03_142203_add_missing_shared_columns_to_izin_malam', 33);

-- --------------------------------------------------------

--
-- Table structure for table `Notifikasi`
--

CREATE TABLE `Notifikasi` (
  `Id_Notifikasi` bigint NOT NULL,
  `Tipe_Notifikasi` enum('Rejected','Accepted','Invitation','Error') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Pesan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Dest_user` int NOT NULL,
  `Source_User` int NOT NULL,
  `Is_Read` tinyint(1) NOT NULL DEFAULT '0',
  `Data_Tambahan` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `Notifikasi`
--

INSERT INTO `Notifikasi` (`Id_Notifikasi`, `Tipe_Notifikasi`, `Pesan`, `Dest_user`, `Source_User`, `Is_Read`, `Data_Tambahan`, `created_at`, `updated_at`) VALUES
(219, 'Accepted', 'Surat pengantar magang Anda dengan nomor B/7345/UN46.3.4/PK.01.06/2026 telah disetujui dan ditandatangani oleh Dekan. Anda dapat melihat dan mengunduh surat di halaman riwayat surat.', 202, 101, 0, NULL, '2026-02-04 05:26:36', '2026-02-04 05:26:36'),
(221, 'Accepted', 'Surat pengantar magang Anda dengan nomor B/7345/UN46.3.4/PK.01.06/2026 telah disetujui dan ditandatangani oleh Dekan. Anda dapat melihat dan mengunduh surat di halaman riwayat surat.', 201, 101, 0, NULL, '2026-02-05 15:13:44', '2026-02-05 15:13:44'),
(222, 'Accepted', 'SK Dosen Wali No. 320/UN46.3.4/HK.04/2026 menunggu persetujuan Anda.', 171, 309, 0, '{\"link\": \"http://localhost:8000/wadek1/sk-dosen-wali\", \"judul\": \"SK Dosen Wali Menunggu Persetujuan\", \"sk_id\": 19, \"nomor_surat\": \"320/UN46.3.4/HK.04/2026\"}', '2026-02-06 07:36:38', '2026-02-06 07:36:38'),
(223, 'Accepted', 'SK Dosen Wali Ganjil 2025/2026 telah ditandatangani oleh Dekan', 309, 101, 0, '\"{\\\"acc_id\\\":19,\\\"nomor_surat\\\":\\\"320\\\\/UN46.3.4\\\\/HK.04\\\\/2026\\\",\\\"semester\\\":\\\"Ganjil\\\",\\\"tahun_akademik\\\":\\\"2025\\\\/2026\\\",\\\"qr_code\\\":\\\"qr-codes\\\\/qr_R9Rco5zEMV3MW7CL.png\\\"}\"', '2026-02-06 07:44:29', '2026-02-06 07:44:29'),
(224, 'Accepted', 'SK Dosen Wali Ganjil 2025/2026 yang Anda ajukan telah ditandatangani oleh Dekan.', 103, 101, 0, '\"{\\\"acc_id\\\":19,\\\"nomor_surat\\\":\\\"320\\\\/UN46.3.4\\\\/HK.04\\\\/2026\\\",\\\"semester\\\":\\\"Ganjil\\\",\\\"tahun_akademik\\\":\\\"2025\\\\/2026\\\",\\\"qr_code\\\":\\\"qr-codes\\\\/qr_R9Rco5zEMV3MW7CL.png\\\"}\"', '2026-02-06 07:44:46', '2026-02-06 07:44:46'),
(225, 'Accepted', 'Anda telah ditetapkan sebagai Dosen Wali untuk semester Ganjil 2025/2026. SK telah ditandatangani oleh Dekan.', 106, 101, 0, '\"{\\\"acc_id\\\":19,\\\"nomor_surat\\\":\\\"320\\\\/UN46.3.4\\\\/HK.04\\\\/2026\\\",\\\"semester\\\":\\\"Ganjil\\\",\\\"tahun_akademik\\\":\\\"2025\\\\/2026\\\",\\\"qr_code\\\":\\\"qr-codes\\\\/qr_R9Rco5zEMV3MW7CL.png\\\"}\"', '2026-02-06 07:44:56', '2026-02-06 07:44:56'),
(226, 'Rejected', '🛑 *SK BEBAN MENGAJAR DITOLAK*\n\nSK Beban Mengajar untuk Teknik Informatika Semester Genap TA 2025/2026 telah ditolak oleh Admin.\n\n*Alasan:* Data pengajaran semester ini tidak sesuai dengan beban kerja dosen. Mohon revisi pada bagian SKS.\n\n_Silakan periksa dashboard SIFAKULTAS untuk detail._', 103, 309, 0, '\"{\\\"url\\\":\\\"http:\\\\/\\\\/localhost:8000\\\\/kaprodi\\\\/sk\\\\/beban-mengajar\\\"}\"', '2026-02-06 17:49:49', '2026-02-06 17:49:49'),
(227, 'Rejected', '🛑 *SK BEBAN MENGAJAR DITOLAK*\n\nSK Beban Mengajar untuk Teknik Informatika Semester Genap TA 2025/2026 telah ditolak oleh Admin.\n\n*Alasan:* Data pengajaran semester ini tidak sesuai dengan beban kerja dosen. Mohon revisi pada bagian SKS.\n\n_Silakan periksa dashboard SIFAKULTAS untuk detail._', 103, 309, 0, '\"{\\\"url\\\":\\\"http:\\\\/\\\\/localhost:8000\\\\/kaprodi\\\\/sk\\\\/beban-mengajar\\\"}\"', '2026-02-06 17:50:50', '2026-02-06 17:50:50'),
(228, 'Rejected', 'SK Dosen Wali untuk Teknik Informatika Semester Ganjil TA 2025/2026 telah ditolak. Alasan: nooo kurang dosenya', 103, 309, 0, '\"{\\\"url\\\":\\\"http:\\\\/\\\\/localhost:8000\\\\/kaprodi\\\\/sk\\\\/dosen-wali\\\"}\"', '2026-02-06 17:58:10', '2026-02-06 17:58:10'),
(230, 'Rejected', 'SK Dosen Wali untuk Teknik Informatika Semester Ganjil TA 2025/2026 telah ditolak. Alasan: masih kurang dosenya', 103, 309, 0, '\"{\\\"url\\\":\\\"http:\\\\/\\\\/localhost:8000\\\\/kaprodi\\\\/sk\\\\/dosen-wali\\\"}\"', '2026-02-06 18:13:43', '2026-02-06 18:13:43');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Pegawai_Fakultas`
--

CREATE TABLE `Pegawai_Fakultas` (
  `Id_Pegawai` int NOT NULL,
  `NIP` varchar(255) DEFAULT NULL,
  `Nama_Pegawai` varchar(255) DEFAULT NULL,
  `Jenis_Kelamin_Pegawai` enum('L','P') DEFAULT NULL,
  `Alamat_Pegawai` text,
  `Id_User` int DEFAULT NULL,
  `Id_Fakultas` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Pegawai_Fakultas`
--

INSERT INTO `Pegawai_Fakultas` (`Id_Pegawai`, `NIP`, `Nama_Pegawai`, `Jenis_Kelamin_Pegawai`, `Alamat_Pegawai`, `Id_User`, `Id_Fakultas`) VALUES
(2, '198502022010012002', 'Admin Teknik', 'L', 'Bangkalan', 309, 2);

-- --------------------------------------------------------

--
-- Table structure for table `Pegawai_Prodi`
--

CREATE TABLE `Pegawai_Prodi` (
  `Id_Pegawai` int NOT NULL,
  `NIP` varchar(255) DEFAULT NULL,
  `Nama_Pegawai` varchar(255) DEFAULT NULL,
  `Jenis_Kelamin_Pegawai` enum('L','P') DEFAULT NULL,
  `Alamat_Pegawai` text,
  `Id_User` int DEFAULT NULL,
  `Id_Prodi` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Pegawai_Prodi`
--

INSERT INTO `Pegawai_Prodi` (`Id_Pegawai`, `NIP`, `Nama_Pegawai`, `Jenis_Kelamin_Pegawai`, `Alamat_Pegawai`, `Id_User`, `Id_Prodi`) VALUES
(1, '199501102020122001', 'Sri Wahyuni', 'P', 'Jl. Administrasi Fakultas No. 1, Surabaya', 301, 1),
(2, '199203152018031002', 'Joko Susanto', 'L', 'Jl. Tata Usaha Gedung A, Surabaya', 302, 2),
(3, '199708172021032005', 'Siti Aminah', 'P', 'Jl. Keuangan Kampus, Surabaya', 305, 3),
(4, '199708172021032006', 'Endang Lestari', 'P', 'Jl merpati no 3 Surabaya', 303, 4),
(5, '199708172021032007', 'Agung Santoso', 'L', 'Jl kembar no 3 Malang', 304, 5),
(6, '199708172021032008', 'Bambang Irawan', 'L', 'Jl mawar no 5 Surabaya', 306, 6),
(7, '198501012010012001', 'Dewi Sartika', 'P', 'Jl. Raya Telang', 307, 2);

-- --------------------------------------------------------

--
-- Table structure for table `Pejabat`
--

CREATE TABLE `Pejabat` (
  `Id_Pejabat` int NOT NULL,
  `Nama_Jabatan` enum('Kaprodi','Kajur','Dekan','Wadek1','Wadek2','Wadek3') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Pejabat`
--

INSERT INTO `Pejabat` (`Id_Pejabat`, `Nama_Jabatan`) VALUES
(1, 'Dekan'),
(2, 'Kajur'),
(3, 'Kaprodi'),
(4, 'Wadek1'),
(5, 'Wadek2'),
(6, 'Wadek3');

-- --------------------------------------------------------

--
-- Table structure for table `Penilaian_Kinerja`
--

CREATE TABLE `Penilaian_Kinerja` (
  `Id_Penilaian` int NOT NULL,
  `Id_Pegawai` int DEFAULT NULL,
  `Id_Penilai` int DEFAULT NULL,
  `Skor` enum('1','2','3','4','5') DEFAULT NULL,
  `Komentar` text,
  `Tanggal_Penilaian` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Penilaian_Kinerja`
--

INSERT INTO `Penilaian_Kinerja` (`Id_Penilaian`, `Id_Pegawai`, `Id_Penilai`, `Skor`, `Komentar`, `Tanggal_Penilaian`) VALUES
(1, 301, 101, '5', 'Kinerja sangat baik selama periode ini. Sangat proaktif dalam menyelesaikan tugas administrasi prodi dan selalu teliti.', '2025-09-10'),
(2, 302, 103, '4', 'Secara umum kinerjanya baik dan responsif. Perlu peningkatan dalam hal inisiatif untuk tugas-tugas baru di luar rutinitas.', '2025-09-10'),
(3, 303, 121, '4', 'Pekerjaan selalu selesai tepat waktu. Kemampuan komunikasi dengan mahasiswa dan dosen sangat baik dan membantu.', '2025-09-11'),
(4, 304, 151, '5', 'Sangat responsif dan solutif dalam menangani masalah teknis di prodi kami, terutama dalam pengelolaan website. Sangat membantu sekali.', '2025-09-11'),
(5, 306, 103, '3', 'Pekerjaan rutin terselesaikan dengan baik, namun perlu meningkatkan ketelitian dalam penyusunan laporan dan pengarsipan dokumen penting.', '2025-09-12');

-- --------------------------------------------------------

--
-- Table structure for table `Prodi`
--

CREATE TABLE `Prodi` (
  `Id_Prodi` int NOT NULL,
  `Nama_Prodi` varchar(255) DEFAULT NULL,
  `Id_Kaprodi` int NOT NULL,
  `Id_Jurusan` int NOT NULL,
  `Id_Fakultas` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Prodi`
--

INSERT INTO `Prodi` (`Id_Prodi`, `Nama_Prodi`, `Id_Kaprodi`, `Id_Jurusan`, `Id_Fakultas`) VALUES
(1, 'Teknik Informatika', 25, 1, 2),
(2, 'Sistem Informasi', 33, 1, 2),
(3, 'Teknik Elektro', 64, 2, 2),
(4, 'Teknik Mekatronika', 30, 2, 2),
(5, 'Teknik Mesin', 69, 3, 2),
(6, 'Teknik Industri', 71, 3, 2);

-- --------------------------------------------------------

--
-- Table structure for table `Req_SK_Beban_Mengajar`
--

CREATE TABLE `Req_SK_Beban_Mengajar` (
  `No` int NOT NULL,
  `Id_Prodi` int NOT NULL,
  `Semester` enum('Ganjil','Genap') NOT NULL,
  `Tahun_Akademik` varchar(12) NOT NULL,
  `Data_Beban_Mengajar` json NOT NULL,
  `Nomor_Surat` varchar(100) DEFAULT NULL,
  `Id_Acc_SK_Beban_Mengajar` int DEFAULT NULL,
  `Tanggal-Pengajuan` timestamp NOT NULL,
  `Tanggal-Tenggat` datetime NOT NULL,
  `Id_Dosen_Kaprodi` int NOT NULL,
  `Status` enum('Dikerjakan admin','Menunggu-Persetujuan-Wadek-1','Menunggu-Persetujuan-Dekan','Selesai','Ditolak-Admin','Ditolak-Wadek1','Ditolak-Dekan') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `Alasan-Tolak` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Req_SK_Beban_Mengajar`
--

INSERT INTO `Req_SK_Beban_Mengajar` (`No`, `Id_Prodi`, `Semester`, `Tahun_Akademik`, `Data_Beban_Mengajar`, `Nomor_Surat`, `Id_Acc_SK_Beban_Mengajar`, `Tanggal-Pengajuan`, `Tanggal-Tenggat`, `Id_Dosen_Kaprodi`, `Status`, `Alasan-Tolak`) VALUES
(14, 1, 'Genap', '2025/2026', '[{\"nip\": \"199005052015032001\", \"sks\": \"3\", \"kelas\": \"IF 3A\", \"id_dosen\": \"26\", \"nama_dosen\": \"Fitriani, S.Kom., M.Cs.\", \"id_mata_kuliah\": \"7\", \"nama_mata_kuliah\": \"Basis Data I\"}, {\"nip\": \"198208172008011005\", \"sks\": \"3\", \"kelas\": \"IF 5A\", \"id_dosen\": \"27\", \"nama_dosen\": \"Agus Wibowo, S.Kom., Ph.D.\", \"id_mata_kuliah\": \"1\", \"nama_mata_kuliah\": \"Pembelajaran Mesin\"}]', '320/UN46.3.4/HK.04/2026', 8, '2026-01-13 02:09:59', '2026-01-16 02:09:59', 25, 'Menunggu-Persetujuan-Wadek-1', NULL),
(15, 2, 'Genap', '2025/2026', '[{\"nip\": \"199008212015032004\", \"sks\": \"4\", \"kelas\": \"IF 1A\", \"id_dosen\": \"34\", \"nama_dosen\": \"Siti Nurhaliza, S.SI., M.Kom.\", \"id_mata_kuliah\": \"12\", \"nama_mata_kuliah\": \"Pengantar Teknologi Informasi\"}]', '320/UN46.3.4/HK.04/2026', 9, '2026-01-13 02:54:37', '2026-01-16 02:54:37', 33, 'Selesai', NULL),
(16, 1, 'Genap', '2025/2026', '[{\"nip\": \"197911102005011003\", \"sks\": \"3\", \"kelas\": \"IF 3B\", \"id_dosen\": \"25\", \"nama_dosen\": \"Prof. Dr. Ir. Iwan Santoso\", \"id_mata_kuliah\": \"17\", \"nama_mata_kuliah\": \"Basis Data I\"}, {\"nip\": \"198503152010121001\", \"sks\": \"4\", \"kelas\": \"IF 2A\", \"id_dosen\": \"23\", \"nama_dosen\": \"Dr. Budi Hartono, S.Kom., M.Kom.\", \"id_mata_kuliah\": \"9\", \"nama_mata_kuliah\": \"Struktur Data (P)\"}, {\"nip\": \"198604222011021004\", \"sks\": \"2\", \"kelas\": \"IF 3A\", \"id_dosen\": \"28\", \"nama_dosen\": \"Teguh Prasetyo, S.T., M.T.\", \"id_mata_kuliah\": \"3\", \"nama_mata_kuliah\": \"Teori Komputasi\"}]', NULL, NULL, '2026-01-23 21:37:00', '2026-01-27 05:37:00', 25, 'Ditolak-Admin', 'Data pengajaran semester ini tidak sesuai dengan beban kerja dosen. Mohon revisi pada bagian SKS.'),
(17, 1, 'Genap', '2025/2026', '[{\"nip\": \"197911102005011003\", \"sks\": \"3\", \"kelas\": \"IF 5A\", \"id_dosen\": \"25\", \"nama_dosen\": \"Prof. Dr. Ir. Iwan Santoso\", \"id_mata_kuliah\": \"1\", \"nama_mata_kuliah\": \"Pembelajaran Mesin\"}, {\"nip\": \"199005052015032001\", \"sks\": \"3\", \"kelas\": \"IF 3A\", \"id_dosen\": \"26\", \"nama_dosen\": \"Fitriani, S.Kom., M.Cs.\", \"id_mata_kuliah\": \"7\", \"nama_mata_kuliah\": \"Basis Data I\"}]', '320/UN46.3.4/HK.04/2026', 10, '2026-01-23 22:12:08', '2026-01-27 06:12:08', 25, 'Selesai', NULL),
(18, 1, 'Genap', '2025/2026', '[{\"nip\": \"198604222011021004\", \"sks\": \"3\", \"kelas\": \"IF 3B\", \"id_dosen\": \"28\", \"nama_dosen\": \"Teguh Prasetyo, S.T., M.T.\", \"id_mata_kuliah\": \"17\", \"nama_mata_kuliah\": \"Basis Data I\"}, {\"nip\": \"197911102005011003\", \"sks\": \"3\", \"kelas\": \"IF 3A\", \"id_dosen\": \"25\", \"nama_dosen\": \"Prof. Dr. Ir. Iwan Santoso\", \"id_mata_kuliah\": \"7\", \"nama_mata_kuliah\": \"Basis Data I\"}]', '320/UN46.3.4/HK.04/2026', 12, '2026-01-23 22:44:58', '2026-01-27 06:44:58', 25, 'Selesai', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `Req_SK_Dosen_Wali`
--

CREATE TABLE `Req_SK_Dosen_Wali` (
  `No` int NOT NULL,
  `Id_Prodi` int NOT NULL,
  `Semester` enum('Ganjil','Genap') NOT NULL,
  `Tahun_Akademik` varchar(12) NOT NULL,
  `Id_Dosen_Kaprodi` int NOT NULL,
  `Data_Dosen_Wali` json NOT NULL,
  `Nomor_Surat` varchar(100) DEFAULT NULL,
  `Status` enum('Dikerjakan admin','Menunggu-Persetujuan-Wadek-1','Menunggu-Persetujuan-Dekan','Selesai','Ditolak-Admin','Ditolak-Wadek1','Ditolak-Dekan') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `Id_Acc_SK_Dosen_Wali` int DEFAULT NULL,
  `Alasan-Tolak` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `Tanggal-Pengajuan` timestamp NOT NULL,
  `Tanggal-Tenggat` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Req_SK_Dosen_Wali`
--

INSERT INTO `Req_SK_Dosen_Wali` (`No`, `Id_Prodi`, `Semester`, `Tahun_Akademik`, `Id_Dosen_Kaprodi`, `Data_Dosen_Wali`, `Nomor_Surat`, `Status`, `Id_Acc_SK_Dosen_Wali`, `Alasan-Tolak`, `Tanggal-Pengajuan`, `Tanggal-Tenggat`) VALUES
(20, 1, 'Genap', '2025/2026', 25, '[{\"nip\": \"197911102005011003\", \"id_dosen\": \"25\", \"nama_dosen\": \"Prof. Dr. Ir. Iwan Santoso\", \"jumlah_anak_wali\": \"23\"}]', '320/UN46.3.4/HK.04/2025', 'Menunggu-Persetujuan-Wadek-1', 18, NULL, '2026-01-04 11:45:48', '2026-01-07 11:45:48'),
(21, 1, 'Ganjil', '2025/2026', 25, '[{\"nip\": \"198604222011021004\", \"id_dosen\": \"28\", \"nama_dosen\": \"Teguh Prasetyo, S.T., M.T.\", \"jumlah_anak_wali\": \"10\"}]', '320/UN46.3.4/HK.04/2026', 'Selesai', 19, NULL, '2026-02-06 07:35:34', '2026-02-09 15:35:34'),
(22, 1, 'Ganjil', '2025/2026', 25, '[{\"nip\": \"198604222011021004\", \"id_dosen\": \"28\", \"nama_dosen\": \"Teguh Prasetyo, S.T., M.T.\", \"jumlah_anak_wali\": \"20\"}]', NULL, 'Ditolak-Admin', NULL, 'masih kurang dosenya', '2026-02-06 17:56:25', '2026-02-10 01:56:25');

-- --------------------------------------------------------

--
-- Table structure for table `Req_SK_Pembimbing_Skripsi`
--

CREATE TABLE `Req_SK_Pembimbing_Skripsi` (
  `No` int NOT NULL,
  `Id_Prodi` int DEFAULT NULL,
  `Semester` enum('Ganjil','Genap') DEFAULT NULL,
  `Tahun_Akademik` varchar(100) NOT NULL,
  `Data_Pembimbing_Skripsi` json DEFAULT NULL,
  `Id_Dosen_Kaprodi` int DEFAULT NULL,
  `Nomor_Surat` varchar(100) DEFAULT NULL,
  `Status` enum('Dikerjakan admin','Menunggu-Persetujuan-Wadek-1','Menunggu-Persetujuan-Dekan','Selesai','Ditolak-Admin','Ditolak-Wadek1','Ditolak-Dekan') DEFAULT NULL,
  `Id_Acc_SK_Pembimbing_Skripsi` int DEFAULT NULL,
  `Alasan-Tolak` text,
  `Tanggal-Pengajuan` timestamp NULL DEFAULT NULL,
  `Tanggal-Tenggat` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Req_SK_Pembimbing_Skripsi`
--

INSERT INTO `Req_SK_Pembimbing_Skripsi` (`No`, `Id_Prodi`, `Semester`, `Tahun_Akademik`, `Data_Pembimbing_Skripsi`, `Id_Dosen_Kaprodi`, `Nomor_Surat`, `Status`, `Id_Acc_SK_Pembimbing_Skripsi`, `Alasan-Tolak`, `Tanggal-Pengajuan`, `Tanggal-Tenggat`) VALUES
(7, 1, 'Genap', '2025/2026', '[{\"nim\": 1121001, \"id_mahasiswa\": \"1\", \"pembimbing_1\": {\"nip\": \"199005052015032001\", \"id_dosen\": \"26\", \"nama_dosen\": \"Fitriani, S.Kom., M.Cs.\"}, \"pembimbing_2\": {\"nip\": \"198801202012112002\", \"id_dosen\": \"24\", \"nama_dosen\": \"Rina Permatasari, S.T., M.Sc.\"}, \"judul_skripsi\": \"klasifikasi hewa ternak\", \"nama_mahasiswa\": \"Adi Saputra\"}]', 25, NULL, 'Selesai', 7, NULL, '2026-01-23 18:31:43', '2026-01-27 02:31:43');

-- --------------------------------------------------------

--
-- Table structure for table `Req_SK_Penguji_Skripsi`
--

CREATE TABLE `Req_SK_Penguji_Skripsi` (
  `No` int NOT NULL,
  `Id_Prodi` int DEFAULT NULL,
  `Semester` enum('Ganjil','Genap') DEFAULT NULL,
  `Tahun_Akademik` varchar(100) NOT NULL,
  `Data_Penguji_Skripsi` json DEFAULT NULL,
  `Id_Dosen_Kaprodi` int DEFAULT NULL,
  `Nomor_Surat` varchar(100) DEFAULT NULL,
  `Status` enum('Dikerjakan admin','Menunggu-Persetujuan-Wadek-1','Menunggu-Persetujuan-Dekan','Selesai','Ditolak-Admin','Ditolak-Wadek1','Ditolak-Dekan') DEFAULT NULL,
  `Id_Acc_SK_Penguji_Skripsi` int DEFAULT NULL,
  `Alasan-Tolak` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `Tanggal-Pengajuan` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `Tanggal-Tenggat` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Req_SK_Penguji_Skripsi`
--

INSERT INTO `Req_SK_Penguji_Skripsi` (`No`, `Id_Prodi`, `Semester`, `Tahun_Akademik`, `Data_Penguji_Skripsi`, `Id_Dosen_Kaprodi`, `Nomor_Surat`, `Status`, `Id_Acc_SK_Penguji_Skripsi`, `Alasan-Tolak`, `Tanggal-Pengajuan`, `Tanggal-Tenggat`) VALUES
(6, 1, 'Genap', '2025/2026', '[{\"nim\": 1121003, \"mahasiswa_id\": \"3\", \"penguji_1_id\": \"26\", \"penguji_2_id\": \"25\", \"penguji_3_id\": \"23\", \"judul_skripsi\": \"Klasifikasi Penyakit daun jagung\", \"nama_mahasiswa\": \"Candra Wijaya\", \"nama_penguji_1\": \"Fitriani, S.Kom., M.Cs.\", \"nama_penguji_2\": \"Prof. Dr. Ir. Iwan Santoso\", \"nama_penguji_3\": \"Dr. Budi Hartono, S.Kom., M.Kom.\"}, {\"nim\": 1121001, \"mahasiswa_id\": \"1\", \"penguji_1_id\": \"27\", \"penguji_2_id\": \"26\", \"penguji_3_id\": \"23\", \"judul_skripsi\": \"Klasifikasi Pneyakit bronkitis\", \"nama_mahasiswa\": \"Adi Saputra\", \"nama_penguji_1\": \"Agus Wibowo, S.Kom., Ph.D.\", \"nama_penguji_2\": \"Fitriani, S.Kom., M.Cs.\", \"nama_penguji_3\": \"Dr. Budi Hartono, S.Kom., M.Kom.\"}]', 25, '320/UN46.3.4/HK.04/2026', 'Menunggu-Persetujuan-Dekan', 16, NULL, '2026-01-13 08:28:29', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `Roles`
--

CREATE TABLE `Roles` (
  `Id_Role` int NOT NULL,
  `Name_Role` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Roles`
--

INSERT INTO `Roles` (`Id_Role`, `Name_Role`) VALUES
(1, 'Pegawai_prodi'),
(2, 'Dekan'),
(3, 'Kajur'),
(4, 'Kaprodi'),
(5, 'Dosen'),
(6, 'Mahasiswa'),
(7, 'Pegawai_Fakultas'),
(8, 'Wadek1'),
(9, 'Wadek2'),
(10, 'Wadek3');

-- --------------------------------------------------------

--
-- Table structure for table `Ruangan`
--

CREATE TABLE `Ruangan` (
  `ID_Ruangan` int NOT NULL,
  `Kelas` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `Gedung` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Ruangan`
--

INSERT INTO `Ruangan` (`ID_Ruangan`, `Kelas`, `Gedung`) VALUES
(1, 'IF 5A', 'RKB-F'),
(2, 'IF 5B', 'RKB-F'),
(3, 'IF 3A', 'RKB-F'),
(4, 'IF 3B', 'RKB-F'),
(5, 'IF 7A', 'RKB-F'),
(6, 'IF 3C', 'RKB-F'),
(7, 'IF 3D', 'RKB-F'),
(8, 'IF 7B', 'RKB-F'),
(9, 'IF 1A', 'RKB-F'),
(10, 'IF 1B', 'RKB-F'),
(11, 'IF 5C', 'RKB-F'),
(12, 'IF 5D', 'RKB-F'),
(13, 'IF 5E', 'RKB-F'),
(14, 'IF 3F', 'RKB-F'),
(15, 'IF 3G', 'RKB-F'),
(16, 'IF 1D', 'RKB-F'),
(17, 'IF 1F', 'RKB-F'),
(18, 'IF 2A', 'RKB-F'),
(19, 'IF 1G', 'RKB-F'),
(20, 'IF 1H', 'RKB-F');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('0CLkbcVF9BsCNyphkZTPMhCRjaQid6kcukxC9g86', NULL, '139.162.7.59', '', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibWd1cVpBN0pzT2s2YmdqczROU1c2RW42QU9TU0ExeWJOZTRHRFZsOCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHBzOi8vZmlybWFuc3lhaGRldi5teS5pZCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771012582),
('1EGKOIFVPVGUtAy3mN7yqL3uhUpcDo9WhfIVYysH', NULL, '204.76.203.69', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85 Safari/537.36 Edg/90.0.818.46', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoic3l4Z0M0aW1CMndTNjJBTUdVZlFTRUg1eXIxSW5LOHhzSGpTT29yUyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771033780),
('1NRrBKMlJxiYz0n9NpuqiQtZBENwk9gWaxUvKMcY', NULL, '139.162.7.59', 'curl/7.54.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiY0ZlT0JzS3ZmT2JqTzhaVkVHa2JYMlBNVEFxQUozQWkyR2VEMG1oNSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly84LjIxOS4yMjEuMTcxIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1771012579),
('2VzXxnCSP7nbkSaFIgRIVCJhvAIYJYru2DrBn8Zb', NULL, '195.3.221.86', 'Mozilla/5.0 (X11; Linux x86_64; rv:144.0) Gecko/20100101 Firefox/144.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSjZBQWRXcGNpdzRNSFp1c2ZCZ0lyc3NGV0R1aEQ1TU9hYXhKMTFKeSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly84LjIxOS4yMjEuMTcxIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1771056918),
('4GSKIMAs4wptgHoLnEk7O9ocQ9y6Pr8yYcykqY4F', NULL, '139.162.7.59', 'curl/7.54.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiU0hJTWROYmZBd1JPN05aeWJoY3AyVXhrMmJTYjdOSDh1UVdnckl5byI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly84LjIxOS4yMjEuMTcxIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1771012579),
('4kBNLls0PxE6XsMUq33hqBHmzHxwVkplE6xKl2Ah', NULL, '139.162.7.59', '', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVTNxcDRHT3dCclRmZ3FuUEVVMTVodThjRDFEdjlBR3hHdFVWR1VLZiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly84LjIxOS4yMjEuMTcxIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1771012599),
('4v9zQgfkPCfAALNc5Q5IITCSiQqeOXdtoReLlLmd', NULL, '139.162.7.59', 'curl/7.54.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoieTBjMVZTMzVXQVN0VnlXdHVFbllzN25xMWNlWDB4a1JodEF2dVJxMyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vOC4yMTkuMjIxLjE3MSI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771012579),
('58lH1c0Wk3jBzZOftxMzysqT6ouBONE4RAev2YtD', NULL, '3.129.187.38', 'visionheight.com/scan Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) Chrome/126.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZ2pUbTRqaGVRMUlVbmRiQjY2dW5GckR5VVlLU2xhRTgyREQ3dWZoTCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly84LjIxOS4yMjEuMTcxIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1771036658),
('6JushM37TvQ2NenvS6J8VAbg2ASnD3fZhtnZGfQ2', NULL, '170.106.192.3', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMnIzSVhsNktzYnppRTI0aXo2NjdTaEY5Y2tOelNPSlRneWczOG5sZSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly84LjIxOS4yMjEuMTcxIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1771021776),
('6u8awS5kwVqpaBopFTJBJR1XmKKoCYLQkDew57fS', NULL, '92.63.197.197', 'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0; )', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZFZyNVpWRlZ3Vk1oSmFVRHUxRlRZTUVwU2E0N3hJbmxsWWswN0FjSSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vOC4yMTkuMjIxLjE3MSI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771028498),
('9ooDeetURg59NYjdMSmVJOOiwjokUwmLgyLGGZfs', NULL, '204.76.203.18', '', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidWszWEtqMEZxZUdNVnpDc1dTRVJsczdTS2NUMHBLUTh0b2ZqMDdaVCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly84LjIxOS4yMjEuMTcxIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1771043826),
('9ovyv1hAcApwa9Le5SJfScaGZl1y0ch5b5uHYNFn', NULL, '34.22.177.31', 'python-requests/2.32.5', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTEtSZmNmTTRDRldXRlQ5dndXU3R3SDJRSWV4M0FCQmtBaExoMFNDMyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly84LjIxOS4yMjEuMTcxIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1771052449),
('9Roen8ygwF9JLoNh31kJRi8rdvB1o7p8b91U1FmC', NULL, '139.162.7.59', '', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWG1qa3JXVDFsVnlVeHVzWDhSeWpUcTRvM3l4UmZjdmt3RVRxZXZYOSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHBzOi8vZmlybWFuc3lhaGRldi5teS5pZCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771012598),
('Ah21e1xEp1OLcgZMXIaSt47dr4wGEz2R4qZxJlEI', NULL, '45.135.193.11', 'Mozilla/5.0 (compatible; NetScope/1.0; +http://45.135.193.11/; security research scanner — submit exclusion requests at the linked URL)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSmdBSnRyRG53YklqS1ZIWWZ4eGNNUmFucURhdk52aDhkYVZJb3BOcSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly84LjIxOS4yMjEuMTcxIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1771031088),
('aJksKQlbfhAQdQQ4AdzDEnMUrWhkm3cnu3wcOHPK', NULL, '198.235.24.134', 'Hello from Palo Alto Networks, find out more about our scans in https://docs-cortex.paloaltonetworks.com/r/1/Cortex-Xpanse/Scanning-activity', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidlhyNmNjdmM3NFgyR2xQY1AxZERCM0V2ZzB1akFxMnlpVWtTQnJNMyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly84LjIxOS4yMjEuMTcxIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1771053312),
('alhCA9Iusnya7OtUt3JOijJWGoYbZrRNvPXnPw8i', NULL, '65.49.1.234', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/135.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZXBzZUpTMkNWMWJRUkZxZk1kV2pBSTJGQjMzb2U0TlJKWHRtRDY2QiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly84LjIxOS4yMjEuMTcxIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1771027458),
('aQZaSK9YTiw6IVtjONe8Sm8PCgLudSAgxopS4oTY', NULL, '79.10.139.244', '', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibFR5eWM0UUR5NncwdVVRRW9GdGNNUlRYb3BwS2VZSFNBaEFMM2JNNyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly84LjIxOS4yMjEuMTcxIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1771005721),
('bfvQKZ3dlXMmU1GpGoetDE6mrcLZzdOfEAi7N8oI', NULL, '95.215.0.144', 'fasthttp', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibjUyWXAwNFA4UzBzZXpDMERIejI0dVhLaUM3S0k2QWg3aTNMWGo5UyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vOC4yMTkuMjIxLjE3MSI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771023163),
('BhCxkk2qQW7xijfJ4ZWy9JNXfWq6tAvJ90lhZRyF', NULL, '35.203.211.138', 'Hello from Palo Alto Networks, find out more about our scans in https://docs-cortex.paloaltonetworks.com/r/1/Cortex-Xpanse/Scanning-activity', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSlRseDFIcVN6bEVmeTU0b3lCZFdIbUljc040ek13TXZZa1NnNnhKQSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly84LjIxOS4yMjEuMTcxIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1771029594),
('bhtSEDFCTto2IuQZcZNONrs7fxJWeA7OJsGMmnTT', NULL, '91.230.168.106', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:134.0) Gecko/20100101 Firefox/134.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSEtaZElDU2N1bXE0bXdsajlmZ1d2YlZBc2RCSjVTSnNXbGpwSENKUSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vOC4yMTkuMjIxLjE3MSI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771006897),
('c2CpHge74AEEyOiGOZnSDLb9QbKL3DGmKu4K2KWk', NULL, '20.163.14.222', 'Mozilla/5.0 zgrab/0.x', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiV1l5Zm5yM0ZaZ3g4ekZqT25HZlVUbEdlek9XRW9vR2xDQlVKMmN0UCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly84LjIxOS4yMjEuMTcxIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1771031868),
('cFzTcPXVuGvAzHZrxjRiQ6HXnGEjGsSBmK7zcfO7', NULL, '3.131.220.121', 'visionheight.com/scan Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) Chrome/126.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoid0hVczRWZUZ2bTlHUEJYQVlsNjZtRE5LaHhpMFFVTndEOEZ4UVNKWiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly84LjIxOS4yMjEuMTcxIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1771038210),
('CgZSbLuJjWPEboecoUVgoqD6CvDrlHQmsOJ8aSQn', NULL, '176.65.139.8', '', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWmZaVzA3RjZ5SnJHU3VoSWVtVGRza0Q2MkF6cXRZYXQ1V3EwWENKYyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly84LjIxOS4yMjEuMTcxIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1771039896),
('CLy5IBn3Gn6wBA8wMiYB7AJShhj0eC3GaGRWkWk3', NULL, '45.156.128.131', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.113 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRXB5MkhTYXlieE1YVXFQMGNLVXpRemxtRUFWN1ZudUpxNjg5TzV6cyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly84LjIxOS4yMjEuMTcxIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1771022538),
('CVJcT2F3L3r5aAbWSYPaanf2kU6JUL7eDjQr0wmv', NULL, '185.177.72.22', 'curl/8.7.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVUV1OGtLMWFvM2RjZ2llZHlIUWdiMTl1RTFiTjVDWGk0aEV6MkR4bSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vd3d3LmZpcm1hbnN5YWhkZXYubXkuaWQiO3M6NToicm91dGUiO047fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771049749),
('DirEuQOXLmORNMmxu3hxSelIYscqb9d0dLdtTvSX', NULL, '188.225.105.171', 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoib0RTdHY1dmU1Skw5VDNnR28waU1sdnlqdFRDRVJ5ZHI5bFNqbjZJaiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly84LjIxOS4yMjEuMTcxIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1771010589),
('doBOY2vURWVaSoqvimpFdbOW8fPLxQV0xAxsiIRa', NULL, '204.76.203.18', '', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoieUNobXRxM0tCcjc4b212Vzg2WFdmdmlwSWJmYmJlQml6NnRJbklSRyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly84LjIxOS4yMjEuMTcxIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1771043760),
('f37DUIoXLBJFbGvNG5uS9gYqoXxc8FwrYAIDeivq', NULL, '185.177.72.22', 'curl/8.7.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVUtzNjBRZEY5OFpSakowZ2ttV2dwMm90aVZhNUZuUENScmd1UFo3bCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHBzOi8vZmlybWFuc3lhaGRldi5teS5pZCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771049748),
('FQncZYVws2hZNhFC88OmGXE5HrcRIx68IydLN75u', NULL, '167.94.138.186', 'Mozilla/5.0 (compatible; CensysInspect/1.1; +https://about.censys.io/)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQlNhRXh3YkVQWmxJVGJQanNkbE8zVGV0TFZ0TXpLQXNva1F4MHVwSSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vOC4yMTkuMjIxLjE3MSI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771019219),
('g0TBmDRutNsYNsZ35cuVrARHIR7BPlOZmhYCGVP3', NULL, '3.131.220.121', 'visionheight.com/scan Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) Chrome/126.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicEluU1BKa1ZNZzEweWRVb0REWTFCdVh5ZWlmcFZBcXlYSXg5VmxTRCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly84LjIxOS4yMjEuMTcxIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1771038443),
('GC2baBK1c9nzISN6EKWkh4THRZXGywjyuKgS8zAj', NULL, '167.94.138.170', 'Mozilla/5.0 (compatible; CensysInspect/1.1; +https://about.censys.io/)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiak83SkljU2M1U3VkYVJwWmJPa0Z2SDZKZmdWNDVOT3I0azlSbUhraiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly84LjIxOS4yMjEuMTcxIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1771044486),
('gcfQeTMJx0fnn0G5lOkCQrOsUQzloqNooYC8BrRq', NULL, '176.65.139.8', '', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoieUU4MlJMM3h0bndmOXdJS1FSRDRSbzlUWmFWSmN0QXgwQkJwTXFORyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly84LjIxOS4yMjEuMTcxIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1771011578),
('HdyUiRFYuopnhJhjMXLstao0lPMhZnmBPyohIvbr', NULL, '139.162.7.59', 'curl/7.54.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiM1cwclJMNXBQTGhmcEw2MEFMbmlRZmM0SUxDNmZIalllYWFUQUN3eSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vOC4yMTkuMjIxLjE3MSI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771012581),
('HjDfiqHw2Vg9w3OJcSK5480ziJQz641n5kAdqGvp', NULL, '139.162.7.59', 'curl/7.54.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMDZzYUpnYW9STlBFdmdSdTdBUE9pSHlWMGVFaHBERWJTS3ZqT2IzNyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly84LjIxOS4yMjEuMTcxIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1771012581),
('Ht2WphpKJ0GKPXnEveA9ojapnf01oEphEnXoxrPE', NULL, '65.49.1.226', 'Mozilla/5.0 (X11; Linux x86_64; rv:140.0) Gecko/20100101 Firefox/140.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiV0F2MllydVMyV2NydzhVRFY3bHRldHF5VTVzN3RaRmd6c0thZnE3QyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vOC4yMTkuMjIxLjE3MSI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771052013),
('HVgV1zUcPYDcKpDqsDGItUpcChJaMahlfZelyi7Z', NULL, '139.162.7.59', 'curl/7.54.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMWZuY2xVY2hpWlpMOXVzbkVFNDJtbm1mWW9wdnU1bVZ3bTZlbE1xQyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly84LjIxOS4yMjEuMTcxIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1771012578),
('HvRIJaYvstibVL0WGGtQOWlt0o0oVD4WYMiT3Tcd', NULL, '139.162.7.59', 'curl/7.54.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWjNueEtIT2tOTzdiSUJDSXFaV0ZPYktFcDdEN3BqQ2RFS0x0MWd4cyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vOC4yMTkuMjIxLjE3MSI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771012579),
('hXYBb4qoJ9VolQs3m0KDxIQn8q6awGR2Qls6OrRK', NULL, '139.162.7.59', 'curl/7.54.0', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiWHBhS3JZTWo0OU51NDV3NnN2U3JJRXR5dUc5dGxmTXRSOGxJQXlHMSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771012579),
('ifMsROvNSNhNOxSf42OrpYWR75oFuQx5sMVxCn8L', NULL, '95.215.0.144', 'fasthttp', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTVo0aDNhZHNzUUJIaURnTWhRU2dHOW9yWGZURUdYWXpGT2ZCZVV6NSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vOC4yMTkuMjIxLjE3MSI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771023162),
('ImGKAmSUM8SuX9ExJXBuzaGBT4jYaE4LqEN9gbpr', NULL, '139.162.7.59', 'curl/7.54.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTzJrNFpZYzI4TElUajkxVXpKcVAzZUlldFJ6ektkcjRBdlpRVEpEMyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly84LjIxOS4yMjEuMTcxIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1771012579),
('IS1bgtpQStXARVvhO4ThVjVRfzXKmN40ajIWzeYt', NULL, '192.241.154.87', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/118.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoickxYNVMzdnNXWlV1QmU0UGQ5c1JHUzFvS0JySTA2Yk9BSTh5U2tUcCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vOC4yMTkuMjIxLjE3MSI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771010237),
('j5Zh5UWEXYH5JIEXUvjXz4ImvLFcSHj3fHyuTFMf', NULL, '95.215.0.144', 'fasthttp', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoieXVybFhJbldwQXJJVlZuV2FGSnNwU1kzOGFjQnNsRUZYM21rZ1RiaCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vOC4yMTkuMjIxLjE3MSI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771023161),
('JajGBvRaejIUnZW0r23vE5nzHbmSpi4vTtlIzanu', NULL, '204.76.203.18', '', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNlMxaGNYNGlUZ3FiUmt1V2gyZElUbmVzYWs5NlhVTHV3UHBLWTRWOCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly84LjIxOS4yMjEuMTcxIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1771043826),
('jYMQBfmZdn1qZ5TNAAawuYkgjX3IViQDVPb5hrKF', NULL, '162.142.125.119', 'Mozilla/5.0 (compatible; CensysInspect/1.1; +https://about.censys.io/)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiU3JSVEVMVGtRWjZyWldKWVhhcjVrWEhxSzdUb1FVRTB2OExLTGE0ayI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly84LjIxOS4yMjEuMTcxIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1771044522),
('jzEvqsgWJ8rmDFSjU2zc5WbYi6JIkGwubveiTlKL', NULL, '185.242.226.106', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.190 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoieVNhT29IMVc0d3ZEa3A3S0tNRDI0MDN4UHVjSHEzS0xKTGw0cWN3bSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly84LjIxOS4yMjEuMTcxIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1771057473),
('kd0vdXl2qZfFJerBnMA2WqhriKQ57Cyrh0ETMcQl', NULL, '71.6.134.235', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZk1GaHMyVVI3S1ZIRHZTaGZxOVROOVdxRGhyY3dGbkJqblA0aGRTdSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHBzOi8vZmlybWFuc3lhaGRldi5teS5pZCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771017657),
('kpkDBVHyKOL3AVUvVe4QxDR9nUxl2XTTiHR0rMpF', NULL, '199.45.154.149', 'Mozilla/5.0 (compatible; CensysInspect/1.1; +https://about.censys.io/)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMDhDNFZybUIwMjNpeHJNZTNXRllTV2hYUjNVMG12Z3I0aEJuM1RLVCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vOC4yMTkuMjIxLjE3MSI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771024306),
('lQCTtcRTZDcO1dRdUCvOBxylNLwvN53k4wdwnowK', NULL, '139.162.7.59', 'curl/7.54.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTmdNSWtuZHN5WjAyZ1RLbDZCT3lMYUZYek1xRGJyUGwyUXhhbzhBRyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly84LjIxOS4yMjEuMTcxIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1771012579),
('LUQASrnSYjWrxCVJKo1Tegyh4BQFA7zxhg5kXLFM', NULL, '66.132.153.113', 'Mozilla/5.0 (compatible; CensysInspect/1.1; +https://about.censys.io/)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZHVJN0d3b2JjM1luNDgyVW1TdGlYc2hZZTNSVlBrZHg5eEdlRWRDRyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly84LjIxOS4yMjEuMTcxIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1771052994),
('Mm3CQpARsmkY9UE6VQMceqzHnHz4tD7ceLrgjC1G', NULL, '45.156.129.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.113 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidzNHUGxCNEZ3ZXcyWjdHWGdwOGtsUDFoRjh0R1ZCT0RqVmV5SjNRdCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly84LjIxOS4yMjEuMTcxIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1771046077),
('mOEpASd3AlDIzS3fbSiXjUpOUhCJ7RJpFO0dqVO3', NULL, '20.15.225.63', 'Mozilla/5.0 zgrab/0.x', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiT1A5NGI5d0ZiVm96bTR1NXV4aklJNTFoOU1udVhmcWxCaW5TZVFlTiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vOC4yMTkuMjIxLjE3MSI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771033532),
('mTX7JOCxSY3o9QqccGIIsnPUAXhYUSWj5KEOx9bt', NULL, '45.135.193.11', 'Mozilla/5.0 (compatible; NetScope/1.0; +http://45.135.193.11/; security research scanner — submit exclusion requests at the linked URL)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSjFxQlV4d1R3Rk1KSjVUczk5MXVOcGc1WEY5Umg2QVFYOUR2M0pBSSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vOC4yMTkuMjIxLjE3MSI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771057135),
('n2uXfenUo6b2gCeQJRVs5MQbRhS3RQEb8GN4Ztna', NULL, '195.3.221.86', 'python-requests/2.31.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiV3BBQmc4bnRGdjBNeVFNQWNoUlI4Tko5cG1YaURSRHpJNktMSDNJQSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly84LjIxOS4yMjEuMTcxIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1771040813),
('N8JcCjYeFFLtRrzHrZk3djCN6SmVIHA0zy3mwXHB', NULL, '65.49.1.222', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Electron/2.0.18 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicjZ4UEo0bzJZVlBJVlRtSWNGZXpsS1pzdWcwZlBEQlJiZ2VQeFN1dSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vOC4yMTkuMjIxLjE3MSI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771051361),
('n8nuOIaoxtXnbQIXM9Mh68BH9pGuvA4DNSmQ3LIC', NULL, '164.92.220.178', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQ2NMUFV5Nm0xSXRGTElFRnZlWEpuRndaTVNHbXh2dXhiNzVMbWdXMCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vZnRwLmZpcm1hbnN5YWhkZXYubXkuaWQiO3M6NToicm91dGUiO047fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771039417),
('NCLHQsFcuxUkUYQppC0gu238B5arw2IaZuUMuLqU', NULL, '139.162.7.59', 'curl/7.54.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQ3ZGT3ZDVVhmZzNyUzV6V1RwMXJZT0ZvWUxGdFl6SDBNOUNaUWNBaiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vOC4yMTkuMjIxLjE3MS9pbmRleC5waHAiO3M6NToicm91dGUiO047fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771012579),
('NrZW1MUxWKTYy4SA1BsjiGB77XYwZSmLzeI7rN0D', NULL, '176.65.139.8', '', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiU2tSQUdNZ3dpaExycUNReW02dEpRWHNpUENwSTltTXJrMHhhajFjMiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly84LjIxOS4yMjEuMTcxIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1771043953),
('ojpmJ41WNiHRgS95gPTxCMDRMJ96xYXk1MqtDEZs', NULL, '185.177.72.49', 'curl/8.7.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiOU9sR1BvNVR6dEVVQzdwTGs0Mm13cTV4dWhpY3dYa1k5VDlaVENSUiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vOC4yMTkuMjIxLjE3MSI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771049624),
('OJRaQNCtSnpsNyoGOfHRtsrrOY9aR72tZlskSlso', NULL, '45.135.193.11', 'Mozilla/5.0 (compatible; NetScope/1.0; +http://45.135.193.11/; security research scanner — submit exclusion requests at the linked URL)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSWlKRG1Va3dqRWxNWjl1SWlnODYzSXBWcUdMZTVTTXFvZlB0TWpnZSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly84LjIxOS4yMjEuMTcxIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1771058143),
('OsmKzfcYPjqbGnXtdDmgOPNeVUD8YbDyyko6CsPA', NULL, '162.216.150.163', 'Hello from Palo Alto Networks, find out more about our scans in https://docs-cortex.paloaltonetworks.com/r/1/Cortex-Xpanse/Scanning-activity', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiT01VTHJFdDdIRUJ0WnUwOHNqbWxaYURtc0FoUkJ4NVVmYUxPSlo3VyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vOC4yMTkuMjIxLjE3MSI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771046040),
('P9uau224gu4sH29lxwyw28lNluK4O6XxRxNbV8fG', NULL, '139.162.7.59', 'curl/7.54.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNWx5SUR3c3lDSWlPRjBNZ3RUMmluY2tER1lFT3NZU3RQU2tXazVDSCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzA6Imh0dHA6Ly84LjIxOS4yMjEuMTcxL2luZGV4LnBocCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771012581),
('paOoyFNMJev8TXfjHLsyrnChwm9vItfE42N3otcG', NULL, '139.162.7.59', 'curl/7.54.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRVF6VW9mQTh0TnVHUUZRbFFkQ09GcWUxTlBSSUhkVkVqa3BDV1ZNTyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vOC4yMTkuMjIxLjE3MSI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771012581),
('pCDZmv93FZJRXsldeLwSAwWqFGSMMBZ33FDFlBqm', NULL, '45.135.193.11', 'Mozilla/5.0 (compatible; NetScope/1.0; +http://45.135.193.11/; security research scanner — submit exclusion requests at the linked URL)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiOU9lYXlMbXQ5dFJzczRtdGNhZjF3SENvRTd0NEc5QXQyZkx1YTY1OCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vOC4yMTkuMjIxLjE3MSI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771031065),
('PRxHKKfa8KeOI8uqnJI6M0716AXYJGICMpofbIR7', NULL, '81.29.142.100', 'Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.107 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWVhCcEtTTGlOZ291c2phMHhicWtLUTQ4dFE1UE8yQUZhem02NThmQyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly84LjIxOS4yMjEuMTcxIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1771044503),
('Pwd2tWjT9fQfm9OHw1X3eojEqLFkUDejGkwt8W54', NULL, '176.65.139.8', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiekFDekRXS1J0a0FKeWhJMTFHSXRWQXJsT2dpTlVYUG5scjgzeUdkNCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly84LjIxOS4yMjEuMTcxIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1771021049),
('q8HvYEWSg101wZuaLIZdv0bMjfwQu6lL8NeOvNNw', NULL, '139.162.7.59', 'curl/7.54.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMjRla1VLWVF4bkV2cVBpYjhpMGVwUGRtcEFuN1owNDU3d2NCOVdoeiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vOC4yMTkuMjIxLjE3MSI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771012578),
('qCnRFVCtBzzTmoSPdouNlyVUhl31rN2wi1Zp27P4', NULL, '52.36.126.158', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidGV3Z3pLWDRTRTlNQ0JrMXl4SzlNZmZ6bmJVWGhNWkpYeExNNHhHMSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly84LjIxOS4yMjEuMTcxIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1771022875),
('qCoqDz4lxbloApfLuoRhvBC819Kr3WofN7wmkVWr', NULL, '43.166.238.12', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWmppS0RLUmRacGVoYVI4ZlJMU1lvbXV5Z2dkdkdFSUhHNzR6SlF3ZyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly84LjIxOS4yMjEuMTcxIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1771025936),
('qFTLT9kWLvgFxe8oh0koO3TEFIP7KHQpYiigJg0H', NULL, '69.5.23.23', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.113 Safari/537.36 Assetnote/1.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSHF2a1libHlXbzh0SlA0RkU2WDNBRzZxYjViUTBKV3JCM0RhTHY4diI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vOC4yMTkuMjIxLjE3MSI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771034270),
('qkUrGmVIB9d6NoYK89RCNsOBywXiKFwCXZjI1kR5', NULL, '87.121.84.15', 'Mozilla/1.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiOHBzdjlsaTFiSld4TmJWQ1VhTFNDNnd2OGJmZGhWSUJqWDNtTTdSaiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly84LjIxOS4yMjEuMTcxIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1771024261),
('qUogAXIdTe4kJjqtKQuw9EstIT3t130ro9hMcOEM', NULL, '204.76.203.69', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85 Safari/537.36 Edg/90.0.818.46', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiT25DdmU3Ukt1Q1dURzFTSTNFRWxpMUpGekpzNExtbUE4NGhlMmxrbyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771006739),
('qX8SIj7aKXy7oXDMXhU0vlK2fqYpMThOabXyRwgu', NULL, '204.76.203.18', '', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoib0dVYzE2T2lZZ3diNzNleHNWeFJrbTJnYnB1WnMxeE9IWjNvSVZIViI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly84LjIxOS4yMjEuMTcxIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1771043760),
('RIf9n8Ux7HhE3GF6TgyFeLlmlJ8JI14yEchwKsNU', NULL, '164.92.220.178', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWDFjY1hEY3hzaUNZUFhHTmIyZm41RW1PNjc2ejFFTDk4NU1sSUdhUSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzA6Imh0dHA6Ly9mdHAuZmlybWFuc3lhaGRldi5teS5pZCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771039408),
('rMiVGlpP5gzrzrlVDgBz6bXkS4PyVatHR3IBb9bM', NULL, '139.162.7.59', 'curl/7.54.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiYXpMNHFkQjhTVks2elpqZ0hqWnFDVHV3WlR0N1A3R2RQQUdFazlJQiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzY6Imh0dHA6Ly84LjIxOS4yMjEuMTcxL2luZGV4LnBocC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771012581),
('RPcjT1cHNOIIl3zdsW1EIvtoeW6X2yhhVDNuBTIX', NULL, '176.65.148.203', 'Mozilla/1.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUlB6NFlGMkZxQjBkVWNFVnlJUHdnNTVPbnk2VUdsM3JnenhuMWdvUiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly84LjIxOS4yMjEuMTcxIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1771047760),
('ryYgnuOkfD7GqX64IvKY7t0Di6yKr7X66hUZbkEj', NULL, '139.162.7.59', 'curl/7.54.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiOXRQMWRiTVk5OVNLbmg4RVc3S2paUTJldFBObERab2Z0c2xJSlFFMiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHBzOi8vOC4yMTkuMjIxLjE3MS9pbmRleC5waHAvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1771012579),
('S9LwxzEmDnfwDUlrg8adeR5R8aXpaiErZhN7lLFC', NULL, '66.132.153.131', 'Mozilla/5.0 (compatible; CensysInspect/1.1; +https://about.censys.io/)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoieHR6S2djOUt0S3VqR3RkMzdoZWNqWGNIR1JWVjZUSzV5TExXUUpZSiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly84LjIxOS4yMjEuMTcxIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1771044551),
('SJsvpPEfwqGHyGxmGEWNjVNWQO1kR24dF7xNptLq', NULL, '139.162.7.59', 'curl/7.54.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSGtOVmRjS2I2bks1bGo5dm5BQjU2dlRlNVp1SjRoZUgyRzBNNGpyaiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly84LjIxOS4yMjEuMTcxIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1771012579),
('sKH8bXSsWzLQWo3zY7O5hZ38hWmMoT5fSdF3OYYY', NULL, '216.180.246.71', 'Mozilla/5.0 (compatible; GenomeCrawlerd/1.0; +https://www.nokia.com/genomecrawler)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQUlBcUdIRkxEeVlSODFlR0tkSVhMR1J4TGJyS004ZGR4RXVGNlRyWCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vOC4yMTkuMjIxLjE3MSI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771049311),
('T98MQpyieokgSddjKqkYuGuL6twbi64MfHOWQzf2', NULL, '204.76.203.69', '', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMGJrSGtkWWhnUzkyZUw5eGt5MGR3ajBaR3d4UjdwQVNiSUhqSDUzaSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly84LjIxOS4yMjEuMTcxIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1771018916),
('TPMKLVMtW03SJMn7mRek0UenUjU2BqZR498bl7W7', NULL, '109.164.199.61', '', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidUhUMHYycGN5bnRMa1dXS1RlelFIUzgzcWxyV3JHbmtZMWd1aWs2YyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vOC4yMTkuMjIxLjE3MSI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771042689),
('UB2gi2kX83BbBmIf2TlXSlesDH2lLVvIGbZZj01N', NULL, '95.215.0.144', 'fasthttp', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoid2FtR3BudTJuRlY1TUJqUVJ1bk5RaW54RDRCUFRIWjV2QjN6MmY3MSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vOC4yMTkuMjIxLjE3MSI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771023161),
('uEatYNmXDGUFHmfLXlS3DLVokpql9uJP6bfUXSWl', NULL, '204.76.203.18', '', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicXVQWHFna0RIOGdMeU9MMjh0ZlBQdzJrSUh6YVNxZVlwZkozNHVEUSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly84LjIxOS4yMjEuMTcxIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1771043760),
('vIEOEP0gs2tsl6jTNzUmMO8voOoNteFCaIJH36Vo', NULL, '204.76.203.69', '', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTERUM0dNdTNVaTJoV2k3MEwzb2pkZm5FbGdIQkpZbWUwTmozdlZlViI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly84LjIxOS4yMjEuMTcxIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1771044147),
('VVAK3iWcHqEXBcwJbd9a6LQOcJkXfs8Y3CgQ6Mej', NULL, '139.162.7.59', 'curl/7.54.0', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiNzFkeVU3WTZ0QllMVG9iaU5BZm91MTh2bWI5ZG9EcDlvWk91UE5lMiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771012580),
('WFhP6HhQtII8aVSYwY5w8B5mohwm34Ds8gOWx8CD', NULL, '195.3.221.86', 'python-requests/2.31.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTDJVc2xwVlM0Y3BUTUFVa2ZIekY2ZnhzWjhTUTI3ajQ2VlB2eUR3ViI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vOC4yMTkuMjIxLjE3MSI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771049098),
('wgmBwHfFZiTtFw36R9hjJCgBpZsl0ijkSk6Crk9Y', NULL, '176.65.139.20', 'Go-http-client/1.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibTIzUkxxSHpPU2VpZzZERDBUVXR0dHpkTUdDc2ZVbmxOVlY4ODc3MiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly84LjIxOS4yMjEuMTcxIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1771026183),
('WMKJb89qallCJ0EUFnHds5MIW6WtRI2rRVEzzMTj', NULL, '95.215.0.144', 'fasthttp', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUjc0amRXMFBPRk1oeU9JaWZ1Q0k1dGxKVU1RTE5ydG1NQmowNW9CNCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vOC4yMTkuMjIxLjE3MSI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771023163),
('wT44EdSzUmzyxcPiojOf7Nf86amCCfmpD0yeGvhJ', NULL, '65.49.1.232', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRko5NXBNaFY2NEtiSVBVVWFmbXY0SWJ0RU1zQXE1QWhqWGlEc2xSSyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly84LjIxOS4yMjEuMTcxIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1771027344),
('wxKEyFMR4FecHYpT5bxXKnzQpY4ImjJvMtIPOmY6', NULL, '139.162.7.59', '', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQnZBa2FlWmdmTHpuNEREVUdTd1dGVGRGY2dMWFJBQmQ5R09Hb05IcCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHBzOi8vZmlybWFuc3lhaGRldi5teS5pZCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771012578),
('x6J8fVr6Zz21tbMnlcveZGUoHqJobCA6OeuEY8oY', NULL, '139.162.7.59', 'curl/7.54.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZmxid2FFRlEzTm54TmpqNURsVE9VOTh1NGx5NDR3cHRYZk4yNUlMcSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vOC4yMTkuMjIxLjE3MSI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771012578),
('xAApCiweyJ7HVGTiw0Jhu8XiYoerGIVEkP1vb94v', NULL, '152.32.201.119', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36 Edg/120.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMnI1aDdGd0w5cUpDUnVyejRKME1rNXZ2Qm9tT0t2QVh1MFZsVjZVUCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly84LjIxOS4yMjEuMTcxIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1771024219),
('xAvfWL1DRKWxgA1ZjgBY2eTfkzi7v0qfyOPcXVSs', NULL, '81.29.142.100', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.152 YaBrowser/21.2.2.101 Yowser/2.5 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiYkZxb0xEUndDZjRxUEhlOWxIR0hiZmVhWFlqY3VsZjhLQWNFSHdPdiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vOC4yMTkuMjIxLjE3MSI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771038016),
('xVz7qqxsuPSiNczQZnmy0lfYXS98jzBdXHjNqTur', NULL, '139.162.7.59', 'curl/7.54.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiYnkzTHFOQkpYZUVNNDF4SEFiQzZLWWZibVo1ZzFtOW5VNUVNdzJUMCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vOC4yMTkuMjIxLjE3MSI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771012579),
('y6Hw02R9Az4QoBZsycbgULkIrcdBHWUenCvEShV5', NULL, '139.162.7.59', '', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiS3hhUkg0SFdwUXBWeXRyYW45akJVYzFNYnR0TGVtVmZLQllnd0x4TiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vOC4yMTkuMjIxLjE3MSI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771012599),
('zmBWLpWOZzASiWy11wrwgkaSEsW3Fn1ogDvstR51', NULL, '45.84.107.74', 'python-requests/2.32.5', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMWpLaDlYM3BaYTk1OHZWNUt0Y0c0cVNLRkFibHlnNE5lWWNSRUhUaiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vOC4yMTkuMjIxLjE3MSI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771049711);

-- --------------------------------------------------------

--
-- Table structure for table `Surat_Dispensasi`
--

CREATE TABLE `Surat_Dispensasi` (
  `id` bigint UNSIGNED NOT NULL,
  `Id_User` int NOT NULL COMMENT 'User yang mengajukan',
  `Id_Pejabat_Wadek3` int DEFAULT NULL COMMENT 'Wadek 3 yang menyetujui',
  `nama_kegiatan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nama kegiatan/alasan: Sakit, Lomba, dll',
  `instansi_penyelenggara` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Kosongkan jika sakit/pribadi',
  `tempat_pelaksanaan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Kosongkan jika tidak relevan',
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `file_lampiran` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Undangan atau Surat Dokter',
  `nomor_surat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nomor surat diisi admin setelah diproses',
  `Status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'baru',
  `Tanggal_Diberikan` timestamp NULL DEFAULT NULL,
  `Tanggal_Diselesaikan` timestamp NULL DEFAULT NULL,
  `verifikasi_admin_by` int DEFAULT NULL COMMENT 'User admin yang memverifikasi',
  `verifikasi_admin_at` date DEFAULT NULL,
  `acc_wadek3_by` int DEFAULT NULL COMMENT 'User yang acc sebagai Wadek3',
  `acc_wadek3_at` date DEFAULT NULL,
  `file_surat_selesai` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'File surat yang sudah ditandatangani',
  `keterangan_status` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Catatan/alasan jika ditolak'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `surat_izin_kegiatan_malams`
--

CREATE TABLE `surat_izin_kegiatan_malams` (
  `id` bigint UNSIGNED NOT NULL,
  `id_user` int NOT NULL,
  `id_pejabat` int DEFAULT NULL COMMENT 'Wakil Dekan 3',
  `nama_kegiatan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nama kegiatan yang akan dilaksanakan',
  `waktu_mulai` datetime NOT NULL COMMENT 'Waktu mulai kegiatan',
  `waktu_selesai` datetime NOT NULL COMMENT 'Waktu selesai kegiatan',
  `lokasi_kegiatan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Lokasi pelaksanaan kegiatan',
  `jumlah_peserta` int NOT NULL COMMENT 'Jumlah peserta yang ikut',
  `alasan` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Alasan mengadakan kegiatan malam',
  `nomor_surat` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'baru',
  `Tanggal_Diberikan` timestamp NULL DEFAULT NULL,
  `Tanggal_Diselesaikan` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Surat_Kelakuan_Baik`
--

CREATE TABLE `Surat_Kelakuan_Baik` (
  `id` bigint UNSIGNED NOT NULL,
  `Id_User` int NOT NULL,
  `Id_Penerima_Tugas` int DEFAULT NULL,
  `Id_Pejabat` int DEFAULT NULL COMMENT 'Wakil Dekan 3',
  `Keperluan` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tujuan penggunaan surat',
  `Semester` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Semester saat pengajuan',
  `Tahun_Akademik` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tahun akademik saat pengajuan',
  `Nomor_Surat` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'baru',
  `Tanggal_Diberikan` timestamp NULL DEFAULT NULL,
  `Tanggal_Diselesaikan` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Surat_Ket_Aktif`
--

CREATE TABLE `Surat_Ket_Aktif` (
  `id_no` int NOT NULL,
  `Nomor_Surat` varchar(255) DEFAULT NULL,
  `Tahun_Akademik` varchar(255) DEFAULT NULL,
  `Deskripsi` text,
  `KRS` text,
  `Id_Pemberi_Tugas` int DEFAULT NULL,
  `Id_Penerima_Tugas` int DEFAULT NULL,
  `is_urgent` tinyint(1) NOT NULL DEFAULT '0',
  `urgent_reason` text,
  `Status` enum('Dikerjakan-Admin','Diajukan-ke-Dekan','Selesai','Ditolak-Admin','Ditolak-Dekan') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `Tanggal_Diberikan` datetime DEFAULT NULL,
  `Tanggal_Diselesaikan` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Surat_Ket_Aktif`
--

INSERT INTO `Surat_Ket_Aktif` (`id_no`, `Nomor_Surat`, `Tahun_Akademik`, `Deskripsi`, `KRS`, `Id_Pemberi_Tugas`, `Id_Penerima_Tugas`, `is_urgent`, `urgent_reason`, `Status`, `Tanggal_Diberikan`, `Tanggal_Diselesaikan`) VALUES
(16, 'B/7345/UN46.3.4/PK.01.06/2026', '2025/2026', 'beasiswa', 'uploads/pendukung/surat-aktif/iawbnv8rSUZLqU9eGFEkCTJqYcZhDjFWKsRnSfjA.pdf', 201, 101, 1, 'beasiswa', 'Diajukan-ke-Dekan', '2026-02-05 13:48:31', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `Surat_Legalisir`
--

CREATE TABLE `Surat_Legalisir` (
  `id_no` int NOT NULL,
  `Id_User` int NOT NULL COMMENT 'User yang mengajukan',
  `Id_Pejabat` int DEFAULT NULL COMMENT 'Pejabat yang berwenang',
  `Jenis_Dokumen` enum('Ijazah','Transkrip') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Jenis dokumen yang dilegalisir',
  `Jumlah_Salinan` int NOT NULL DEFAULT '1',
  `Biaya` int DEFAULT NULL COMMENT 'Biaya legalisir',
  `File_Scan_Path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `File_Signed_Path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Is_Verified` tinyint(1) NOT NULL DEFAULT '0',
  `TTD_Oleh` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Dekan atau Wadek1',
  `TTD_At` timestamp NULL DEFAULT NULL,
  `Tanggal_Bayar` date DEFAULT NULL COMMENT 'Tanggal pembayaran lunas',
  `Status` enum('pending','verifikasi_berkas','menunggu_pembayaran','pembayaran_lunas','proses_stempel_paraf','menunggu_ttd_pimpinan','siap_diambil','selesai','ditolak') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Surat_Magang`
--

CREATE TABLE `Surat_Magang` (
  `id_no` int NOT NULL,
  `Data_Mahasiswa` json DEFAULT NULL COMMENT 'JSON: {nama, nim, prodi}',
  `Data_Dosen_pembiming` json DEFAULT NULL COMMENT 'JSON: {dosen_pembimbing_1, dosen_pembimbing_2}',
  `Judul_Penelitian` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Dokumen_Proposal` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Path file proposal mahasiswa',
  `Surat_Pengantar_Magang` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Path file surat pengantar dari instansi',
  `Nama_Instansi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Alamat_Instansi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `Tanggal_Mulai` date NOT NULL,
  `Tanggal_Selesai` date NOT NULL,
  `Foto_ttd` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Qr_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Qr_code_dekan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Nomor_Surat` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Nama_Koordinator` int NOT NULL,
  `Nama_Dekan` int NOT NULL,
  `Nip_Dekan` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Acc_Koordinator` tinyint(1) NOT NULL DEFAULT '0',
  `Acc_Dekan` tinyint(1) NOT NULL DEFAULT '0',
  `Status` enum('Draft','Diajukan-ke-koordinator','Dikerjakan-admin','Diajukan-ke-dekan','Success','Ditolak-Dekan','Ditolak-Kaprodi') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Komentar` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `Id_Pemberi_Tugas` int DEFAULT NULL,
  `Id_Penerima_Tugas` int DEFAULT NULL,
  `Id_Jenis_Surat` int DEFAULT NULL,
  `Id_Jenis_Pekerjaan` int DEFAULT NULL,
  `Judul_Tugas` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Nomor_Surat_Tugas` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Dari Tugas_Surat',
  `data_spesifik` json DEFAULT NULL,
  `signature_qr_data` text COLLATE utf8mb4_unicode_ci,
  `qr_image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Tanggal_Diberikan` date DEFAULT NULL,
  `Tanggal_Tenggat` date DEFAULT NULL,
  `Tanggal_Diselesaikan` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `Surat_Magang`
--

INSERT INTO `Surat_Magang` (`id_no`, `Data_Mahasiswa`, `Data_Dosen_pembiming`, `Judul_Penelitian`, `Dokumen_Proposal`, `Surat_Pengantar_Magang`, `Nama_Instansi`, `Alamat_Instansi`, `Tanggal_Mulai`, `Tanggal_Selesai`, `Foto_ttd`, `Qr_code`, `Qr_code_dekan`, `Nomor_Surat`, `Nama_Koordinator`, `Nama_Dekan`, `Nip_Dekan`, `Acc_Koordinator`, `Acc_Dekan`, `Status`, `Komentar`, `Id_Pemberi_Tugas`, `Id_Penerima_Tugas`, `Id_Jenis_Surat`, `Id_Jenis_Pekerjaan`, `Judul_Tugas`, `Nomor_Surat_Tugas`, `data_spesifik`, `signature_qr_data`, `qr_image_path`, `Tanggal_Diberikan`, `Tanggal_Tenggat`, `Tanggal_Diselesaikan`) VALUES
(50, '\"[{\\\"nama\\\":\\\"Adi Saputra\\\",\\\"nim\\\":\\\"1121001\\\",\\\"program-studi\\\":\\\"Teknik Informatika\\\",\\\"jurusan\\\":\\\"Teknik Informatika\\\",\\\"angkatan\\\":\\\"2022\\\",\\\"no_wa\\\":\\\"085648475948\\\"}]\"', '\"{\\\"dosen_pembimbing_1\\\":\\\"Agus Wibowo, S.Kom., Ph.D.\\\",\\\"dosen_pembimbing_2\\\":null}\"', 'Software Engineer Application', 'file_pendukung_magang/jCoN7AdBZ6PcEVvTXy3MOV5hR9avJpUB5doTGwhx.pdf', NULL, 'PT Google', 'Jl xxxx no 9', '2026-03-01', '2026-03-14', 'file_tanda_tangan/mEYzXbUAdiF2aqLmQRHW8U98E6aersBQY5EVhVA1.jpg', 'qrcodes/surat_magang_50.svg', 'qr-codes/10_QmdrPBwG6nLWXKof.png', 'B/7345/UN46.3.4/PK.01.06/2026', 25, 23, '198503152010121001', 1, 1, 'Success', NULL, 201, 103, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-05', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `Surat_Magang_Invitations`
--

CREATE TABLE `Surat_Magang_Invitations` (
  `id_no` bigint UNSIGNED NOT NULL,
  `id_surat_magang` int NOT NULL,
  `id_mahasiswa_diundang` int NOT NULL,
  `id_mahasiswa_pengundang` int NOT NULL,
  `status` enum('pending','accepted','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `invited_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `responded_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Surat_Peminjaman_Mobil`
--

CREATE TABLE `Surat_Peminjaman_Mobil` (
  `id` bigint UNSIGNED NOT NULL,
  `Status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'baru',
  `Tanggal_Diberikan` timestamp NULL DEFAULT NULL,
  `Tanggal_Diselesaikan` timestamp NULL DEFAULT NULL,
  `Id_User` int NOT NULL,
  `Id_Kendaraan` bigint UNSIGNED DEFAULT NULL,
  `Id_Pejabat` int DEFAULT NULL,
  `tujuan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `keperluan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_pemakaian_mulai` date NOT NULL,
  `tanggal_pemakaian_selesai` date NOT NULL,
  `jumlah_penumpang` int NOT NULL,
  `rekomendasi_admin` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `catatan_wadek2` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `alasan_penolakan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `nomor_surat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_surat_final` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qr_code_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_pengajuan` enum('Diajukan','Diverifikasi_Admin','Disetujui_Wadek2','Ditolak','Selesai') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Diajukan',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Surat_Peminjaman_Ruang`
--

CREATE TABLE `Surat_Peminjaman_Ruang` (
  `id` bigint UNSIGNED NOT NULL,
  `Id_Ruangan` int DEFAULT NULL,
  `nama_kegiatan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `penyelenggara` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_mulai` datetime NOT NULL,
  `tanggal_selesai` datetime NOT NULL,
  `jumlah_peserta` int NOT NULL,
  `file_lampiran` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `rekomendasi_admin` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `catatan_wadek2` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `alasan_penolakan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `nomor_surat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_surat_final` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qr_code_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_pengajuan` enum('Diajukan','Diverifikasi_Admin','Disetujui_Wadek2','Ditolak','Selesai') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Diajukan',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Surat_Tidak_Beasiswa`
--

CREATE TABLE `Surat_Tidak_Beasiswa` (
  `id` bigint UNSIGNED NOT NULL,
  `Id_User` int NOT NULL,
  `Id_Penerima_Tugas` int DEFAULT NULL,
  `Id_Pejabat` int DEFAULT NULL,
  `Nama_Orang_Tua` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Pekerjaan_Orang_Tua` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `NIP_Orang_Tua` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Khusus PNS/ASN',
  `Pendapatan_Orang_Tua` decimal(15,2) NOT NULL COMMENT 'Pendapatan per bulan dalam Rupiah',
  `Keperluan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `File_Pernyataan` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Path file surat pernyataan',
  `Nomor_Surat` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'baru',
  `Tanggal_Diberikan` timestamp NULL DEFAULT NULL,
  `Tanggal_Diselesaikan` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `surat_verifications`
--

CREATE TABLE `surat_verifications` (
  `id` bigint UNSIGNED NOT NULL,
  `id_letter` bigint UNSIGNED DEFAULT NULL,
  `letter_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_tugas_surat` int UNSIGNED DEFAULT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Token unik untuk validasi QR',
  `signed_by` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nama atau ID Dekan yang menandatangani',
  `signed_by_user_id` int UNSIGNED DEFAULT NULL COMMENT 'ID User Dekan',
  `signed_at` timestamp NOT NULL COMMENT 'Waktu persetujuan',
  `qr_path` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `surat_verifications`
--

INSERT INTO `surat_verifications` (`id`, `id_letter`, `letter_type`, `id_tugas_surat`, `token`, `signed_by`, `signed_by_user_id`, `signed_at`, `qr_path`, `created_at`, `updated_at`) VALUES
(1, NULL, NULL, 6, 'tkzNz2kWaSwDdGW9krAA95DI2jQhtR36Pp9vCozVR52izpo2FdBkZ5FB7IOO9amH', 'Dr. Budi Hartono, S.Kom., M.Kom.', 101, '2025-11-22 01:02:49', 'http://localhost/storage/qr-codes/qr_mb7hJTAYDXdCP5CW.png', '2025-11-22 01:02:49', '2025-11-23 20:57:41'),
(2, NULL, NULL, 7, 'AcpQTfh03vD7BApCSHPdmAmWVsFjdCT7aiPT5UtIxLso2vCSv453HoW0YOtLHFWX', 'Dr. Budi Hartono, S.Kom., M.Kom.', 101, '2025-11-22 01:33:28', 'http://localhost/storage/qr-codes/qr_NYDtGZlFhOKIbRfp.png', '2025-11-22 01:33:28', '2025-11-23 20:57:43'),
(3, NULL, NULL, 7, 'S7hf7DKl9RBNEJUdEzIpVDl3kVZqpB1oW6fCSOXDOGOo7Wx5OLfsV1mlFPiCc3pd', 'Dr. Budi Hartono, S.Kom., M.Kom.', 101, '2025-11-22 01:40:44', 'http://localhost/storage/qr-codes/qr_OfwGqZZas3bRp4AN.png', '2025-11-22 01:40:44', '2025-11-23 20:57:44'),
(4, NULL, NULL, 8, 'iToggV5oNA03ooNOVdeB2pRQzw5ANdwFBEsR1WmpZV7QBemPm2LQhscDOGhxO6L4', 'Dr. Budi Hartono, S.Kom., M.Kom.', 101, '2025-11-22 02:15:39', 'http://localhost/storage/qr-codes/qr_1UZrahDYtCrOoGql.png', '2025-11-22 02:15:39', '2025-11-23 20:57:48'),
(5, NULL, NULL, 9, 'FNVtrMv1Df2FykyMnA37MUrP4HrCYCPF8q5S2au4iRBJAE1lepImgd4cNuJGKiw9', 'Dr. Budi Hartono, S.Kom., M.Kom.', 101, '2025-11-23 22:03:36', 'http://127.0.0.1:8000/storage/qr-codes/qr_n8yHnf2OtrSzwQqC.png', '2025-11-23 22:03:36', '2025-11-23 22:03:37'),
(6, NULL, NULL, 11, 'nC98nNnUAPnB5TjhmKL69rU9YHQbOFPgg0RPK8NAg67Bdhzqu3oAeYkwI06oU2bU', 'Dr. Budi Hartono, S.Kom., M.Kom.', 101, '2025-11-23 22:25:30', 'http://127.0.0.1:8000/storage/qr-codes/qr_yPTxAcawKcU1fU6x.png', '2025-11-23 22:25:30', '2025-11-23 22:25:31'),
(7, NULL, NULL, 31, 'HibUOckNbCb6DMiZJvNDiA3KIuDYIRfffN2D4dC2l0JsyqdyI27kzsDPNV4s2m2l', 'Dr. Budi Hartono, S.Kom., M.Kom.', 101, '2025-11-29 02:25:42', 'http://127.0.0.1:8000/storage/qr-codes/qr_rKr3kiH6uaADVsCc.png', '2025-11-29 02:25:42', '2025-11-29 02:25:43'),
(8, NULL, NULL, 31, 'A6ndmyKTLxTMi9AGGB4iFPUBAw6h3Rw74ABtpdicApwg3L6ynoifctFKdKCfe1JG', 'Dr. Budi Hartono, S.Kom., M.Kom.', 101, '2025-11-29 02:28:34', 'http://127.0.0.1:8000/storage/qr-codes/qr_rv83NdhP8g9bb3LL.png', '2025-11-29 02:28:34', '2025-11-29 02:28:34'),
(9, NULL, NULL, 31, 'Mt4TtbpgQ1cK8j3BA5FJJ6HuWgPHZ4YvZZCf7XAPeHCzWBIzD2NWBxC5iPxIlMN2', 'Dr. Budi Hartono, S.Kom., M.Kom.', 101, '2025-11-29 02:35:04', 'http://127.0.0.1:8000/storage/qr-codes/qr_FyjvnDJ9m4CF7UH5.png', '2025-11-29 02:35:04', '2025-11-29 02:35:05'),
(10, NULL, NULL, 32, 'WgujdiLSLCSEXrveBmIRevW1ZxSY7Xvx8X4aFdrwtAtgeV8pjgBrjtGMvZnmwlUq', 'Dr. Budi Hartono, S.Kom., M.Kom.', 101, '2025-11-29 02:53:00', 'http://127.0.0.1:8000/storage/qr-codes/qr_L0FLUD1onEGYk72i.png', '2025-11-29 02:53:00', '2025-11-29 02:53:01'),
(11, NULL, NULL, 70, 'qAsjur3SKvKopyn5NgElkw8dNLmCuTFVrJmHsqPB4saKqi5z45vQvBAtyWMdtbfP', 'Dr. Budi Hartono, S.Kom., M.Kom.', 101, '2025-12-28 12:51:51', 'http://localhost:8000/storage/qr-codes/qr_AxldqdOMrqh57Knz.png', '2025-12-28 12:51:51', '2025-12-28 12:51:51'),
(12, NULL, NULL, 71, 'Zixt7i5WtlmnfQ2ygUrCXKbsaXpDUKf0SGwiK48sareoh77fPkqvd73NpWWRMOYb', 'Dr. Budi Hartono, S.Kom., M.Kom.', 101, '2025-12-28 19:36:14', 'http://127.0.0.1:8000/storage/qr-codes/qr_mlbbhj4aBauF6o9X.png', '2025-12-28 19:36:14', '2025-12-28 19:36:16'),
(13, NULL, NULL, 84, 'Qekm5tzy2BdaqN5wqpwhGjnbfVMFGQpLKUarxd4vj96ipUhulX8t4R1heO3Q8bzy', 'Dr. Budi Hartono, S.Kom., M.Kom.', 101, '2026-01-01 09:18:32', 'http://127.0.0.1:8000/storage/qr-codes/qr_LaUv804fbGhL7K9u.png', '2026-01-01 09:18:32', '2026-01-01 09:18:33'),
(14, NULL, NULL, 101, 'AlbHaxvCvDpNcyhE9rNu6REx5EvdWRkS', 'Fitriani, S.Kom., M.Cs.', 104, '2026-01-04 14:32:38', '/qr-codes/qr_BxZanAxXxjS3S9Wc.png', '2026-01-04 14:32:38', '2026-01-04 14:32:38'),
(15, NULL, NULL, 103, 'SwECIXnIifruTIqzZdvTw2gcAJTRYVAV', 'Fitriani, S.Kom., M.Cs.', 104, '2026-01-05 03:49:10', 'qr-codes/qr_DdlmwRnqnHs8q5fV.png', '2026-01-05 03:49:10', '2026-01-05 03:49:10'),
(16, NULL, NULL, 110, 'SPhv7CnSPgElMGtQkzBTNjklWmVNq8yY', 'Fitriani, S.Kom., M.Cs.', 104, '2026-01-05 06:17:58', 'http://127.0.0.1:8000/storage/qr-codes/qr_h1LobPfbHC27be7S.png', '2026-01-05 06:17:58', '2026-01-05 06:17:58'),
(17, NULL, NULL, 111, '1AMRjHEMrPqVgIC6gE3VOdyKpsksHCnm', 'Fitriani, S.Kom., M.Cs.', 104, '2026-01-05 06:29:35', 'qr-codes/qr_nH2O4AYkfCZiBAA7.png', '2026-01-05 06:29:35', '2026-01-05 06:29:35'),
(18, NULL, NULL, 113, 'tJjZ5UmlEpjXUU39Vav7zlpGMNrcXW9D', 'Agus Wibowo, S.Kom., Ph.D.', 105, '2026-01-08 10:36:35', 'qr-codes/peminjaman_mobil_1_NHsywDRKQJMAehtG.png', '2026-01-08 10:36:35', '2026-01-08 10:36:35'),
(19, NULL, NULL, 122, 'ABWbvZehJtmzW2iiOvazANPvRnSW5Qr8', 'Agus Wibowo, S.Kom., Ph.D.', 105, '2026-01-10 09:51:31', 'qr-codes/peminjaman_mobil_4_s6rrk0TKRA3Vdqp9.png', '2026-01-10 09:51:31', '2026-01-10 09:51:31'),
(20, NULL, NULL, 123, 'oM69BBXC03nATEujAv985ZrhkpQ22zeV', 'Fitriani, S.Kom., M.Cs.', 104, '2026-01-23 20:06:59', 'qr-codes/qr_BysvcEruU8hZCoMD.png', '2026-01-23 20:06:59', '2026-01-23 20:06:59');

-- --------------------------------------------------------

--
-- Table structure for table `Tugas`
--

CREATE TABLE `Tugas` (
  `Id_Tugas` int NOT NULL,
  `Id_Pemberi_Tugas` int DEFAULT NULL,
  `Id_Penerima_Tugas` int DEFAULT NULL,
  `Judul_Tugas` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `Deskripsi_Tugas` text,
  `Tanggal_Diberikan_Tugas` date DEFAULT NULL,
  `Tanggal_Tenggat_Tugas` date DEFAULT NULL,
  `Status` enum('Dikerjakan','Selesai','Terlambat') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `Tanggal_Diselesaikan` date DEFAULT NULL,
  `File_Laporan` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Tugas`
--

INSERT INTO `Tugas` (`Id_Tugas`, `Id_Pemberi_Tugas`, `Id_Penerima_Tugas`, `Judul_Tugas`, `Deskripsi_Tugas`, `Tanggal_Diberikan_Tugas`, `Tanggal_Tenggat_Tugas`, `Status`, `Tanggal_Diselesaikan`, `File_Laporan`) VALUES
(1, 101, 301, 'Entri Nilai Mata Kuliah Dasar Pemrograman', 'Mohon untuk segera melakukan entri nilai akhir untuk mata kuliah Dasar Pemrograman kelas A dan B ke dalam sistem akademik.', '2025-09-12', '2025-09-19', 'Dikerjakan', NULL, NULL),
(2, 103, 302, 'Update Data Inventaris Laboratorium', 'Lakukan pengecekan dan update data inventaris untuk seluruh perangkat komputer di Laboratorium 1 dan 2. Laporkan jika ada kerusakan.', '2025-09-08', '2025-09-15', 'Selesai', '2025-09-12', '/laporan/inventaris/2025/Laporan_Inventaris_Lab_Sept2025.xlsx'),
(3, 151, 304, 'Posting Berita Prestasi Mahasiswa', 'Tolong unggah berita mengenai kemenangan tim mahasiswa DKV dalam kompetisi desain tingkat nasional. Materi berita dan foto sudah dikirim via email.', '2025-09-11', '2025-09-12', 'Selesai', '2025-09-11', NULL),
(4, 121, 305, 'Penjadwalan Rapat Koordinasi Dosen', 'Mohon atur jadwal dan kirim undangan untuk rapat koordinasi seluruh dosen prodi Manajemen. Target pelaksanaan minggu depan.', '2025-09-12', '2025-09-16', 'Dikerjakan', NULL, NULL),
(5, 141, 307, 'Pengarsipan Berkas Akademik Mahasiswa', 'Lakukan pengarsipan berkas fisik (KRS, KHS, dll.) untuk mahasiswa angkatan 2023. Pastikan semua tersimpan di lemari arsip yang sesuai.', '2025-09-10', '2025-09-24', 'Dikerjakan', NULL, NULL),
(6, 118, 304, 'Perbaikan Proyektor di Ruang Kelas', 'Proyektor di ruang R-301 tidak dapat terhubung ke laptop. Mohon segera diperiksa sebelum perkuliahan jam 10 pagi ini.', '2025-09-12', '2025-09-12', 'Selesai', '2025-09-12', NULL),
(7, 131, 301, 'Rekonsiliasi Keuangan Bulan Agustus', 'Lakukan rekonsiliasi pengeluaran dan pemasukan dana prodi Akuntansi untuk bulan Agustus 2025. Laporan ditunggu paling lambat akhir minggu ini.', '2025-09-12', '2025-09-15', 'Dikerjakan', NULL, NULL),
(8, 103, 306, 'Persiapan Materi untuk Akreditasi', 'Tolong siapkan dan cetak semua borang dan dokumen pendukung untuk visitasi akreditasi. Pastikan semua sudah dijilid rapi.', '2025-09-12', '2025-09-22', 'Dikerjakan', NULL, NULL),
(9, 114, 307, 'Arsipkan Soal Ujian Semester Lalu', 'Arsipkan semua berkas soal dan jawaban UAS semester Ganjil 2024/2025 ke dalam lemari arsip sesuai kode mata kuliah.', '2025-09-05', '2025-09-12', 'Selesai', '2025-09-11', '/laporan/arsip/2025/Checklist_Arsip_UAS_Ganjil_2024.txt'),
(10, 111, 304, 'Update Informasi Kurikulum di Website', 'Mohon perbarui halaman kurikulum di website prodi Sistem Informasi dengan struktur mata kuliah terbaru yang telah disetujui.', '2025-09-12', '2025-09-15', 'Dikerjakan', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `Users`
--

CREATE TABLE `Users` (
  `Id_User` int NOT NULL,
  `Username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `Name_User` varchar(255) DEFAULT NULL,
  `Id_Role` int DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `No_WA` varchar(16) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Users`
--

INSERT INTO `Users` (`Id_User`, `Username`, `password`, `Name_User`, `Id_Role`, `email`, `No_WA`) VALUES
(101, 'budi_hartono', '$2y$12$7uQ8p9ZEjz7zKgA28jsuu.LUv8U9wdU9Rr17dHrh1mSmQD8Jaa3Jq', 'Dr. Budi Hartono, S.Kom., M.Kom.', 2, 'budi.hartono@fakultas.ac.id', '081255500108'),
(102, 'rina_permatasari', '$2y$12$VM0tE3mRNmFmjf7V69WiQ.5OvUuGrwfTi3EMnVac6dYAJRpiOduQi', 'Rina Permatasari, S.T., M.Sc.', 3, 'rina.permatasari@fakultas.ac.id', '083123133839'),
(103, 'iwan_santoso', '$2y$12$GW53Yc6WTZYLL1HJooRZ1ueP2Q3QTyi0rqrWaxfjgpzPGnI1IfBdm', 'Prof. Dr. Ir. Iwan Santoso', 4, 'iwan.santoso@fakultas.ac.id', '085648475948'),
(104, 'fitriani_kom', '$2y$12$R3H8ACGH0dbS0x1s/M4JaOLfi7oLMCTnqBkyjl7NidzUtYC20Ki0i', 'Fitriani, S.Kom., M.Cs.', 10, 'fitriani.kom@fakultas.ac.id', '085708654651'),
(105, 'agus_wibowo', '$2y$12$.WLgOu/uHaJsu1yd/jd9KubFVf/3.0OJVrGGrk2V3.ai1GGt9j/6a', 'Agus Wibowo, S.Kom., Ph.D.', 9, 'agus.wibowo@fakultas.ac.id', '081255500116'),
(106, 'teguh_prasetyo', '$2y$12$PhGbehDzq1EMoJbYg6jROOgn6K9TPSR1H03wHKZFIr5T0CorHjZI2', 'Teguh Prasetyo, S.T., M.T.', 5, 'teguh.prasetyo@fakultas.ac.id', '085648475948'),
(107, 'dewi_lestari', '$2y$12$vEPsVB6pAbU.gkWbvA3JAOHOcqyXvd3eyMZPnlfHe1CPvhrO1Psi.', 'Dewi Lestari, S.Kom., M.Kom.', 5, 'dewi.lestari@fakultas.ac.id', '081255500118'),
(108, 'hendro_susilo', '$2y$12$OLh9LsVt3NyohtGoUfgtQeFd3sXn0fMgfOHsh1F97KVVR59D8Qe3e', 'Hendro Susilo, B.Eng., M.Eng.', 5, 'hendro.susilo@fakultas.ac.id', '081255500119'),
(109, 'yulia_citra', '$2y$12$Cd4SrcX0ulpEHo5kCMXerO9z6kQR1QO8Fd0JMraOP2NGadr6JdKPm', 'Yulia Citra, S.Si., M.Sc.', 5, 'yulia.citra@fakultas.ac.id', '081255500120'),
(110, 'bambang_purnomo', '$2y$12$Q65uskxOMlvJ8o2qoflprus8eJowZi4awB/qyuO4pgwfOu2GFPkEe', 'Dr. Bambang Purnomo, M.T.', 5, 'bambang.purnomo@fakultas.ac.id', '081255500121'),
(111, 'ahmad_fauzi', '$2y$12$h2zBi678ql0ySUfZueFTxe017ZMUAERHbSASG8VHxD3fWTqz501LC', 'Ahmad Fauzi, S.Kom., M.M.S.I.', 4, 'ahmad.fauzi@fakultas.ac.id', '081255500112'),
(112, 'siti_nurhaliza', '$2y$12$jmTPhrRpEEw2VTyAf8EOvOJ4PI4hJ9fxj8ufH7ucP9L4YgF1qDRvm', 'Siti Nurhaliza, S.SI., M.Kom.', 5, 'siti.nurhaliza@fakultas.ac.id', '081255500122'),
(113, 'donny_setiawan', '$2y$12$rM.7YeGYSg9sSvKh/WCXHeGPbkI9gvTst7LAIT7z5v7DPoN6MwEIi', 'Donny Setiawan, M.Sc.', 5, 'donny.setiawan@fakultas.ac.id', '081255500123'),
(114, 'linda_wati', '$2y$12$FjzkL9EBdYCh47vBIqLa3ulqLbnG/wGFWjDoplhi.TbtmYN7jzpZu', 'Linda Wati, S.Kom., M.T.', 5, 'linda.wati@fakultas.ac.id', '081255500124'),
(115, 'eko_nugroho', '$2y$12$DVG6zNwbByqc7f4ncnrvWOrrZn330wTNPR0fpIW6jEeB49f7ZfKva', 'Dr. Eko Nugroho, M.Kom.', 5, 'eko.nugroho@fakultas.ac.id', '081255500125'),
(116, 'cahyo_purnomo', '$2y$12$UP18Yllz2rO89l9QeLd2YuephwLNLzTzvyR3DERhwxEc22cmaXyIm', 'Cahyo Purnomo, S.T., M.Sc.', 5, 'cahyo.purnomo@fakultas.ac.id', '081255500126'),
(117, 'anisa_rahmawati', '$2y$12$x6XaiTMfqKAQoKg5NQna1.tgxRxyT3WI21HUWLkbkxhWaleI8kIA.', 'Anisa Rahmawati, S.Kom., M.Kom.', 5, 'anisa.rahmawati@fakultas.ac.id', '081255500127'),
(118, 'fajar_sidik', '$2y$12$sUtVgE00QZBhzOY3iE3DzeW2xubQBqMMNRa80HkdupUYnIMiSuR8G', 'Fajar Sidik, S.Kom., M.M.', 5, 'fajar.sidik@fakultas.ac.id', '081255500128'),
(119, 'maya_anggraini', '$2y$12$8L/6bA56RCYBbeVFxbl2dOjINDUeejgkzXjY93vpVVMLoEAyWt.ZS', 'Maya Anggraini, S.Kom., M.Sc.', 5, 'maya.anggraini@fakultas.ac.id', '081255500129'),
(120, 'surya_dharma', '$2y$12$94L5er5IfeXxe0.EwKhpSOTPwHT6aybASYXxYc.aeZtiZLDzIg7ja', 'Prof. Dr. Ir. Surya Dharma', 5, 'surya.dharma@fakultas.ac.id', '081255500130'),
(121, 'hendra_wijaya', '$2y$12$VnxlQI7PqbReF5Vv6SeIo.R3zH36vI7aeukJ6OF/AUcmKNGFsAeZm', 'Dr. Hendra Wijaya, S.E., M.M.', 5, 'hendra.wijaya@fakultas.ac.id', '081255500131'),
(122, 'amelia_santoso', '$2y$12$oGHklWqJW/.tm1HTvEG1Luu5zbrAgMWtwa5Tub4EkxvNXwpFufoNa', 'Dr. Amelia Santoso, S.E., M.SM.', 5, 'amelia.santoso@fakultas.ac.id', '081255500132'),
(123, 'sutrisno_mm', '$2y$12$6sHh3NmIIMcXjYCD.nqq3uVPiHSJKqRpFpcQzUbB5yvgm4YGB.IHq', 'Prof. Dr. Sutrisno, M.M.', 5, 'sutrisno.mm@fakultas.ac.id', '081255500133'),
(124, 'diana_puspita', '$2y$12$r8YEBhi2QTo7A6.kUNh2Su0YIsZ0vD6xm4E22/5URM6tfFcnAF7He', 'Diana Puspita, S.E., M.B.A.', 5, 'diana.puspita@fakultas.ac.id', '081255500134'),
(125, 'rizky_maulana', '$2y$12$HShSpY2EQj44ktb4fEf3rO9zAQRXioDC5xqJKep9JUoCIVjOFXl7S', 'Rizky Maulana, S.M., M.M.', 5, 'rizky.maulana@fakultas.ac.id', '081255500135'),
(126, 'putri_wulandari', '$2y$12$KV30oCHnn9yV7O5M0ETP7.g.Oo..3tOAj0YwZNyuqt7bkgpLVHdkm', 'Putri Wulandari, S.E., M.M.', 5, 'putri.wulandari@fakultas.ac.id', '081255500136'),
(127, 'baskoro_adiwijaya', '$2y$12$OVgJ.hTAGaliEifdwdVJyeU6hxYiz30Y1RCORINtUd31Bed9bwTKS', 'Baskoro Adiwijaya, S.E., M.M.', 5, 'baskoro.adiwijaya@fakultas.ac.id', '081255500137'),
(128, 'antonius_mm', '$2y$12$wXm7Dk2tRbRvycMbKwYZVeSFubPye3BWdeqFyehpGdHWORZQ1timi', 'Dr. Antonius, M.M.', 5, 'antonius.mm@fakultas.ac.id', '081255500138'),
(129, 'jessica_tanoe', '$2y$12$PE4WmAiw57j.cuf44zbml.daKIeCtfxI3zBloNxC5nJkrSJVeuc.S', 'Jessica Tanoe, S.E., M.Sc.', 5, 'jessica.tanoe@fakultas.ac.id', '081255500139'),
(130, 'hermanto_mba', '$2y$12$5PxVcSeetk7HtWpkgeVOg.q/1TAJVwNFlsavNJ67IO9rnhrBvAes2', 'Prof. Dr. Hermanto, M.B.A.', 5, 'hermanto.mba@fakultas.ac.id', '081255500140'),
(131, 'liana_susanti', '$2y$12$WTf5xrvsci7/4EDlRhopgueKTwwEey07ASOuqACDKq3f1EmDASJRu', 'Dr. Liana Susanti, S.E., M.Ak., Ak.', 5, 'liana.susanti@fakultas.ac.id', '081255500141'),
(132, 'dedi_setiadi', '$2y$12$0KCTCTn7NHqm2tiT3IPIfuJHyHtby1Z24fw7GpOqQPU2fCkkkQXdq', 'Dedi Setiadi, S.Ak., M.Ak.', 5, 'dedi.setiadi@fakultas.ac.id', '081255500142'),
(133, 'kartika_dewi', '$2y$12$0tjOyCFJvGfiJno3kpdfme6eoeIxbCiIEy9o11a8M0NbjxtkU1bVq', 'Dr. Kartika Dewi, S.E., M.Si., Ak., CA.', 5, 'kartika.dewi@fakultas.ac.id', '081255500143'),
(134, 'andi_prasetyo', '$2y$12$3Og5fXUSlrBRj8q/JNV5vOYspjrpsLlMPlC/pwZJ2gFMj1.2kzy.K', 'Andi Prasetyo, S.Ak., M.Ak.', 5, 'andi.prasetyo@fakultas.ac.id', '081255500144'),
(135, 'evelyn_wijaya', '$2y$12$oVJ7SrD9tBPX2xkyPUmPsencyky2JiUsDynpCtYZEFdblzDk6TkZG', 'Evelyn Wijaya, S.E., M.Ak.', 5, 'evelyn.wijaya@fakultas.ac.id', '081255500145'),
(136, 'yohanes_hartanto', '$2y$12$EdAoeuECp7CsN4jIUzMCxuhKV154SGrZkfcsLVRa9wOy4zQUM6PDe', 'Yohanes Hartanto, S.Ak., M.Ak.', 5, 'yohanes.hartanto@fakultas.ac.id', '081255500146'),
(137, 'cindy_aurelia', '$2y$12$Pyjn0fTIxp9n2UHxwBhD/.c1TNk5Imc0JaF6za1jXAVrgIgdncFyy', 'Cindy Aurelia, S.E., M.Ak.', 5, 'cindy.aurelia@fakultas.ac.id', '081255500147'),
(138, 'edi_sukamto', '$2y$12$mUpLv0qYZvLPNDfO3H0tlO8puM3T4lhfcN3jdKfkwDNS9WYFNanie', 'Prof. Dr. Edi Sukamto, M.Si., Ak.', 5, 'edi.sukamto@fakultas.ac.id', '081255500148'),
(139, 'rahman_hakim', '$2y$12$jqbCtb8/5hb38FUmT9M/weLf40CrRcs1oFn4jlqGlDN5VFU/8GMCi', 'Rahman Hakim, S.Ak., M.Ak.', 5, 'rahman.hakim@fakultas.ac.id', '081255500149'),
(140, 'putri_amelia', '$2y$12$.u0Y4JqbpRUZz9pk1zO6KOunBbBMLNZRz5X2E.HOZE7sXUY5KC4u6', 'Putri Amelia, S.E., M.Ak.', 5, 'putri.amelia@fakultas.ac.id', '081255500150'),
(141, 'heru_santoso', '$2y$12$kjjSucdTy0AWbAest6iPp.76czaQZRn/6XP2.6yTyY9O/I0tnvsNO', 'Dr. Ir. Heru Santoso, M.Eng.', 3, 'heru.santoso@fakultas.ac.id', '081255500110'),
(142, 'anita_sari', '$2y$12$NEwRPLNofB4UVd9xM.ojfukNxoXH.HwbjjSFHZTMSJwsI67Gkf/.O', 'Anita Sari, S.T., M.T.', 4, 'anita.sari@fakultas.ac.id', '081255500113'),
(143, 'taufik_phd', '$2y$12$D7OiyZECIOQZRZJ46P3l..sUeAQbT0hlZAv8rBe1jGl8UKZ3Ikblq', 'Prof. Dr. Ir. Taufik, M.Sc., Ph.D.', 5, 'taufik.phd@fakultas.ac.id', '081255500151'),
(144, 'rendra_setiawan', '$2y$12$OW12Qg9wjgQu5CX4RBT4z.oLep.UCRGVuaqteYJ/RE5AKUsIyGBe2', 'Rendra Setiawan, S.T., M.T.', 5, 'rendra.setiawan@fakultas.ac.id', '081255500152'),
(145, 'diana_anggraini', '$2y$12$cf73P7CLsmsoV3Ubyu42ou0EB6cftciFNsBOh7TD3N4vE9UtY3FDG', 'Diana Anggraini, S.T., M.Eng.', 5, 'diana.anggraini@fakultas.ac.id', '081255500153'),
(146, 'bayu_adhi', '$2y$12$IkN3FZaiRUnJdM/uI.oroOEg80XomgIQyIJueHkPrU5Lm4nT4ln3y', 'Dr. Bayu Adhi, M.T.', 5, 'bayu.adhi@fakultas.ac.id', '081255500154'),
(147, 'siska_amelia', '$2y$12$jtfhuoDxE/Mk2nyqCpArEuksxz9eHNTnyc6QtGiRk8wP23FRFEAg6', 'Siska Amelia, S.T., M.T.', 5, 'siska.amelia@fakultas.ac.id', '081255500155'),
(148, 'galih_prakoso', '$2y$12$maEKyNsludan2jvJUIrn4.RLACrXumjbHtE106XqYUExhPojxb1Ai', 'Galih Prakoso, S.T., Ph.D.', 5, 'galih.prakoso@fakultas.ac.id', '081255500156'),
(149, 'rini_nur', '$2y$12$V8Wrt2NDrxRCbb3aPx/3i.fG7/WRy518nXk/eySqh5RTd/35Fi8O.', 'Prof. Dr. Ir. Rini Nur, M.Sc.', 5, 'rini.nur@fakultas.ac.id', '081255500157'),
(150, 'faisal_rahman', '$2y$12$yqWT7Tly9.GPL12EikUIjujT.4Gn1wX.icPDGATo3D7g3L71gq8da', 'Faisal Rahman, S.T., M.T.', 5, 'faisal.rahman@fakultas.ac.id', '081255500158'),
(151, 'adi_nugroho', '$2y$12$uuqTnfZLKMRjOWFmJHJb8OO5Qnkkgs2z0V8BfQ0J6bkcMofDHR02S', 'Adi Nugroho, S.Ds., M.Sn.', 5, 'adi.nugroho@fakultas.ac.id', '081255500159'),
(152, 'clara_bella', '$2y$12$C8vZSNFYuu6XXfwUsTNkLef2T7T.bVQPMgfwZqTtcgmJ9Uil/G6iW', 'Clara Bella, S.Sn., M.Ds.', 5, 'clara.bella@fakultas.ac.id', '081255500160'),
(153, 'deni_kreatif', '$2y$12$4peR1aFOHb/88vNsUPY9J.o4j74tuJHYTZA8e2hcfbxzxR9KvBvLe', 'Dr. Deni Kreatif, M.Sn.', 5, 'deni.kreatif@fakultas.ac.id', '081255500161'),
(154, 'vina_alvionita', '$2y$12$b6vOc58XfT5Leiq1dGKJnO7ufz7uNeJpEpSXL3felP/YPz.eIyiqq', 'Vina Alvionita, S.Ds., M.A.', 5, 'vina.alvionita@fakultas.ac.id', '081255500162'),
(155, 'reza_priyambada', '$2y$12$GJolSZWZOE4ILqHrWpgBkepmGqij41WQGKSOPK5sNNimnKYsSYgTm', 'Reza Priyambada, S.Sn., M.Ds.', 5, 'reza.priyambada@fakultas.ac.id', '081255500163'),
(156, 'sari_indah', '$2y$12$RbpMCBa.pcQEfosvH8RMN.ugfmKhOq.pvEpTAq51eQ0n/7u6RNV1O', 'Sari Indah, S.Ds., M.Sn.', 5, 'sari.indah@fakultas.ac.id', '081255500164'),
(157, 'leo_firmansyah', '$2y$12$txW6JyPxSgmBmDz6Y.WPS.Hyl8Xi7NOOKSTNE5FMOYwA.gywwzqSm', 'Leo Firmansyah, S.Sn.', 5, 'leo.firmansyah@fakultas.ac.id', '081255500165'),
(158, 'gita_permata', '$2y$12$ytOrQQ7AIpLcHSpz1U0ATuDpptwkoaEjgg.H0XGIcj9YUm2MWkqp6', 'Gita Permata, S.Ds., M.A.', 5, 'gita.permata@fakultas.ac.id', '081255500166'),
(159, 'bambang_subekti', '$2y$12$dsFMKrOghEpDAyVS8yEEReSI0p4679C8eQ5A/E7N4cYNGxfJ9CWIi', 'Dr. Bambang Subekti, M.Sn.', 5, 'bambang.subekti@fakultas.ac.id', '081255500167'),
(160, 'citra_kirana', '$2y$12$BkeiU3g7kZWkHTrYgktJyub3N2t1u4PuD4WsJCVvW4ZofjdwM5K..', 'Prof. Dr. Citra Kirana, M.Hum.', 5, 'citra.kirana@fakultas.ac.id', '081255500168'),
(161, 'agung_laksana', '$2y$12$.K.iCFMKmddiT6yxverGo.4.viv2Jth1YLuiV7iVZIJ9Hh2RP7MeO', 'Dr. Agung Laksana, S.I.Kom., M.Si.', 4, 'agung.laksana@fakultas.ac.id', '081255500114'),
(162, 'riana_fitri', '$2y$12$7HW/fKW/6dv4d1gg3IIvieH1tKcntOQQY3DSXHN6JXDR5a42XiNSe', 'Riana Fitri, S.Sos., M.I.Kom.', 5, 'riana.fitri@fakultas.ac.id', '081255500169'),
(163, 'dedi_mulyana', '$2y$12$23SxbhQVvw5rRgUqDD94fuDlR88Ia4EPze5LSUb5tAEyGmBWWGwhS', 'Prof. Dr. Dedi Mulyana, M.A.', 5, 'dedi.mulyana@fakultas.ac.id', '081255500170'),
(164, 'fahmi_idris', '$2y$12$t9DW3UWqfH98k77Zl9ABluU2JGQaEnZ1WfDOlvS2Ncj8itsDJw84.', 'Fahmi Idris, S.I.Kom., M.Si.', 5, 'fahmi.idris@fakultas.ac.id', '081255500171'),
(165, 'nadia_utami', '$2y$12$0fu7d1tvP0iA5H77ZtRcceDkXIifc/yva0qR.p1xsX4d9d8TN4tnS', 'Nadia Utami, S.Sos., M.I.Kom.', 5, 'nadia.utami@fakultas.ac.id', '081255500172'),
(166, 'gilang_pratama', '$2y$12$AhAzWfjN82D.PO3OpKsiDuvsiQDC5GzlvqE1tR9Llq94dQQdIPSFy', 'Dr. Gilang Pratama, M.Si.', 5, 'gilang.pratama@fakultas.ac.id', '081255500173'),
(167, 'indah_cahyani', '$2y$12$9VMPlZmvvCDyacM7kIy03enDBw3FG9AWMqrfULJ0OKMnDWu.up.yO', 'Indah Cahyani, S.I.Kom., M.A.', 5, 'indah.cahyani@fakultas.ac.id', '081255500174'),
(168, 'yudi_candra', '$2y$12$r.1hl9rxiuJkMbtp1st8P.2RnD8xEh9UTTJfZWlTxJMmtwpL1tByy', 'Yudi Candra, S.Sos., M.Si.', 5, 'yudi.candra@fakultas.ac.id', '081255500175'),
(169, 'shinta_bella', '$2y$12$MuuewNCejqOY1hGWg3cn3.Vz5.PZo22GwLnX3h86efyDxhhJv81HK', 'Shinta Bella, S.I.Kom., M.Med.Kom.', 5, 'shinta.bella@fakultas.ac.id', '081255500176'),
(170, 'widodo_ms', '$2y$12$JufMmIJZTU0XJgqOHo7k1OJotkqsqI2lDGkzVDRRTOh9ekq0zjQP2', 'Prof. Dr. Widodo, M.S.', 5, 'widodo.ms@fakultas.ac.id', '081255500177'),
(171, 'firmansyahdani', '$2y$12$GW53Yc6WTZYLL1HJooRZ1ueP2Q3QTyi0rqrWaxfjgpzPGnI1IfBdm', 'dosen_fakultas_teknik', 8, 'firmansyahdani@fakultas.ac.id', '0856484754333'),
(201, 'mahasiswa1', '$2y$12$/roC3CpDOzVylgBlGV4lAeWoWNsRWZQkU0vXYG0zPRKH9CieKihyS', 'Adi Saputra', 6, 'adi.saputra@student.ac.id', '085648475948'),
(202, 'mahasiswa2', '$2y$12$KXuYU0wD1I6fL.bePeGUTuOqy9.WmkPdprMSV6c3DclTfVIM1dB2y', 'Bunga Citra Lestari', 6, 'bunga.citra@student.ac.id', '081255500179'),
(203, 'mahasiswa3', '$2y$12$A.sCl4sptMZpluxGx5A0bugtRwSQaINxiRWnR2QG9YlACVoZEvSua', 'Candra Wijaya', 6, 'candra.wijaya@student.ac.id', '081255500180'),
(204, 'mahasiswa4', '$2y$12$YUy/TMNgobM.0TgMSeQlw.E/TKUANZ5CzhW/iQVN40tTuxaMr8xf2', 'Dewi Anggraini', 6, 'dewi.anggraini@student.ac.id', '081255500181'),
(205, 'mahasiswa5', '$2y$12$ZaAST.XhP3uiOD7rM1NyxuBrDK.5B09cLKXXXVQ2eaNaTxratKdre', 'Eko Prasetyo', 6, 'eko.prasetyo@student.ac.id', '081255500182'),
(206, 'mahasiswa6', '$2y$12$Qji6X6ZKO9TBhZpFu/Capeqt6HstHXZkTHzVliYRlRnJDirwnr60u', 'Fitriana Rahmawati', 6, 'fitriana.rahmawati@student.ac.id', '081255500183'),
(207, 'mahasiswa7', '$2y$12$JinN/zV7o2zdizIDT3eq3.Qhc70obatE3FERwphy8PXWKgqi2YTse', 'Guntur Hidayat', 6, 'guntur.hidayat@student.ac.id', '081255500184'),
(208, 'mahasiswa8', '$2y$12$3s8sPol71QmxaKTioctuGulB5MMpJeKJRu4IyFQT0ZnxA22ojE.s.', 'Herlina Sari', 6, 'herlina.sari@student.ac.id', '081255500185'),
(209, 'mahasiswa9', '$2y$12$BJ4xVGtji1qJYOKeJrqihuftXAdCexBrnnohPt/I94ZyIZpeIWOD6', 'Irfan Maulana', 6, 'irfan.maulana@student.ac.id', '081255500186'),
(210, 'mahasiswa10', '$2y$12$SgOGlhtSyQL/htp6E/588.QuJkmbpGBNNAR/4nEGOJ2O4S3NpvVeK', 'Jasmine Putri', 6, 'jasmine.putri@student.ac.id', '081255500187'),
(211, 'mahasiswa11', '$2y$12$7IY8vDk2ve4PJ1fkY2DAx.OFlOGvQ161kugEfJTYSFsl.kfmcKtq.', 'Kevin Sanjaya', 6, 'kevin.sanjaya@student.ac.id', '081255500188'),
(212, 'mahasiswa12', '$2y$12$TIX4E63GarN.lO7uptN8aux5gaF5vuKA8SOddk/R8JbYrejcGe78K', 'Lia Kartika', 6, 'lia.kartika@student.ac.id', '081255500189'),
(213, 'mahasiswa13', '$2y$12$1x2a9R1WKA2Yq.K3cj32o.3ir2DB2VxQP.UsunEJTrQBQ/D1YXvbq', 'Muhammad Rizki', 6, 'muhammad.rizki@student.ac.id', '081255500190'),
(214, 'mahasiswa14', '$2y$12$L6BrPIe8OfK6w5abuojsQ.Mzt7VpQ3RBhcr2bVaR2ZuZ/cNLKHNpi', 'Nadia Paramita', 6, 'nadia.paramita@student.ac.id', '081255500191'),
(215, 'mahasiswa15', '$2y$12$oi9gKqEvsKhKhIBMcfl/6eGyLGvAxdJHCaooHfNmZpouzvws5Wa8O', 'Oscar Maulana', 6, 'oscar.maulana@student.ac.id', '081255500192'),
(216, 'mahasiswa16', '$2y$12$EqmMPy29B6.CQgnRPWnLQ.m9RV7LJE0Eht4MLbr.GAVZ3dtEntYu.', 'Putri Ayu', 6, 'putri.ayu@student.ac.id', '081255500193'),
(217, 'mahasiswa17', '$2y$12$uTMoN/P82YDq0XQXPsfa6.bYpeZCFM/dTpzUjhc.OtxsQjQMOOAf6', 'Qori Akbar', 6, 'qori.akbar@student.ac.id', '081255500194'),
(218, 'mahasiswa18', '$2y$12$F7jcoljopBHmNrLInBSTlun9YqRWtad1A6NbHMwPebdX.xyLubOI2', 'Rahmat Hidayat', 6, 'rahmat.hidayat@student.ac.id', '081255500195'),
(219, 'mahasiswa19', '$2y$12$CswuL.nxsQ0M3WRlLDGXd.36.5EFbMw8QQ9uFBMQa/MFweK9oGHYq', 'Siska Wulandari', 6, 'siska.wulandari@student.ac.id', '081255500196'),
(220, 'mahasiswa20', '$2y$12$Cnn8Bnev2uCYQ9EtitJWee3xmEZnrzmoxlasMvWk2UiIjNN66SOfm', 'Taufik Ramadhan', 6, 'taufik.ramadhan@student.ac.id', '081255500197'),
(221, 'mahasiswa21', '$2y$12$uUjqPOHLNzTymrzCPfoy5eVHg9OxM6rK/aFdjzCL84fAnQSuzZ9cO', 'Utari Dewi', 6, 'utari.dewi@student.ac.id', '081255500198'),
(222, 'mahasiswa22', '$2y$12$akCKH0YLsT1/7gCZGKwnpuG9KbinQu918cBoxBciIHYXzoG.hvObm', 'Vino Bastian', 6, 'vino.bastian@student.ac.id', '081255500199'),
(223, 'mahasiswa23', '$2y$12$.uJz5OMgYsHNiComIckFLuVMmwdyxNj4A9b6CBfP2Vrsd4ufcXv9i', 'Winda Lestari', 6, 'winda.lestari@student.ac.id', '081255500200'),
(224, 'mahasiswa24', '$2y$12$QmyAPh4OnPK0Cmg..RD2Tu9mDjr0LkTCFzyhUtgaC773fVQr02NyO', 'Xavier Nugraha', 6, 'xavier.nugraha@student.ac.id', '081255500201'),
(225, 'mahasiswa25', '$2y$12$r8RyGEPi22gXwYkX6UqOSujeek.VUsYzIjWnJ8jOrszVq1LA.6PM2', 'Yulia Puspita', 6, 'yulia.puspita@student.ac.id', '081255500202'),
(226, 'mahasiswa26', '$2y$12$EgLvAugPE3FO40cEQdRaWeWVmVXMYrrZm1y.amfgV/erMebEsNtzy', 'Zainal Abidin', 6, 'zainal.abidin@student.ac.id', '081255500203'),
(227, 'mahasiswa27', '$2y$12$Vzq2ozkiEz0QjUTBd9GV/.BnKUVymQVL94ardzE31PN12reuqVSie', 'Andika Pratama', 6, 'andika.pratama@student.ac.id', '081255500204'),
(228, 'mahasiswa28', '$2y$12$y.arU7Q0L9NY4yhtZiDtQeBYUMMDY3Xa9Hrg72o5fUKeOsETzm/J2', 'Bella Saphira', 6, 'bella.saphira@student.ac.id', '081255500205'),
(229, 'mahasiswa29', '$2y$12$UQRXGIGRE.263fmZBsOH2eX0w.F6TNX2A6U4egIsM2S1JY2xMlV8e', 'Citra Adinda', 6, 'citra.adinda@student.ac.id', '081255500206'),
(230, 'mahasiswa30', '$2y$12$B1iuXNexqjaVyIzi5l9mjelZl1r4vZ0w1iVlMiOWB.U8U601u.0TG', 'Dika Wahyudi', 6, 'dika.wahyudi@student.ac.id', '081255500207'),
(231, 'mahasiswa31', '$2y$12$TvtQjaPxfGeO.xZzG/BYS.ddj5SfDnkwJVADeC13ZNDhJkhHbNlO.', 'Erika Putri', 6, 'erika.putri@student.ac.id', '081255500208'),
(232, 'mahasiswa32', '$2y$12$gwNxd0bWZp6w.8.CNtFhku5Isfhe91kvB1DO0PMxM6B3WSVT9Rp4K', 'Fajar Nugraha', 6, 'fajar.nugraha@student.ac.id', '081255500209'),
(233, 'mahasiswa33', '$2y$12$2ohZyn.od1Y1KS2knwVlQe4rhmRYTpH7yXhYdaWPYSOkclVS0qqUq', 'Gita Amelia', 6, 'gita.amelia@student.ac.id', '081255500210'),
(234, 'mahasiswa34', '$2y$12$AN9bA8cW4BIa7l/KVwuLN.RV0oHhpgZxRxkqB.zNeJ1YJ2kmEzoaW', 'Hafiz Alfarizi', 6, 'hafiz.alfarizi@student.ac.id', '081255500211'),
(235, 'mahasiswa35', '$2y$12$DOZPx5Y90u9M3knV0SBYDufcCJse..9DZ9kN5ZzW/GYqfYm9lSYgW', 'Indah Permatasari', 6, 'indah.permatasari@student.ac.id', '081255500212'),
(236, 'mahasiswa36', '$2y$12$VnWxbDbhv.vIIy0ARR4gj.pL/PwqmKQVGbCR8YV1Fpr2iXn5s9HDy', 'Joko Susilo', 6, 'joko.susilo@student.ac.id', '081255500213'),
(237, 'mahasiswa37', '$2y$12$TznZ18S9J7IDs2SAA/8mCe.KwxcLdvTqYKKYXA50/MxVVL3fRf6nK', 'Kartika Sari', 6, 'kartika.sari@student.ac.id', '081255500214'),
(238, 'mahasiswa38', '$2y$12$eHf291LoXRRuJb35UutrD.N/5K70vBCJVhGsRQ4tDvff7JRwsJzze', 'Lutfi Hakim', 6, 'lutfi.hakim@student.ac.id', '081255500215'),
(239, 'mahasiswa39', '$2y$12$bCnGAFdUSRXrPqf74iSswefnDJhXLiEVI.f.TUFOc6BeSe5ofo8kC', 'Mega Utami', 6, 'mega.utami@student.ac.id', '081255500216'),
(240, 'mahasiswa40', '$2y$12$XnxpN595L3gm4Ozkih24YOPI76TlHv3UJqJfMdiHOZAwOpgbEm3zS', 'Nanda Pratama', 6, 'nanda.pratama@student.ac.id', '081255500217'),
(241, 'mahasiswa41', '$2y$12$y5QIFwr11SQtkc0sNv0ALeLYPIxxlI1XWVHdGdmXU.Z3pcV5do1Ly', 'Olivia Jensen', 6, 'olivia.jensen@student.ac.id', '081255500218'),
(242, 'mahasiswa42', '$2y$12$JE5zskfqditq4gdGMRiYmuqvvxzHnVcn0/ViDCrnAo88kRCKxAnlG', 'Pandu Wijaya', 6, 'pandu.wijaya@student.ac.id', '081255500219'),
(243, 'mahasiswa43', '$2y$12$m/br5IQIS2U8Z5.d4wzTM.MikyzuQXXL4qCs2L8MaY4drG9rxjOau', 'Ratih Kumala', 6, 'ratih.kumala@student.ac.id', '081255500220'),
(244, 'mahasiswa44', '$2y$12$A1x.Qst7fVeY8XJw5R3ylOGBiDg.Go0OdukhOXOMimRCqpGxhG.p6', 'Sakti Mahendra', 6, 'sakti.mahendra@student.ac.id', '081255500221'),
(245, 'mahasiswa45', '$2y$12$Td3ElpvNEOBKBeKzufRZlOScPpUBGi91UJuWcnqbdHimP5yW/UJc.', 'Tiara Ananda', 6, 'tiara.ananda@student.ac.id', '081255500222'),
(246, 'mahasiswa46', '$2y$12$W/YW79Sc8OwpbfjnR1fls.7e2QRD8HiW9BLbszQazPJ8DZGCKT2nW', 'Umar Abdullah', 6, 'umar.abdullah@student.ac.id', '081255500223'),
(247, 'mahasiswa47', '$2y$12$cxjr8.4n1mpUanlT.lKbDebuLuvT25Rry1VvxGHFDF9ijd79nJHnm', 'Vania Mariska', 6, 'vania.mariska@student.ac.id', '081255500224'),
(248, 'mahasiswa48', '$2y$12$aCHaGZRyOZYXetP7R4C6D.v4hY4hLclFL1Evy8VdfNpOrAUatpAC6', 'Wahyu Setiawan', 6, 'wahyu.setiawan@student.ac.id', '081255500225'),
(249, 'mahasiswa49', '$2y$12$od6x5XRvuR42sMSVpKJcneB7UgeYC7yGhnoR6ru2itHz4YmDnNXxq', 'Yasmine Wildblood', 6, 'yasmine.wildblood@student.ac.id', '081255500226'),
(250, 'mahasiswa50', '$2y$12$ZWhRiFBpu9LsHAC73jAOu.5uR1psHu96A0vKcNh5vmaOaNaT8sbJu', 'Zacky Ramadhan', 6, 'zacky.ramadhan@student.ac.id', '081255500227'),
(251, 'mahasiswa51', '$2y$12$irq538./zweZlfczsTAacuRmtDcj6En4o0K80Y2f1WdBY5IFTIlim', 'Amanda Manopo', 6, 'amanda.manopo@student.ac.id', '081255500228'),
(252, 'mahasiswa52', '$2y$12$GTyDTv09uQgX86Wf9ce3fOXM6QPk7VDMRLPcn2d6OR9XijNJVIiYO', 'Bagas Aditya', 6, 'bagas.aditya@student.ac.id', '081255500229'),
(253, 'mahasiswa53', '$2y$12$nV0Hjd8HWKWinUD.YeC3Q.0lgf/b0AXfC0wXkKr1lhTVQrz/oX2sK', 'Cindy Gulla', 6, 'cindy.gulla@student.ac.id', '081255500230'),
(254, 'mahasiswa54', '$2y$12$8rlSKxFYoikDx0nE9tZQa.mW/PbisTvQHn3IpCuwhRjT3wXbMhWyG', 'Dimas Anggara', 6, 'dimas.anggara@student.ac.id', '081255500231'),
(255, 'mahasiswa55', '$2y$12$2zrGf6wDfbp3SSLvallpEuHtLsrsakq9DJ2VJeTC4.3fkzenpcZt6', 'Elina Joerg', 6, 'elina.joerg@student.ac.id', '081255500232'),
(256, 'mahasiswa56', '$2y$12$X93UI32Q9sV7Bbu0Luqdq.9QE0H9qpGrtYLS3TWuenn/QGlmvu/Qu', 'Farhan Jamil', 6, 'farhan.jamil@student.ac.id', '081255500233'),
(257, 'mahasiswa57', '$2y$12$DjEpoEdImqineG3BwdaYe.ga3KIMPzecp2BLpwtie4joNPx.7cboe', 'Gisella Anastasia', 6, 'gisella.anastasia@student.ac.id', '081255500234'),
(258, 'mahasiswa58', '$2y$12$Cpnpx4l21trC9z36hPUituDHw/zQw2XknMXE2toSXP7kBtlTjnJUC', 'Harris Vriza', 6, 'harris.vriza@student.ac.id', '081255500235'),
(259, 'mahasiswa59', '$2y$12$OCrVQD4iPddMuSvCqe9HYetkavB7Aye2GwouBPCRj5nd7quFsm3NW', 'Irish Bella', 6, 'irish.bella@student.ac.id', '081255500236'),
(260, 'mahasiswa60', '$2y$12$wrwiuEgGyyD.bAmP4n2iA.HwR3lPDdxI0NcDwDuQy/KuGM3lJlle2', 'Jeffri Nichol', 6, 'jeffri.nichol@student.ac.id', '081255500237'),
(261, 'mahasiswa61', '$2y$12$Tel8BQxYo7ESDvo3JlGQfemDoeRJcws1lNG8GI5U63nC3a1mpeRf6', 'Kesha Ratuliu', 6, 'kesha.ratuliu@student.ac.id', '081255500238'),
(262, 'mahasiswa62', '$2y$12$dCfQ1OL.QtmA3DVIKW3Y3Ol.e0FTAUEx/turw2xJgAyXCNhzBJoN2', 'Laura Basuki', 6, 'laura.basuki@student.ac.id', '081255500239'),
(263, 'mahasiswa63', '$2y$12$U3M2sIEhs5tYToQeQBzf5e11cZzTFMaiVm.BFz8DIl/SmE4Pnjm3a', 'Marcell Darwin', 6, 'marcell.darwin@student.ac.id', '081255500240'),
(264, 'mahasiswa64', '$2y$12$uYfBQvr8Zt2maO8RnJqib.a2yFVUgmcBHFvwxAroEh3xA.Ukb9Bf6', 'Natasha Wilona', 6, 'natasha.wilona@student.ac.id', '081255500241'),
(265, 'mahasiswa65', '$2y$12$DFWe9UoLmOCt9Mh9as4Vremcr97heEsgkKW40SHALdqvCtfI1o2zC', 'Omar Daniel', 6, 'omar.daniel@student.ac.id', '081255500242'),
(266, 'mahasiswa66', '$2y$12$pWuhCXOqfOYHqSWI7Vp4P.jrtePDC2thW0RJ1ACWurL6kWG9j7JRO', 'Pamela Bowie', 6, 'pamela.bowie@student.ac.id', '081255500243'),
(267, 'mahasiswa67', '$2y$12$PX2f0csHEm.nf1V57vStB.fE4vEYYhq8LKfFV5NM8Q10yQE5ndE4G', 'Randy Martin', 6, 'randy.martin@student.ac.id', '081255500244'),
(268, 'mahasiswa68', '$2y$12$tAbQskOFl7z4khvWtPPv/eeaIzzJ1sjGqsUEdFImsNc2XSiuXy.te', 'Salshabilla Adriani', 6, 'salshabilla.adriani@student.ac.id', '081255500245'),
(269, 'mahasiswa69', '$2y$12$KC7O49gPraqch7kGY0qRVOwgWNydh7jNZv5tsdM6CdokswX7dnRH.', 'Teuku Rassya', 6, 'teuku.rassya@student.ac.id', '081255500246'),
(270, 'mahasiswa70', '$2y$12$eRyZdUGZH.IZbFthKDe7Pen8PZR0hkMcg3hXttBToM2aKlE2i4qa2', 'Valerie Thomas', 6, 'valerie.thomas@student.ac.id', '081255500247'),
(301, 'sri_wahyuni', '$2y$10$Tkm0TFZVJw9VKC6gd6q.dOCkeyVIh4NaD0OVrNeJZXI6XW4XsjDV2', 'Sri Wahyuni', 1, 'sri.wahyuni@fakultas.ac.id', '081255500101'),
(302, 'joko_susanto', '$2y$10$Tkm0TFZVJw9VKC6gd6q.dOCkeyVIh4NaD0OVrNeJZXI6XW4XsjDV2', 'Joko Susanto', 1, 'joko.susanto@fakultas.ac.id', '081255500102'),
(303, 'endang_lestari', '$2y$10$Tkm0TFZVJw9VKC6gd6q.dOCkeyVIh4NaD0OVrNeJZXI6XW4XsjDV2', 'Endang Lestari', 1, 'endang.lestari@fakultas.ac.id', '081255500103'),
(304, 'agung_santoso', '$2y$10$Tkm0TFZVJw9VKC6gd6q.dOCkeyVIh4NaD0OVrNeJZXI6XW4XsjDV2', 'Agung Santoso', 1, 'agung.santoso@fakultas.ac.id', '081255500104'),
(305, 'siti_aminah', '$2y$10$Tkm0TFZVJw9VKC6gd6q.dOCkeyVIh4NaD0OVrNeJZXI6XW4XsjDV2', 'Siti Aminah', 1, 'siti.aminah@fakultas.ac.id', '081255500105'),
(306, 'bambang_irawan', '$2y$10$Tkm0TFZVJw9VKC6gd6q.dOCkeyVIh4NaD0OVrNeJZXI6XW4XsjDV2', 'Bambang Irawan', 1, 'bambang.irawan@fakultas.ac.id', '081255500106'),
(307, 'dewi_sartika', '$2y$10$Tkm0TFZVJw9VKC6gd6q.dOCkeyVIh4NaD0OVrNeJZXI6XW4XsjDV2', 'Dewi Sartika', 1, 'dewi.sartika@fakultas.ac.id', '081255500107'),
(309, 'admin_teknik', '$2y$10$FYtzm2Zh6s1Dz/JHrhtOheuh2eNnoeophr7TYU4n3rImzN2O2zKWG', 'Admin Teknik', 7, 'admin_teknik@fakultas.ac.id', '085648475948'),
(310, 'wadek2', '$2y$12$3QiM2MBVB4xzCdRo//8QxOkjxYxniPHZBXCppmzHOuxIyZblzBWFm', 'Dr. M. Sultan Syahputra, S.T., M.T.', 9, 'sultan.syahputra@fakultas.ac.id', '081255667788'),
(311, 'wadek3', '$2y$12$Dcs/562N3Mgb/aJpD8oOHengrkhFbqdAkEaz/jxQZNKr2zPBq2hi2', 'Dr. Wiwik Amalia, S.Kom., M.Kom.', 10, 'wiwik.amalia@fakultas.ac.id', '081399881122');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Acc_SK_Beban_Mengajar`
--
ALTER TABLE `Acc_SK_Beban_Mengajar`
  ADD PRIMARY KEY (`No`),
  ADD KEY `fk_Acc_SK_Beban_Mengajar_to_Dekan` (`Id_Dekan`);

--
-- Indexes for table `Acc_SK_Dosen_Wali`
--
ALTER TABLE `Acc_SK_Dosen_Wali`
  ADD PRIMARY KEY (`No`),
  ADD KEY `fk_Acc_SK_Dosen_Wali_to_Dekan` (`Id_Dekan`);

--
-- Indexes for table `Acc_SK_Pembimbing_Skripsi`
--
ALTER TABLE `Acc_SK_Pembimbing_Skripsi`
  ADD PRIMARY KEY (`No`),
  ADD KEY `fk_Pembimbing_Skripsi_Dosen` (`Id_Dekan`);

--
-- Indexes for table `Acc_SK_Penguji_Skripsi`
--
ALTER TABLE `Acc_SK_Penguji_Skripsi`
  ADD PRIMARY KEY (`No`),
  ADD KEY `fk_penguji_dekan` (`Id_Dekan`);

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
  ADD KEY `fk_dosen_prodi` (`Id_Prodi`),
  ADD KEY `fk_fakultas` (`Id_Fakultas`);

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
  ADD PRIMARY KEY (`Id_Fakultas`),
  ADD KEY `fk_dekan` (`Id_Dekan`);

--
-- Indexes for table `File_Arsip`
--
ALTER TABLE `File_Arsip`
  ADD PRIMARY KEY (`Id_File_Arsip`),
  ADD KEY `Id_Tugas_Surat` (`Id_Tugas_Surat`),
  ADD KEY `Id_User` (`Id_Pemberi_Tugas_Surat`),
  ADD KEY `arsip_penerima` (`Id_Penerima_Tugas_Surat`);

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
-- Indexes for table `Jurusan`
--
ALTER TABLE `Jurusan`
  ADD PRIMARY KEY (`Id_Jurusan`),
  ADD KEY `Id_Fakultas` (`Id_Fakultas`),
  ADD KEY `fk_Kajur` (`Id_Kajur`);

--
-- Indexes for table `Kendaraan`
--
ALTER TABLE `Kendaraan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kendaraan_plat_nomor_unique` (`plat_nomor`);

--
-- Indexes for table `Mahasiswa`
--
ALTER TABLE `Mahasiswa`
  ADD PRIMARY KEY (`Id_Mahasiswa`),
  ADD KEY `fk_users` (`Id_User`),
  ADD KEY `fk_prodi` (`Id_Prodi`),
  ADD KEY `fk_fakultas_mahasiswa` (`Id_Fakultas`);

--
-- Indexes for table `Matakuliah`
--
ALTER TABLE `Matakuliah`
  ADD PRIMARY KEY (`Nomor`),
  ADD KEY `fk_prodi_MATKUL` (`Id_Prodi`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Notifikasi`
--
ALTER TABLE `Notifikasi`
  ADD PRIMARY KEY (`Id_Notifikasi`),
  ADD KEY `fk_dest_user` (`Dest_user`),
  ADD KEY `fk_source_user` (`Source_User`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `Pegawai_Fakultas`
--
ALTER TABLE `Pegawai_Fakultas`
  ADD PRIMARY KEY (`Id_Pegawai`),
  ADD KEY `Id_User` (`Id_User`),
  ADD KEY `Id_Fakultas` (`Id_Fakultas`);

--
-- Indexes for table `Pegawai_Prodi`
--
ALTER TABLE `Pegawai_Prodi`
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
  ADD KEY `prodi_fakultas` (`Id_Fakultas`),
  ADD KEY `fk_Id_Jurusan` (`Id_Jurusan`),
  ADD KEY `fk_kaprodi` (`Id_Kaprodi`);

--
-- Indexes for table `Req_SK_Beban_Mengajar`
--
ALTER TABLE `Req_SK_Beban_Mengajar`
  ADD PRIMARY KEY (`No`),
  ADD KEY `fk_Prodi_Beban_Mengajar` (`Id_Prodi`),
  ADD KEY `fk_kaProdi_Beban_Mengajar` (`Id_Dosen_Kaprodi`),
  ADD KEY `fk_req_to_acc_beban_mnegajar` (`Id_Acc_SK_Beban_Mengajar`);

--
-- Indexes for table `Req_SK_Dosen_Wali`
--
ALTER TABLE `Req_SK_Dosen_Wali`
  ADD PRIMARY KEY (`No`),
  ADD KEY `fk_Id_Prodi` (`Id_Prodi`),
  ADD KEY `fk_req_sk_dosen_wali_dosen` (`Id_Dosen_Kaprodi`),
  ADD KEY `fk_req_acc_sk_dosen_wali` (`Id_Acc_SK_Dosen_Wali`);

--
-- Indexes for table `Req_SK_Pembimbing_Skripsi`
--
ALTER TABLE `Req_SK_Pembimbing_Skripsi`
  ADD PRIMARY KEY (`No`),
  ADD KEY `fk_sk_pembimbing_skripsi_kaprodi` (`Id_Prodi`),
  ADD KEY `fk_Acc_Beban_Mengajar` (`Id_Acc_SK_Pembimbing_Skripsi`),
  ADD KEY `fk_pembimbing_dosen_kaprodi` (`Id_Dosen_Kaprodi`);

--
-- Indexes for table `Req_SK_Penguji_Skripsi`
--
ALTER TABLE `Req_SK_Penguji_Skripsi`
  ADD PRIMARY KEY (`No`),
  ADD KEY `fk_prodi_penguji` (`Id_Prodi`),
  ADD KEY `fk_kaprodi_penguji` (`Id_Dosen_Kaprodi`),
  ADD KEY `fk_Acc_Penguji_skripsi` (`Id_Acc_SK_Penguji_Skripsi`);

--
-- Indexes for table `Roles`
--
ALTER TABLE `Roles`
  ADD PRIMARY KEY (`Id_Role`);

--
-- Indexes for table `Ruangan`
--
ALTER TABLE `Ruangan`
  ADD PRIMARY KEY (`ID_Ruangan`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `Surat_Dispensasi`
--
ALTER TABLE `Surat_Dispensasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `surat_dispensasi_id_user_foreign` (`Id_User`),
  ADD KEY `surat_dispensasi_id_pejabat_wadek3_foreign` (`Id_Pejabat_Wadek3`),
  ADD KEY `surat_dispensasi_verifikasi_admin_by_foreign` (`verifikasi_admin_by`),
  ADD KEY `surat_dispensasi_acc_wadek3_by_foreign` (`acc_wadek3_by`);

--
-- Indexes for table `surat_izin_kegiatan_malams`
--
ALTER TABLE `surat_izin_kegiatan_malams`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Surat_Kelakuan_Baik`
--
ALTER TABLE `Surat_Kelakuan_Baik`
  ADD PRIMARY KEY (`id`),
  ADD KEY `surat_kelakuan_baik_id_user_foreign` (`Id_User`),
  ADD KEY `surat_kelakuan_baik_id_pejabat_foreign` (`Id_Pejabat`);

--
-- Indexes for table `Surat_Ket_Aktif`
--
ALTER TABLE `Surat_Ket_Aktif`
  ADD PRIMARY KEY (`id_no`),
  ADD KEY `fk_surat_keterangan_aktif_to_penerima_tugas` (`Id_Penerima_Tugas`),
  ADD KEY `fk_surat_keterangan_aktif_to_pemberi_tugas` (`Id_Pemberi_Tugas`);

--
-- Indexes for table `Surat_Legalisir`
--
ALTER TABLE `Surat_Legalisir`
  ADD PRIMARY KEY (`id_no`),
  ADD KEY `surat_legalisir_id_user_foreign` (`Id_User`),
  ADD KEY `surat_legalisir_id_pejabat_foreign` (`Id_Pejabat`);

--
-- Indexes for table `Surat_Magang`
--
ALTER TABLE `Surat_Magang`
  ADD PRIMARY KEY (`id_no`),
  ADD KEY `fk_nama_koordinator` (`Nama_Koordinator`),
  ADD KEY `fk_nama_dekan` (`Nama_Dekan`);

--
-- Indexes for table `Surat_Magang_Invitations`
--
ALTER TABLE `Surat_Magang_Invitations`
  ADD PRIMARY KEY (`id_no`),
  ADD KEY `surat_magang_invitations_id_mahasiswa_pengundang_foreign` (`id_mahasiswa_pengundang`),
  ADD KEY `surat_magang_invitations_id_mahasiswa_diundang_status_index` (`id_mahasiswa_diundang`,`status`),
  ADD KEY `surat_magang_invitations_id_surat_magang_index` (`id_surat_magang`);

--
-- Indexes for table `Surat_Peminjaman_Mobil`
--
ALTER TABLE `Surat_Peminjaman_Mobil`
  ADD PRIMARY KEY (`id`),
  ADD KEY `surat_peminjaman_mobil_id_user_foreign` (`Id_User`),
  ADD KEY `surat_peminjaman_mobil_id_kendaraan_foreign` (`Id_Kendaraan`),
  ADD KEY `surat_peminjaman_mobil_id_pejabat_foreign` (`Id_Pejabat`);

--
-- Indexes for table `Surat_Peminjaman_Ruang`
--
ALTER TABLE `Surat_Peminjaman_Ruang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `surat_peminjaman_ruang_id_ruangan_foreign` (`Id_Ruangan`);

--
-- Indexes for table `Surat_Tidak_Beasiswa`
--
ALTER TABLE `Surat_Tidak_Beasiswa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `surat_tidak_beasiswa_id_user_foreign` (`Id_User`),
  ADD KEY `surat_tidak_beasiswa_id_pejabat_foreign` (`Id_Pejabat`);

--
-- Indexes for table `surat_verifications`
--
ALTER TABLE `surat_verifications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `surat_verifications_token_unique` (`token`),
  ADD KEY `surat_verifications_id_tugas_surat_index` (`id_tugas_surat`),
  ADD KEY `surat_verifications_id_letter_index` (`id_letter`),
  ADD KEY `surat_verifications_letter_type_index` (`letter_type`);

--
-- Indexes for table `Tugas`
--
ALTER TABLE `Tugas`
  ADD PRIMARY KEY (`Id_Tugas`),
  ADD KEY `Id_Pemberi_Tugas` (`Id_Pemberi_Tugas`),
  ADD KEY `Id_Penerima_Tugas` (`Id_Penerima_Tugas`);

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
-- AUTO_INCREMENT for table `Acc_SK_Beban_Mengajar`
--
ALTER TABLE `Acc_SK_Beban_Mengajar`
  MODIFY `No` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `Acc_SK_Dosen_Wali`
--
ALTER TABLE `Acc_SK_Dosen_Wali`
  MODIFY `No` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `Acc_SK_Pembimbing_Skripsi`
--
ALTER TABLE `Acc_SK_Pembimbing_Skripsi`
  MODIFY `No` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `Acc_SK_Penguji_Skripsi`
--
ALTER TABLE `Acc_SK_Penguji_Skripsi`
  MODIFY `No` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `Dosen`
--
ALTER TABLE `Dosen`
  MODIFY `Id_Dosen` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Fakultas`
--
ALTER TABLE `Fakultas`
  MODIFY `Id_Fakultas` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `File_Arsip`
--
ALTER TABLE `File_Arsip`
  MODIFY `Id_File_Arsip` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Jurusan`
--
ALTER TABLE `Jurusan`
  MODIFY `Id_Jurusan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `Kendaraan`
--
ALTER TABLE `Kendaraan`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `Matakuliah`
--
ALTER TABLE `Matakuliah`
  MODIFY `Nomor` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `Notifikasi`
--
ALTER TABLE `Notifikasi`
  MODIFY `Id_Notifikasi` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=231;

--
-- AUTO_INCREMENT for table `Pegawai_Prodi`
--
ALTER TABLE `Pegawai_Prodi`
  MODIFY `Id_Pegawai` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `Req_SK_Beban_Mengajar`
--
ALTER TABLE `Req_SK_Beban_Mengajar`
  MODIFY `No` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `Req_SK_Dosen_Wali`
--
ALTER TABLE `Req_SK_Dosen_Wali`
  MODIFY `No` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `Req_SK_Pembimbing_Skripsi`
--
ALTER TABLE `Req_SK_Pembimbing_Skripsi`
  MODIFY `No` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `Req_SK_Penguji_Skripsi`
--
ALTER TABLE `Req_SK_Penguji_Skripsi`
  MODIFY `No` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `Ruangan`
--
ALTER TABLE `Ruangan`
  MODIFY `ID_Ruangan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `Surat_Dispensasi`
--
ALTER TABLE `Surat_Dispensasi`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `surat_izin_kegiatan_malams`
--
ALTER TABLE `surat_izin_kegiatan_malams`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Surat_Kelakuan_Baik`
--
ALTER TABLE `Surat_Kelakuan_Baik`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `Surat_Ket_Aktif`
--
ALTER TABLE `Surat_Ket_Aktif`
  MODIFY `id_no` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `Surat_Legalisir`
--
ALTER TABLE `Surat_Legalisir`
  MODIFY `id_no` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `Surat_Magang`
--
ALTER TABLE `Surat_Magang`
  MODIFY `id_no` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `Surat_Magang_Invitations`
--
ALTER TABLE `Surat_Magang_Invitations`
  MODIFY `id_no` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `Surat_Peminjaman_Mobil`
--
ALTER TABLE `Surat_Peminjaman_Mobil`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `Surat_Peminjaman_Ruang`
--
ALTER TABLE `Surat_Peminjaman_Ruang`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `Surat_Tidak_Beasiswa`
--
ALTER TABLE `Surat_Tidak_Beasiswa`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `surat_verifications`
--
ALTER TABLE `surat_verifications`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `Tugas`
--
ALTER TABLE `Tugas`
  MODIFY `Id_Tugas` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Acc_SK_Beban_Mengajar`
--
ALTER TABLE `Acc_SK_Beban_Mengajar`
  ADD CONSTRAINT `fk_Acc_SK_Beban_Mengajar_to_Dekan` FOREIGN KEY (`Id_Dekan`) REFERENCES `Dosen` (`Id_Dosen`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `Acc_SK_Dosen_Wali`
--
ALTER TABLE `Acc_SK_Dosen_Wali`
  ADD CONSTRAINT `fk_Acc_SK_Dosen_Wali_to_Dekan` FOREIGN KEY (`Id_Dekan`) REFERENCES `Dosen` (`Id_Dosen`);

--
-- Constraints for table `Acc_SK_Pembimbing_Skripsi`
--
ALTER TABLE `Acc_SK_Pembimbing_Skripsi`
  ADD CONSTRAINT `fk_Pembimbing_Skripsi_Dosen` FOREIGN KEY (`Id_Dekan`) REFERENCES `Dosen` (`Id_Dosen`);

--
-- Constraints for table `Acc_SK_Penguji_Skripsi`
--
ALTER TABLE `Acc_SK_Penguji_Skripsi`
  ADD CONSTRAINT `fk_penguji_dekan` FOREIGN KEY (`Id_Dekan`) REFERENCES `Dosen` (`Id_Dosen`);

--
-- Constraints for table `Dosen`
--
ALTER TABLE `Dosen`
  ADD CONSTRAINT `Dosen_ibfk_1` FOREIGN KEY (`Id_User`) REFERENCES `Users` (`Id_User`),
  ADD CONSTRAINT `fk_dosen_pejabat` FOREIGN KEY (`Id_Pejabat`) REFERENCES `Pejabat` (`Id_Pejabat`),
  ADD CONSTRAINT `fk_dosen_prodi` FOREIGN KEY (`Id_Prodi`) REFERENCES `Prodi` (`Id_Prodi`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_fakultas` FOREIGN KEY (`Id_Fakultas`) REFERENCES `Fakultas` (`Id_Fakultas`);

--
-- Constraints for table `Fakultas`
--
ALTER TABLE `Fakultas`
  ADD CONSTRAINT `fk_dekan` FOREIGN KEY (`Id_Dekan`) REFERENCES `Dosen` (`Id_Dosen`);

--
-- Constraints for table `File_Arsip`
--
ALTER TABLE `File_Arsip`
  ADD CONSTRAINT `arsip_penerima` FOREIGN KEY (`Id_Penerima_Tugas_Surat`) REFERENCES `Users` (`Id_User`),
  ADD CONSTRAINT `File_Arsip_ibfk_2` FOREIGN KEY (`Id_Pemberi_Tugas_Surat`) REFERENCES `Users` (`Id_User`);

--
-- Constraints for table `Jurusan`
--
ALTER TABLE `Jurusan`
  ADD CONSTRAINT `fk_Kajur` FOREIGN KEY (`Id_Kajur`) REFERENCES `Dosen` (`Id_Dosen`),
  ADD CONSTRAINT `Jurusan_ibfk_1` FOREIGN KEY (`Id_Fakultas`) REFERENCES `Fakultas` (`Id_Fakultas`);

--
-- Constraints for table `Mahasiswa`
--
ALTER TABLE `Mahasiswa`
  ADD CONSTRAINT `fk_fakultas_mahasiswa` FOREIGN KEY (`Id_Fakultas`) REFERENCES `Fakultas` (`Id_Fakultas`),
  ADD CONSTRAINT `fk_prodi` FOREIGN KEY (`Id_Prodi`) REFERENCES `Prodi` (`Id_Prodi`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_users` FOREIGN KEY (`Id_User`) REFERENCES `Users` (`Id_User`);

--
-- Constraints for table `Matakuliah`
--
ALTER TABLE `Matakuliah`
  ADD CONSTRAINT `fk_prodi_MATKUL` FOREIGN KEY (`Id_Prodi`) REFERENCES `Prodi` (`Id_Prodi`);

--
-- Constraints for table `Notifikasi`
--
ALTER TABLE `Notifikasi`
  ADD CONSTRAINT `fk_dest_user` FOREIGN KEY (`Dest_user`) REFERENCES `Users` (`Id_User`),
  ADD CONSTRAINT `fk_source_user` FOREIGN KEY (`Source_User`) REFERENCES `Users` (`Id_User`);

--
-- Constraints for table `Pegawai_Fakultas`
--
ALTER TABLE `Pegawai_Fakultas`
  ADD CONSTRAINT `Pegawai_Fakultas_ibfk_1` FOREIGN KEY (`Id_User`) REFERENCES `Users` (`Id_User`),
  ADD CONSTRAINT `Pegawai_Fakultas_ibfk_2` FOREIGN KEY (`Id_Fakultas`) REFERENCES `Fakultas` (`Id_Fakultas`);

--
-- Constraints for table `Pegawai_Prodi`
--
ALTER TABLE `Pegawai_Prodi`
  ADD CONSTRAINT `fk_pegawai_prodi` FOREIGN KEY (`Id_Prodi`) REFERENCES `Prodi` (`Id_Prodi`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `Pegawai_Prodi_ibfk_1` FOREIGN KEY (`Id_User`) REFERENCES `Users` (`Id_User`);

--
-- Constraints for table `Penilaian_Kinerja`
--
ALTER TABLE `Penilaian_Kinerja`
  ADD CONSTRAINT `Penilaian_Kinerja_ibfk_1` FOREIGN KEY (`Id_Pegawai`) REFERENCES `Users` (`Id_User`),
  ADD CONSTRAINT `Penilaian_Kinerja_ibfk_2` FOREIGN KEY (`Id_Penilai`) REFERENCES `Users` (`Id_User`);

--
-- Constraints for table `Prodi`
--
ALTER TABLE `Prodi`
  ADD CONSTRAINT `fk_Id_Jurusan` FOREIGN KEY (`Id_Jurusan`) REFERENCES `Jurusan` (`Id_Jurusan`),
  ADD CONSTRAINT `fk_kaprodi` FOREIGN KEY (`Id_Kaprodi`) REFERENCES `Dosen` (`Id_Dosen`),
  ADD CONSTRAINT `prodi_fakultas` FOREIGN KEY (`Id_Fakultas`) REFERENCES `Fakultas` (`Id_Fakultas`);

--
-- Constraints for table `Req_SK_Beban_Mengajar`
--
ALTER TABLE `Req_SK_Beban_Mengajar`
  ADD CONSTRAINT `fk_kaProdi_Beban_Mengajar` FOREIGN KEY (`Id_Dosen_Kaprodi`) REFERENCES `Dosen` (`Id_Dosen`),
  ADD CONSTRAINT `fk_Prodi_Beban_Mengajar` FOREIGN KEY (`Id_Prodi`) REFERENCES `Prodi` (`Id_Prodi`),
  ADD CONSTRAINT `fk_req_to_acc_beban_mnegajar` FOREIGN KEY (`Id_Acc_SK_Beban_Mengajar`) REFERENCES `Acc_SK_Beban_Mengajar` (`No`) ON DELETE CASCADE;

--
-- Constraints for table `Req_SK_Dosen_Wali`
--
ALTER TABLE `Req_SK_Dosen_Wali`
  ADD CONSTRAINT `fk_Id_Prodi` FOREIGN KEY (`Id_Prodi`) REFERENCES `Prodi` (`Id_Prodi`),
  ADD CONSTRAINT `fk_req_acc_sk_dosen_wali` FOREIGN KEY (`Id_Acc_SK_Dosen_Wali`) REFERENCES `Acc_SK_Dosen_Wali` (`No`),
  ADD CONSTRAINT `fk_req_sk_dosen_wali_dosen` FOREIGN KEY (`Id_Dosen_Kaprodi`) REFERENCES `Dosen` (`Id_Dosen`);

--
-- Constraints for table `Req_SK_Pembimbing_Skripsi`
--
ALTER TABLE `Req_SK_Pembimbing_Skripsi`
  ADD CONSTRAINT `fk_Acc_Beban_Mengajar` FOREIGN KEY (`Id_Acc_SK_Pembimbing_Skripsi`) REFERENCES `Acc_SK_Pembimbing_Skripsi` (`No`),
  ADD CONSTRAINT `fk_pembimbing_dosen_kaprodi` FOREIGN KEY (`Id_Dosen_Kaprodi`) REFERENCES `Dosen` (`Id_Dosen`),
  ADD CONSTRAINT `fk_sk_pembimbing_skripsi_kaprodi` FOREIGN KEY (`Id_Prodi`) REFERENCES `Prodi` (`Id_Prodi`);

--
-- Constraints for table `Req_SK_Penguji_Skripsi`
--
ALTER TABLE `Req_SK_Penguji_Skripsi`
  ADD CONSTRAINT `fk_Acc_Penguji_skripsi` FOREIGN KEY (`Id_Acc_SK_Penguji_Skripsi`) REFERENCES `Acc_SK_Penguji_Skripsi` (`No`),
  ADD CONSTRAINT `fk_kaprodi_penguji` FOREIGN KEY (`Id_Dosen_Kaprodi`) REFERENCES `Dosen` (`Id_Dosen`),
  ADD CONSTRAINT `fk_prodi_penguji` FOREIGN KEY (`Id_Prodi`) REFERENCES `Prodi` (`Id_Prodi`);

--
-- Constraints for table `Surat_Dispensasi`
--
ALTER TABLE `Surat_Dispensasi`
  ADD CONSTRAINT `surat_dispensasi_acc_wadek3_by_foreign` FOREIGN KEY (`acc_wadek3_by`) REFERENCES `Users` (`Id_User`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `surat_dispensasi_id_pejabat_wadek3_foreign` FOREIGN KEY (`Id_Pejabat_Wadek3`) REFERENCES `Pejabat` (`Id_Pejabat`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `surat_dispensasi_id_user_foreign` FOREIGN KEY (`Id_User`) REFERENCES `Users` (`Id_User`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `surat_dispensasi_verifikasi_admin_by_foreign` FOREIGN KEY (`verifikasi_admin_by`) REFERENCES `Users` (`Id_User`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `Surat_Kelakuan_Baik`
--
ALTER TABLE `Surat_Kelakuan_Baik`
  ADD CONSTRAINT `surat_kelakuan_baik_id_pejabat_foreign` FOREIGN KEY (`Id_Pejabat`) REFERENCES `Pejabat` (`Id_Pejabat`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `surat_kelakuan_baik_id_user_foreign` FOREIGN KEY (`Id_User`) REFERENCES `Users` (`Id_User`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Surat_Ket_Aktif`
--
ALTER TABLE `Surat_Ket_Aktif`
  ADD CONSTRAINT `fk_surat_keterangan_aktif_to_pemberi_tugas` FOREIGN KEY (`Id_Pemberi_Tugas`) REFERENCES `Users` (`Id_User`),
  ADD CONSTRAINT `fk_surat_keterangan_aktif_to_penerima_tugas` FOREIGN KEY (`Id_Penerima_Tugas`) REFERENCES `Users` (`Id_User`);

--
-- Constraints for table `Surat_Legalisir`
--
ALTER TABLE `Surat_Legalisir`
  ADD CONSTRAINT `surat_legalisir_id_pejabat_foreign` FOREIGN KEY (`Id_Pejabat`) REFERENCES `Pejabat` (`Id_Pejabat`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `surat_legalisir_id_user_foreign` FOREIGN KEY (`Id_User`) REFERENCES `Users` (`Id_User`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Surat_Magang`
--
ALTER TABLE `Surat_Magang`
  ADD CONSTRAINT `fk_nama_dekan` FOREIGN KEY (`Nama_Dekan`) REFERENCES `Dosen` (`Id_Dosen`),
  ADD CONSTRAINT `fk_nama_koordinator` FOREIGN KEY (`Nama_Koordinator`) REFERENCES `Dosen` (`Id_Dosen`);

--
-- Constraints for table `Surat_Magang_Invitations`
--
ALTER TABLE `Surat_Magang_Invitations`
  ADD CONSTRAINT `surat_magang_invitations_id_mahasiswa_diundang_foreign` FOREIGN KEY (`id_mahasiswa_diundang`) REFERENCES `Mahasiswa` (`Id_Mahasiswa`) ON DELETE CASCADE,
  ADD CONSTRAINT `surat_magang_invitations_id_mahasiswa_pengundang_foreign` FOREIGN KEY (`id_mahasiswa_pengundang`) REFERENCES `Mahasiswa` (`Id_Mahasiswa`) ON DELETE CASCADE,
  ADD CONSTRAINT `surat_magang_invitations_id_surat_magang_foreign` FOREIGN KEY (`id_surat_magang`) REFERENCES `Surat_Magang` (`id_no`) ON DELETE CASCADE;

--
-- Constraints for table `Surat_Peminjaman_Mobil`
--
ALTER TABLE `Surat_Peminjaman_Mobil`
  ADD CONSTRAINT `surat_peminjaman_mobil_id_kendaraan_foreign` FOREIGN KEY (`Id_Kendaraan`) REFERENCES `Kendaraan` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `surat_peminjaman_mobil_id_pejabat_foreign` FOREIGN KEY (`Id_Pejabat`) REFERENCES `Pejabat` (`Id_Pejabat`) ON DELETE SET NULL,
  ADD CONSTRAINT `surat_peminjaman_mobil_id_user_foreign` FOREIGN KEY (`Id_User`) REFERENCES `Users` (`Id_User`) ON DELETE CASCADE;

--
-- Constraints for table `Surat_Peminjaman_Ruang`
--
ALTER TABLE `Surat_Peminjaman_Ruang`
  ADD CONSTRAINT `surat_peminjaman_ruang_id_ruangan_foreign` FOREIGN KEY (`Id_Ruangan`) REFERENCES `Ruangan` (`ID_Ruangan`) ON DELETE SET NULL;

--
-- Constraints for table `Surat_Tidak_Beasiswa`
--
ALTER TABLE `Surat_Tidak_Beasiswa`
  ADD CONSTRAINT `surat_tidak_beasiswa_id_pejabat_foreign` FOREIGN KEY (`Id_Pejabat`) REFERENCES `Pejabat` (`Id_Pejabat`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `surat_tidak_beasiswa_id_user_foreign` FOREIGN KEY (`Id_User`) REFERENCES `Users` (`Id_User`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Tugas`
--
ALTER TABLE `Tugas`
  ADD CONSTRAINT `Tugas_ibfk_2` FOREIGN KEY (`Id_Pemberi_Tugas`) REFERENCES `Users` (`Id_User`),
  ADD CONSTRAINT `Tugas_ibfk_3` FOREIGN KEY (`Id_Penerima_Tugas`) REFERENCES `Users` (`Id_User`);

--
-- Constraints for table `Users`
--
ALTER TABLE `Users`
  ADD CONSTRAINT `Users_ibfk_1` FOREIGN KEY (`Id_Role`) REFERENCES `Roles` (`Id_Role`) ON DELETE RESTRICT ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
