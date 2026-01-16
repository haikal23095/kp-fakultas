<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$fakultasId = 2;
$arsipTugas = \App\Models\TugasSurat::query()
    ->whereNotNull('Nomor_Surat')
    ->where('Nomor_Surat', '!=', '')
    ->where(function ($q) use ($fakultasId) {
        $q->whereHas('pemberiTugas.mahasiswa.prodi.fakultas', function ($subQ) use ($fakultasId) {
            $subQ->where('Id_Fakultas', $fakultasId);
        });
    })
    ->get();

echo "Total: " . $arsipTugas->count() . "\n\n";

foreach($arsipTugas as $item) {
    echo "ID {$item->Id_Tugas_Surat}: Id_Jenis_Surat = '{$item->Id_Jenis_Surat}' (type: " . gettype($item->Id_Jenis_Surat) . ")\n";
}

echo "\n--- Test filtering ---\n";
$id5_where = $arsipTugas->where('Id_Jenis_Surat', 5);
echo "where(5): " . $id5_where->count() . "\n";

$id5_filter = $arsipTugas->filter(fn($x) => $x->Id_Jenis_Surat == 5);
echo "filter(==5): " . $id5_filter->count() . "\n";

$id5_filter_string = $arsipTugas->filter(fn($x) => $x->Id_Jenis_Surat === '5');
echo "filter(==='5'): " . $id5_filter_string->count() . "\n";
