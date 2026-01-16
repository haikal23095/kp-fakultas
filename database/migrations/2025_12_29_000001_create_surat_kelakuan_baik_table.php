<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('Surat_Kelakuan_Baik', function (Blueprint $table) {
            $table->id();
            $table->integer('Id_Tugas_Surat');
            $table->integer('Id_User');
            $table->integer('Id_Pejabat')->nullable()->comment('Wakil Dekan 3');
            
            // Data Spesifik
            $table->string('Keperluan', 500)->comment('Tujuan penggunaan surat');
            $table->string('Semester', 20)->comment('Semester saat pengajuan');
            $table->string('Tahun_Akademik', 20)->comment('Tahun akademik saat pengajuan');
            
            // Nomor surat diisi oleh admin setelah diproses
            $table->string('Nomor_Surat', 100)->nullable();

            // Foreign Key Constraints
            $table->foreign('Id_Tugas_Surat')
                ->references('Id_Tugas_Surat')
                ->on('Tugas_Surat')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('Id_User')
                ->references('Id_User')
                ->on('Users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('Id_Pejabat')
                ->references('Id_Pejabat')
                ->on('Pejabat')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Surat_Kelakuan_Baik');
    }
};
