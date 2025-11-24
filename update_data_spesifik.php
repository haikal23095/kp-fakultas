<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TugasSurat;

echo "=== UPDATE DATA SPESIFIK UNTUK SURAT AKTIF ===\n\n";

$surat = TugasSurat::find(1);

if (!$surat) {
    echo "❌ Surat tidak ditemukan!\n";
    exit;
}

echo "📝 Surat ID: {$surat->Id_Tugas_Surat}\n";
echo "   Nomor: {$surat->Nomor_Surat}\n\n";

// Update data_spesifik
$dataSpesifik = [
    'semester' => '5',
    'tahun_akademik' => '2024/2025',
    'keperluan' => 'Keperluan Administrasi',
];

$surat->data_spesifik = $dataSpesifik;
$surat->save();

echo "✅ Data spesifik berhasil diupdate:\n";
echo "   " . json_encode($dataSpesifik, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

echo "\n=== SELESAI ===\n";
