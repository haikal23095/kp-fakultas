<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\ReqSKPembimbingSkripsi;

echo "Testing ReqSKPembimbingSkripsi relation...\n\n";

$sk = ReqSKPembimbingSkripsi::where('Status', 'Selesai')->first();

if ($sk) {
    echo "SK No: " . $sk->No . "\n";
    echo "Id_Acc_SK_Pembimbing_Skripsi: " . $sk->Id_Acc_SK_Pembimbing_Skripsi . "\n\n";

    // Load relation
    $sk->load('accSKPembimbingSkripsi.dekan');

    echo "AccSK Relation Loaded: " . ($sk->accSKPembimbingSkripsi ? 'YES' : 'NO') . "\n";

    if ($sk->accSKPembimbingSkripsi) {
        echo "AccSK No: " . $sk->accSKPembimbingSkripsi->No . "\n";
        echo "Nomor_Surat: " . ($sk->accSKPembimbingSkripsi->Nomor_Surat ?? 'NULL') . "\n";
        echo "QR_Code: " . ($sk->accSKPembimbingSkripsi->QR_Code ?? 'NULL') . "\n";
        echo "Id_Dekan: " . ($sk->accSKPembimbingSkripsi->Id_Dekan ?? 'NULL') . "\n\n";

        if ($sk->accSKPembimbingSkripsi->dekan) {
            echo "Dekan Name: " . $sk->accSKPembimbingSkripsi->dekan->Nama_Dosen . "\n";
            echo "Dekan NIP: " . $sk->accSKPembimbingSkripsi->dekan->NIP . "\n";
        } else {
            echo "Dekan relation: NOT LOADED\n";
        }
    } else {
        echo "ERROR: AccSK relation is NULL!\n";
        echo "Trying to find AccSK manually...\n";

        $accSK = \App\Models\AccSKPembimbingSkripsi::where('No', $sk->Id_Acc_SK_Pembimbing_Skripsi)->first();
        if ($accSK) {
            echo "Found AccSK manually with No: " . $accSK->No . "\n";
            echo "Nomor_Surat: " . ($accSK->Nomor_Surat ?? 'NULL') . "\n";
            echo "QR_Code: " . ($accSK->QR_Code ?? 'NULL') . "\n";
        } else {
            echo "AccSK not found in database!\n";
        }
    }

    // Test JSON serialization
    echo "\n\nJSON Test:\n";

    // Force access the relation first
    $accSKData = $sk->accSKPembimbingSkripsi;

    $json = $sk->toArray();
    echo "Has acc_sk_pembimbing_skripsi in array: " . (isset($json['acc_sk_pembimbing_skripsi']) ? 'YES' : 'NO') . "\n";
    if (isset($json['acc_sk_pembimbing_skripsi'])) {
        echo "QR_Code in JSON: " . ($json['acc_sk_pembimbing_skripsi']['QR_Code'] ?? 'NULL') . "\n";
    }

    // Try with fresh query
    echo "\n\nFresh Query Test:\n";
    $freshSK = ReqSKPembimbingSkripsi::with(['accSKPembimbingSkripsi.dekan'])->find($sk->No);
    $freshJson = $freshSK->toArray();
    echo "Fresh - Has acc_sk_pembimbing_skripsi: " . (isset($freshJson['acc_sk_pembimbing_skripsi']) ? 'YES' : 'NO') . "\n";
    if (isset($freshJson['acc_sk_pembimbing_skripsi'])) {
        echo "Fresh - QR_Code: " . ($freshJson['acc_sk_pembimbing_skripsi']['QR_Code'] ?? 'NULL') . "\n";
        echo "Fresh - Nomor_Surat: " . ($freshJson['acc_sk_pembimbing_skripsi']['Nomor_Surat'] ?? 'NULL') . "\n";
        if (isset($freshJson['acc_sk_pembimbing_skripsi']['dekan'])) {
            echo "Fresh - Dekan Name: " . ($freshJson['acc_sk_pembimbing_skripsi']['dekan']['Nama_Dosen'] ?? 'NULL') . "\n";
        }
    }

} else {
    echo "No SK with Status='Selesai' found!\n";
}
