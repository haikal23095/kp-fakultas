<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== CEK SURAT AKTIF (ID_JENIS_SURAT = 1) ===\n\n";

echo "--- SEMUA SURAT AKTIF ---\n";
$allAktif = \App\Models\TugasSurat::where('Id_Jenis_Surat', 1)
    ->with(['pemberiTugas', 'jenisSurat'])
    ->get();

echo "Total surat aktif: " . $allAktif->count() . "\n\n";

foreach($allAktif as $surat) {
    $userName = $surat->pemberiTugas->Name_User ?? 'N/A';
    $status = $surat->Status;
    $nomor = $surat->Nomor_Surat ?? 'BELUM ADA';
    $tanggalSelesai = $surat->Tanggal_Diselesaikan ?? 'NULL';
    
    echo "ID: {$surat->Id_Tugas_Surat} | User: {$userName} | Status: {$status} | Nomor: {$nomor} | Tgl Selesai: {$tanggalSelesai}\n";
}

echo "\n--- SURAT AKTIF YANG SUDAH ADA NOMOR ---\n";
$arsipAktif = \App\Models\TugasSurat::where('Id_Jenis_Surat', 1)
    ->whereNotNull('Nomor_Surat')
    ->where('Nomor_Surat', '!=', '')
    ->get();

echo "Total yang sudah ada nomor: " . $arsipAktif->count() . "\n\n";

foreach($arsipAktif as $surat) {
    $userName = $surat->pemberiTugas->Name_User ?? 'N/A';
    $status = $surat->Status;
    $nomor = $surat->Nomor_Surat;
    
    echo "ID: {$surat->Id_Tugas_Surat} | User: {$userName} | Status: {$status} | Nomor: {$nomor}\n";
}

echo "\n--- SURAT AKTIF BELUM ADA NOMOR ---\n";
$manajemenAktif = \App\Models\TugasSurat::where('Id_Jenis_Surat', 1)
    ->where(function($q) {
        $q->whereNull('Nomor_Surat')->orWhere('Nomor_Surat', '');
    })
    ->get();

echo "Total yang belum ada nomor: " . $manajemenAktif->count() . "\n\n";

foreach($manajemenAktif as $surat) {
    $userName = $surat->pemberiTugas->Name_User ?? 'N/A';
    $status = $surat->Status;
    
    echo "ID: {$surat->Id_Tugas_Surat} | User: {$userName} | Status: {$status}\n";
}
