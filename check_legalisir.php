<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = \App\Models\User::where('Name_User', 'Admin FAI')->first();
if (!$user || !$user->pegawaiFakultas) {
    echo "Admin FAI tidak ditemukan atau tidak punya pegawaiFakultas\n";
    exit;
}

$fakultasId = $user->pegawaiFakultas->Id_Fakultas;
echo "Fakultas ID Admin FAI: {$fakultasId}\n";
echo "Nama Fakultas: " . $user->pegawaiFakultas->fakultas->Nama_Fakultas . "\n\n";

echo "=== SEMUA LEGALISIR YANG TIDAK SELESAI (TANPA FILTER) ===\n";
$allLegalisir = \App\Models\SuratLegalisir::where('Status', '!=', 'selesai')->get();
echo "Total: {$allLegalisir->count()}\n";
foreach($allLegalisir as $leg) {
    $userName = $leg->user->Name_User ?? 'N/A';
    $prodiName = $leg->user->mahasiswa->prodi->Nama_Prodi ?? 'N/A';
    $fakultasName = $leg->user->mahasiswa->prodi->fakultas->Nama_Fakultas ?? 'N/A';
    $fakultasIdLeg = $leg->user->mahasiswa->prodi->fakultas->Id_Fakultas ?? 'N/A';
    echo "ID: {$leg->id_no} | Status: {$leg->Status} | User: {$userName} | Prodi: {$prodiName} | Fakultas: {$fakultasName} (ID: {$fakultasIdLeg})\n";
}

echo "\n=== LEGALISIR DENGAN FILTER FAKULTAS ID={$fakultasId} ===\n";
$filteredLegalisir = \App\Models\SuratLegalisir::where('Status', '!=', 'selesai')
    ->whereHas('user.mahasiswa.prodi.fakultas', function ($q) use ($fakultasId) {
        $q->where('Id_Fakultas', $fakultasId);
    })
    ->get();
echo "Total dengan filter: {$filteredLegalisir->count()}\n";
foreach($filteredLegalisir as $leg) {
    $userName = $leg->user->Name_User ?? 'N/A';
    echo "ID: {$leg->id_no} | Status: {$leg->Status} | User: {$userName}\n";
}

echo "\n=== LEGALISIR SELESAI (DI ARSIP) ===\n";
$arsipLegalisir = \App\Models\SuratLegalisir::where('Status', 'selesai')
    ->whereHas('user.mahasiswa.prodi.fakultas', function ($q) use ($fakultasId) {
        $q->where('Id_Fakultas', $fakultasId);
    })
    ->get();
echo "Total di arsip: {$arsipLegalisir->count()}\n";
foreach($arsipLegalisir as $leg) {
    $userName = $leg->user->Name_User ?? 'N/A';
    echo "ID: {$leg->id_no} | Status: {$leg->Status} | User: {$userName}\n";
}
