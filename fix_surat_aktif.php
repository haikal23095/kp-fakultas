<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TugasSurat;
use App\Models\SuratKetAktif;

echo "=== PERBAIKI DATA SURAT YANG CORRUPT ===\n\n";

// Cari semua surat dengan jenis "Surat Keterangan Aktif" (ID 3)
$suratAktif = TugasSurat::where('Id_Jenis_Surat', 3)
    ->whereDoesntHave('suratKetAktif')
    ->get();

if ($suratAktif->isEmpty()) {
    echo "✅ Tidak ada surat aktif yang perlu diperbaiki.\n";
    exit;
}

echo "Ditemukan " . $suratAktif->count() . " surat aktif yang tidak punya data child:\n\n";

foreach ($suratAktif as $surat) {
    echo "📝 Surat ID: {$surat->Id_Tugas_Surat}\n";
    echo "   Nomor: {$surat->Nomor_Surat}\n";
    echo "   Status: {$surat->Status}\n";
    
    try {
        // Buat record di tabel surat_ket_aktif
        $suratKetAktif = new SuratKetAktif();
        $suratKetAktif->Id_Tugas_Surat = $surat->Id_Tugas_Surat;
        
        // Ambil data dari data_spesifik jika ada
        if ($surat->data_spesifik && is_array($surat->data_spesifik)) {
            $suratKetAktif->Tahun_Akademik = $surat->data_spesifik['tahun_akademik'] ?? '2024/2025';
            $suratKetAktif->KRS = $surat->data_spesifik['dokumen_pendukung'] ?? null;
        } else {
            $suratKetAktif->Tahun_Akademik = '2024/2025'; // Default
            $suratKetAktif->KRS = null;
        }
        
        $suratKetAktif->is_urgent = false;
        $suratKetAktif->urgent_reason = null;
        
        $suratKetAktif->save();
        
        echo "   ✅ Berhasil dibuat record di surat_ket_aktif (ID: {$suratKetAktif->Id_Surat_Ket_Aktif})\n\n";
        
    } catch (\Exception $e) {
        echo "   ❌ Gagal: " . $e->getMessage() . "\n\n";
    }
}

echo "=== SELESAI ===\n";
