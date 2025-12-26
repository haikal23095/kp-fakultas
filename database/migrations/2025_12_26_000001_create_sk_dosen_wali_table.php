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
        Schema::create('SK_Dosen_Wali', function (Blueprint $table) {
            $table->integer('No')->primary()->autoIncrement();
            $table->integer('Prodi');
            $table->enum('Semester', ['Ganjil', 'Genap']);
            $table->string('Tahun_Akademik', 12);
            $table->json('Data_Dosen_Wali');
            $table->enum('Status', [
                'Dikerjakan admin',
                'Menunggu-Persetujuan-Wadek-1',
                'Menunggu-Persetujuan-Dekan',
                'Selesai',
                'Ditolak'
            ])->default('Dikerjakan admin');
            $table->timestamp('Tanggal_Pengajuan')->useCurrent();
            $table->dateTime('Tanggal_Tenggat');

            // Foreign key
            $table->foreign('Prodi')->references('Id_Prodi')->on('Prodi')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('SK_Dosen_Wali');
    }
};
