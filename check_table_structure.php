<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CEK STRUKTUR TABEL TUGAS_SURAT ===\n\n";

$columns = DB::select('SHOW COLUMNS FROM Tugas_Surat');

foreach ($columns as $column) {
    echo "Column: {$column->Field} | Type: {$column->Type} | Null: {$column->Null} | Default: {$column->Default}\n";
}

echo "\n=== APAKAH ADA KOLOM STATUS? ===\n";
$hasStatus = collect($columns)->contains('Field', 'Status');
echo $hasStatus ? "YA, kolom Status ada!\n" : "TIDAK, kolom Status TIDAK ada!\n";
