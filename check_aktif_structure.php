<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== STRUKTUR TABEL Surat_Ket_Aktif ===\n\n";
$columns = DB::select('DESCRIBE Surat_Ket_Aktif');
foreach ($columns as $col) {
    echo "{$col->Field} - {$col->Type} - Null: {$col->Null} - Key: {$col->Key}\n";
}
