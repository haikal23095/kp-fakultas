-- ============================================
-- Script untuk Memperbaiki AUTO_INCREMENT
-- dengan Foreign Key Constraint
-- ============================================
-- STATUS: ✅ SUDAH DIJALANKAN DENGAN SUKSES
-- Tanggal: 2025-11-03
-- ============================================

-- Step 1: Cek foreign key yang ada
SELECT 
    CONSTRAINT_NAME, 
    TABLE_NAME, 
    COLUMN_NAME, 
    REFERENCED_TABLE_NAME, 
    REFERENCED_COLUMN_NAME 
FROM information_schema.KEY_COLUMN_USAGE 
WHERE TABLE_SCHEMA = DATABASE() 
AND REFERENCED_TABLE_NAME = 'Tugas_Surat';

-- Step 2: Drop foreign key constraint sementara
ALTER TABLE `File_Arsip` 
DROP FOREIGN KEY `File_Arsip_ibfk_1`;

-- Step 3: Ubah kolom Id_Tugas_Surat menjadi AUTO_INCREMENT
-- CATATAN: Jangan tambahkan PRIMARY KEY karena sudah ada
ALTER TABLE `Tugas_Surat` 
MODIFY COLUMN `Id_Tugas_Surat` INT NOT NULL AUTO_INCREMENT;

-- Step 4: Ubah kolom Id_File_Arsip menjadi AUTO_INCREMENT (jika belum)
-- CATATAN: Jangan tambahkan PRIMARY KEY karena sudah ada
ALTER TABLE `File_Arsip` 
MODIFY COLUMN `Id_File_Arsip` INT NOT NULL AUTO_INCREMENT;

-- Step 5: Tambahkan kembali foreign key constraint
ALTER TABLE `File_Arsip` 
ADD CONSTRAINT `File_Arsip_ibfk_1` 
FOREIGN KEY (`Id_Tugas_Surat`) 
REFERENCES `Tugas_Surat`(`Id_Tugas_Surat`) 
ON DELETE CASCADE 
ON UPDATE CASCADE;

-- Step 6: Verifikasi perubahan
SHOW COLUMNS FROM `Tugas_Surat` WHERE `Field` = 'Id_Tugas_Surat';
SHOW COLUMNS FROM `File_Arsip` WHERE `Field` = 'Id_File_Arsip';

-- Cek foreign key yang telah dibuat ulang
SHOW CREATE TABLE `File_Arsip`;

-- ============================================
-- HASIL VERIFIKASI:
-- ✅ Id_Tugas_Surat: auto_increment AKTIF
-- ✅ Id_File_Arsip: auto_increment AKTIF
-- ✅ Foreign key File_Arsip_ibfk_1 AKTIF
-- ============================================
