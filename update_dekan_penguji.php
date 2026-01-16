<?php

/**
 * Script untuk update Id_Dekan pada tabel Acc_SK_Penguji_Skripsi yang belum memiliki Id_Dekan
 * Jalankan dengan: php update_dekan_penguji.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\AccSKPengujiSkripsi;
use App\Models\Dosen;
use Illuminate\Support\Facades\DB;

echo "=== Update Id_Dekan untuk Acc_SK_Penguji_Skripsi ===\n\n";

// Ambil semua record yang belum punya Id_Dekan
$records = AccSKPengujiSkripsi::with('requestSK.prodi.fakultas')
    ->whereNull('Id_Dekan')
    ->orWhere('Id_Dekan', '')
    ->get();

if ($records->isEmpty()) {
    echo "Tidak ada data yang perlu di-update.\n";
    exit(0);
}

echo "Ditemukan " . $records->count() . " record yang perlu di-update.\n\n";

$updated = 0;
$failed = 0;

foreach ($records as $record) {
    try {
        // Cari Dekan berdasarkan fakultas dari request SK
        $fakultasId = $record->requestSK?->prodi?->Id_Fakultas;

        if (!$fakultasId) {
            echo "⚠ Record No {$record->No}: Fakultas tidak ditemukan, skip.\n";
            $failed++;
            continue;
        }

        $dekan = Dosen::with(['pejabat', 'prodi.fakultas'])
            ->whereHas('prodi.fakultas', function ($q) use ($fakultasId) {
                $q->where('Id_Fakultas', $fakultasId);
            })
            ->whereHas('pejabat', function ($q) {
                $q->where('Nama_Jabatan', 'like', 'DEKAN%');
            })
            ->first();

        if ($dekan) {
            $record->Id_Dekan = $dekan->Id_Dosen;
            $record->save();
            echo "✓ Record No {$record->No}: Updated dengan Dekan {$dekan->Nama_Dosen}\n";
            $updated++;
        } else {
            echo "⚠ Record No {$record->No}: Dekan tidak ditemukan untuk Fakultas ID {$fakultasId}\n";
            $failed++;
        }
    } catch (\Exception $e) {
        echo "✗ Record No {$record->No}: Error - {$e->getMessage()}\n";
        $failed++;
    }
}

echo "\n=== Selesai ===\n";
echo "Berhasil di-update: {$updated}\n";
echo "Gagal/Skip: {$failed}\n";
