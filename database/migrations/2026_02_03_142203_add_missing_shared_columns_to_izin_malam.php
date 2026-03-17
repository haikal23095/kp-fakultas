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
        Schema::table('surat_izin_kegiatan_malams', function (Blueprint $table) {
            if (!Schema::hasColumn('surat_izin_kegiatan_malams', 'Status')) {
                $table->string('Status')->default('baru')->after('nomor_surat');
            }
            if (!Schema::hasColumn('surat_izin_kegiatan_malams', 'Tanggal_Diberikan')) {
                $table->timestamp('Tanggal_Diberikan')->nullable()->after('Status');
            }
            if (!Schema::hasColumn('surat_izin_kegiatan_malams', 'Tanggal_Diselesaikan')) {
                $table->timestamp('Tanggal_Diselesaikan')->nullable()->after('Tanggal_Diberikan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_izin_kegiatan_malams', function (Blueprint $table) {
            $table->dropColumn(['Status', 'Tanggal_Diberikan', 'Tanggal_Diselesaikan']);
        });
    }
};
