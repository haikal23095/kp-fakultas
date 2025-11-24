<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Debug Query Dekan ===\n\n";

// Ambil user dekan (sesuaikan dengan user yang login)
$dekanUser = \App\Models\User::where('email', 'budi.hartono@fakultas.ac.id')->first();

if (!$dekanUser) {
    echo "❌ User dekan tidak ditemukan!\n";
    echo "Coba cek email dekan yang ada di database:\n";
    $dekans = \App\Models\User::whereHas('role', function($q) {
        $q->where('Name_Role', 'like', '%dekan%');
    })->get();
    foreach ($dekans as $d) {
        echo "  - {$d->email} ({$d->Name_User})\n";
    }
    exit;
}

echo "Dekan: {$dekanUser->Name_User} (ID: {$dekanUser->Id_User})\n\n";

// Ambil surat dengan status menunggu-ttd
$suratMenunggu = \App\Models\TugasSurat::with(['pemberiTugas', 'penerimaTugas', 'jenisSurat'])
    ->where('Status', 'menunggu-ttd')
    ->get();

echo "Total surat menunggu-ttd: " . $suratMenunggu->count() . "\n\n";

foreach ($suratMenunggu as $surat) {
    echo "=== Surat #{$surat->Id_Tugas_Surat} ===\n";
    echo "Jenis: {$surat->jenisSurat->Nama_Surat}\n";
    echo "Pemberi: {$surat->pemberiTugas->Name_User} (ID: {$surat->Id_Pemberi_Tugas_Surat})\n";
    echo "Penerima: {$surat->penerimaTugas->Name_User} (ID: {$surat->Id_Penerima_Tugas_Surat})\n";
    echo "Status: {$surat->Status}\n";
    
    // Cek apakah pemberi tugas ada di fakultas dekan
    $mahasiswa = \App\Models\Mahasiswa::where('Id_User', $surat->Id_Pemberi_Tugas_Surat)->first();
    if ($mahasiswa) {
        echo "Mahasiswa Prodi ID: {$mahasiswa->Id_Prodi}\n";
        $prodi = \App\Models\Prodi::find($mahasiswa->Id_Prodi);
        if ($prodi) {
            echo "Fakultas: {$prodi->Id_Fakultas}\n";
        }
    }
    
    echo "\n";
}

// Cek fakultas dekan
$dosen = \App\Models\Dosen::where('Id_User', $dekanUser->Id_User)->first();
if ($dosen) {
    echo "Dekan adalah Dosen dengan Prodi ID: {$dosen->Id_Prodi}\n";
    $prodiDekan = \App\Models\Prodi::find($dosen->Id_Prodi);
    if ($prodiDekan) {
        echo "Fakultas Dekan: {$prodiDekan->Id_Fakultas}\n";
    }
}
