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
        Schema::create('tb_simpanan', function (Blueprint $table) {
            $table->string('id_simpanan', 20)->primary(); // e.g., SMP-20260811-001
            $table->string('id_anggota', 20)->index();
            $table->enum('jenis_simpanan', ['Pokok', 'Wajib', 'Sukarela']);
            $table->decimal('nominal', 15, 2)->default(0.00);
            $table->boolean('is_jurnaled')->default(false); // Penanda status posting
            $table->string('created_by', 50)->nullable()->index(); // Audit kasir
            $table->timestamps();

            // Foreign Keys (Restricted ON DELETE to preserve audit integrity)
            $table->foreign('id_anggota')
                  ->references('id_anggota')
                  ->on('tb_anggota')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');

            $table->foreign('created_by')
                  ->references('username')
                  ->on('users')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_simpanan');
    }
};