<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Checking Acc_SK_Penguji_Skripsi table structure:\n";
echo "=================================================\n\n";

$columns = DB::select('DESCRIBE Acc_SK_Penguji_Skripsi');

foreach ($columns as $col) {
    echo sprintf("%-40s %s\n", $col->Field, $col->Type);
}
