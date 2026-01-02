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
        Schema::table('Surat_Legalisir', function (Blueprint $table) {
            $table->string('File_Scan_Path')->nullable()->after('Biaya');
            $table->string('File_Signed_Path')->nullable()->after('File_Scan_Path');
            $table->boolean('Is_Verified')->default(false)->after('File_Signed_Path');
            $table->string('TTD_Oleh')->nullable()->after('Is_Verified')->comment('Dekan atau Wadek1');
            $table->timestamp('TTD_At')->nullable()->after('TTD_Oleh');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('Surat_Legalisir', function (Blueprint $table) {
            $table->dropColumn(['File_Scan_Path', 'File_Signed_Path', 'Is_Verified', 'TTD_Oleh', 'TTD_At']);
        });
    }
};
