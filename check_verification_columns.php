<?php
require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Struktur Tabel surat_verifications ===\n";
$columns = DB::select("SHOW COLUMNS FROM surat_verifications");

foreach ($columns as $col) {
    echo "Field: {$col->Field}\n";
    echo "  Type: {$col->Type}\n";
    echo "  Null: {$col->Null}\n";
    echo "  Key: {$col->Key}\n";
    echo "  Default: {$col->Default}\n";
    echo "  Extra: {$col->Extra}\n\n";
}
