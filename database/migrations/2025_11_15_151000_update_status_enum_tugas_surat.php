<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ubah kolom Status dari ENUM yang terbatas menjadi VARCHAR yang lebih fleksibel
        // atau tambahkan nilai baru ke ENUM
        
        DB::statement("ALTER TABLE Tugas_Surat MODIFY COLUMN Status ENUM(
            'baru',
            'Diterima Admin',
            'Proses',
            'Diajukan ke Dekan',
            'menunggu-ttd',
            'Telah Ditandatangani Dekan',
            'Ditolak',
            'Selesai',
            'Terlambat'
        ) NULL DEFAULT 'baru'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan ke ENUM original
        DB::statement("ALTER TABLE Tugas_Surat MODIFY COLUMN Status ENUM(
            'baru',
            'menunggu-ttd',
            'Selesai',
            'Terlambat'
        ) NULL");
    }
};
