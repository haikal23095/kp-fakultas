<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TugasSurat;
use App\Models\SuratMagang;
use Illuminate\Support\Facades\Storage;

echo "=== CHECKING ALL FILES ===\n";

$tasks = TugasSurat::all();
$missingCount = 0;
$foundCount = 0;

foreach ($tasks as $task) {
    $path = null;
    $magang = SuratMagang::where('Id_Tugas_Surat', $task->Id_Tugas_Surat)->first();
    
    if ($magang && $magang->Dokumen_Proposal) {
        $path = $magang->Dokumen_Proposal;
    } elseif (isset($task->data_spesifik['dokumen_pendukung'])) {
        $path = $task->data_spesifik['dokumen_pendukung'];
    }
    
    if ($path) {
        if (Storage::disk('public')->exists($path)) {
            $foundCount++;
        } else {
            echo "MISSING: Task ID {$task->Id_Tugas_Surat} - Path: $path\n";
            $missingCount++;
        }
    } else {
        echo "NO PATH: Task ID {$task->Id_Tugas_Surat}\n";
    }
}


echo "\nSummary:\n";
echo "Found: $foundCount\n";
echo "Missing: $missingCount\n";
