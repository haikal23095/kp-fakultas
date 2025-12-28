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
        Schema::create('Surat_Tidak_Beasiswa', function (Blueprint $table) {
            $table->id();
            $table->integer('Id_Tugas_Surat');
            $table->integer('Id_User');
            $table->integer('Id_Pejabat')->nullable();
            $table->string('Nama_Orang_Tua', 255);
            $table->string('Pekerjaan_Orang_Tua', 255);
            $table->string('NIP_Orang_Tua', 50)->nullable()->comment('Khusus PNS/ASN');
            $table->decimal('Pendapatan_Orang_Tua', 15, 2)->comment('Pendapatan per bulan dalam Rupiah');
            $table->string('Keperluan', 255);
            $table->string('File_Pernyataan', 500)->comment('Path file surat pernyataan');
            $table->string('Nomor_Surat', 100)->nullable();

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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Surat_Tidak_Beasiswa');
    }
};
