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
        Schema::create('Jenis_Surat', function (Blueprint $table) {
            $table->integer('Id_Jenis_Surat')->primary();
            $table->enum('Tipe_Surat', ['Surat-Keluar', 'Surat-Masuk'])->nullable();
            $table->string('Nama_Surat', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Jenis_Surat');
    }
};
