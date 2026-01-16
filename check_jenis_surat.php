<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TABEL JENIS_SURAT ===\n\n";
$jenisSurat = \App\Models\JenisSurat::orderBy('Id_Jenis_Surat')->get();

foreach($jenisSurat as $js) {
    echo "ID: {$js->Id_Jenis_Surat} | Nama: {$js->Nama_Surat}\n";
}
