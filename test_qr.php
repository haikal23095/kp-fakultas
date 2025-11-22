<?php
require 'vendor/autoload.php';

try {
    $gen = new \SimpleSoftwareIO\QrCode\Generator();
    echo "✅ Generator class berhasil di-load!\n";
    
    // Test generate QR dengan SVG (tidak butuh imagick/GD)
    $qr = $gen->format('svg')->size(200)->generate('https://example.com');
    echo "✅ QR Code SVG berhasil di-generate! (" . strlen($qr) . " bytes)\n";
    
    // Test PNG dengan GD jika tersedia
    if (extension_loaded('gd')) {
        $qrPng = $gen->format('png')->size(200)->generate('https://example.com');
        echo "✅ QR Code PNG berhasil di-generate! (" . strlen($qrPng) . " bytes)\n";
    } else {
        echo "ℹ️  GD extension tidak tersedia, pakai SVG saja\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack: " . $e->getTraceAsString() . "\n";
}
