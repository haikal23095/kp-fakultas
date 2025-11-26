<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== UPDATE STATUS SURAT ID 5 ===\n\n";

$surat = App\Models\TugasSurat::find(5);

if ($surat) {
    echo "Surat ditemukan:\n";
    echo "- ID: 5\n";
    echo "- Jenis: " . optional($surat->jenisSurat)->Nama_Surat . "\n";
    echo "- Status sekarang: " . ($surat->Status ?? 'NULL') . "\n\n";
    
    // Update ke menunggu-ttd
    $surat->Status = 'menunggu-ttd';
    $surat->save();
    
    echo "✓ Status berhasil diupdate ke: menunggu-ttd\n\n";
    
    // Verifikasi
    $surat->refresh();
    echo "Status setelah update: " . $surat->Status . "\n";
} else {
    echo "Surat ID 5 tidak ditemukan!\n";
}
