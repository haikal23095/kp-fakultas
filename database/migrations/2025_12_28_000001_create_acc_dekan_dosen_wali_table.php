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
        // Tabel ACC_SK_Dosen_Wali sesuai struktur yang diminta
        Schema::create('ACC_SK_Dosen_Wali', function (Blueprint $table) {
            $table->integer('No')->primary()->autoIncrement();
            $table->enum('Semester', ['Ganjil', 'Genap']);
            $table->string('Tahun_Akademik', 12);
            $table->json('Data_Dosen_Wali');
            $table->string('Nomor_Surat', 100)->nullable();
            $table->enum('Status', [
                'Dikerjakan admin',
                'Menunggu-Persetujuan-Wadek-1',
                'Menunggu-Persetujuan-Dekan',
                'Selesai',
                'Ditolak'
            ])->default('Menunggu-Persetujuan-Wadek-1');
            $table->timestamp('Tanggal_Pengajuan')->useCurrent();
            $table->dateTime('Tanggal_Tenggat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ACC_SK_Dosen_Wali');
    }
};
