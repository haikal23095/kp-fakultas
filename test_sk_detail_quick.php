<?php

// Quick test untuk SK Pembimbing Skripsi Detail
echo "=== QUICK TEST SK PEMBIMBING DETAIL ===\n\n";

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\ReqSKPembimbingSkripsi;

try {
    echo "1. Testing fetch SK with relations\n";
    echo str_repeat("-", 50) . "\n";

    $sk = ReqSKPembimbingSkripsi::with(['prodi', 'kaprodi', 'approval'])->find(2);

    if ($sk) {
        echo "✓ SK #2 found\n";
        echo "  Prodi: " . ($sk->prodi->Nama_Prodi ?? 'N/A') . "\n";
        echo "  Kaprodi: " . ($sk->kaprodi->Nama_Dosen ?? 'N/A') . "\n";
        echo "  Status: " . $sk->Status . "\n";

        echo "\n2. Testing Data_Pembimbing_Skripsi handling\n";
        echo str_repeat("-", 50) . "\n";

        $dataPembimbing = $sk->Data_Pembimbing_Skripsi;
        echo "  Raw data type: " . gettype($dataPembimbing) . "\n";

        if (is_string($dataPembimbing)) {
            echo "  Data is string, decoding...\n";
            $dataPembimbing = json_decode($dataPembimbing, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                echo "  ✗ JSON decode error: " . json_last_error_msg() . "\n";
            } else {
                echo "  ✓ JSON decoded successfully\n";
            }
        }

        if (is_array($dataPembimbing)) {
            echo "  ✓ Data is array with " . count($dataPembimbing) . " items\n";

            if (count($dataPembimbing) > 0) {
                echo "\n3. Testing first item structure\n";
                echo str_repeat("-", 50) . "\n";

                $firstItem = $dataPembimbing[0];
                echo "  Keys: " . implode(', ', array_keys($firstItem)) . "\n";

                echo "\n  Sample data:\n";
                echo "    - nim: " . ($firstItem['nim'] ?? 'N/A') . "\n";
                echo "    - nama_mahasiswa: " . ($firstItem['nama_mahasiswa'] ?? 'N/A') . "\n";
                echo "    - pembimbing_1: " . ($firstItem['pembimbing_1'] ?? 'N/A') . "\n";
                echo "    - pembimbing_2: " . ($firstItem['pembimbing_2'] ?? 'N/A') . "\n";
            }
        } else {
            echo "  ✗ Data is not array: " . gettype($dataPembimbing) . "\n";
        }

        echo "\n✓ TEST PASSED - No timeout or infinite loop\n";

    } else {
        echo "✗ SK #2 not found\n";
    }

} catch (Exception $e) {
    echo "\n✗ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\n=== TEST COMPLETED ===\n";
