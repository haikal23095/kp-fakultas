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
        Schema::create('Pegawai_Prodi', function (Blueprint $table) {
            $table->integer('Id_Pegawai')->autoIncrement()->primary();
            $table->string('NIP', 255)->nullable();
            $table->string('Nama_Pegawai', 255)->nullable();
            $table->enum('Jenis_Kelamin_Pegawai', ['L', 'P'])->nullable();
            $table->text('Alamat_Pegawai')->nullable();
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
        Schema::dropIfExists('Pegawai_Prodi');
    }
};
