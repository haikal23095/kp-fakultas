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
            // Add Status column first if it doesn't exist
            if (!Schema::hasColumn('Tugas_Surat', 'Status')) {
                $table->enum('Status', [
                    'baru',
                    'Diterima Admin',
                    'Proses',
                    'Diajukan ke Dekan',
                    'menunggu-ttd',
                    'Telah Ditandatangani Dekan',
                    'Ditolak',
                    'Selesai',
                    'Terlambat'
                ])->nullable()->default('baru')->after('Nomor_Surat');
            }
            
            // Kolom JSON untuk data spesifik (komentar penolakan, dll)
            if (!Schema::hasColumn('Tugas_Surat', 'data_spesifik')) {
                $table->json('data_spesifik')->nullable()->after('Status');
            }
            
            // Kolom untuk TTE (Tanda Tangan Elektronik) QR Code - untuk future implementation
            if (!Schema::hasColumn('Tugas_Surat', 'signature_qr_data')) {
                $table->text('signature_qr_data')->nullable()->after('data_spesifik')
                    ->comment('JSON data untuk QR signature (signed_by, signed_at, qr_token, dll)');
            }
            
            if (!Schema::hasColumn('Tugas_Surat', 'qr_image_path')) {
                $table->string('qr_image_path', 255)->nullable()->after('signature_qr_data')
                    ->comment('Path ke file QR code image di storage');
            }
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
