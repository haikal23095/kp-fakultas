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
        Schema::table('Surat_Peminjaman_Mobil', function (Blueprint $table) {
            $table->string('qr_code_path')->nullable()->after('file_surat_final');
            $table->text('catatan_wadek2')->nullable()->after('rekomendasi_admin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('Surat_Peminjaman_Mobil', function (Blueprint $table) {
            $table->dropColumn(['qr_code_path', 'catatan_wadek2']);
        });
    }
};
