<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('surat_magang', function (Blueprint $table) {
            $table->dropColumn('No_WA');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_magang', function (Blueprint $table) {
            $table->string('No_WA', 20)->nullable()->after('Tanggal_Selesai');
        });
    }
};
