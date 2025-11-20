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
        Schema::create('File_Arsip', function (Blueprint $table) {
            $table->integer('Id_File_Arsip')->primary();
            $table->integer('Id_Tugas_Surat')->nullable();
            $table->integer('Id_Pemberi_Tugas_Surat')->nullable();
            $table->integer('Id_Penerima_Tugas_Surat')->nullable();
            $table->text('Keterangan')->nullable();

            $table->foreign('Id_Tugas_Surat')
                ->references('Id_Tugas_Surat')
                ->on('Tugas_Surat')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('Id_Pemberi_Tugas_Surat')
                ->references('Id_User')
                ->on('Users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('Id_Penerima_Tugas_Surat')
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
        Schema::dropIfExists('File_Arsip');
    }
};
