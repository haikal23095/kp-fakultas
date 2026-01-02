<?php
/**
 * Test script untuk membuat dummy data legalisir dengan file
 * Jalankan dengan: php artisan tinker < test_legalisir_upload.php
 */

// Buat dummy PDF content
$dummyPdfContent = "%PDF-1.4\n1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n3 0 obj\n<< /Type /Page /Parent 2 0 R /Resources << /Font << /F1 << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> >> >> /MediaBox [0 0 612 792] /Contents 4 0 R >>\nendobj\n4 0 obj\n<< /Length 44 >>\nstream\nBT\n/F1 12 Tf\n100 700 Td\n(Test Legalisir PDF) Tj\nET\nendstream\nendobj\nxref\n0 5\n0000000000 65535 f\n0000000009 00000 n\n0000000058 00000 n\n0000000115 00000 n\n0000000317 00000 n\ntrailer\n<< /Size 5 /Root 1 0 R >>\nstartxref\n410\n%%EOF";

// Simpan ke storage
$filePath = 'legalisir/scans/test_' . time() . '.pdf';
Storage::disk('public')->put($filePath, $dummyPdfContent);

// Buat record legalisir
$legalisir = \App\Models\SuratLegalisir::create([
    'Id_Tugas_Surat' => 5, // ID untuk legalisir di tabel Tugas_Surat
    'Id_User' => 201, // Sesuaikan dengan user mahasiswa yang ada
    'Jenis_Dokumen' => 'Ijazah',
    'Jumlah_Salinan' => 2,
    'Biaya' => 10000,
    'File_Scan_Path' => $filePath,
    'Is_Verified' => false,
    'Status' => 'pending'
]);

echo "✅ Test data legalisir berhasil dibuat!\n";
echo "ID: {$legalisir->id_no}\n";
echo "File: {$filePath}\n";
echo "\nSekarang cek di halaman admin untuk verifikasi file.\n";
