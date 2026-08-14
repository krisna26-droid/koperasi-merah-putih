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
        Schema::create('tb_anggota', function (Blueprint $table) {
            $table->string('id_anggota', 20)->primary(); // e.g., A-001
            $table->string('nama', 100);
            $table->text('alamat');
            $table->string('no_telepon', 15);
            $table->enum('status', ['Aktif', 'Cuti'])->default('Aktif');
            $table->string('created_by', 50)->nullable()->index(); // Audit user pengurus
            $table->timestamps();

            // Foreign Key
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
        Schema::dropIfExists('tb_anggota');
    }
};