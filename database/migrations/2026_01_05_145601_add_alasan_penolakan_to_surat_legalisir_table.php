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
        Schema::table('surat_legalisir', function (Blueprint $table) {
            $table->text('Alasan_Penolakan')->nullable()->after('Status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_legalisir', function (Blueprint $table) {
            $table->dropColumn('Alasan_Penolakan');
        });
    }
};
