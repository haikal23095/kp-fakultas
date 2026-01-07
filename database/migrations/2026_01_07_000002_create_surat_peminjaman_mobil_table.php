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
        Schema::create('Surat_Peminjaman_Mobil', function (Blueprint $table) {
            $table->id();
            
            // Foreign Keys
            $table->integer('Id_Tugas_Surat')->nullable();
            $table->integer('Id_User');
            $table->unsignedBigInteger('Id_Kendaraan')->nullable();
            $table->integer('Id_Pejabat')->nullable();
            
            // Data Input dari Mahasiswa
            $table->text('tujuan');
            $table->text('keperluan');
            $table->date('tanggal_pemakaian_mulai');
            $table->date('tanggal_pemakaian_selesai');
            $table->integer('jumlah_penumpang');
            
            // Data dari Admin/Wadek
            $table->text('rekomendasi_admin')->nullable();
            $table->text('alasan_penolakan')->nullable();
            $table->string('nomor_surat')->nullable();
            $table->string('file_surat_final')->nullable();
            
            // Status Pengajuan
            $table->enum('status_pengajuan', [
                'Diajukan',
                'Diverifikasi_Admin',
                'Disetujui_Wadek2',
                'Ditolak',
                'Selesai'
            ])->default('Diajukan');
            
            $table->timestamps();
            
            // Foreign Key Constraints
            $table->foreign('Id_Tugas_Surat')
                  ->references('Id_Tugas_Surat')->on('Tugas_Surat')
                  ->onDelete('cascade');
                  
            $table->foreign('Id_User')
                  ->references('Id_User')->on('Users')
                  ->onDelete('cascade');
                  
            $table->foreign('Id_Kendaraan')
                  ->references('id')->on('Kendaraan')
                  ->onDelete('set null');
                  
            $table->foreign('Id_Pejabat')
                  ->references('Id_Pejabat')->on('Pejabat')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Surat_Peminjaman_Mobil');
    }
};
