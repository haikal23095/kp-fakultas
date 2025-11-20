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
        Schema::create('Dosen', function (Blueprint $table) {
            $table->integer('Id_Dosen')->autoIncrement()->primary();
            $table->string('NIP', 255)->nullable();
            $table->string('Nama_Dosen', 255)->nullable();
            $table->text('Alamat_Dosen')->nullable();
            $table->integer('Id_User')->nullable();
            $table->integer('Id_Prodi')->nullable();
            $table->integer('Id_Pejabat')->nullable();

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

            $table->foreign('Id_Pejabat')
                ->references('Id_Pejabat')
                ->on('Pejabat')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Dosen');
    }
};
