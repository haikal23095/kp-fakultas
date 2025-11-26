<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Roles:\n";
$roles = DB::table('Roles')->get();
foreach ($roles as $r) {
    echo "{$r->Id_Role}: {$r->Name_Role}\n";
}

echo "\nPegawai_Prodi:\n";
$pegawai = DB::table('Pegawai_Prodi')->get();
foreach ($pegawai as $p) {
    echo "User ID: {$p->Id_User}, Prodi ID: {$p->Id_Prodi}\n";
}

echo "\nPegawai_Fakultas:\n";
$pegawaiF = DB::table('Pegawai_Fakultas')->get();
foreach ($pegawaiF as $p) {
    echo "User ID: {$p->Id_User}, Fakultas ID: {$p->Id_Fakultas}\n";
}

echo "\nProdi:\n";
$prodi = DB::table('Prodi')->get();
foreach ($prodi as $p) {
    echo "{$p->Id_Prodi}: {$p->Nama_Prodi}\n";
}
