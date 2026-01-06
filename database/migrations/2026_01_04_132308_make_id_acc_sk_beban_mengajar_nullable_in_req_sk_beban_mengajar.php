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
        Schema::table('Req_SK_Beban_Mengajar', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign('fk_req_to_acc_beban_mnegajar');

            // Modify columns to nullable (karena saat kaprodi ajukan, belum ada Acc dan Nomor Surat)
            $table->integer('Id_Acc_SK_Beban_Mengajar')->nullable()->change();
            $table->string('Nomor_Surat', 100)->nullable()->change();

            // Re-add foreign key constraint
            $table->foreign('Id_Acc_SK_Beban_Mengajar', 'fk_req_to_acc_beban_mnegajar')
                ->references('No')
                ->on('Acc_SK_Beban_Mengajar')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('Req_SK_Beban_Mengajar', function (Blueprint $table) {
            // Drop foreign key constraint
            $table->dropForeign('fk_req_to_acc_beban_mnegajar');

            // Revert columns to NOT NULL
            $table->integer('Id_Acc_SK_Beban_Mengajar')->nullable(false)->change();
            $table->string('Nomor_Surat', 100)->nullable(false)->change();

            // Re-add foreign key constraint
            $table->foreign('Id_Acc_SK_Beban_Mengajar', 'fk_req_to_acc_beban_mnegajar')
                ->references('No')
                ->on('Acc_SK_Beban_Mengajar')
                ->onDelete('cascade');
        });
    }
};
