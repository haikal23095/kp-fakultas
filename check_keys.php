<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\ReqSKPembimbingSkripsi;

$sk = ReqSKPembimbingSkripsi::with('accSKPembimbingSkripsi.dekan')->where('Status', 'Selesai')->first();

if ($sk) {
    $array = $sk->toArray();

    echo "=== ALL KEYS IN SK ARRAY ===\n";
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            echo "$key: [array]\n";
            if ($key === 'acc_s_k_pembimbing_skripsi' || strpos($key, 'acc') !== false) {
                echo "  Sub-keys: " . implode(', ', array_keys($value)) . "\n";
            }
        } else {
            echo "$key: $value\n";
        }
    }

    echo "\n=== ACCESSING DEKAN ===\n";
    if (isset($array['acc_s_k_pembimbing_skripsi']['dekan'])) {
        $dekan = $array['acc_s_k_pembimbing_skripsi']['dekan'];
        echo "Dekan keys: " . implode(', ', array_keys($dekan)) . "\n";
        foreach ($dekan as $k => $v) {
            echo "Key: $k -> Value: $v\n";
        }
    }
}
