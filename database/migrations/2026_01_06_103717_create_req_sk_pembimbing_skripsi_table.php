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
        // Tabel Request SK Pembimbing Skripsi
        Schema::create('Req_SK_Pembimbing_Skripsi', function (Blueprint $table) {
            $table->id('No');
            $table->unsignedBigInteger('Id_Prodi');
            $table->enum('Semester', ['Ganjil', 'Genap']);
            $table->string('Tahun_Akademik', 20);
            $table->json('Data_Pembimbing'); // Store array of mahasiswa with pembimbing
            $table->string('Nomor_Surat', 100)->nullable();
            $table->unsignedBigInteger('Id_Acc_SK_Pembimbing_Skripsi')->nullable();
            $table->dateTime('Tanggal-Pengajuan');
            $table->dateTime('Tanggal-Tenggat');
            $table->unsignedBigInteger('Id_Dosen_Kaprodi')->nullable();
            $table->enum('Status', ['Dikerjakan admin', 'Ditolak', 'Selesai'])->default('Dikerjakan admin');
            $table->text('Alasan-Tolak')->nullable();

            // Foreign keys
            $table->foreign('Id_Prodi')->references('Id_Prodi')->on('Prodi')->onDelete('cascade');
            $table->foreign('Id_Dosen_Kaprodi')->references('Id_Dosen')->on('Dosen')->onDelete('set null');
        });

        // Tabel Acc SK Pembimbing Skripsi (untuk SK yang sudah disetujui)
        Schema::create('Acc_SK_Pembimbing_Skripsi', function (Blueprint $table) {
            $table->id('No');
            $table->unsignedBigInteger('Id_Prodi');
            $table->enum('Semester', ['Ganjil', 'Genap']);
            $table->string('Tahun_Akademik', 20);
            $table->json('Data_Pembimbing');
            $table->string('Nomor_Surat', 100);
            $table->dateTime('Tanggal-Persetujuan-Dekan')->nullable();
            $table->string('QR_Code', 255)->nullable();

            // Foreign keys
            $table->foreign('Id_Prodi')->references('Id_Prodi')->on('Prodi')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Acc_SK_Pembimbing_Skripsi');
        Schema::dropIfExists('Req_SK_Pembimbing_Skripsi');
    }
};
