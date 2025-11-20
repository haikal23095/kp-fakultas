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
        // Skip jika tabel sudah ada
        if (!Schema::hasTable('Surat_Magang')) {
            Schema::create('Surat_Magang', function (Blueprint $table) {
                $table->integer('id_no')->autoIncrement()->primary();
                $table->integer('Id_Tugas_Surat');
                $table->json('Data_Mahasiswa')->nullable()->comment('JSON: {nama, nim, jurusan, path_tanda_tangan}');
                $table->json('Data_Dosen_pembiming')->nullable()->comment('JSON: {dosen_pembimbing_1, dosen_pembimbing_2}');
                $table->string('Dokumen_Proposal', 500)->nullable()->comment('Path file proposal mahasiswa');
                $table->string('Surat_Pengantar_Magang', 500)->nullable()->comment('Path file surat pengantar dari instansi');
                $table->string('Nama_Instansi', 255)->nullable();
                $table->text('Alamat_Instansi')->nullable();
                $table->date('Tanggal_Mulai');
                $table->date('Tanggal_Selesai');
                $table->string('Foto_ttd', 500)->nullable();
                $table->integer('Nama_Koordinator');
                $table->tinyInteger('Acc_Koordinator')->default(0);
                $table->enum('Status', [
                    'Diajukan-ke-koordinator',
                    'Dikerjakan-admin',
                    'Diajukan-ke-dekan',
                    'Success',
                    'Ditolak'
                ]);
                $table->text('Komentar')->nullable();

                // Foreign key constraints
                $table->foreign('Id_Tugas_Surat')
                    ->references('Id_Tugas_Surat')
                    ->on('Tugas_Surat')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');

                $table->foreign('Nama_Koordinator')
                    ->references('Id_Dosen')
                    ->on('Dosen')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Surat_Magang');
    }
};
