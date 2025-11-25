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
        Schema::table('Notifikasi', function (Blueprint $table) {
            $table->json('Data_Tambahan')->nullable()->after('Is_Read');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('Notifikasi', function (Blueprint $table) {
            $table->dropColumn('Data_Tambahan');
        });
    }
};
