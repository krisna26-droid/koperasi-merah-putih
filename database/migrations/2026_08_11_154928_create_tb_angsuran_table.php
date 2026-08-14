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
        Schema::create('tb_angsuran', function (Blueprint $table) {
            $table->string('id_angsuran', 20)->primary(); // e.g., ANG-20260811-001
            $table->string('id_pinjam', 20)->index();
            $table->string('id_anggota', 20)->index();
            $table->integer('angsuran_ke')->default(1);
            $table->decimal('bayar_pokok', 15, 2)->default(0.00); // Porsi Pokok
            $table->decimal('bayar_bunga', 15, 2)->default(0.00); // Porsi Bunga (1.5%)
            $table->decimal('total_bayar', 15, 2)->default(0.00); // Pokok + Bunga
            $table->boolean('is_jurnaled')->default(false);
            $table->string('created_by', 50)->nullable()->index();
            $table->timestamps();

            // Foreign Keys
            $table->foreign('id_pinjam')
                  ->references('id_pinjam')
                  ->on('tb_pinjaman')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');

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
        Schema::dropIfExists('tb_angsuran');
    }
};