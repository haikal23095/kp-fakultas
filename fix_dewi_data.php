<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$userId = 307; // Dewi Sartika
$prodiId = 2; // Sistem Informasi

// Check if already exists
$exists = DB::table('Pegawai_Prodi')->where('Id_User', $userId)->first();

if ($exists) {
    echo "User $userId already exists in Pegawai_Prodi linked to Prodi {$exists->Id_Prodi}.\n";
} else {
    echo "Inserting User $userId into Pegawai_Prodi linked to Prodi $prodiId...\n";
    DB::table('Pegawai_Prodi')->insert([
        'Id_User' => $userId,
        'Id_Prodi' => $prodiId,
        'Nama_Pegawai' => 'Dewi Sartika',
        'NIP' => '198501012010012001', // Dummy NIP
        'Jenis_Kelamin_Pegawai' => 'P',
        'Alamat_Pegawai' => 'Jl. Raya Telang',
    ]);
    echo "Done.\n";
}
