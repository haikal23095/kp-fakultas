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
        Schema::create('Req_SK_Penguji_Skripsi', function (Blueprint $table) {
            $table->integer('No', true);
            $table->integer('Id_Prodi')->nullable();
            $table->enum('Semester', ['Ganjil', 'Genap'])->nullable();
            $table->string('Tahun_Akademik', 100);
            $table->json('Data_Penguji_Skripsi')->nullable();
            $table->integer('Id_Dosen_Kaprodi')->nullable();
            $table->string('Nomor_Surat', 100)->nullable();
            $table->enum('Status', [
                'Dikerjakan admin',
                'Menunggu-Persetujuan-Wadek-1',
                'Menunggu-Persetujuan-Dekan',
                'Selesai',
                'Ditolak-Admin',
                'Ditolak-Wadek1',
                'Ditolak-Dekan'
            ])->nullable();
            $table->integer('Id_Acc_SK_Penguji_Skripsi')->nullable();
            $table->text('Alasan_Tolak')->nullable();
            $table->timestamp('Tanggal_Pengajuan')->useCurrent();
            $table->dateTime('Tanggal_Tenggat')->nullable();

            $table->foreign('Id_Prodi', 'fk_prodi_penguji')->references('Id_Prodi')->on('Prodi')->onDelete('cascade');
            $table->foreign('Id_Dosen_Kaprodi', 'fk_kaprodi_penguji')->references('Id_Dosen')->on('Dosen')->onDelete('cascade');
            $table->foreign('Id_Acc_SK_Penguji_Skripsi', 'fk_Acc_Penguji_skripsi')->references('No')->on('Acc_SK_Penguji_Skripsi')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Req_SK_Penguji_Skripsi');
    }
};
