<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

echo "Adding columns to Surat_Ket_Aktif...\n";

Schema::table('Surat_Ket_Aktif', function (Blueprint $table) {
    if (!Schema::hasColumn('Surat_Ket_Aktif', 'is_urgent')) {
        $table->boolean('is_urgent')->default(false)->after('KRS');
        echo "Added is_urgent column.\n";
    }
    if (!Schema::hasColumn('Surat_Ket_Aktif', 'urgent_reason')) {
        $table->text('urgent_reason')->nullable()->after('is_urgent');
        echo "Added urgent_reason column.\n";
    }
});

echo "Done.\n";
