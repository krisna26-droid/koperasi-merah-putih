<?php

namespace Database\Seeders;

use App\Models\Anggota;
use App\Models\Angsuran;
use App\Models\Coa;
use App\Models\JurnalDetail;
use App\Models\JurnalUmum;
use App\Models\Pinjaman;
use App\Models\Simpanan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Pengguna (Admin Pengurus & Ketua Koperasi)
        User::updateOrCreate(
            ['username' => 'pengurus'],
            [
                'name' => 'I Made Surya (Pengurus)',
                'password' => Hash::make('password'),
                'peran' => 'Admin',
            ]
        );

        User::updateOrCreate(
            ['username' => 'ketua'],
            [
                'name' => 'I Wayan Sudarta (Ketua)',
                'password' => Hash::make('password'),
                'peran' => 'Ketua',
            ]
        );

        // 2. Bagan Akun / Chart of Accounts (CoA) Baku
        $coas = [
            ['kode_akun' => '101', 'nama_akun' => 'Kas Koperasi', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '102', 'nama_akun' => 'Kas Bank BPD Bali', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '103', 'nama_akun' => 'Piutang Pinjaman Anggota', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '201', 'nama_akun' => 'Hutang Operasional', 'saldo_normal' => 'Kredit'],
            ['kode_akun' => '301', 'nama_akun' => 'Simpanan Pokok', 'saldo_normal' => 'Kredit'],
            ['kode_akun' => '302', 'nama_akun' => 'Simpanan Wajib', 'saldo_normal' => 'Kredit'],
            ['kode_akun' => '303', 'nama_akun' => 'Simpanan Sukarela', 'saldo_normal' => 'Kredit'],
            ['kode_akun' => '401', 'nama_akun' => 'Pendapatan Bunga Pinjaman', 'saldo_normal' => 'Kredit'],
            ['kode_akun' => '501', 'nama_akun' => 'Beban Operasional & ATK', 'saldo_normal' => 'Debit'],
        ];

        foreach ($coas as $coa) {
            Coa::updateOrCreate(['kode_akun' => $coa['kode_akun']], $coa);
        }

        // 3. Data Anggota
        $anggotaList = [
            ['id_anggota' => 'A-001', 'nama' => 'I Wayan Darmada', 'nik' => '5104030101850001', 'alamat' => 'Br. Bangunliman, Desa Buruan', 'no_telepon' => '081234567891', 'status' => 'Aktif'],
            ['id_anggota' => 'A-002', 'nama' => 'Ni Made Sukerni', 'nik' => '5104034502880002', 'alamat' => 'Br. Buruan Kaja, Desa Buruan', 'no_telepon' => '081234567892', 'status' => 'Aktif'],
            ['id_anggota' => 'A-003', 'nama' => 'I Nyoman Suardana', 'nik' => '5104031205900003', 'alamat' => 'Br. Celuk, Desa Buruan', 'no_telepon' => '081234567893', 'status' => 'Aktif'],
            ['id_anggota' => 'A-004', 'nama' => 'I Ketut Gede Wiguna', 'nik' => '5104032008920004', 'alamat' => 'Br. Bangunliman, Desa Buruan', 'no_telepon' => '081234567894', 'status' => 'Aktif'],
            ['id_anggota' => 'A-005', 'nama' => 'Ni Luh Putu Astuti', 'nik' => '5104036011950005', 'alamat' => 'Br. Kutri, Desa Buruan', 'no_telepon' => '081234567895', 'status' => 'Cuti'],
        ];

        foreach ($anggotaList as $ang) {
            Anggota::updateOrCreate(['id_anggota' => $ang['id_anggota']], $ang);
        }

        // 4. Bersihkan data transaksi lama
        DB::table('tb_jurnal_detail')->delete();
        DB::table('tb_jurnal_umum')->delete();
        DB::table('tb_angsuran')->delete();
        DB::table('tb_pinjaman')->delete();
        DB::table('tb_simpanan')->delete();

        // 5. Transaksi Simpanan & Auto-Jurnal
        $simpananData = [
            ['id' => 'SMP-20260701-0001', 'anggota' => 'A-001', 'jenis' => 'Pokok', 'nominal' => 100000.00, 'tgl' => '2026-07-01', 'akun_kredit' => '301'],
            ['id' => 'SMP-20260701-0002', 'anggota' => 'A-001', 'jenis' => 'Wajib', 'nominal' => 50000.00, 'tgl' => '2026-07-01', 'akun_kredit' => '302'],
            ['id' => 'SMP-20260702-0001', 'anggota' => 'A-002', 'jenis' => 'Pokok', 'nominal' => 100000.00, 'tgl' => '2026-07-02', 'akun_kredit' => '301'],
            ['id' => 'SMP-20260702-0002', 'anggota' => 'A-002', 'jenis' => 'Wajib', 'nominal' => 50000.00, 'tgl' => '2026-07-02', 'akun_kredit' => '302'],
            ['id' => 'SMP-20260705-0001', 'anggota' => 'A-003', 'jenis' => 'Sukarela', 'nominal' => 500000.00, 'tgl' => '2026-07-05', 'akun_kredit' => '303'],
            ['id' => 'SMP-20260710-0001', 'anggota' => 'A-004', 'jenis' => 'Pokok', 'nominal' => 100000.00, 'tgl' => '2026-07-10', 'akun_kredit' => '301'],
        ];

        $jrnIndex = 1;
        foreach ($simpananData as $s) {
            Simpanan::create([
                'id_simpanan' => $s['id'],
                'id_anggota' => $s['anggota'],
                'jenis_simpanan' => $s['jenis'],
                'nominal' => $s['nominal'],
                'is_jurnaled' => true,
                'created_by' => 'pengurus',
                'created_at' => Carbon::parse($s['tgl']),
            ]);

            $idJrn = sprintf("JRN-%s-%04d", str_replace('-', '', $s['tgl']), $jrnIndex++);
            JurnalUmum::create([
                'id_jurnal' => $idJrn,
                'tanggal' => $s['tgl'],
                'id_referensi' => $s['id'],
                'tipe_transaksi' => 'Simpanan',
                'keterangan' => "Setoran Simpanan {$s['jenis']} a.n {$s['anggota']}",
                'is_reversal' => false,
                'created_by' => 'pengurus',
            ]);

            JurnalDetail::create(['id_jurnal' => $idJrn, 'kode_akun' => '101', 'posisi' => 'Debit', 'nominal' => $s['nominal']]);
            JurnalDetail::create(['id_jurnal' => $idJrn, 'kode_akun' => $s['akun_kredit'], 'posisi' => 'Kredit', 'nominal' => $s['nominal']]);
        }

        // 6. Transaksi Pinjaman & Angsuran
        $idPinjam1 = 'PNJ-20260710-0001';
        Pinjaman::create([
            'id_pinjam' => $idPinjam1,
            'id_anggota' => 'A-001',
            'jumlah_pinjam' => 5000000.00,
            'tenor' => 10,
            'bunga_rate' => 1.50,
            'pokok_per_bulan' => 500000.00,
            'bunga_per_bulan' => 75000.00,
            'sisa_tagihan' => 4500000.00,
            'status' => 'Berjalan',
            'is_jurnaled' => true,
            'created_by' => 'pengurus',
            'approved_by' => 'ketua',
            'approved_at' => Carbon::parse('2026-07-10 10:00:00'),
            'created_at' => Carbon::parse('2026-07-10 09:00:00'),
        ]);

        $idJrnPinjam1 = 'JRN-20260710-0005';
        JurnalUmum::create([
            'id_jurnal' => $idJrnPinjam1,
            'tanggal' => '2026-07-10',
            'id_referensi' => $idPinjam1,
            'tipe_transaksi' => 'Pinjaman',
            'keterangan' => "Pencairan Pinjaman {$idPinjam1} a.n I Wayan Darmada",
            'is_reversal' => false,
            'created_by' => 'ketua',
        ]);
        JurnalDetail::create(['id_jurnal' => $idJrnPinjam1, 'kode_akun' => '103', 'posisi' => 'Debit', 'nominal' => 5000000.00]);
        JurnalDetail::create(['id_jurnal' => $idJrnPinjam1, 'kode_akun' => '101', 'posisi' => 'Kredit', 'nominal' => 5000000.00]);

        $idAngsuran1 = 'ANG-20260810-0001';
        Angsuran::create([
            'id_angsuran' => $idAngsuran1,
            'id_pinjam' => $idPinjam1,
            'id_anggota' => 'A-001',
            'angsuran_ke' => 1,
            'bayar_pokok' => 500000.00,
            'bayar_bunga' => 75000.00,
            'total_bayar' => 575000.00,
            'is_jurnaled' => true,
            'created_by' => 'pengurus',
            'created_at' => Carbon::parse('2026-08-10 14:00:00'),
        ]);

        $idJrnAngsuran1 = 'JRN-20260810-0001';
        JurnalUmum::create([
            'id_jurnal' => $idJrnAngsuran1,
            'tanggal' => '2026-08-10',
            'id_referensi' => $idAngsuran1,
            'tipe_transaksi' => 'Angsuran',
            'keterangan' => "Angsuran ke-1 Pinjaman {$idPinjam1} a.n I Wayan Darmada",
            'is_reversal' => false,
            'created_by' => 'pengurus',
        ]);
        JurnalDetail::create(['id_jurnal' => $idJrnAngsuran1, 'kode_akun' => '101', 'posisi' => 'Debit', 'nominal' => 575000.00]);
        JurnalDetail::create(['id_jurnal' => $idJrnAngsuran1, 'kode_akun' => '103', 'posisi' => 'Kredit', 'nominal' => 500000.00]);
        JurnalDetail::create(['id_jurnal' => $idJrnAngsuran1, 'kode_akun' => '401', 'posisi' => 'Kredit', 'nominal' => 75000.00]);

        Pinjaman::create([
            'id_pinjam' => 'PNJ-20260815-0001',
            'id_anggota' => 'A-002',
            'jumlah_pinjam' => 3000000.00,
            'tenor' => 6,
            'bunga_rate' => 1.50,
            'pokok_per_bulan' => 500000.00,
            'bunga_per_bulan' => 45000.00,
            'sisa_tagihan' => 3000000.00,
            'status' => 'Pengajuan',
            'is_jurnaled' => false,
            'created_by' => 'pengurus',
            'created_at' => Carbon::parse('2026-08-15 11:30:00'),
        ]);
    }
}