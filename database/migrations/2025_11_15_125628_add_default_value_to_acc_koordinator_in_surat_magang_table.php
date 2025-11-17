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
            // Ubah kolom Acc_Koordinator untuk memiliki default value false
            $table->boolean('Acc_Koordinator')->default(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('Surat_Magang', function (Blueprint $table) {
            // Kembalikan ke kondisi semula (tanpa default value)
            $table->boolean('Acc_Koordinator')->default(null)->change();
        });
    }
};
