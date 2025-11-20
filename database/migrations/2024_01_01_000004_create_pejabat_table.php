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
        Schema::create('Pejabat', function (Blueprint $table) {
            $table->integer('Id_Pejabat')->primary();
            $table->enum('Nama_Jabatan', ['Kaprodi', 'Kajur', 'Dekan'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Pejabat');
    }
};
