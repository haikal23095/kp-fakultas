-- MySQL dump 10.13  Distrib 8.0.44, for Linux (x86_64)
--
-- Host: localhost    Database: sistem_fakultas
-- ------------------------------------------------------
-- Server version	8.0.44-0ubuntu0.22.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `Dosen`
--

DROP TABLE IF EXISTS `Dosen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Dosen` (
  `Id_Dosen` int NOT NULL AUTO_INCREMENT,
  `NIP` varchar(255) DEFAULT NULL,
  `Nama_Dosen` varchar(255) DEFAULT NULL,
  `Jenis_Kelamin_Dosen` enum('L','P') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `Alamat_Dosen` text,
  `Id_User` int DEFAULT NULL,
  `Id_Prodi` int DEFAULT NULL,
  `Id_Pejabat` int DEFAULT NULL,
  PRIMARY KEY (`Id_Dosen`),
  KEY `Id_User` (`Id_User`),
  KEY `fk_dosen_pejabat` (`Id_Pejabat`),
  KEY `fk_dosen_prodi` (`Id_Prodi`),
  CONSTRAINT `Dosen_ibfk_1` FOREIGN KEY (`Id_User`) REFERENCES `Users` (`Id_User`),
  CONSTRAINT `fk_dosen_pejabat` FOREIGN KEY (`Id_Pejabat`) REFERENCES `Pejabat` (`Id_Pejabat`),
  CONSTRAINT `fk_dosen_prodi` FOREIGN KEY (`Id_Prodi`) REFERENCES `Prodi` (`Id_Prodi`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=93 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Fakultas`
--

DROP TABLE IF EXISTS `Fakultas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Fakultas` (
  `Id_Fakultas` int NOT NULL AUTO_INCREMENT,
  `Nama_Fakultas` varchar(255) NOT NULL,
  `Id_Dekan` int NOT NULL,
  PRIMARY KEY (`Id_Fakultas`),
  KEY `fk_dekan` (`Id_Dekan`),
  CONSTRAINT `fk_dekan` FOREIGN KEY (`Id_Dekan`) REFERENCES `Dosen` (`Id_Dosen`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `File_Arsip`
--

DROP TABLE IF EXISTS `File_Arsip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `File_Arsip` (
  `Id_File_Arsip` int NOT NULL AUTO_INCREMENT,
  `Id_Tugas_Surat` int DEFAULT NULL,
  `Id_Pemberi_Tugas_Surat` int DEFAULT NULL,
  `Id_Penerima_Tugas_Surat` int DEFAULT NULL,
  `Keterangan` text,
  PRIMARY KEY (`Id_File_Arsip`),
  KEY `Id_Tugas_Surat` (`Id_Tugas_Surat`),
  KEY `Id_User` (`Id_Pemberi_Tugas_Surat`),
  KEY `arsip_penerima` (`Id_Penerima_Tugas_Surat`),
  CONSTRAINT `arsip_penerima` FOREIGN KEY (`Id_Penerima_Tugas_Surat`) REFERENCES `Users` (`Id_User`),
  CONSTRAINT `File_Arsip_ibfk_1` FOREIGN KEY (`Id_Tugas_Surat`) REFERENCES `Tugas_Surat` (`Id_Tugas_Surat`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `File_Arsip_ibfk_2` FOREIGN KEY (`Id_Pemberi_Tugas_Surat`) REFERENCES `Users` (`Id_User`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Jenis_Pekerjaan`
--

DROP TABLE IF EXISTS `Jenis_Pekerjaan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Jenis_Pekerjaan` (
  `Id_Jenis_Pekerjaan` int NOT NULL,
  `Jenis_Pekerjaan` enum('Surat','Non-Surat') DEFAULT NULL,
  `Nama_Pekerjaan` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`Id_Jenis_Pekerjaan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Jenis_Surat`
--

DROP TABLE IF EXISTS `Jenis_Surat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Jenis_Surat` (
  `Id_Jenis_Surat` int NOT NULL,
  `Tipe_Surat` enum('Surat-Keluar','Surat-Masuk') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `Nama_Surat` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`Id_Jenis_Surat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Jurusan`
--

DROP TABLE IF EXISTS `Jurusan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Jurusan` (
  `Id_Jurusan` int NOT NULL AUTO_INCREMENT,
  `Nama_Jurusan` varchar(255) DEFAULT NULL,
  `Id_Fakultas` int DEFAULT NULL,
  `Id_Kajur` int NOT NULL,
  PRIMARY KEY (`Id_Jurusan`),
  KEY `Id_Fakultas` (`Id_Fakultas`),
  KEY `fk_Kajur` (`Id_Kajur`),
  CONSTRAINT `fk_Kajur` FOREIGN KEY (`Id_Kajur`) REFERENCES `Dosen` (`Id_Dosen`),
  CONSTRAINT `Jurusan_ibfk_1` FOREIGN KEY (`Id_Fakultas`) REFERENCES `Fakultas` (`Id_Fakultas`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Mahasiswa`
--

DROP TABLE IF EXISTS `Mahasiswa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Mahasiswa` (
  `Id_Mahasiswa` int NOT NULL,
  `NIM` int NOT NULL,
  `Nama_Mahasiswa` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `Jenis_Kelamin_Mahasiswa` enum('L','P') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `Alamat_Mahasiswa` text,
  `Status_KP` enum('Sedang_Melaksanakan','Tidak_Sedang_Melaksanakan','Telah_Melaksanakan','') NOT NULL DEFAULT 'Tidak_Sedang_Melaksanakan',
  `Angkatan` int DEFAULT NULL,
  `Id_User` int DEFAULT NULL,
  `Id_Prodi` int DEFAULT NULL,
  PRIMARY KEY (`Id_Mahasiswa`),
  KEY `fk_users` (`Id_User`),
  KEY `fk_prodi` (`Id_Prodi`),
  CONSTRAINT `fk_prodi` FOREIGN KEY (`Id_Prodi`) REFERENCES `Prodi` (`Id_Prodi`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_users` FOREIGN KEY (`Id_User`) REFERENCES `Users` (`Id_User`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Notifikasi`
--

DROP TABLE IF EXISTS `Notifikasi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Notifikasi` (
  `Id_Notifikasi` bigint NOT NULL AUTO_INCREMENT,
  `Tipe_Notifikasi` enum('Rejected','Accepted','Invitation','Error') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Pesan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `Dest_user` int NOT NULL,
  `Source_User` int NOT NULL,
  `Is_Read` tinyint(1) NOT NULL DEFAULT '0',
  `Data_Tambahan` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL,
  PRIMARY KEY (`Id_Notifikasi`),
  KEY `fk_dest_user` (`Dest_user`),
  KEY `fk_source_user` (`Source_User`),
  CONSTRAINT `fk_dest_user` FOREIGN KEY (`Dest_user`) REFERENCES `Users` (`Id_User`),
  CONSTRAINT `fk_source_user` FOREIGN KEY (`Source_User`) REFERENCES `Users` (`Id_User`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Pegawai_Fakultas`
--

DROP TABLE IF EXISTS `Pegawai_Fakultas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Pegawai_Fakultas` (
  `Id_Pegawai` int NOT NULL,
  `NIP` varchar(255) DEFAULT NULL,
  `Nama_Pegawai` varchar(255) DEFAULT NULL,
  `Jenis_Kelamin_Pegawai` enum('L','P') DEFAULT NULL,
  `Alamat_Pegawai` text,
  `Id_User` int DEFAULT NULL,
  `Id_Fakultas` int DEFAULT NULL,
  PRIMARY KEY (`Id_Pegawai`),
  KEY `Id_User` (`Id_User`),
  KEY `Id_Fakultas` (`Id_Fakultas`),
  CONSTRAINT `Pegawai_Fakultas_ibfk_1` FOREIGN KEY (`Id_User`) REFERENCES `Users` (`Id_User`),
  CONSTRAINT `Pegawai_Fakultas_ibfk_2` FOREIGN KEY (`Id_Fakultas`) REFERENCES `Fakultas` (`Id_Fakultas`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Pegawai_Prodi`
--

DROP TABLE IF EXISTS `Pegawai_Prodi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Pegawai_Prodi` (
  `Id_Pegawai` int NOT NULL AUTO_INCREMENT,
  `NIP` varchar(255) DEFAULT NULL,
  `Nama_Pegawai` varchar(255) DEFAULT NULL,
  `Jenis_Kelamin_Pegawai` enum('L','P') DEFAULT NULL,
  `Alamat_Pegawai` text,
  `Id_User` int DEFAULT NULL,
  `Id_Prodi` int DEFAULT NULL,
  PRIMARY KEY (`Id_Pegawai`),
  KEY `Id_User` (`Id_User`),
  KEY `fk_pegawai_prodi` (`Id_Prodi`),
  CONSTRAINT `fk_pegawai_prodi` FOREIGN KEY (`Id_Prodi`) REFERENCES `Prodi` (`Id_Prodi`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `Pegawai_Prodi_ibfk_1` FOREIGN KEY (`Id_User`) REFERENCES `Users` (`Id_User`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Pejabat`
--

DROP TABLE IF EXISTS `Pejabat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Pejabat` (
  `Id_Pejabat` int NOT NULL,
  `Nama_Jabatan` enum('Kaprodi','Kajur','Dekan') DEFAULT NULL,
  PRIMARY KEY (`Id_Pejabat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Penilaian_Kinerja`
--

DROP TABLE IF EXISTS `Penilaian_Kinerja`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Penilaian_Kinerja` (
  `Id_Penilaian` int NOT NULL,
  `Id_Pegawai` int DEFAULT NULL,
  `Id_Penilai` int DEFAULT NULL,
  `Skor` enum('1','2','3','4','5') DEFAULT NULL,
  `Komentar` text,
  `Tanggal_Penilaian` date DEFAULT NULL,
  PRIMARY KEY (`Id_Penilaian`),
  KEY `Id_Pegawai` (`Id_Pegawai`),
  KEY `Id_Penilai` (`Id_Penilai`),
  CONSTRAINT `Penilaian_Kinerja_ibfk_1` FOREIGN KEY (`Id_Pegawai`) REFERENCES `Users` (`Id_User`),
  CONSTRAINT `Penilaian_Kinerja_ibfk_2` FOREIGN KEY (`Id_Penilai`) REFERENCES `Users` (`Id_User`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Prodi`
--

DROP TABLE IF EXISTS `Prodi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Prodi` (
  `Id_Prodi` int NOT NULL,
  `Nama_Prodi` varchar(255) DEFAULT NULL,
  `Id_Kaprodi` int NOT NULL,
  `Id_Jurusan` int NOT NULL,
  `Id_Fakultas` int NOT NULL,
  PRIMARY KEY (`Id_Prodi`),
  KEY `prodi_fakultas` (`Id_Fakultas`),
  KEY `fk_Id_Jurusan` (`Id_Jurusan`),
  KEY `fk_kaprodi` (`Id_Kaprodi`),
  CONSTRAINT `fk_Id_Jurusan` FOREIGN KEY (`Id_Jurusan`) REFERENCES `Jurusan` (`Id_Jurusan`),
  CONSTRAINT `fk_kaprodi` FOREIGN KEY (`Id_Kaprodi`) REFERENCES `Dosen` (`Id_Dosen`),
  CONSTRAINT `prodi_fakultas` FOREIGN KEY (`Id_Fakultas`) REFERENCES `Fakultas` (`Id_Fakultas`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Roles`
--

DROP TABLE IF EXISTS `Roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Roles` (
  `Id_Role` int NOT NULL,
  `Name_Role` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`Id_Role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Surat_Ket_Aktif`
--

DROP TABLE IF EXISTS `Surat_Ket_Aktif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Surat_Ket_Aktif` (
  `id_no` int NOT NULL AUTO_INCREMENT,
  `Id_Tugas_Surat` int DEFAULT NULL,
  `Nomor_Surat` varchar(255) DEFAULT NULL,
  `Tahun_Akademik` varchar(255) DEFAULT NULL,
  `Deskripsi` text,
  `KRS` text,
  `is_urgent` tinyint(1) NOT NULL DEFAULT '0',
  `urgent_reason` text,
  PRIMARY KEY (`id_no`),
  KEY `Id_Tugas_Surat` (`Id_Tugas_Surat`),
  CONSTRAINT `Surat_Ket_Aktif_ibfk_1` FOREIGN KEY (`Id_Tugas_Surat`) REFERENCES `Tugas_Surat` (`Id_Tugas_Surat`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Surat_Magang`
--

DROP TABLE IF EXISTS `Surat_Magang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Surat_Magang` (
  `id_no` int NOT NULL AUTO_INCREMENT,
  `Id_Tugas_Surat` int NOT NULL,
  `Data_Mahasiswa` json DEFAULT NULL COMMENT 'JSON: {nama, nim, prodi}',
  `Data_Dosen_pembiming` json DEFAULT NULL COMMENT 'JSON: {dosen_pembimbing_1, dosen_pembimbing_2}',
  `Judul_Penelitian` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Dokumen_Proposal` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Path file proposal mahasiswa',
  `Surat_Pengantar_Magang` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Path file surat pengantar dari instansi',
  `Nama_Instansi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Alamat_Instansi` text COLLATE utf8mb4_unicode_ci,
  `Tanggal_Mulai` date NOT NULL,
  `Tanggal_Selesai` date NOT NULL,
  `Foto_ttd` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Qr_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Qr_code_dekan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Nama_Koordinator` int NOT NULL,
  `Nama_Dekan` int NOT NULL,
  `Nip_Dekan` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Acc_Koordinator` tinyint(1) NOT NULL DEFAULT '0',
  `Acc_Dekan` tinyint(1) NOT NULL DEFAULT '0',
  `Status` enum('Draft','Diajukan-ke-koordinator','Dikerjakan-admin','Diajukan-ke-dekan','Success','Ditolak') COLLATE utf8mb4_unicode_ci NOT NULL,
  `Komentar` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id_no`),
  KEY `fk_nama_koordinator` (`Nama_Koordinator`),
  KEY `fk_id_tugas_surat` (`Id_Tugas_Surat`),
  KEY `fk_nama_dekan` (`Nama_Dekan`),
  CONSTRAINT `fk_id_tugas_surat` FOREIGN KEY (`Id_Tugas_Surat`) REFERENCES `Tugas_Surat` (`Id_Tugas_Surat`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_nama_dekan` FOREIGN KEY (`Nama_Dekan`) REFERENCES `Dosen` (`Id_Dosen`),
  CONSTRAINT `fk_nama_koordinator` FOREIGN KEY (`Nama_Koordinator`) REFERENCES `Dosen` (`Id_Dosen`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Surat_Magang_Invitations`
--

DROP TABLE IF EXISTS `Surat_Magang_Invitations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Surat_Magang_Invitations` (
  `id_no` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_surat_magang` int NOT NULL,
  `id_mahasiswa_diundang` int NOT NULL,
  `id_mahasiswa_pengundang` int NOT NULL,
  `status` enum('pending','accepted','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `invited_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `responded_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_no`),
  KEY `surat_magang_invitations_id_mahasiswa_pengundang_foreign` (`id_mahasiswa_pengundang`),
  KEY `surat_magang_invitations_id_mahasiswa_diundang_status_index` (`id_mahasiswa_diundang`,`status`),
  KEY `surat_magang_invitations_id_surat_magang_index` (`id_surat_magang`),
  CONSTRAINT `surat_magang_invitations_id_mahasiswa_diundang_foreign` FOREIGN KEY (`id_mahasiswa_diundang`) REFERENCES `Mahasiswa` (`Id_Mahasiswa`) ON DELETE CASCADE,
  CONSTRAINT `surat_magang_invitations_id_mahasiswa_pengundang_foreign` FOREIGN KEY (`id_mahasiswa_pengundang`) REFERENCES `Mahasiswa` (`Id_Mahasiswa`) ON DELETE CASCADE,
  CONSTRAINT `surat_magang_invitations_id_surat_magang_foreign` FOREIGN KEY (`id_surat_magang`) REFERENCES `Surat_Magang` (`id_no`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Tugas`
--

DROP TABLE IF EXISTS `Tugas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Tugas` (
  `Id_Tugas` int NOT NULL AUTO_INCREMENT,
  `Id_Pemberi_Tugas` int DEFAULT NULL,
  `Id_Penerima_Tugas` int DEFAULT NULL,
  `Id_Jenis_Pekerjaan` int DEFAULT NULL,
  `Judul_Tugas` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `Deskripsi_Tugas` text,
  `Tanggal_Diberikan_Tugas` date DEFAULT NULL,
  `Tanggal_Tenggat_Tugas` date DEFAULT NULL,
  `Status` enum('Dikerjakan','Selesai','Terlambat') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `Tanggal_Diselesaikan` date DEFAULT NULL,
  `File_Laporan` text,
  PRIMARY KEY (`Id_Tugas`),
  KEY `Id_Jenis_Pekerjaan` (`Id_Jenis_Pekerjaan`),
  KEY `Id_Pemberi_Tugas` (`Id_Pemberi_Tugas`),
  KEY `Id_Penerima_Tugas` (`Id_Penerima_Tugas`),
  CONSTRAINT `Tugas_ibfk_1` FOREIGN KEY (`Id_Jenis_Pekerjaan`) REFERENCES `Jenis_Pekerjaan` (`Id_Jenis_Pekerjaan`),
  CONSTRAINT `Tugas_ibfk_2` FOREIGN KEY (`Id_Pemberi_Tugas`) REFERENCES `Users` (`Id_User`),
  CONSTRAINT `Tugas_ibfk_3` FOREIGN KEY (`Id_Penerima_Tugas`) REFERENCES `Users` (`Id_User`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Tugas_Surat`
--

DROP TABLE IF EXISTS `Tugas_Surat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Tugas_Surat` (
  `Id_Tugas_Surat` int NOT NULL AUTO_INCREMENT,
  `Id_Pemberi_Tugas_Surat` int DEFAULT NULL,
  `Id_Penerima_Tugas_Surat` int DEFAULT NULL,
  `Id_Jenis_Surat` int DEFAULT NULL,
  `Judul_Tugas_Surat` varchar(255) DEFAULT NULL,
  `Id_Jenis_Pekerjaan` int DEFAULT NULL,
  `Nomor_Surat` varchar(255) DEFAULT NULL,
  `Status` enum('baru','Diterima Admin','Proses','Diajukan ke Dekan','menunggu-ttd','Telah Ditandatangani Dekan','Ditolak','Selesai','Terlambat') DEFAULT 'baru',
  `Tanggal_Diberikan_Tugas_Surat` date DEFAULT NULL,
  `Tanggal_Tenggat_Tugas_Surat` date DEFAULT NULL,
  `Tanggal_Diselesaikan` date DEFAULT NULL,
  PRIMARY KEY (`Id_Tugas_Surat`),
  KEY `Id_Jenis_Pekerjaan` (`Id_Jenis_Pekerjaan`),
  KEY `Id_Pemberi_Tugas_Surat` (`Id_Pemberi_Tugas_Surat`),
  KEY `Id_Penerima_Tugas_Surat` (`Id_Penerima_Tugas_Surat`),
  KEY `fk_jenis_surat` (`Id_Jenis_Surat`),
  CONSTRAINT `fk_jenis_surat` FOREIGN KEY (`Id_Jenis_Surat`) REFERENCES `Jenis_Surat` (`Id_Jenis_Surat`),
  CONSTRAINT `Tugas_Surat_ibfk_1` FOREIGN KEY (`Id_Jenis_Pekerjaan`) REFERENCES `Jenis_Pekerjaan` (`Id_Jenis_Pekerjaan`),
  CONSTRAINT `Tugas_Surat_ibfk_2` FOREIGN KEY (`Id_Pemberi_Tugas_Surat`) REFERENCES `Users` (`Id_User`),
  CONSTRAINT `Tugas_Surat_ibfk_3` FOREIGN KEY (`Id_Penerima_Tugas_Surat`) REFERENCES `Users` (`Id_User`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Users`
--

DROP TABLE IF EXISTS `Users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Users` (
  `Id_User` int NOT NULL,
  `Username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `Name_User` varchar(255) DEFAULT NULL,
  `Id_Role` int DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`Id_User`),
  KEY `Users_ibfk_1` (`Id_Role`),
  CONSTRAINT `Users_ibfk_1` FOREIGN KEY (`Id_Role`) REFERENCES `Roles` (`Id_Role`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `surat_verifications`
--

DROP TABLE IF EXISTS `surat_verifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `surat_verifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_tugas_surat` int unsigned NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Token unik untuk validasi QR',
  `signed_by` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nama atau ID Dekan yang menandatangani',
  `signed_by_user_id` int unsigned DEFAULT NULL COMMENT 'ID User Dekan',
  `signed_at` timestamp NOT NULL COMMENT 'Waktu persetujuan',
  `qr_path` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `surat_verifications_token_unique` (`token`),
  KEY `surat_verifications_id_tugas_surat_index` (`id_tugas_surat`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-12-05 15:25:59
