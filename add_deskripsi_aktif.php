<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

try {
    if (!Schema::hasColumn('Surat_Ket_Aktif', 'Deskripsi')) {
        Schema::table('Surat_Ket_Aktif', function (Blueprint $table) {
            $table->text('Deskripsi')->nullable()->after('Tahun_Akademik');
        });
        echo "Column 'Deskripsi' added successfully to 'Surat_Ket_Aktif'.\n";
    } else {
        echo "Column 'Deskripsi' already exists in 'Surat_Ket_Aktif'.\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
