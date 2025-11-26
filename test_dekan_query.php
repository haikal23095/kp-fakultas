<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Simulate Query Dekan ===\n\n";

// Login as Dekan
$dekan = \App\Models\User::where('email', 'budi.hartono@fakultas.ac.id')->first();
echo "Login as: {$dekan->Name_User} (ID: {$dekan->Id_User})\n\n";

// Query yang sama dengan controller
$daftarSurat = \App\Models\TugasSurat::with([
        'jenisSurat', 
        'pemberiTugas.role', 
        'penerimaTugas', 
        'suratMagang', 
        'suratKetAktif',
        'pemberiTugas.mahasiswa.prodi'
    ])
    ->where(function ($q) {
        $q->where('Status', 'menunggu-ttd')
          ->orWhereHas('suratMagang', function ($subQ) {
              $subQ->where('Status', 'menunggu-ttd');
          })
          ->orWhereHas('suratKetAktif', function ($subQ) {
              $subQ->where('Status', 'menunggu-ttd');
          });
    })
    ->where('Id_Penerima_Tugas_Surat', $dekan->Id_User)
    ->orderBy('Tanggal_Diberikan_Tugas_Surat', 'desc')
    ->get();

echo "Total surat ditemukan: " . $daftarSurat->count() . "\n\n";

if ($daftarSurat->count() > 0) {
    foreach ($daftarSurat as $surat) {
        echo "=== Surat #{$surat->Id_Tugas_Surat} ===\n";
        echo "Jenis: {$surat->jenisSurat->Nama_Surat}\n";
        echo "Dari: {$surat->pemberiTugas->Name_User}\n";
        echo "Status: {$surat->Status}\n";
        echo "Tanggal: {$surat->Tanggal_Diberikan_Tugas_Surat}\n";
        echo "\n";
    }
    echo "✅ Surat AKAN MUNCUL di halaman dekan!\n";
} else {
    echo "❌ Tidak ada surat yang muncul!\n";
    echo "\nDebug:\n";
    echo "1. Cek apakah status 'menunggu-ttd' dengan huruf kecil/besar yang benar\n";
    echo "2. Cek apakah Id_Penerima_Tugas_Surat = {$dekan->Id_User}\n";
}
