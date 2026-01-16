<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== SEMUA LEGALISIR YANG TIDAK SELESAI ===\n";
$allLegalisir = \App\Models\SuratLegalisir::where('Status', '!=', 'selesai')->get();
echo "Total tidak selesai: {$allLegalisir->count()}\n\n";

foreach($allLegalisir as $leg) {
    $userName = $leg->user->Name_User ?? 'N/A';
    $fakultasId = 'N/A';
    $fakultasName = 'N/A';
    
    if ($leg->user && $leg->user->mahasiswa && $leg->user->mahasiswa->prodi && $leg->user->mahasiswa->prodi->fakultas) {
        $fakultasId = $leg->user->mahasiswa->prodi->fakultas->Id_Fakultas;
        $fakultasName = $leg->user->mahasiswa->prodi->fakultas->Nama_Fakultas;
    }
    
    echo "ID: {$leg->id_no} | Status: {$leg->Status} | User: {$userName} | Fakultas ID: {$fakultasId} | Fakultas: {$fakultasName}\n";
}

echo "\n=== SEMUA LEGALISIR YANG SELESAI ===\n";
$selesaiLegalisir = \App\Models\SuratLegalisir::where('Status', 'selesai')->get();
echo "Total selesai: {$selesaiLegalisir->count()}\n\n";

foreach($selesaiLegalisir as $leg) {
    $userName = $leg->user->Name_User ?? 'N/A';
    $fakultasId = 'N/A';
    $fakultasName = 'N/A';
    
    if ($leg->user && $leg->user->mahasiswa && $leg->user->mahasiswa->prodi && $leg->user->mahasiswa->prodi->fakultas) {
        $fakultasId = $leg->user->mahasiswa->prodi->fakultas->Id_Fakultas;
        $fakultasName = $leg->user->mahasiswa->prodi->fakultas->Nama_Fakultas;
    }
    
    echo "ID: {$leg->id_no} | Status: {$leg->Status} | User: {$userName} | Fakultas ID: {$fakultasId} | Fakultas: {$fakultasName}\n";
}

echo "\n=== GROUP BY FAKULTAS (TIDAK SELESAI) ===\n";
$byFakultas = [];
foreach($allLegalisir as $leg) {
    $fakultasId = 'N/A';
    if ($leg->user && $leg->user->mahasiswa && $leg->user->mahasiswa->prodi && $leg->user->mahasiswa->prodi->fakultas) {
        $fakultasId = $leg->user->mahasiswa->prodi->fakultas->Id_Fakultas;
    }
    if (!isset($byFakultas[$fakultasId])) {
        $byFakultas[$fakultasId] = 0;
    }
    $byFakultas[$fakultasId]++;
}

foreach($byFakultas as $fakId => $count) {
    echo "Fakultas ID {$fakId}: {$count} legalisir\n";
}
