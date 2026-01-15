<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing SK Penguji Skripsi Structure\n";
echo "=====================================\n\n";

// Test 1: Check if table exists and has data
$skCount = DB::table('Acc_SK_Penguji_Skripsi')->count();
echo "Total SK Penguji Skripsi: $skCount\n\n";

// Test 2: Get sample SK
$sk = DB::table('Acc_SK_Penguji_Skripsi')->first();
if ($sk) {
    echo "Sample SK:\n";
    echo "  No: $sk->No\n";
    echo "  Semester: $sk->Semester\n";
    echo "  Tahun Akademik: $sk->Tahun_Akademik\n";
    echo "  Status: $sk->Status\n";
    echo "  Nomor Surat: " . ($sk->Nomor_Surat ?? '-') . "\n\n";

    // Test 3: Check Data_Penguji_Skripsi structure
    if ($sk->Data_Penguji_Skripsi) {
        $data = json_decode($sk->Data_Penguji_Skripsi, true);
        if ($data && count($data) > 0) {
            echo "Sample Data Penguji (first mahasiswa):\n";
            $first = $data[0];
            echo "  NIM: " . ($first['nim'] ?? '-') . "\n";
            echo "  Nama: " . ($first['nama_mahasiswa'] ?? '-') . "\n";
            echo "  Judul: " . ($first['judul_skripsi'] ?? '-') . "\n";
            echo "  Penguji 1 ID: " . ($first['penguji_1_id'] ?? '-') . "\n";
            echo "  Penguji 2 ID: " . ($first['penguji_2_id'] ?? '-') . "\n";
            echo "  Penguji 3 ID: " . ($first['penguji_3_id'] ?? '-') . "\n";
        }
    }
} else {
    echo "No SK found in database\n";
}

echo "\n\nTest completed!\n";
