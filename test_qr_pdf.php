<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Test QR Code in PDF ===\n\n";

// Ambil surat yang sudah selesai (ada QR)
$tugasSurat = \App\Models\TugasSurat::with(['verification', 'penerimaTugas'])
    ->whereHas('verification')
    ->first();

if ($tugasSurat) {
    echo "Surat Ditemukan:\n";
    echo "ID: {$tugasSurat->Id_Tugas_Surat}\n";
    echo "Judul: {$tugasSurat->Judul_Tugas_Surat}\n";
    echo "Penerima: {$tugasSurat->penerimaTugas->name}\n";
    echo "\n";
    
    if ($tugasSurat->verification) {
        echo "Verification:\n";
        echo "QR Path: {$tugasSurat->verification->qr_path}\n";
        
        // Parse URL
        $parsed = parse_url($tugasSurat->verification->qr_path);
        $relativePath = ltrim($parsed['path'] ?? '', '/');
        $absolutePath = public_path($relativePath);
        
        echo "Absolute Path: {$absolutePath}\n";
        echo "File Exists: " . (file_exists($absolutePath) ? "YES" : "NO") . "\n";
        
        if (file_exists($absolutePath)) {
            $size = filesize($absolutePath);
            echo "File Size: {$size} bytes (" . round($size/1024, 2) . " KB)\n";
            
            // Convert to base64
            $imageData = base64_encode(file_get_contents($absolutePath));
            $base64Length = strlen($imageData);
            echo "Base64 Length: {$base64Length} chars\n";
            echo "\n";
            echo "✅ QR Code siap ditampilkan di PDF!\n";
            echo "\n";
            echo "URL untuk test download PDF:\n";
            echo "http://127.0.0.1:8000/mahasiswa/riwayat-surat/{$tugasSurat->Id_Tugas_Surat}/download\n";
        }
    } else {
        echo "❌ Tidak ada verification record\n";
    }
} else {
    echo "❌ Tidak ada surat dengan status Selesai\n";
}
