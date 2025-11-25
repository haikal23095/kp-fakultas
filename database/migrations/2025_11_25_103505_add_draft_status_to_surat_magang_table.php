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
            // Ubah kolom Status untuk menambahkan 'Draft'
            DB::statement("ALTER TABLE Surat_Magang MODIFY COLUMN Status ENUM('Draft', 'Diajukan-ke-koordinator', 'Dikerjakan-admin', 'Diajukan-ke-dekan', 'Success', 'Ditolak') NOT NULL");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('Surat_Magang', function (Blueprint $table) {
            // Kembalikan ke status awal (tanpa Draft)
            DB::statement("ALTER TABLE Surat_Magang MODIFY COLUMN Status ENUM('Diajukan-ke-koordinator', 'Dikerjakan-admin', 'Diajukan-ke-dekan', 'Success', 'Ditolak') NOT NULL");
        });
    }
};
