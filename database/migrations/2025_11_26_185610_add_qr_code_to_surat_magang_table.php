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
        Schema::table('Surat_Magang', function (Blueprint $table) {
            $table->string('Qr_code', 255)->nullable()->after('Foto_ttd');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('Surat_Magang', function (Blueprint $table) {
            $table->dropColumn('Qr_code');
        });
    }
};
