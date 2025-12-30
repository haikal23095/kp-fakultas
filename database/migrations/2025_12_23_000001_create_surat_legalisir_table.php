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
        if (!Schema::hasTable('Surat_Legalisir')) {
            Schema::create('Surat_Legalisir', function (Blueprint $table) {
                $table->integer('id_no')->autoIncrement()->primary();
                $table->integer('Id_Tugas_Surat');
                $table->integer('Id_User')->comment('User yang mengajukan');
                $table->integer('Id_Pejabat')->nullable()->comment('Pejabat yang berwenang');
                
                $table->enum('Jenis_Dokumen', ['Ijazah', 'Transkrip'])->comment('Jenis dokumen yang dilegalisir');
                $table->integer('Jumlah_Salinan')->default(1);
                $table->integer('Biaya')->nullable()->comment('Biaya legalisir');
                $table->date('Tanggal_Bayar')->nullable()->comment('Tanggal pembayaran lunas');
                $table->string('Nomor_Surat_Legalisir', 100)->nullable()->comment('Nomor surat legalisir yang diberikan admin');
                
                $table->enum('Status', [
                    'pending',
                    'verifikasi_berkas',
                    'menunggu_pembayaran',
                    'pembayaran_lunas',
                    'proses_stempel_paraf',
                    'menunggu_ttd_pimpinan',
                    'siap_diambil',
                    'selesai',
                    'ditolak'
                ])->default('pending');

                // Foreign key constraints
                $table->foreign('Id_Tugas_Surat')
                    ->references('Id_Tugas_Surat')
                    ->on('Tugas_Surat')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');

                $table->foreign('Id_User')
                    ->references('Id_User')
                    ->on('Users')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');

                $table->foreign('Id_Pejabat')
                    ->references('Id_Pejabat')
                    ->on('Pejabat')
                    ->onDelete('set null')
                    ->onUpdate('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Surat_Legalisir');
    }
};
