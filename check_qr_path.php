<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$verification = \App\Models\SuratVerification::with('tugasSurat')->latest()->first();

if ($verification) {
    echo "=== Latest Verification Record ===\n";
    echo "ID: {$verification->id}\n";
    echo "ID Tugas Surat: {$verification->id_tugas_surat}\n";
    echo "QR Path: {$verification->qr_path}\n";
    echo "Token: " . substr($verification->token, 0, 16) . "...\n";
    echo "Signed By: {$verification->signed_by}\n";
    echo "\n";
    
    // Check if file exists
    $parsed = parse_url($verification->qr_path);
    $relativePath = ltrim($parsed['path'] ?? '', '/');
    $absolutePath = public_path($relativePath);
    
    echo "Relative Path: {$relativePath}\n";
    echo "Absolute Path: {$absolutePath}\n";
    echo "File Exists: " . (file_exists($absolutePath) ? "YES" : "NO") . "\n";
    
    if (file_exists($absolutePath)) {
        echo "File Size: " . filesize($absolutePath) . " bytes\n";
    }
} else {
    echo "No verification records found.\n";
}
