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
        Schema::table('Tugas_Surat', function (Blueprint $table) {
            // Kolom JSON untuk data spesifik (komentar penolakan, dll)
            $table->json('data_spesifik')->nullable()->after('Status');
            
            // Kolom untuk TTE (Tanda Tangan Elektronik) QR Code - untuk future implementation
            $table->text('signature_qr_data')->nullable()->after('data_spesifik')
                ->comment('JSON data untuk QR signature (signed_by, signed_at, qr_token, dll)');
            $table->string('qr_image_path', 255)->nullable()->after('signature_qr_data')
                ->comment('Path ke file QR code image di storage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('Tugas_Surat', function (Blueprint $table) {
            $table->dropColumn(['data_spesifik', 'signature_qr_data', 'qr_image_path']);
        });
    }
};
