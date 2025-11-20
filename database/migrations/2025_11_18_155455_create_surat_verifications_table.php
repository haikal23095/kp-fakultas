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
        Schema::create('surat_verifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('id_tugas_surat')->index();
            $table->string('token', 64)->unique()->comment('Token unik untuk validasi QR');
            $table->string('signed_by')->comment('Nama atau ID Dekan yang menandatangani');
            $table->unsignedInteger('signed_by_user_id')->nullable()->comment('ID User Dekan');
            $table->timestamp('signed_at')->comment('Waktu persetujuan');
            $table->string('qr_path')->nullable()->comment('Path file QR Code image');
            $table->timestamps();
            
            // Foreign key (no constraint karena type mismatch bisa terjadi)
            // $table->foreign('id_tugas_surat')
            //       ->references('Id_Tugas_Surat')
            //       ->on('Tugas_Surat')
            //       ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_verifications');
    }
};
