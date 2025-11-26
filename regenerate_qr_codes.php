<?php
require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\SuratVerification;

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Regenerate ALL QR Codes dengan Python ===\n\n";

// Ambil SEMUA verification (regenerate semua karena pindah dari Google Charts ke Python)
$verifications = SuratVerification::all();

echo "Ditemukan " . $verifications->count() . " verification records\n\n";

foreach ($verifications as $verification) {
    echo "Processing Verification ID {$verification->id} (Token: " . substr($verification->token, 0, 16) . "...)\n";
    
    // Generate URL verifikasi
    $verifyUrl = route('surat.verify', $verification->token);
    
    // Generate QR Code menggunakan Python (simpan sebagai file PNG)
    $qrCodeUrl = \App\Helpers\QrCodeHelper::generate($verifyUrl, 10);
    
    if ($qrCodeUrl) {
        // Update qr_path dengan URL ke file PNG
        $verification->qr_path = $qrCodeUrl;
        $verification->save();
        
        echo "  ✅ QR Code generated: {$qrCodeUrl}\n";
        
        // Update juga di Tugas_Surat
        $tugasSurat = $verification->tugasSurat;
        if ($tugasSurat) {
            $tugasSurat->qr_image_path = $qrCodeUrl;
            $tugasSurat->save();
            echo "  ✅ Updated TugasSurat {$tugasSurat->Id_Tugas_Surat}\n";
        }
        
        echo "\n";
    } else {
        echo "  ❌ Failed to generate QR Code\n\n";
    }
}

echo "\n=== Selesai ===\n";
