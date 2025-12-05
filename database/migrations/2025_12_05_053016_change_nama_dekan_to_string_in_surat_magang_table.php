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
            // Drop foreign key constraint dulu
            $table->dropForeign('fk_nama_dekan');

            // Ubah Nama_Dekan dari int menjadi string
            $table->string('Nama_Dekan', 255)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('Surat_Magang', function (Blueprint $table) {
            // Kembalikan ke int
            $table->integer('Nama_Dekan')->nullable()->change();

            // Kembalikan foreign key
            $table->foreign('Nama_Dekan', 'fk_nama_dekan')
                ->references('Id_Dosen')
                ->on('Dosen')
                ->onDelete('set null');
        });
    }
};
