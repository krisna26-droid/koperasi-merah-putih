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
        Schema::create('tb_jurnal_detail', function (Blueprint $table) {
            $table->id('id_detail'); // BigIncrements Primary Key
            $table->string('id_jurnal', 20)->index();
            $table->string('kode_akun', 10)->index();
            $table->enum('posisi', ['Debit', 'Kredit']);
            $table->decimal('nominal', 15, 2)->default(0.00);
            $table->timestamps();

            // Foreign Keys
            $table->foreign('id_jurnal')
                  ->references('id_jurnal')
                  ->on('tb_jurnal_umum')
                  ->onDelete('cascade') // Detail terhapus HANYA jika header unposted dihapus
                  ->onUpdate('cascade');

            $table->foreign('kode_akun')
                  ->references('kode_akun')
                  ->on('tb_coa')
                  ->onDelete('restrict') // Master Akun DILARANG HAPUS jika ada transaksi
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_jurnal_detail');
    }
};