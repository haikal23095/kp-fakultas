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
        Schema::create('Jenis_Pekerjaan', function (Blueprint $table) {
            $table->integer('Id_Jenis_Pekerjaan')->primary();
            $table->enum('Jenis_Pekerjaan', ['Surat', 'Non-Surat'])->nullable();
            $table->string('Nama_Pekerjaan', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Jenis_Pekerjaan');
    }
};
