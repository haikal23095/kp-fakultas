<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CEK TUGAS SURAT 7 ===\n\n";

$tugas = DB::table('Tugas_Surat')->where('Id_Tugas_Surat', 7)->first();
echo "Status: {$tugas->Status}\n";
echo "QR Image Path: " . ($tugas->qr_image_path ?? 'NULL') . "\n\n";

$verification = DB::table('surat_verifications')->where('id_tugas_surat', 7)->orderBy('id', 'desc')->first();
if ($verification) {
    echo "=== VERIFICATION DATA ===\n";
    echo "Token: " . substr($verification->token, 0, 30) . "...\n";
    echo "QR Path: " . ($verification->qr_path ?? 'NULL') . "\n";
    echo "Signed By: {$verification->signed_by}\n";
} else {
    echo "Tidak ada data verification\n";
}
