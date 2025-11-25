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
        Schema::dropIfExists('Surat_Magang_Draft');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('Surat_Magang_Draft', function (Blueprint $table) {
            $table->id('id_draft');
            $table->unsignedBigInteger('Id_Mahasiswa_Pembuat');
            $table->unsignedBigInteger('Id_Jenis_Surat');
            $table->json('Data_Mahasiswa_Confirmed')->nullable();
            $table->json('Data_Mahasiswa_Pending')->nullable();
            $table->string('Nama_Instansi')->nullable();
            $table->text('Alamat_Instansi')->nullable();
            $table->string('Judul_Penelitian')->nullable();
            $table->date('Tanggal_Mulai')->nullable();
            $table->date('Tanggal_Selesai')->nullable();
            $table->string('Dosen_Pembimbing_1')->nullable();
            $table->string('Dosen_Pembimbing_2')->nullable();
            $table->timestamps();

            $table->foreign('Id_Mahasiswa_Pembuat')->references('Id_Mahasiswa')->on('Mahasiswa')->onDelete('cascade');
            $table->foreign('Id_Jenis_Surat')->references('Id_Jenis_Surat')->on('Jenis_Surat')->onDelete('cascade');
        });
    }
};
