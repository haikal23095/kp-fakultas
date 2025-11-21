<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('Notifikasi', function (Blueprint $table) {
            if (!Schema::hasColumn('Notifikasi', 'Is_Read')) {
                $table->boolean('Is_Read')->default(false)->after('Source_User');
            }
            if (!Schema::hasColumn('Notifikasi', 'created_at')) {
                $table->timestamps();
            }
            // Fix column name case if needed
            if (Schema::hasColumn('Notifikasi', 'Dest_user') && !Schema::hasColumn('Notifikasi', 'Dest_User')) {
                $table->renameColumn('Dest_user', 'Dest_User');
            }
        });
    }

    public function down()
    {
        Schema::table('Notifikasi', function (Blueprint $table) {
            $table->dropColumn(['Is_Read', 'created_at', 'updated_at']);
        });
    }
};
