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
        Schema::table('Surat_Kelakuan_Baik', function (Blueprint $table) {
            if (!Schema::hasColumn('Surat_Kelakuan_Baik', 'Status')) {
                $table->string('Status')->default('baru')->after('Nomor_Surat');
            }
            if (!Schema::hasColumn('Surat_Kelakuan_Baik', 'Tanggal_Diberikan')) {
                $table->timestamp('Tanggal_Diberikan')->nullable()->after('Status');
            }
            if (!Schema::hasColumn('Surat_Kelakuan_Baik', 'Tanggal_Diselesaikan')) {
                $table->timestamp('Tanggal_Diselesaikan')->nullable()->after('Tanggal_Diberikan');
            }
            if (!Schema::hasColumn('Surat_Kelakuan_Baik', 'Id_Penerima_Tugas')) {
                $table->integer('Id_Penerima_Tugas')->nullable()->after('Id_User');
            }
        });

        Schema::table('Surat_Tidak_Beasiswa', function (Blueprint $table) {
            if (!Schema::hasColumn('Surat_Tidak_Beasiswa', 'Status')) {
                $table->string('Status')->default('baru')->after('Nomor_Surat');
            }
            if (!Schema::hasColumn('Surat_Tidak_Beasiswa', 'Tanggal_Diberikan')) {
                $table->timestamp('Tanggal_Diberikan')->nullable()->after('Status');
            }
            if (!Schema::hasColumn('Surat_Tidak_Beasiswa', 'Tanggal_Diselesaikan')) {
                $table->timestamp('Tanggal_Diselesaikan')->nullable()->after('Tanggal_Diberikan');
            }
            if (!Schema::hasColumn('Surat_Tidak_Beasiswa', 'Id_Penerima_Tugas')) {
                $table->integer('Id_Penerima_Tugas')->nullable()->after('Id_User');
            }
        });

        Schema::table('Surat_Dispensasi', function (Blueprint $table) {
            if (!Schema::hasColumn('Surat_Dispensasi', 'Status')) {
                $table->string('Status')->default('baru')->after('nomor_surat');
            }
            if (!Schema::hasColumn('Surat_Dispensasi', 'Tanggal_Diberikan')) {
                $table->timestamp('Tanggal_Diberikan')->nullable()->after('Status');
            }
            if (!Schema::hasColumn('Surat_Dispensasi', 'Tanggal_Diselesaikan')) {
                $table->timestamp('Tanggal_Diselesaikan')->nullable()->after('Tanggal_Diberikan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('Surat_Kelakuan_Baik', function (Blueprint $table) {
            $table->dropColumn(['Status', 'Tanggal_Diberikan', 'Tanggal_Diselesaikan', 'Id_Penerima_Tugas']);
        });

        Schema::table('Surat_Tidak_Beasiswa', function (Blueprint $table) {
            $table->dropColumn(['Status', 'Tanggal_Diberikan', 'Tanggal_Diselesaikan', 'Id_Penerima_Tugas']);
        });

        Schema::table('Surat_Dispensasi', function (Blueprint $table) {
            $table->dropColumn(['Status', 'Tanggal_Diberikan', 'Tanggal_Diselesaikan']);
        });
    }
};
