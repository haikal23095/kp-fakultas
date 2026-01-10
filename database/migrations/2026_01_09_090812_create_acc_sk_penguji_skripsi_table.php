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
        Schema::create('Acc_SK_Penguji_Skripsi', function (Blueprint $table) {
            $table->integer('No', true);
            $table->enum('Semester', ['Ganjil', 'Genap'])->nullable();
            $table->string('Tahun_Akademik', 100)->nullable();
            $table->json('Data_Penguji_Skripsi')->nullable();
            $table->string('Nomor_Surat', 100)->nullable();
            $table->enum('Status', [
                'Menunggu-Persetujuan-Wadek-1',
                'Menunggu-Persetujuan-Dekan',
                'Selesai',
                'Ditolak-Wadek1',
                'Ditolak-Dekan'
            ])->nullable();
            $table->text('Alasan_Tolak')->nullable();
            $table->string('QR_Code', 255)->nullable();
            $table->timestamp('Tanggal_Persetujuan_Dekan')->nullable();
            $table->integer('Id_Dekan')->nullable();
            $table->dateTime('Tanggal_Pengajuan')->nullable();
            $table->dateTime('Tanggal_Tenggat')->nullable();

            $table->foreign('Id_Dekan', 'fk_penguji_dekan')->references('Id_Dosen')->on('Dosen')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Acc_SK_Penguji_Skripsi');
    }
};
