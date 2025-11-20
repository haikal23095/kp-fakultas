<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Cek tabel Fakultas:\n";
$fakultas = DB::table('Fakultas')->get();

if ($fakultas->isEmpty()) {
    echo "Tidak ada data fakultas!\n";
    exit;
}

echo "Ditemukan " . $fakultas->count() . " fakultas:\n";
foreach ($fakultas as $fak) {
    foreach ($fak as $key => $value) {
        echo "  {$key}: {$value}\n";
    }
    echo "\n";
}

echo "\nCek struktur tabel Pegawai_Fakultas:\n";
$columns = DB::select('SHOW COLUMNS FROM Pegawai_Fakultas');
foreach ($columns as $col) {
    echo "- {$col->Field} ({$col->Type})\n";
}
