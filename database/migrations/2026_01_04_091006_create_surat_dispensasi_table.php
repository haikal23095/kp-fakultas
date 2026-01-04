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
        Schema::create('Surat_Dispensasi', function (Blueprint $table) {
            $table->id();
            $table->integer('Id_Tugas_Surat');
            $table->integer('Id_User')->comment('User yang mengajukan');
            $table->integer('Id_Pejabat_Wadek3')->nullable()->comment('Wadek 3 yang menyetujui');
            
            // Data Input Kegiatan
            $table->string('nama_kegiatan')->comment('Nama kegiatan/alasan: Sakit, Lomba, dll');
            $table->string('instansi_penyelenggara')->nullable()->comment('Kosongkan jika sakit/pribadi');
            $table->string('tempat_pelaksanaan')->nullable()->comment('Kosongkan jika tidak relevan');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');

            // --- FILE LAMPIRAN ---
            // 1. Surat Permohonan (Wajib)
            $table->string('file_permohonan')->comment('Surat permohonan resmi dari mahasiswa'); 

            // 2. Bukti Pendukung (Opsional: Undangan / Surat Dokter / Lainnya)
            $table->string('file_lampiran')->nullable()->comment('Undangan atau Surat Dokter'); 

            // Bagian Admin & Approval
            $table->string('nomor_surat')->nullable()->comment('Nomor surat diisi admin setelah diproses');
            $table->integer('verifikasi_admin_by')->nullable()->comment('User admin yang memverifikasi');
            $table->date('verifikasi_admin_at')->nullable();
            
            $table->integer('acc_wadek3_by')->nullable()->comment('User yang acc sebagai Wadek3');
            $table->date('acc_wadek3_at')->nullable();
            
            $table->string('file_surat_selesai')->nullable()->comment('File surat yang sudah ditandatangani');
            
            $table->text('keterangan_status')->nullable()->comment('Catatan/alasan jika ditolak');

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

            $table->foreign('Id_Pejabat_Wadek3')
                ->references('Id_Pejabat')
                ->on('Pejabat')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->foreign('verifikasi_admin_by')
                ->references('Id_User')
                ->on('Users')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->foreign('acc_wadek3_by')
                ->references('Id_User')
                ->on('Users')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Surat_Dispensasi');
    }
};
