<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CEK DATA VERIFIKASI ===\n\n";

$verifications = DB::table('surat_verifications')
    ->orderBy('id', 'desc')
    ->limit(5)
    ->get();

foreach ($verifications as $v) {
    echo "ID: {$v->id}\n";
    echo "Tugas Surat: {$v->id_tugas_surat}\n";
    echo "Token: " . substr($v->token, 0, 20) . "...\n";
    echo "QR Path: " . ($v->qr_path ?? 'NULL') . "\n";
    echo "Signed By: {$v->signed_by}\n";
    echo "Signed At: {$v->signed_at}\n";
    echo "---\n\n";
}
