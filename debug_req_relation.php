<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$accId = 7;
$reqs = App\Models\ReqSKPembimbingSkripsi::where('Id_Acc_SK_Pembimbing_Skripsi', $accId)->get();

echo "Checking Acc ID: $accId\n";
echo "Found " . $reqs->count() . " requests.\n";

$sk = App\Models\AccSKPembimbingSkripsi::find($accId);
if ($sk) {
    echo "\nChecking Dosen from JSON:\n";
    $data = $sk->Data_Pembimbing_Skripsi;
    if (is_string($data))
        $data = json_decode($data, true);

    foreach ($data as $mhs) {
        foreach (['pembimbing_1', 'pembimbing_2'] as $key) {
            if (isset($mhs[$key])) {
                $p = $mhs[$key];
                $nip = $p['nip'] ?? 'NULL';
                $idD = $p['id_dosen'] ?? 'NULL';
                echo "Key: $key | NIP: $nip | ID: $idD\n";

                $d = App\Models\Dosen::with('user')->where('NIP', $nip)->orWhere('Id_Dosen', $idD)->first();
                if ($d) {
                    echo "  Found Dosen: " . $d->Nama_Dosen . "\n";
                    echo "  User ID: " . ($d->user->Id_User ?? 'NULL') . "\n";
                    echo "  WA: " . ($d->user->No_WA ?? 'NULL') . "\n";
                } else {
                    echo "  Dosen NOT FOUND in DB\n";
                }
            }
        }
    }
}
