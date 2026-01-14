<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== CEK SEMUA SURAT AKTIF DI DATABASE ===\n\n";

// Cek semua surat aktif
$allAktif = \App\Models\TugasSurat::where('Id_Jenis_Surat', 1)
    ->with(['pemberiTugas.mahasiswa.prodi.fakultas'])
    ->get();

echo "Total surat aktif (Id_Jenis_Surat = 1): " . $allAktif->count() . "\n\n";

if ($allAktif->isEmpty()) {
    echo "TIDAK ADA DATA SURAT AKTIF SAMA SEKALI!\n";
} else {
    echo "--- DETAIL SEMUA SURAT AKTIF ---\n";
    foreach($allAktif as $surat) {
        $nomor = $surat->Nomor_Surat ?: 'BELUM ADA';
        $status = $surat->Status;
        $user = $surat->pemberiTugas->Name_User ?? 'N/A';
        $fakultas = $surat->pemberiTugas->mahasiswa->prodi->fakultas->Nama_Fakultas ?? 'N/A';
        $tanggalSelesai = $surat->Tanggal_Diselesaikan ? $surat->Tanggal_Diselesaikan->format('d M Y H:i') : 'NULL';
        
        echo "ID: {$surat->Id_Tugas_Surat}\n";
        echo "  User: {$user}\n";
        echo "  Fakultas: {$fakultas}\n";
        echo "  Status: {$status}\n";
        echo "  Nomor: {$nomor}\n";
        echo "  Tgl Selesai: {$tanggalSelesai}\n";
        echo "\n";
    }
}

echo "\n=== SURAT AKTIF YANG MASUK ARSIP (sudah ada nomor) ===\n";
$arsipAktif = \App\Models\TugasSurat::where('Id_Jenis_Surat', 1)
    ->whereNotNull('Nomor_Surat')
    ->where('Nomor_Surat', '!=', '')
    ->with(['pemberiTugas.mahasiswa.prodi.fakultas'])
    ->get();

echo "Total yang sudah ada nomor: " . $arsipAktif->count() . "\n\n";

foreach($arsipAktif as $surat) {
    $user = $surat->pemberiTugas->Name_User ?? 'N/A';
    $fakultas = $surat->pemberiTugas->mahasiswa->prodi->fakultas->Nama_Fakultas ?? 'N/A';
    echo "ID: {$surat->Id_Tugas_Surat} | User: {$user} | Fakultas: {$fakultas} | Nomor: {$surat->Nomor_Surat}\n";
}

echo "\n=== SURAT AKTIF YANG MASIH DI MANAJEMEN (belum ada nomor) ===\n";
$manajemenAktif = \App\Models\TugasSurat::where('Id_Jenis_Surat', 1)
    ->where(function($q) {
        $q->whereNull('Nomor_Surat')->orWhere('Nomor_Surat', '');
    })
    ->whereNotIn('Status', ['ditolak', 'Ditolak'])
    ->with(['pemberiTugas'])
    ->get();

echo "Total yang belum ada nomor (masih proses): " . $manajemenAktif->count() . "\n\n";

foreach($manajemenAktif as $surat) {
    $user = $surat->pemberiTugas->Name_User ?? 'N/A';
    echo "ID: {$surat->Id_Tugas_Surat} | User: {$user} | Status: {$surat->Status}\n";
}

echo "\n=== KESIMPULAN ===\n";
echo "Total surat aktif: " . $allAktif->count() . "\n";
echo "Yang sudah selesai (arsip): " . $arsipAktif->count() . "\n";
echo "Yang masih proses (manajemen): " . $manajemenAktif->count() . "\n";
