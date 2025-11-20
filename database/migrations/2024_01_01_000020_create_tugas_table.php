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
        Schema::create('Tugas', function (Blueprint $table) {
            $table->integer('Id_Tugas')->autoIncrement()->primary();
            $table->integer('Id_Pemberi_Tugas')->nullable();
            $table->integer('Id_Penerima_Tugas')->nullable();
            $table->integer('Id_Jenis_Pekerjaan')->nullable();
            $table->string('Judul_Tugas', 255)->nullable();
            $table->text('Deskripsi_Tugas')->nullable();
            $table->date('Tanggal_Diberikan_Tugas')->nullable();
            $table->date('Tanggal_Tenggat_Tugas')->nullable();
            $table->enum('Status', ['Dikerjakan', 'Selesai', 'Terlambat'])->nullable();
            $table->date('Tanggal_Diselesaikan')->nullable();
            $table->text('File_Laporan')->nullable();

            $table->foreign('Id_Pemberi_Tugas')
                ->references('Id_User')
                ->on('Users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('Id_Penerima_Tugas')
                ->references('Id_User')
                ->on('Users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('Id_Jenis_Pekerjaan')
                ->references('Id_Jenis_Pekerjaan')
                ->on('Jenis_Pekerjaan')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Tugas');
    }
};
