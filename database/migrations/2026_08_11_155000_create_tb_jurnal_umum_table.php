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
        Schema::create('tb_jurnal_umum', function (Blueprint $table) {
            $table->string('id_jurnal', 20)->primary(); // e.g., JRN-20260811-001
            $table->date('tanggal')->index(); // Tanggal transaksi akuntansi
            $table->string('id_referensi', 20)->nullable()->index(); // ID Simpanan/Pinjaman/Angsuran
            $table->string('tipe_transaksi', 50); // e.g., 'Simpanan', 'Pinjaman', 'Angsuran', 'Reversal'
            $table->text('keterangan');
            $table->boolean('is_reversal')->default(false)->index(); // Anti-hard delete flag
            $table->string('reversed_ref', 20)->nullable()->index(); // ID Jurnal yang dibalik
            $table->string('created_by', 50)->nullable()->index();
            $table->timestamps();

            // Foreign Keys
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
        Schema::dropIfExists('tb_jurnal_umum');
    }
};