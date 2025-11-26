<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== MENAMBAHKAN KOLOM STATUS DAN TTE KE TUGAS_SURAT ===\n\n";

try {
    // Tambahkan kolom Nomor_Surat dulu jika belum ada
    DB::statement("ALTER TABLE Tugas_Surat ADD COLUMN Nomor_Surat VARCHAR(255) NULL AFTER Judul_Tugas_Surat");
    echo "✓ Kolom Nomor_Surat berhasil ditambahkan\n";
} catch (Exception $e) {
    echo "⚠ Kolom Nomor_Surat: " . $e->getMessage() . "\n";
}

try {
    // Tambahkan kolom Status
    DB::statement("ALTER TABLE Tugas_Surat ADD COLUMN Status ENUM(
        'baru',
        'Diterima Admin',
        'Proses',
        'Diajukan ke Dekan',
        'menunggu-ttd',
        'Telah Ditandatangani Dekan',
        'Ditolak',
        'Selesai',
        'Terlambat'
    ) NULL DEFAULT 'baru' AFTER Nomor_Surat");
    echo "✓ Kolom Status berhasil ditambahkan\n";
} catch (Exception $e) {
    echo "⚠ Kolom Status: " . $e->getMessage() . "\n";
}

try {
    // Tambahkan kolom data_spesifik
    DB::statement("ALTER TABLE Tugas_Surat ADD COLUMN data_spesifik JSON NULL");
    echo "✓ Kolom data_spesifik berhasil ditambahkan\n";
} catch (Exception $e) {
    echo "⚠ Kolom data_spesifik: " . $e->getMessage() . "\n";
}

try {
    // Tambahkan kolom signature_qr_data
    DB::statement("ALTER TABLE Tugas_Surat ADD COLUMN signature_qr_data TEXT NULL");
    echo "✓ Kolom signature_qr_data berhasil ditambahkan\n";
} catch (Exception $e) {
    echo "⚠ Kolom signature_qr_data: " . $e->getMessage() . "\n";
}

try {
    // Tambahkan kolom qr_image_path
    DB::statement("ALTER TABLE Tugas_Surat ADD COLUMN qr_image_path VARCHAR(255) NULL");
    echo "✓ Kolom qr_image_path berhasil ditambahkan\n";
} catch (Exception $e) {
    echo "⚠ Kolom qr_image_path: " . $e->getMessage() . "\n";
}

echo "\n=== SELESAI ===\n";
echo "Silakan cek dengan: php check_table_structure.php\n";
