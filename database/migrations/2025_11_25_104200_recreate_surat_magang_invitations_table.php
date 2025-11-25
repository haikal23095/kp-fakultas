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
        // Drop tabel lama jika ada
        Schema::dropIfExists('Surat_Magang_Invitations');

        // Buat tabel baru dengan struktur yang benar
        Schema::create('Surat_Magang_Invitations', function (Blueprint $table) {
            $table->id();
            $table->integer('id_surat_magang'); // Ganti dari id_draft, integer untuk match dengan id_no
            $table->integer('id_mahasiswa_diundang'); // Mahasiswa yang diundang
            $table->integer('id_mahasiswa_pengundang'); // Mahasiswa yang mengundang
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->text('keterangan')->nullable(); // Alasan jika reject
            $table->timestamp('invited_at')->useCurrent();
            $table->timestamp('responded_at')->nullable();

            // Foreign keys
            $table->foreign('id_surat_magang')->references('id_no')->on('Surat_Magang')->onDelete('cascade');
            $table->foreign('id_mahasiswa_diundang')->references('Id_Mahasiswa')->on('Mahasiswa')->onDelete('cascade');
            $table->foreign('id_mahasiswa_pengundang')->references('Id_Mahasiswa')->on('Mahasiswa')->onDelete('cascade');

            // Index untuk query cepat
            $table->index(['id_mahasiswa_diundang', 'status']);
            $table->index('id_surat_magang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Surat_Magang_Invitations');
    }
};
