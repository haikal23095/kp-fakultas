<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CEK STATUS SURAT ===\n\n";

// Cek surat ID 5
$surat = App\Models\TugasSurat::with(['suratMagang', 'suratKetAktif', 'jenisSurat'])->find(5);

if ($surat) {
    echo "Surat ID: 5\n";
    echo "Jenis Surat: " . optional($surat->jenisSurat)->Nama_Surat . "\n";
    echo "Status (Parent Table): " . ($surat->attributes['Status'] ?? 'NULL') . "\n";
    
    if ($surat->suratMagang) {
        echo "Status Surat Magang: " . $surat->suratMagang->Status . "\n";
    }
    
    if ($surat->suratKetAktif) {
        echo "Surat Keterangan Aktif: ID " . $surat->suratKetAktif->id_no . "\n";
    }
    
    echo "\nStatus via Accessor: " . $surat->Status . "\n";
} else {
    echo "Surat ID 5 tidak ditemukan.\n";
}

echo "\n=== SEMUA SURAT YANG PERLU DI-UPDATE ===\n\n";

// Cek semua surat yang statusnya bukan menunggu-ttd
$suratList = App\Models\TugasSurat::whereNotIn('Status', ['menunggu-ttd', 'Selesai', 'Ditolak'])
    ->orWhereNull('Status')
    ->get();

foreach ($suratList as $s) {
    echo "ID: {$s->Id_Tugas_Surat} | Status: " . ($s->attributes['Status'] ?? 'NULL') . " | Jenis: " . optional($s->jenisSurat)->Nama_Surat . "\n";
}

echo "\nTotal: " . $suratList->count() . " surat\n";
