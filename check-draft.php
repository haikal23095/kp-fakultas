<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\SuratMagangDraft;

$count = SuratMagangDraft::count();
echo "Total drafts: $count\n";

if ($count > 0) {
    $latest = SuratMagangDraft::latest()->first();
    echo "\nLatest draft:\n";
    echo "ID: " . $latest->id_draft . "\n";
    echo "Mahasiswa Pembuat: " . $latest->Id_Mahasiswa_Pembuat . "\n";
    echo "Jenis Surat: " . $latest->Id_Jenis_Surat . "\n";
    echo "Created: " . $latest->created_at . "\n";

    $confirmed = is_array($latest->Data_Mahasiswa_Confirmed)
        ? $latest->Data_Mahasiswa_Confirmed
        : json_decode($latest->Data_Mahasiswa_Confirmed, true);

    echo "Confirmed count: " . (is_array($confirmed) ? count($confirmed) : 0) . "\n";
}
