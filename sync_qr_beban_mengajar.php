<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Sinkronisasi QR Code dari Acc_SK_Beban_Mengajar ke Req_SK_Beban_Mengajar...\n\n";

// Ambil semua SK yang sudah selesai dari Acc_SK_Beban_Mengajar
$accSKList = DB::table('Acc_SK_Beban_Mengajar')
    ->where('Status', 'Selesai')
    ->whereNotNull('QR_Code')
    ->get();

echo "Ditemukan " . $accSKList->count() . " SK yang sudah selesai dengan QR Code\n\n";

$updated = 0;

foreach ($accSKList as $accSK) {
    // Update semua Req_SK_Beban_Mengajar yang terkait
    $affected = DB::table('Req_SK_Beban_Mengajar')
        ->where('Id_Acc_SK_Beban_Mengajar', $accSK->No)
        ->update([
            'QR_Code' => $accSK->QR_Code,
            'Tanggal-Persetujuan-Dekan' => $accSK->{'Tanggal-Persetujuan-Dekan'}
        ]);

    if ($affected > 0) {
        echo "✓ Updated {$affected} row(s) untuk Acc_SK No: {$accSK->No} (Nomor Surat: {$accSK->Nomor_Surat})\n";
        $updated += $affected;
    }
}

echo "\n===========================================\n";
echo "Selesai! Total {$updated} baris diupdate.\n";
echo "===========================================\n";
