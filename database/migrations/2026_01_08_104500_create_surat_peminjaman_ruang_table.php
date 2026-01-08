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
        Schema::create('Surat_Peminjaman_Ruang', function (Blueprint $table) {
            $table->id();
            $table->integer('Id_Tugas_Surat')->nullable();
            $table->integer('Id_Ruangan')->nullable();
            $table->string('nama_kegiatan');
            $table->string('penyelenggara');
            $table->dateTime('tanggal_mulai');
            $table->dateTime('tanggal_selesai');
            $table->integer('jumlah_peserta');
            $table->string('file_lampiran')->nullable();
            $table->text('keterangan')->nullable();
            $table->text('rekomendasi_admin')->nullable();
            $table->text('catatan_wadek2')->nullable();
            $table->text('alasan_penolakan')->nullable();
            $table->string('nomor_surat')->nullable();
            $table->string('file_surat_final')->nullable();
            $table->string('qr_code_path')->nullable();
            $table->enum('status_pengajuan', ['Diajukan', 'Diverifikasi_Admin', 'Disetujui_Wadek2', 'Ditolak', 'Selesai'])->default('Diajukan');
            $table->timestamps();

            // Foreign keys
            $table->foreign('Id_Tugas_Surat')->references('Id_Tugas_Surat')->on('Tugas_Surat')->onDelete('cascade');
            $table->foreign('Id_Ruangan')->references('Id_Ruangan')->on('Ruangan')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Surat_Peminjaman_Ruang');
    }
};
