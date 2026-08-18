<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_anggota', function (Blueprint $table) {
            $table->string('nik', 20)->nullable()->after('nama');
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan'])->default('Laki-laki')->after('nik');
            $table->string('pekerjaan', 100)->nullable()->after('jenis_kelamin');
            $table->string('email', 100)->nullable()->after('no_telepon');
        });
    }

    public function down(): void
    {
        Schema::table('tb_anggota', function (Blueprint $table) {
            $table->dropColumn(['nik', 'jenis_kelamin', 'pekerjaan', 'email']);
        });
    }
};