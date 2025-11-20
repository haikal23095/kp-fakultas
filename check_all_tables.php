<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "===== CHECKING ALL TABLES IN sistem_fakultas =====\n\n";

// Get all tables
$tables = DB::select('SHOW TABLES');
$tableKey = 'Tables_in_sistem_fakultas';

foreach ($tables as $table) {
    $tableName = $table->$tableKey;

    echo "TABLE: {$tableName}\n";
    echo str_repeat("-", 80) . "\n";

    // Get columns
    $columns = DB::select("SHOW COLUMNS FROM `{$tableName}`");

    echo sprintf(
        "%-30s %-15s %-10s %-10s %-10s %-20s\n",
        "Field",
        "Type",
        "Null",
        "Key",
        "Default",
        "Extra"
    );
    echo str_repeat("-", 80) . "\n";

    foreach ($columns as $column) {
        echo sprintf(
            "%-30s %-15s %-10s %-10s %-10s %-20s\n",
            $column->Field,
            $column->Type,
            $column->Null,
            $column->Key,
            $column->Default ?? 'NULL',
            $column->Extra
        );
    }

    echo "\n";
}

echo "===== DONE =====\n";
