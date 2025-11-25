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
        Schema::create('Surat_Magang_Invitations', function (Blueprint $table) {
            $table->id('id_invitation');
            $table->unsignedBigInteger('id_draft');
            $table->integer('Id_Mahasiswa_Diundang'); // Mahasiswa yang diundang
            $table->integer('Id_Mahasiswa_Pengundang'); // Mahasiswa yang mengundang
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->text('keterangan')->nullable(); // Alasan jika reject
            $table->timestamp('invited_at')->useCurrent();
            $table->timestamp('responded_at')->nullable();

            // Foreign keys
            $table->foreign('id_draft')->references('id_draft')->on('Surat_Magang_Draft')->onDelete('cascade');
            $table->foreign('Id_Mahasiswa_Diundang')->references('Id_Mahasiswa')->on('Mahasiswa')->onDelete('cascade');
            $table->foreign('Id_Mahasiswa_Pengundang')->references('Id_Mahasiswa')->on('Mahasiswa')->onDelete('cascade');

            // Index untuk query cepat
            $table->index(['Id_Mahasiswa_Diundang', 'status']);
            $table->index('id_draft');
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
