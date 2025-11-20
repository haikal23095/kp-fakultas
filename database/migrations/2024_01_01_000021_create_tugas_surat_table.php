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
        Schema::create('Tugas_Surat', function (Blueprint $table) {
            $table->integer('Id_Tugas_Surat')->primary();
            $table->integer('Id_Pemberi_Tugas_Surat')->nullable();
            $table->integer('Id_Penerima_Tugas_Surat')->nullable();
            $table->integer('Id_Jenis_Surat')->nullable();
            $table->integer('Id_Jenis_Pekerjaan')->nullable();
            $table->string('Judul_Tugas_Surat', 255)->nullable();
            $table->string('Nomor_Surat', 255)->nullable();
            $table->enum('Status', [
                'baru',
                'Diterima Admin',
                'Proses',
                'Diajukan ke Dekan',
                'menunggu-ttd',
                'Telah Ditandatangani Dekan',
                'Ditolak',
                'Selesai',
                'Terlambat'
            ])->nullable()->default('baru');
            $table->json('data_spesifik')->nullable();
            $table->text('signature_qr_data')->nullable();
            $table->string('qr_image_path', 255)->nullable();
            $table->date('Tanggal_Diberikan_Tugas_Surat')->nullable();
            $table->date('Tanggal_Tenggat_Tugas_Surat')->nullable();
            $table->date('Tanggal_Diselesaikan')->nullable();

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

            $table->foreign('Id_Jenis_Surat')
                ->references('Id_Jenis_Surat')
                ->on('Jenis_Surat')
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
        Schema::dropIfExists('Tugas_Surat');
    }
};
