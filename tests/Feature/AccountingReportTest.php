<?php

namespace Tests\Feature;

use App\Models\Anggota;
use App\Models\Coa;
use App\Models\User;
use App\Services\SavingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountingReportTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $ketuaUser;

    protected function setUp(): void
    {
        parent::setUp();

        Coa::create(['kode_akun' => '101', 'nama_akun' => 'Kas', 'saldo_normal' => 'Debit']);
        Coa::create(['kode_akun' => '201', 'nama_akun' => 'Simpanan Pokok', 'saldo_normal' => 'Kredit']);

        $this->adminUser = User::create([
            'username' => 'admin_rpt',
            'name' => 'Admin Rpt',
            'email' => 'admin_rpt@test.com',
            'password' => bcrypt('password'),
            'peran' => 'Admin',
        ]);

        $this->ketuaUser = User::create([
            'username' => 'ketua_rpt',
            'name' => 'Ketua Rpt',
            'email' => 'ketua_rpt@test.com',
            'password' => bcrypt('password'),
            'peran' => 'Ketua',
        ]);

        Anggota::create([
            'id_anggota' => 'A-001',
            'nama' => 'I Made Surya',
            'alamat' => 'Desa Buruan',
            'no_telepon' => '08123456789',
            'status' => 'Aktif',
            'created_by' => $this->adminUser->username,
        ]);
    }

    public function test_journal_and_ledger_pages_can_be_rendered(): void
    {
        $this->actingAs($this->adminUser);

        // Buat 1 setoran
        app(SavingsService::class)->processDeposit('A-001', 'Pokok', 500000.00);

        // Akses Jurnal
        $this->get(route('pengurus.jurnal.index'))->assertStatus(200);

        // Akses Buku Besar
        $this->get(route('pengurus.buku-besar.index', ['kode_akun' => '101']))->assertStatus(200);
    }

    public function test_ketua_can_render_reports(): void
    {
        $this->actingAs($this->ketuaUser);

        $this->get(route('ketua.laporan.jurnal'))->assertStatus(200);
        $this->get(route('ketua.laporan.buku-besar', ['kode_akun' => '101']))->assertStatus(200);
    }
}