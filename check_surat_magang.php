<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Data Surat_Magang dengan id_no = 1:\n";
$data = DB::table('Surat_Magang')->where('id_no', 1)->first();
if ($data) {
    foreach ($data as $key => $value) {
        echo "$key: " . (is_null($value) ? 'NULL' : $value) . "\n";
    }
} else {
    echo "Data tidak ditemukan\n";
}

echo "\n\nData terbaru dari Tugas_Surat:\n";
$tugas = DB::table('Tugas_Surat')->orderBy('Id_Tugas_Surat', 'desc')->limit(5)->get();
foreach ($tugas as $t) {
    echo "Id_Tugas_Surat: {$t->Id_Tugas_Surat}, Id_Jenis_Surat: {$t->Id_Jenis_Surat}, Status: {$t->Status_Surat}\n";
}
