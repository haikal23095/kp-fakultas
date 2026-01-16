<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== CEK SURAT AKTIF LENGKAP ===\n\n";

$allAktif = \App\Models\TugasSurat::where('Id_Jenis_Surat', 1)->get();
echo "Total surat aktif: " . $allAktif->count() . "\n\n";

if ($allAktif->isEmpty()) {
    echo "TIDAK ADA SURAT AKTIF DI DATABASE!\n";
    echo "Membuat data dummy...\n\n";
    
    // Buat surat aktif dengan nomor untuk test
    $user = \App\Models\User::where('Name_User', 'Adi Saputra')->first();
    if (!$user) {
        echo "User tidak ditemukan!\n";
        exit;
    }
    
    $tugas = new \App\Models\TugasSurat();
    $tugas->Id_Pemberi_Tugas_Surat = $user->Id_User;
    $tugas->Id_Jenis_Surat = 1;
    $tugas->Nomor_Surat = 'FT/001/SK-AKTIF/2026';
    $tugas->Status = 'Selesai';
    $tugas->Tanggal_Diberikan_Tugas_Surat = now();
    $tugas->Tanggal_Diselesaikan = now();
    $tugas->Deadline_Tugas_Surat = now()->addDays(7);
    $tugas->save();
    
    echo "Data dummy BERHASIL dibuat!\n";
    echo "ID: {$tugas->Id_Tugas_Surat}\n";
    echo "Nomor: {$tugas->Nomor_Surat}\n\n";
} else {
    foreach($allAktif as $surat) {
        $nomor = $surat->Nomor_Surat ?: 'BELUM ADA';
        echo "ID: {$surat->Id_Tugas_Surat} | Status: {$surat->Status} | Nomor: {$nomor}\n";
        
        if (!$surat->Nomor_Surat || $surat->Nomor_Surat == '') {
            echo "  -> SURAT INI BELUM ADA NOMOR, UPDATE DULU...\n";
            $surat->Nomor_Surat = 'FT/001/SK-AKTIF/2026';
            $surat->Status = 'Selesai';
            $surat->Tanggal_Diselesaikan = now();
            $surat->save();
            echo "  -> UPDATED! Nomor: {$surat->Nomor_Surat}\n";
        }
    }
}

echo "\n=== CEK ARSIP SURAT AKTIF (setelah update) ===\n";
$fakultasId = 2; // Teknik

$arsip = \App\Models\TugasSurat::query()
    ->where('Id_Jenis_Surat', 1)
    ->whereNotNull('Nomor_Surat')
    ->where('Nomor_Surat', '!=', '')
    ->where(function ($q) use ($fakultasId) {
        $q->whereHas('pemberiTugas.mahasiswa.prodi.fakultas', function ($subQ) use ($fakultasId) {
            $subQ->where('Id_Fakultas', $fakultasId);
        })
        ->orWhereHas('pemberiTugas.dosen.prodi.fakultas', function ($subQ) use ($fakultasId) {
            $subQ->where('Id_Fakultas', $fakultasId);
        })
        ->orWhereHas('pemberiTugas.pegawai.prodi.fakultas', function ($subQ) use ($fakultasId) {
            $subQ->where('Id_Fakultas', $fakultasId);
        });
    })
    ->get();

echo "Total di arsip: " . $arsip->count() . "\n\n";

foreach($arsip as $s) {
    $user = $s->pemberiTugas->Name_User ?? 'N/A';
    echo "ID: {$s->Id_Tugas_Surat} | User: {$user} | Nomor: {$s->Nomor_Surat}\n";
}

echo "\nSELESAI! Silakan refresh halaman arsip.\n";
