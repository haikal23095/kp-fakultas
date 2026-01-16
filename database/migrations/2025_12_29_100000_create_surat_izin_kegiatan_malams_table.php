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
        Schema::create('surat_izin_kegiatan_malams', function (Blueprint $table) {
            $table->id();
            $table->integer('id_tugas_surat');
            $table->integer('id_user');
            $table->integer('id_pejabat')->nullable()->comment('Wakil Dekan 3');
            
            // Data Spesifik Kegiatan Malam
            $table->string('nama_kegiatan', 255)->comment('Nama kegiatan yang akan dilaksanakan');
            $table->dateTime('waktu_mulai')->comment('Waktu mulai kegiatan');
            $table->dateTime('waktu_selesai')->comment('Waktu selesai kegiatan');
            $table->string('lokasi_kegiatan', 255)->comment('Lokasi pelaksanaan kegiatan');
            $table->integer('jumlah_peserta')->comment('Jumlah peserta yang ikut');
            $table->text('alasan')->comment('Alasan mengadakan kegiatan malam');
            
            // Nomor surat diisi oleh admin setelah diproses
            $table->string('nomor_surat', 100)->nullable();

            // Foreign Key Constraints
            $table->foreign('id_tugas_surat')
                ->references('Id_Tugas_Surat')
                ->on('Tugas_Surat')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('id_user')
                ->references('Id_User')
                ->on('Users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('id_pejabat')
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
        Schema::dropIfExists('surat_izin_kegiatan_malams');
    }
};
