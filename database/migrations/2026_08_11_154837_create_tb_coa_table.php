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
        Schema::create('tb_coa', function (Blueprint $table) {
            $table->string('kode_akun', 10)->primary(); // e.g., 101, 103, 401
            $table->string('nama_akun', 100);
            $table->enum('saldo_normal', ['Debit', 'Kredit']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_coa');
    }
};