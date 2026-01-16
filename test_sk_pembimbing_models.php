#!/usr/bin/env php
<?php

// Test Script untuk Model SK Pembimbing Skripsi
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "===========================================\n";
echo "Testing Models: Req & Acc SK Pembimbing Skripsi\n";
echo "===========================================\n\n";

// Test ReqSKPembimbingSkripsi
echo "1. ReqSKPembimbingSkripsi Model:\n";
echo "-------------------------------------------\n";
$reqModel = new App\Models\ReqSKPembimbingSkripsi();
echo "Table: " . $reqModel->getTable() . "\n";
echo "Primary Key: " . $reqModel->getKeyName() . "\n";
echo "Fillable Fields:\n";
foreach ($reqModel->getFillable() as $field) {
    echo "  - $field\n";
}
echo "\nCasts:\n";
foreach ($reqModel->getCasts() as $field => $type) {
    echo "  - $field => $type\n";
}

echo "\n\n2. AccSKPembimbingSkripsi Model:\n";
echo "-------------------------------------------\n";
$accModel = new App\Models\AccSKPembimbingSkripsi();
echo "Table: " . $accModel->getTable() . "\n";
echo "Primary Key: " . $accModel->getKeyName() . "\n";
echo "Fillable Fields:\n";
foreach ($accModel->getFillable() as $field) {
    echo "  - $field\n";
}
echo "\nCasts:\n";
foreach ($accModel->getCasts() as $field => $type) {
    echo "  - $field => $type\n";
}

echo "\n\n3. Testing Relations:\n";
echo "-------------------------------------------\n";
echo "ReqSKPembimbingSkripsi Relations:\n";
echo "  - prodi() -> belongsTo Prodi\n";
echo "  - kaprodi() -> belongsTo Dosen\n";
echo "  - accSKPembimbingSkripsi() -> belongsTo AccSKPembimbingSkripsi\n";

echo "\nAccSKPembimbingSkripsi Relations:\n";
echo "  - dekan() -> belongsTo Pejabat\n";
echo "  - reqSKPembimbingSkripsi() -> hasMany ReqSKPembimbingSkripsi\n";

echo "\n\n4. Database Structure:\n";
echo "-------------------------------------------\n";
echo "Req_SK_Pembimbing_Skripsi columns:\n";
$reqColumns = DB::select('DESCRIBE Req_SK_Pembimbing_Skripsi');
foreach ($reqColumns as $col) {
    echo sprintf("  - %-40s %s\n", $col->Field, $col->Type);
}

echo "\nAcc_SK_Pembimbing_Skripsi columns:\n";
$accColumns = DB::select('DESCRIBE Acc_SK_Pembimbing_Skripsi');
foreach ($accColumns as $col) {
    echo sprintf("  - %-40s %s\n", $col->Field, $col->Type);
}

echo "\n===========================================\n";
echo "Test completed!\n";
echo "===========================================\n";
