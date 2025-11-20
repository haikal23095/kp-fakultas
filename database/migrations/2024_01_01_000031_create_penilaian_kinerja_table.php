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
        Schema::create('Penilaian_Kinerja', function (Blueprint $table) {
            $table->integer('Id_Penilaian')->primary();
            $table->integer('Id_Pegawai')->nullable();
            $table->integer('Id_Penilai')->nullable();
            $table->enum('Skor', ['1', '2', '3', '4', '5'])->nullable();
            $table->text('Komentar')->nullable();
            $table->date('Tanggal_Penilaian')->nullable();

            $table->foreign('Id_Pegawai')
                ->references('Id_Pegawai')
                ->on('Pegawai_Prodi')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('Id_Penilai')
                ->references('Id_User')
                ->on('Users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Penilaian_Kinerja');
    }
};
