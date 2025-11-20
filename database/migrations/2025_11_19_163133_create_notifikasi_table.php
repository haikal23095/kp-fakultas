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
        Schema::create('Notifikasi', function (Blueprint $table) {
            $table->id('Id_Notifikasi');
            $table->string('Tipe_Notifikasi', 50); // surat, approval, rejection, info, dll
            $table->text('Pesan');
            $table->unsignedInteger('Dest_User'); // User penerima notifikasi
            $table->unsignedInteger('Source_User')->nullable(); // User pengirim (bisa null untuk notif sistem)
            $table->boolean('Is_Read')->default(false);
            $table->timestamps();

            // Foreign keys
            $table->foreign('Dest_User')->references('Id_User')->on('Users')->onDelete('cascade');
            $table->foreign('Source_User')->references('Id_User')->on('Users')->onDelete('set null');

            // Index untuk performa
            $table->index('Dest_User');
            $table->index('Is_Read');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Notifikasi');
    }
};
