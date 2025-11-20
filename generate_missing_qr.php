<?php

// Script untuk generate QR Code untuk surat verification yang belum punya QR
// Run: php generate_missing_qr.php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\SuratVerification;
use Illuminate\Support\Facades\Storage;

echo "🔍 Mencari verification tanpa QR Code...\n\n";

$verifications = SuratVerification::whereNull('qr_path')
    ->orWhere('qr_path', '')
    ->get();

echo "📊 Ditemukan: " . $verifications->count() . " verification\n\n";

foreach ($verifications as $v) {
    echo "⚙️  Processing ID {$v->id} (Token: " . substr($v->token, 0, 16) . "...)\n";
    
    try {
        // Generate QR Code
        $verifyUrl = route('surat.verify', $v->token);
        $qrCode = app('QrCode')->format('png')
            ->size(300)
            ->margin(1)
            ->errorCorrection('H')
            ->generate($verifyUrl);
        
        // Save to storage
        $qrFilename = 'qr_codes/' . $v->token . '.png';
        Storage::disk('public')->put($qrFilename, $qrCode);
        
        // Update database
        $v->qr_path = $qrFilename;
        $v->save();
        
        echo "   ✅ QR Code generated: storage/app/public/{$qrFilename}\n";
        echo "   🔗 Verify URL: {$verifyUrl}\n\n";
        
    } catch (\Exception $e) {
        echo "   ❌ Error: " . $e->getMessage() . "\n\n";
    }
}

echo "✨ Selesai!\n";
