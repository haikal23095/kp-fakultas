<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\WahaService;

$service = new WahaService();
$number = '6285648475948'; // Testing by sending to self
$message = "🛠️ *TEST KONEKSI SIFAKULTAS*\n\nSesi WAHA sudah *WORKING* dan siap digunakan untuk fitur notifikasi SK.\n\n_Waktu: " . date('H:i:s') . "_";

echo "Mengirim pesan ke $number...\n";
$result = $service->sendMessage($number, $message);

if ($result) {
    echo "SUCCESS: Pesan terkirim!\n";
} else {
    echo "FAILED: Gagal mengirim pesan.\n";
}
