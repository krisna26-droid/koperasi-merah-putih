<?php

namespace Database\Seeders;

use App\Models\Coa;
use Illuminate\Database\Seeder;

class CoaSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['kode_akun' => '101', 'nama_akun' => 'Kas Koperasi', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '102', 'nama_akun' => 'Kas Bank', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '103', 'nama_akun' => 'Piutang Pinjaman Anggota', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '104', 'nama_akun' => 'Aktiva Tetap', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '201', 'nama_akun' => 'Hutang Operasional', 'saldo_normal' => 'Kredit'],
            ['kode_akun' => '301', 'nama_akun' => 'Simpanan Pokok', 'saldo_normal' => 'Kredit'],
            ['kode_akun' => '302', 'nama_akun' => 'Simpanan Wajib', 'saldo_normal' => 'Kredit'],
            ['kode_akun' => '303', 'nama_akun' => 'Simpanan Sukarela', 'saldo_normal' => 'Kredit'],
            ['kode_akun' => '401', 'nama_akun' => 'Pendapatan Bunga Pinjaman', 'saldo_normal' => 'Kredit'],
            ['kode_akun' => '402', 'nama_akun' => 'Pendapatan Provisi', 'saldo_normal' => 'Kredit'],
            ['kode_akun' => '501', 'nama_akun' => 'Beban Operasional Kantor', 'saldo_normal' => 'Debit'],
        ];

        foreach ($accounts as $acc) {
            Coa::updateOrCreate(['kode_akun' => $acc['kode_akun']], $acc);
        }
    }
}
