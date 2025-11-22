<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$tables = DB::select('SHOW TABLES');
echo "=== TABLES DENGAN 'USER' ===\n";
foreach ($tables as $table) {
    $tableName = array_values((array)$table)[0];
    if (stripos($tableName, 'user') !== false) {
        echo $tableName . "\n";
    }
}
