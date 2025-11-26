<?php
/**
 * Script untuk update status surat yang masih NULL menjadi 'baru'
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== UPDATE NULL STATUS TO 'baru' ===\n\n";

try {
    // Update semua surat yang statusnya NULL atau empty
    $updated = DB::table('Tugas_Surat')
        ->whereNull('Status')
        ->orWhere('Status', '')
        ->update(['Status' => 'baru']);
    
    echo "✅ Berhasil update $updated surat dengan status NULL/empty menjadi 'baru'\n\n";
    
    // Tampilkan surat yang statusnya 'baru' atau 'menunggu-ttd'
    $surats = DB::table('Tugas_Surat')
        ->leftJoin('Jenis_Surat', 'Tugas_Surat.Id_Jenis_Surat', '=', 'Jenis_Surat.Id_Jenis_Surat')
        ->select('Tugas_Surat.Id_Tugas_Surat', 'Tugas_Surat.Status', 'Tugas_Surat.Nomor_Surat', 'Jenis_Surat.Nama_Surat')
        ->whereIn('Tugas_Surat.Status', ['baru', 'menunggu-ttd'])
        ->orderBy('Tugas_Surat.Id_Tugas_Surat', 'desc')
        ->limit(10)
        ->get();
    
    echo "=== SURAT DENGAN STATUS 'baru' atau 'menunggu-ttd' (10 terbaru) ===\n";
    foreach ($surats as $surat) {
        echo "ID: {$surat->Id_Tugas_Surat} | Status: {$surat->Status} | Nomor: " . ($surat->Nomor_Surat ?? 'Belum ada') . " | Jenis: {$surat->Nama_Surat}\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
