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
        Schema::create('surat_verifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('id_tugas_surat');
            $table->string('token', 64)->unique();
            $table->string('signed_by', 255);
            $table->unsignedInteger('signed_by_user_id')->nullable();
            $table->timestamp('signed_at');
            $table->string('qr_path', 255)->nullable();
            $table->timestamps();

            $table->foreign('id_tugas_surat')
                ->references('Id_Tugas_Surat')
                ->on('Tugas_Surat')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_verifications');
    }
};
