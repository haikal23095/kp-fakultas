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
        Schema::table('surat_verifications', function (Blueprint $table) {
            if (!Schema::hasColumn('surat_verifications', 'id_letter')) {
                $table->unsignedBigInteger('id_letter')->nullable()->after('id')->index();
            }
            if (!Schema::hasColumn('surat_verifications', 'letter_type')) {
                $table->string('letter_type')->nullable()->after('id_letter')->index();
            }
            // id_tugas_surat nullable because it might be null for new letters
            $table->unsignedInteger('id_tugas_surat')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_verifications', function (Blueprint $table) {
            $table->dropColumn(['id_letter', 'letter_type']);
            $table->unsignedInteger('id_tugas_surat')->nullable(false)->change();
        });
    }
};
