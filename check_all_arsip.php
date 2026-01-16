<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== CEK SEMUA SURAT YANG SUDAH ADA NOMOR (ARSIP) ===\n\n";

$jenisSurat = [
    1 => 'Surat Keterangan Aktif',
    2 => 'Surat Pengantar Magang',
    4 => 'SK Dosen Wali',
    5 => 'Berkelakuan Baik',
    6 => 'Dispensasi',
    7 => 'Tidak Beasiswa',
    13 => 'Mobil Dinas'
];

foreach($jenisSurat as $id => $nama) {
    $count = \App\Models\TugasSurat::where('Id_Jenis_Surat', $id)
        ->whereNotNull('Nomor_Surat')
        ->where('Nomor_Surat', '!=', '')
        ->count();
    
    echo "{$nama} (ID {$id}): {$count} surat dengan nomor\n";
}

echo "\n=== LEGALISIR SELESAI ===\n";
$legalisir = \App\Models\SuratLegalisir::where('Status', 'selesai')->count();
echo "Legalisir selesai: {$legalisir}\n";

echo "\n=== TOTAL SURAT DENGAN NOMOR PER STATUS ===\n";
$allWithNomor = \App\Models\TugasSurat::whereNotNull('Nomor_Surat')
    ->where('Nomor_Surat', '!=', '')
    ->get();

echo "Total surat dengan nomor: " . $allWithNomor->count() . "\n\n";

$byStatus = $allWithNomor->groupBy('Status');
foreach($byStatus as $status => $items) {
    echo "Status '{$status}': " . $items->count() . " surat\n";
}

echo "\n=== DETAIL SURAT DENGAN NOMOR ===\n";
foreach($allWithNomor as $surat) {
    $jenis = $surat->jenisSurat->Nama_Surat ?? 'N/A';
    echo "ID: {$surat->Id_Tugas_Surat} | Jenis: {$jenis} | Nomor: {$surat->Nomor_Surat} | Status: {$surat->Status}\n";
}
