<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\ReqSKPembimbingSkripsi;
use App\Models\AccSKPembimbingSkripsi;

echo "===== DEBUGGING RELATION ISSUE =====\n\n";

// Get first Selesai SK
$sk = ReqSKPembimbingSkripsi::where('Status', 'Selesai')->first();

echo "1. Req SK Data:\n";
echo "   No: " . $sk->No . "\n";
echo "   Id_Acc_SK_Pembimbing_Skripsi (FK): " . $sk->Id_Acc_SK_Pembimbing_Skripsi . "\n\n";

// Try to find AccSK with same No
echo "2. Find AccSK with No = " . $sk->Id_Acc_SK_Pembimbing_Skripsi . ":\n";
$accSK = AccSKPembimbingSkripsi::where('No', $sk->Id_Acc_SK_Pembimbing_Skripsi)->first();
if ($accSK) {
    echo "   FOUND!\n";
    echo "   AccSK No: " . $accSK->No . "\n";
    echo "   Nomor_Surat: " . ($accSK->Nomor_Surat ?? 'NULL') . "\n";
} else {
    echo "   NOT FOUND!\n\n";
    echo "   Let's check all AccSK records:\n";
    $allAccSK = AccSKPembimbingSkripsi::all();
    foreach ($allAccSK as $item) {
        echo "   - AccSK No: " . $item->No . ", Nomor_Surat: " . ($item->Nomor_Surat ?? 'NULL') . "\n";
    }
}

echo "\n3. Test belongsTo relation definition:\n";
$relation = $sk->accSKPembimbingSkripsi();
echo "   Relation Type: " . get_class($relation) . "\n";
echo "   Foreign Key: " . $relation->getForeignKeyName() . "\n";
echo "   Owner Key: " . $relation->getOwnerKeyName() . "\n";
echo "   Parent (AccSK): " . get_class($relation->getRelated()) . "\n\n";

echo "4. Load relation via query:\n";
$skWithRelation = ReqSKPembimbingSkripsi::with('accSKPembimbingSkripsi')->find($sk->No);
echo "   Relation Loaded: " . ($skWithRelation->relationLoaded('accSKPembimbingSkripsi') ? 'YES' : 'NO') . "\n";
echo "   AccSK is null: " . ($skWithRelation->accSKPembimbingSkripsi === null ? 'YES' : 'NO') . "\n\n";

if ($skWithRelation->accSKPembimbingSkripsi) {
    echo "   AccSK loaded successfully!\n";
} else {
    echo "   AccSK is NULL - relation failed!\n";
    echo "   Checking why...\n";
    echo "   Foreign Key Value: " . $skWithRelation->Id_Acc_SK_Pembimbing_Skripsi . "\n";
    echo "   Looking for AccSK with No = " . $skWithRelation->Id_Acc_SK_Pembimbing_Skripsi . "\n";
}

echo "\n5. Test serialization:\n";
$array = $skWithRelation->toArray();
echo "   Keys in array: " . implode(', ', array_keys($array)) . "\n";
echo "   Has 'acc_sk_pembimbing_skripsi': " . (isset($array['acc_sk_pembimbing_skripsi']) ? 'YES' : 'NO') . "\n";

if (!isset($array['acc_sk_pembimbing_skripsi'])) {
    echo "\n   PROBLEM: Relation not in toArray() even though loaded!\n";
    echo "   This suggests the relation is null or not being serialized.\n";
}
