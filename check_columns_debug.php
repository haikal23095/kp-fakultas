<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

$columns = Schema::getColumnListing('Tugas_Surat');
echo "Columns in Tugas_Surat: " . implode(', ', $columns) . "\n";

$columnsMagang = Schema::getColumnListing('Surat_Magang');
echo "Columns in Surat_Magang: " . implode(', ', $columnsMagang) . "\n";
