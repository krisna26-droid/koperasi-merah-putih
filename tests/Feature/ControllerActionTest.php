<?php

namespace Tests\Feature;

use App\Models\Anggota;
use App\Models\Coa;
use App\Models\Pinjaman;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ControllerActionTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $ketuaUser;
    protected Anggota $anggota;

    protected function setUp(): void
    {
        parent::setUp();

        Coa::create(['kode_akun' => '101', 'nama_akun' => 'Kas', 'saldo_normal' => 'Debit']);
        Coa::create(['kode_akun' => '103', 'nama_akun' => 'Piutang Pinjaman', 'saldo_normal' => 'Debit']);
        Coa::create(['kode_akun' => '201', 'nama_akun' => 'Simpanan Pokok', 'saldo_normal' => 'Kredit']);
        Coa::create(['kode_akun' => '401', 'nama_akun' => 'Pendapatan Bunga', 'saldo_normal' => 'Kredit']);

        $this->adminUser = User::create([
            'username' => 'admin_ctrl',
            'name' => 'Admin Ctrl',
            'email' => 'admin_ctrl@test.com',
            'password' => bcrypt('password'),
            'peran' => 'Admin',
        ]);

        $this->ketuaUser = User::create([
            'username' => 'ketua_ctrl',
            'name' => 'Ketua Ctrl',
            'email' => 'ketua_ctrl@test.com',
            'password' => bcrypt('password'),
            'peran' => 'Ketua',
        ]);

        $this->anggota = Anggota::create([
            'id_anggota' => 'A-001',
            'nama' => 'I Made Surya',
            'alamat' => 'Desa Buruan',
            'no_telepon' => '08123456789',
            'status' => 'Aktif',
            'created_by' => $this->adminUser->username,
        ]);
    }

    public function test_admin_can_store_savings_via_controller(): void
    {
        $response = $this->actingAs($this->adminUser)->post(route('pengurus.simpanan.store'), [
            'id_anggota' => 'A-001',
            'jenis_simpanan' => 'Pokok',
            'nominal' => 500000,
        ]);

        $response->assertRedirect(route('pengurus.simpanan.index'));
        $this->assertDatabaseHas('tb_simpanan', ['id_anggota' => 'A-001', 'nominal' => 500000]);
    }

    public function test_ketua_can_approve_loan_application_via_controller(): void
    {
        // Admin buat pengajuan
        $this->actingAs($this->adminUser)->post(route('pengurus.pinjaman.store'), [
            'id_anggota' => 'A-001',
            'jumlah_pinjam' => 1000000,
            'tenor' => 10,
        ]);

        $pinjaman = Pinjaman::first();

        // Ketua Approve
        $response = $this->actingAs($this->ketuaUser)->post(route('ketua.approval.approve', $pinjaman->id_pinjam));

        $response->assertSessionHasNoErrors();
        $this->assertEquals('Berjalan', $pinjaman->fresh()->status);
    }
}