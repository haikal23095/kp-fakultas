<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Cek Status Surat ===\n\n";

// Cek di Tugas_Surat
$countTugas = \App\Models\TugasSurat::where('Status', 'menunggu-ttd')->count();
echo "Surat dengan status 'menunggu-ttd' di Tugas_Surat: {$countTugas}\n";

// Cek di Surat_Magang
$countMagang = \App\Models\SuratMagang::where('Status', 'menunggu-ttd')->count();
echo "Surat dengan status 'menunggu-ttd' di Surat_Magang: {$countMagang}\n";

// Cek di Surat_Ket_Aktif
$countAktif = \App\Models\SuratKetAktif::where('Status', 'menunggu-ttd')->count();
echo "Surat dengan status 'menunggu-ttd' di Surat_Ket_Aktif: {$countAktif}\n\n";

// Tampilkan semua surat dengan status yang ada
echo "=== Semua Status di Tugas_Surat ===\n";
$statuses = \App\Models\TugasSurat::select('Status', \DB::raw('count(*) as total'))
    ->groupBy('Status')
    ->get();

foreach ($statuses as $status) {
    echo "Status: '{$status->Status}' => {$status->total} surat\n";
}

echo "\n=== Semua Status di Surat_Magang ===\n";
$statusesMagang = \App\Models\SuratMagang::select('Status', \DB::raw('count(*) as total'))
    ->groupBy('Status')
    ->get();

foreach ($statusesMagang as $status) {
    echo "Status: '{$status->Status}' => {$status->total} surat\n";
}

echo "\n=== Semua Status di Surat_Ket_Aktif ===\n";
$statusesAktif = \App\Models\SuratKetAktif::select('Status', \DB::raw('count(*) as total'))
    ->groupBy('Status')
    ->get();

foreach ($statusesAktif as $status) {
    echo "Status: '{$status->Status}' => {$status->total} surat\n";
}
