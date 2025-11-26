<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Test QrCodeHelper dengan Python ===\n\n";

// Test generate QR Code
$testUrl = "http://127.0.0.1:8000/verify-surat/TEST123ABC";
echo "Generating QR Code untuk: {$testUrl}\n";

$qrUrl = \App\Helpers\QrCodeHelper::generate($testUrl, 10);

if ($qrUrl) {
    echo "✅ QR Code berhasil di-generate!\n";
    echo "URL: {$qrUrl}\n\n";
    
    // Extract path dari URL
    $parsedUrl = parse_url($qrUrl);
    $path = ltrim($parsedUrl['path'], '/');
    $filePath = public_path($path);
    
    if (file_exists($filePath)) {
        echo "✅ File exists: {$filePath}\n";
        echo "File size: " . filesize($filePath) . " bytes\n";
    } else {
        echo "❌ File not found: {$filePath}\n";
    }
} else {
    echo "❌ Failed to generate QR Code\n";
}
