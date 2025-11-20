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
        Schema::create('Mahasiswa', function (Blueprint $table) {
            $table->integer('Id_Mahasiswa')->primary();
            $table->integer('NIM')->nullable();
            $table->string('Nama_Mahasiswa', 255)->nullable();
            $table->enum('Jenis_Kelamin_Mahasiswa', ['L', 'P'])->nullable();
            $table->text('Alamat_Mahasiswa')->nullable();
            $table->integer('Id_User')->nullable();
            $table->integer('Id_Prodi')->nullable();

            $table->foreign('Id_User')
                ->references('Id_User')
                ->on('Users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('Id_Prodi')
                ->references('Id_Prodi')
                ->on('Prodi')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Mahasiswa');
    }
};
