<?php

// Deep inspection of data structure
echo "=== DEEP DATA INSPECTION ===\n\n";

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\ReqSKPembimbingSkripsi;

$sk = ReqSKPembimbingSkripsi::find(2);

if ($sk) {
    $data = $sk->Data_Pembimbing_Skripsi;

    if (is_string($data)) {
        $data = json_decode($data, true);
    }

    if (is_array($data) && count($data) > 0) {
        echo "First item structure:\n";
        echo str_repeat("-", 50) . "\n";

        $firstItem = $data[0];

        foreach ($firstItem as $key => $value) {
            $type = gettype($value);
            $display = $value;

            if (is_array($value)) {
                $display = json_encode($value, JSON_PRETTY_PRINT);
            } elseif (is_object($value)) {
                $display = "Object: " . get_class($value);
            }

            echo "$key ($type):\n";
            echo "  " . str_replace("\n", "\n  ", $display) . "\n\n";
        }
    }
}
