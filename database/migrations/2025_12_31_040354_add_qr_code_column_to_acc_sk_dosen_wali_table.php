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
        Schema::table('Acc_SK_Dosen_Wali', function (Blueprint $table) {
            $table->string('QR_Code', 255)->nullable()->after('Status');
            $table->timestamp('Tanggal-Persetujuan-Dekan')->nullable()->after('QR_Code');
            $table->integer('Id_Dekan')->nullable()->after('Tanggal-Persetujuan-Dekan');

            // Add foreign key constraint
            $table->foreign('Id_Dekan')
                ->references('Id_Dosen')
                ->on('Dosen')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('Acc_SK_Dosen_Wali', function (Blueprint $table) {
            $table->dropColumn(['QR_Code', 'Tanggal-Persetujuan-Dekan', 'Id_Dekan']);
        });
    }
};
