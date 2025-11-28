<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

try {
    if (!Schema::hasColumn('Tugas_Surat', 'Judul_Tugas_Surat')) {
        Schema::table('Tugas_Surat', function (Blueprint $table) {
            $table->string('Judul_Tugas_Surat', 255)->nullable()->after('Id_Jenis_Surat');
        });
        echo "Column 'Judul_Tugas_Surat' added successfully to 'Tugas_Surat'.\n";
    } else {
        echo "Column 'Judul_Tugas_Surat' already exists.\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
