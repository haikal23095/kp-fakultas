<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Cek Flow Surat ===\n\n";

// Ambil surat #9 (yang menunggu TTD)
$surat = \App\Models\TugasSurat::with([
    'pemberiTugas', 
    'penerimaTugas', 
    'jenisSurat',
    'suratKetAktif'
])->find(9);

if (!$surat) {
    echo "Surat #9 tidak ditemukan\n";
    exit;
}

echo "=== Surat #9 ===\n";
echo "Jenis: {$surat->jenisSurat->Nama_Surat}\n";
echo "Judul: {$surat->Judul_Tugas_Surat}\n";
echo "Status (Tugas_Surat): {$surat->Status}\n";

if ($surat->suratKetAktif) {
    echo "Status (Surat_Ket_Aktif): {$surat->suratKetAktif->Status}\n";
}

echo "\n--- Flow Surat ---\n";
echo "Pemberi Tugas (Pengaju): {$surat->pemberiTugas->Name_User} (ID: {$surat->Id_Pemberi_Tugas_Surat})\n";
echo "  Role: {$surat->pemberiTugas->role->Name_Role}\n";

echo "\nPenerima Tugas (Processor): {$surat->penerimaTugas->Name_User} (ID: {$surat->Id_Penerima_Tugas_Surat})\n";
echo "  Role: {$surat->penerimaTugas->role->Name_Role}\n";

echo "\n--- Tanggal ---\n";
echo "Tanggal Pengajuan: {$surat->Tanggal_Diberikan_Tugas_Surat}\n";
echo "Tenggat: {$surat->Tanggal_Tenggat_Tugas_Surat}\n";

echo "\n=== Pertanyaan ===\n";
echo "1. Apakah surat ini sudah diproses admin fakultas? (Status bukan 'Baru')\n";
echo "2. Apakah setelah admin proses, status berubah jadi 'menunggu-ttd'?\n";
echo "3. Siapa yang harus tanda tangan? Dekan atau Kaprodi?\n";
echo "4. Apakah ada field untuk track 'siapa yang harus TTD berikutnya'?\n";

// Cek semua role
echo "\n=== Daftar User Dekan & Admin ===\n";
$dekans = \App\Models\User::whereHas('role', function($q) {
    $q->where('Name_Role', 'like', '%dekan%');
})->get();

echo "Dekan:\n";
foreach ($dekans as $d) {
    echo "  - {$d->Name_User} (ID: {$d->Id_User}) - {$d->email}\n";
}

$admins = \App\Models\User::whereHas('role', function($q) {
    $q->where('Name_Role', 'like', '%admin%')->where('Name_Role', 'like', '%fakultas%');
})->get();

echo "\nAdmin Fakultas:\n";
foreach ($admins as $a) {
    echo "  - {$a->Name_User} (ID: {$a->Id_User}) - {$a->email}\n";
}
