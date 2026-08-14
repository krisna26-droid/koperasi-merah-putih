<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Anggota;
use App\Models\Coa;
use App\Models\Simpanan;
use App\Models\Pinjaman;
use App\Models\Angsuran;
use App\Models\JurnalUmum;
use App\Models\JurnalDetail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelRelationshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_models_and_relationships_can_be_instantiated(): void
    {
        // 1. Create User & Anggota
        $user = User::create([
            'username' => 'admin_test',
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'peran' => 'Admin',
        ]);

        $anggota = Anggota::create([
            'id_anggota' => 'A-001',
            'nama' => 'Budi Santoso',
            'alamat' => 'Desa Buruan',
            'no_telepon' => '08123456789',
            'status' => 'Aktif',
            'created_by' => $user->username,
        ]);

        $this->assertEquals('Admin Test', $anggota->creator->name);
        $this->assertCount(1, $user->anggotaCreated);

        // 2. Create CoA & Jurnal
        $coaKas = Coa::create([
            'kode_akun' => '101',
            'nama_akun' => 'Kas',
            'saldo_normal' => 'Debit',
        ]);

        $jurnal = JurnalUmum::create([
            'id_jurnal' => 'JRN-001',
            'tanggal' => '2026-08-11',
            'tipe_transaksi' => 'Manual',
            'keterangan' => 'Test Jurnal',
            'created_by' => $user->username,
        ]);

        $detail = JurnalDetail::create([
            'id_jurnal' => $jurnal->id_jurnal,
            'kode_akun' => $coaKas->kode_akun,
            'posisi' => 'Debit',
            'nominal' => 100000.00,
        ]);

        $this->assertEquals('Test Jurnal', $detail->header->keterangan);
        $this->assertEquals('Kas', $detail->coa->nama_akun);
        $this->assertCount(1, $jurnal->details);

        // 3. Create Pinjaman & Angsuran
        $pinjaman = Pinjaman::create([
            'id_pinjam' => 'PNJ-001',
            'id_anggota' => $anggota->id_anggota,
            'jumlah_pinjam' => 1000000.00,
            'tenor' => 10,
            'bunga_rate' => 1.50,
            'pokok_per_bulan' => 100000.00,
            'bunga_per_bulan' => 15000.00,
            'sisa_tagihan' => 1000000.00,
            'status' => 'Berjalan',
            'created_by' => $user->username,
        ]);

        $angsuran = Angsuran::create([
            'id_angsuran' => 'ANG-001',
            'id_pinjam' => $pinjaman->id_pinjam,
            'id_anggota' => $anggota->id_anggota,
            'angsuran_ke' => 1,
            'bayar_pokok' => 100000.00,
            'bayar_bunga' => 15000.00,
            'total_bayar' => 115000.00,
            'created_by' => $user->username,
        ]);

        $this->assertEquals('PNJ-001', $angsuran->pinjaman->id_pinjam);
        $this->assertCount(1, $pinjaman->angsuran);
        $this->assertEquals('Budi Santoso', $pinjaman->anggota->nama);
    }
}