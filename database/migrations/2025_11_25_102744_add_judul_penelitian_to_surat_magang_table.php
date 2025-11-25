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
            $table->string('Judul_Penelitian', 500)->nullable()->after('Data_Dosen_pembiming');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('Surat_Magang', function (Blueprint $table) {
            $table->dropColumn('Judul_Penelitian');
        });
    }
};
