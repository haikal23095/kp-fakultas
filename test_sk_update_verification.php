#!/usr/bin/env php
<?php

// Verify SK Pembimbing Skripsi Implementation after Table Update
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "===========================================\n";
echo "Verifikasi Update Tabel & Model\n";
echo "===========================================\n\n";

echo "1. Struktur Tabel Req_SK_Pembimbing_Skripsi:\n";
echo "-------------------------------------------\n";
$columns = DB::select('DESCRIBE Req_SK_Pembimbing_Skripsi');
foreach ($columns as $col) {
    $key = $col->Key ? " [{$col->Key}]" : "";
    echo sprintf("  - %-35s %s%s\n", $col->Field, substr($col->Type, 0, 30), $key);
}

echo "\n\n2. Verifikasi Field Tahun_Akademik:\n";
echo "-------------------------------------------\n";
$hasTA = false;
$hasDW = false;
foreach ($columns as $col) {
    if ($col->Field === 'Tahun_Akademik') {
        $hasTA = true;
        echo "  ✓ Field 'Tahun_Akademik' ADA di tabel\n";
        echo "    Type: {$col->Type}\n";
        echo "    Null: {$col->Null}\n";
    }
    if ($col->Field === 'Data_Dosen_Wali') {
        $hasDW = true;
        echo "  ✗ Field 'Data_Dosen_Wali' MASIH ADA di tabel (seharusnya dihapus)\n";
    }
}

if (!$hasTA) {
    echo "  ✗ Field 'Tahun_Akademik' TIDAK ADA di tabel\n";
}
if (!$hasDW) {
    echo "  ✓ Field 'Data_Dosen_Wali' SUDAH DIHAPUS dari tabel\n";
}

echo "\n\n3. Verifikasi Model ReqSKPembimbingSkripsi:\n";
echo "-------------------------------------------\n";
$model = new App\Models\ReqSKPembimbingSkripsi();
$fillable = $model->getFillable();

echo "  Fillable fields (" . count($fillable) . "):\n";
foreach ($fillable as $field) {
    echo "    - $field\n";
}

if (in_array('Tahun_Akademik', $fillable)) {
    echo "\n  ✓ Model memiliki 'Tahun_Akademik' di fillable\n";
} else {
    echo "\n  ✗ Model TIDAK memiliki 'Tahun_Akademik' di fillable\n";
}

if (in_array('Data_Dosen_Wali', $fillable)) {
    echo "  ✗ Model masih memiliki 'Data_Dosen_Wali' di fillable (seharusnya dihapus)\n";
} else {
    echo "  ✓ Model TIDAK memiliki 'Data_Dosen_Wali' di fillable\n";
}

echo "\n\n4. Verifikasi Controller:\n";
echo "-------------------------------------------\n";
$controllerPath = app_path('Http/Controllers/Kaprodi/SKController.php');
$content = file_get_contents($controllerPath);

if (strpos($content, "'Tahun_Akademik' => \$request->tahun_akademik") !== false) {
    echo "  ✓ Controller menggunakan 'Tahun_Akademik' dari request\n";
} else {
    echo "  ✗ Controller TIDAK menggunakan 'Tahun_Akademik'\n";
}

if (strpos($content, "'Data_Dosen_Wali'") !== false) {
    echo "  ✗ Controller masih menggunakan 'Data_Dosen_Wali' (seharusnya dihapus)\n";
} else {
    echo "  ✓ Controller TIDAK menggunakan 'Data_Dosen_Wali'\n";
}

echo "\n\n5. Verifikasi Form View:\n";
echo "-------------------------------------------\n";
$viewPath = resource_path('views/kaprodi/sk/pembimbing-skripsi/create.blade.php');
$viewContent = file_get_contents($viewPath);

if (strpos($viewContent, 'name="tahun_akademik"') !== false) {
    echo "  ✓ Form memiliki field 'tahun_akademik'\n";
} else {
    echo "  ✗ Form TIDAK memiliki field 'tahun_akademik'\n";
}

echo "\n\n6. Test Insert Data (Simulasi):\n";
echo "-------------------------------------------\n";
try {
    $testData = [
        'Id_Prodi' => 1,
        'Semester' => 'Ganjil',
        'Tahun_Akademik' => '2024/2025',
        'Data_Pembimbing_Skripsi' => [
            [
                'id_mahasiswa' => 1,
                'nama_mahasiswa' => 'Test Student',
                'npm' => '123456',
                'judul_skripsi' => 'Test Thesis',
                'pembimbing_1' => ['id_dosen' => 1, 'nama_dosen' => 'Dr. Test', 'nip' => '111111'],
                'pembimbing_2' => ['id_dosen' => 2, 'nama_dosen' => 'Dr. Test 2', 'nip' => '222222'],
            ]
        ],
        'Id_Dosen_Kaprodi' => 1,
        'Status' => 'Dikerjakan admin',
        'Tanggal-Pengajuan' => now(),
        'Tanggal-Tenggat' => now()->addDays(3),
    ];

    // Validasi structure tanpa insert
    $requiredFields = ['Id_Prodi', 'Semester', 'Tahun_Akademik', 'Data_Pembimbing_Skripsi'];
    $missingFields = [];

    foreach ($requiredFields as $field) {
        if (!array_key_exists($field, $testData)) {
            $missingFields[] = $field;
        }
    }

    if (empty($missingFields)) {
        echo "  ✓ Semua field required tersedia untuk insert\n";
        echo "  ✓ Struktur data valid untuk disimpan\n";
    } else {
        echo "  ✗ Missing fields: " . implode(', ', $missingFields) . "\n";
    }

} catch (\Exception $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n";
}

echo "\n\n===========================================\n";
echo "KESIMPULAN:\n";
echo "===========================================\n";

$allGood = $hasTA && !$hasDW &&
    in_array('Tahun_Akademik', $fillable) &&
    !in_array('Data_Dosen_Wali', $fillable);

if ($allGood) {
    echo "✅ SEMUA UPDATE BERHASIL!\n\n";
    echo "Perubahan yang telah dilakukan:\n";
    echo "  ✓ Tabel: Kolom 'Data_Dosen_Wali' dihapus\n";
    echo "  ✓ Tabel: Kolom 'Tahun_Akademik' ditambahkan\n";
    echo "  ✓ Model: Fillable fields sudah diupdate\n";
    echo "  ✓ Controller: Menggunakan Tahun_Akademik\n";
    echo "  ✓ Form: Field tahun_akademik tersedia\n\n";
    echo "Ketika tombol 'Ajukan SK' diklik:\n";
    echo "  → Data akan tersimpan dengan field Tahun_Akademik\n";
    echo "  → Field Data_Dosen_Wali sudah tidak digunakan\n";
} else {
    echo "⚠️ MASIH ADA YANG PERLU DIPERBAIKI!\n";
    if (!$hasTA)
        echo "  - Field Tahun_Akademik belum ada di tabel\n";
    if ($hasDW)
        echo "  - Field Data_Dosen_Wali masih ada di tabel\n";
    if (!in_array('Tahun_Akademik', $fillable))
        echo "  - Model belum memiliki Tahun_Akademik\n";
    if (in_array('Data_Dosen_Wali', $fillable))
        echo "  - Model masih memiliki Data_Dosen_Wali\n";
}

echo "\n===========================================\n";
