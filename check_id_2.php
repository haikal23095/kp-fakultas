<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Data Surat_Magang dengan id_no = 2:\n";
$data = DB::table('Surat_Magang')->where('id_no', 2)->first();
if ($data) {
    foreach ($data as $key => $value) {
        if ($key === 'Data_Mahasiswa' || $key === 'Data_Dosen_pembiming') {
            echo "$key: " . (is_null($value) ? 'NULL' : $value) . "\n";
        } else {
            echo "$key: " . (is_null($value) ? 'NULL' : $value) . "\n";
        }
    }
} else {
    echo "Data tidak ditemukan\n";
}
