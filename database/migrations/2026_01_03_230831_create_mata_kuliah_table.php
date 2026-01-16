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
        Schema::create('Matakuliah', function (Blueprint $table) {
            $table->id('Nomor');
            $table->string('Kode', 20);
            $table->string('Nama_Matakuliah', 255);
            $table->string('Kelas', 10);
            $table->integer('SKS');
            $table->integer('Id_Prodi');

            // Foreign key constraint
            $table->foreign('Id_Prodi')
                ->references('Id_Prodi')
                ->on('Prodi')
                ->onDelete('cascade');

            // Indexes for better performance
            $table->index(['Id_Prodi', 'Nama_Matakuliah']);
            $table->index('Kode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Matakuliah');
    }
};
