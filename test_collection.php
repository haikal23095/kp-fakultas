<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$data = collect([
    (object)['Id_Jenis_Surat' => 5, 'name' => 'A'],
    (object)['Id_Jenis_Surat' => 5, 'name' => 'B'],
    (object)['Id_Jenis_Surat' => 5, 'name' => 'C'],
    (object)['Id_Jenis_Surat' => 3, 'name' => 'D'],
]);

echo "Total: " . $data->count() . "\n";
echo "Where 5: " . $data->where('Id_Jenis_Surat', 5)->count() . "\n";
echo "Where 3: " . $data->where('Id_Jenis_Surat', 3)->count() . "\n";

// Coba dengan nilai sebenarnya
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

echo "\nDari database:\n";
echo "Total: " . $arsipTugas->count() . "\n";
echo "ID 5: " . $arsipTugas->where('Id_Jenis_Surat', 5)->count() . "\n";

$filtered = $arsipTugas->filter(function($item) {
    return $item->Id_Jenis_Surat == 5;
});
echo "Filter ID 5: " . $filtered->count() . "\n";

foreach($filtered as $item) {
    echo "  - ID {$item->Id_Tugas_Surat}: Id_Jenis_Surat = {$item->Id_Jenis_Surat}\n";
}
