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
        Schema::create('tb_pinjaman', function (Blueprint $table) {
            $table->string('id_pinjam', 20)->primary(); // e.g., PNJ-20260811-001
            $table->string('id_anggota', 20)->index();
            $table->decimal('jumlah_pinjam', 15, 2)->default(0.00); // Pokok pinjaman
            $table->integer('tenor')->default(1); // Jangka waktu (Bulan)
            $table->decimal('bunga_rate', 5, 2)->default(1.50); // Flat Rate 1.50%
            $table->decimal('pokok_per_bulan', 15, 2)->default(0.00); // Pokok/tenor
            $table->decimal('bunga_per_bulan', 15, 2)->default(0.00); // Pokok * 1.5%
            $table->decimal('sisa_tagihan', 15, 2)->default(0.00); // Remaining principal
            $table->enum('status', ['Pengajuan', 'Disetujui', 'Ditolak', 'Berjalan', 'Lunas'])
                  ->default('Pengajuan')
                  ->index(); // Status index for multi-loan check
            $table->boolean('is_jurnaled')->default(false);
            $table->string('created_by', 50)->nullable()->index(); // Pengurus pengaju
            $table->string('approved_by', 50)->nullable()->index(); // Ketua approver
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            // Foreign Keys
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

            $table->foreign('approved_by')
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
        Schema::dropIfExists('tb_pinjaman');
    }
};