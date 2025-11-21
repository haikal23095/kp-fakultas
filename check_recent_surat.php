<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TugasSurat;

echo "=== 5 SURAT TERBARU DI DATABASE ===\n\n";

$surat = TugasSurat::with(['pemberiTugas', 'penerimaTugas', 'jenisSurat'])
    ->orderBy('Id_Tugas_Surat', 'desc')
    ->take(5)
    ->get();

if ($surat->isEmpty()) {
    echo "Tidak ada surat di database.\n";
} else {
    foreach ($surat as $s) {
        echo "ID: {$s->Id_Tugas_Surat}\n";
        echo "Status: {$s->Status}\n";
        echo "Jenis: " . ($s->jenisSurat->Nama_Surat ?? 'N/A') . "\n";
        echo "Pemberi: " . ($s->pemberiTugas->Name_User ?? 'N/A') . " (ID: {$s->Id_Pemberi_Tugas_Surat})\n";
        echo "Penerima: " . ($s->penerimaTugas->Name_User ?? 'NULL') . " (ID: {$s->Id_Penerima_Tugas_Surat})\n";
        echo "Tanggal: " . ($s->Tanggal_Diberikan_Tugas_Surat ?? 'N/A') . "\n";
        echo "---\n\n";
    }
}
