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
            $table->bigInteger('Id_Notifikasi')->autoIncrement()->primary();
            $table->enum('Tipe_Notifikasi', ['Rejected', 'Accepted', 'Caution', 'Error']);
            $table->text('Pesan');
            $table->integer('Dest_User'); // Perbaiki penulisan Dest_user menjadi Dest_User
            $table->integer('Source_User');
            $table->boolean('Is_Read')->default(false); // Tambahkan kolom Is_Read
            $table->timestamps(); // Tambahkan timestamps (created_at, updated_at)

            $table->foreign('Dest_User')
                ->references('Id_User')
                ->on('Users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('Source_User')
                ->references('Id_User')
                ->on('Users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};
