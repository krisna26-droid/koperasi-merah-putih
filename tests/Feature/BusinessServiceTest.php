<?php

namespace Tests\Feature;

use App\Models\Anggota;
use App\Models\Coa;
use App\Models\JurnalUmum;
use App\Models\Pinjaman;
use App\Models\User;
use App\Services\AccountingService;
use App\Services\LoanService;
use App\Services\SavingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Exception;

class BusinessServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AccountingService $accountingService;
    protected SavingsService $savingsService;
    protected LoanService $loanService;
    protected User $adminUser;
    protected User $ketuaUser;
    protected Anggota $anggota;

    protected function setUp(): void
    {
        parent::setUp();

        $this->accountingService = app(AccountingService::class);
        $this->savingsService = app(SavingsService::class);
        $this->loanService = app(LoanService::class);

        // Seed Chart of Accounts
        Coa::create(['kode_akun' => '101', 'nama_akun' => 'Kas', 'saldo_normal' => 'Debit']);
        Coa::create(['kode_akun' => '103', 'nama_akun' => 'Piutang Pinjaman', 'saldo_normal' => 'Debit']);
        Coa::create(['kode_akun' => '201', 'nama_akun' => 'Simpanan Pokok', 'saldo_normal' => 'Kredit']);
        Coa::create(['kode_akun' => '401', 'nama_akun' => 'Pendapatan Bunga', 'saldo_normal' => 'Kredit']);

        // Create Users
        $this->adminUser = User::create([
            'username' => 'admin_srv',
            'name' => 'Admin Srv',
            'email' => 'admin_srv@test.com',
            'password' => bcrypt('password'),
            'peran' => 'Admin',
        ]);

        $this->ketuaUser = User::create([
            'username' => 'ketua_srv',
            'name' => 'Ketua Srv',
            'email' => 'ketua_srv@test.com',
            'password' => bcrypt('password'),
            'peran' => 'Ketua',
        ]);

        // Create Active Member
        $this->anggota = Anggota::create([
            'id_anggota' => 'A-001',
            'nama' => 'I Made Surya',
            'alamat' => 'Desa Buruan',
            'no_telepon' => '08123456789',
            'status' => 'Aktif',
            'created_by' => $this->adminUser->username,
        ]);
    }

    public function test_deposit_savings_creates_record_and_balanced_journal(): void
    {
        $this->actingAs($this->adminUser);

        $simpanan = $this->savingsService->processDeposit('A-001', 'Pokok', 500000.00);

        $this->assertDatabaseHas('tb_simpanan', [
            'id_simpanan' => $simpanan->id_simpanan,
            'nominal' => 500000.00,
            'is_jurnaled' => true,
        ]);

        $jurnal = JurnalUmum::where('id_referensi', $simpanan->id_simpanan)->first();
        $this->assertNotNull($jurnal);
        $this->assertCount(2, $jurnal->details);
    }

    public function test_flat_1_point_5_percent_loan_calculation_and_single_active_loan_rule(): void
    {
        $this->actingAs($this->adminUser);

        // Pokok = 1.000.000, Tenor = 10 Bulan
        // Bunga/Bln = 1.000.000 * 1.5% = 15.000
        // Pokok/Bln = 1.000.000 / 10 = 100.000
        $pinjaman = $this->loanService->createApplication('A-001', 1000000.00, 10);

        $this->assertEquals(100000.00, $pinjaman->pokok_per_bulan);
        $this->assertEquals(15000.00, $pinjaman->bunga_per_bulan);
        $this->assertEquals('Pengajuan', $pinjaman->status);

        // Approve oleh Ketua Koperasi
        $this->actingAs($this->ketuaUser);
        $this->loanService->processApproval($pinjaman->id_pinjam, 'Approve');

        $this->assertEquals('Berjalan', $pinjaman->fresh()->status);

        // UJI RULE MULTI-LOAN (BR-002): Pengajuan ke-2 wajib DITOLAK
        $this->actingAs($this->adminUser);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Pengajuan ditolak: Anggota [A-001] masih memiliki pinjaman yang berstatus 'Berjalan'!");

        $this->loanService->createApplication('A-001', 2000000.00, 12);
    }

    public function test_installment_payment_creates_3_rows_split_journal(): void
    {
        $this->actingAs($this->adminUser);

        $pinjaman = $this->loanService->createApplication('A-001', 1000000.00, 10);

        $this->actingAs($this->ketuaUser);
        $this->loanService->processApproval($pinjaman->id_pinjam, 'Approve');

        // Bayar Angsuran Ke-1
        $this->actingAs($this->adminUser);
        $angsuran = $this->loanService->processInstallmentPayment($pinjaman->id_pinjam);

        $this->assertEquals(100000.00, $angsuran->bayar_pokok);
        $this->assertEquals(15000.00, $angsuran->bayar_bunga);
        $this->assertEquals(115000.00, $angsuran->total_bayar);

        // Verify Split 3-Baris Journal
        $jurnal = JurnalUmum::where('id_referensi', $angsuran->id_angsuran)->first();
        $this->assertCount(3, $jurnal->details);

        // Verify Balance Check: Kas Debit (115.000) == Piutang Kredit (100.000) + Bunga Kredit (15.000)
        $this->assertEquals('101', $jurnal->details[0]->kode_akun);
        $this->assertEquals('Debit', $jurnal->details[0]->posisi);
        $this->assertEquals(115000.00, $jurnal->details[0]->nominal);

        $this->assertEquals('103', $jurnal->details[1]->kode_akun);
        $this->assertEquals('Kredit', $jurnal->details[1]->posisi);
        $this->assertEquals(100000.00, $jurnal->details[1]->nominal);

        $this->assertEquals('401', $jurnal->details[2]->kode_akun);
        $this0 = $this->assertEquals('Kredit', $jurnal->details[2]->posisi);
        $this->assertEquals(15000.00, $jurnal->details[2]->nominal);
    }
}