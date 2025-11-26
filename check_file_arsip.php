<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== STRUKTUR TABEL File_Arsip ===\n\n";
$columns = DB::select('DESCRIBE File_Arsip');
foreach ($columns as $col) {
    echo "{$col->Field} - {$col->Type} - Null: {$col->Null}\n";
}
