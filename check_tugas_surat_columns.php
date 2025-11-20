<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== COLUMNS IN Tugas_Surat ===\n";
$cols = DB::select('SHOW COLUMNS FROM Tugas_Surat');
foreach($cols as $c) {
    echo $c->Field . " (" . $c->Type . ")\n";
}
