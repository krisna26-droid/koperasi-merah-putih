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
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. SEED USERS (2 Akun Utama)
        $admin = User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Pengurus Operasional',
                'email' => 'admin@koperasimerahputih.id',
                'password' => Hash::make('password123'),
                'peran' => 'Admin',
            ]
        );

        $ketua = User::updateOrCreate(
            ['username' => 'ketua'],
            [
                'name' => 'Ketua Koperasi',
                'email' => 'ketua@koperasimerahputih.id',
                'password' => Hash::make('password123'),
                'peran' => 'Ketua',
            ]
        );

        // 2. SEED CHART OF ACCOUNTS (11 Akun Standar)
        $accounts = [
            ['kode_akun' => '101', 'nama_akun' => 'Kas', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '102', 'nama_akun' => 'Bank', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '103', 'nama_akun' => 'Piutang Pinjaman', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '104', 'nama_akun' => 'Aktiva Tetap', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '201', 'nama_akun' => 'Simpanan Pokok', 'saldo_normal' => 'Kredit'],
            ['kode_akun' => '202', 'nama_akun' => 'Simpanan Wajib', 'saldo_normal' => 'Kredit'],
            ['kode_akun' => '203', 'nama_akun' => 'Simpanan Sukarela', 'saldo_normal' => 'Kredit'],
            ['kode_akun' => '301', 'nama_akun' => 'Modal Sendiri', 'saldo_normal' => 'Kredit'],
            ['kode_akun' => '401', 'nama_akun' => 'Pendapatan Bunga Pinjaman', 'saldo_normal' => 'Kredit'],
            ['kode_akun' => '402', 'nama_akun' => 'Pendapatan Provisi', 'saldo_normal' => 'Kredit'],
            ['kode_akun' => '501', 'nama_akun' => 'Beban Operasional Kantor', 'saldo_normal' => 'Debit'],
        ];

        foreach ($accounts as $acc) {
            Coa::updateOrCreate(['kode_akun' => $acc['kode_akun']], $acc);
        }

        // 3. SEED ANGGOTA (10 Data Anggota)
        $anggotaList = [
            ['id_anggota' => 'A-001', 'nama' => 'I Made Surya', 'alamat' => 'Desa Buruan No. 12', 'no_telepon' => '081234567801', 'status' => 'Aktif'],
            ['id_anggota' => 'A-002', 'nama' => 'Ni Wayan Sukma', 'alamat' => 'Desa Buruan No. 15', 'no_telepon' => '081234567802', 'status' => 'Aktif'],
            ['id_anggota' => 'A-003', 'nama' => 'I Nyoman Budi', 'alamat' => 'Desa Buruan No. 20', 'no_telepon' => '081234567803', 'status' => 'Aktif'],
            ['id_anggota' => 'A-004', 'nama' => 'I Ketut Agus', 'alamat' => 'Desa Buruan No. 22', 'no_telepon' => '081234567804', 'status' => 'Aktif'],
            ['id_anggota' => 'A-005', 'nama' => 'Ni Luh Putu', 'alamat' => 'Desa Buruan No. 30', 'no_telepon' => '081234567805', 'status' => 'Aktif'],
            ['id_anggota' => 'A-006', 'nama' => 'I Gede Rai', 'alamat' => 'Desa Buruan No. 33', 'no_telepon' => '081234567806', 'status' => 'Aktif'],
            ['id_anggota' => 'A-007', 'nama' => 'Ni Made Kadek', 'alamat' => 'Desa Buruan No. 41', 'no_telepon' => '081234567807', 'status' => 'Aktif'],
            ['id_anggota' => 'A-008', 'nama' => 'I Komang Darta', 'alamat' => 'Desa Buruan No. 45', 'no_telepon' => '081234567808', 'status' => 'Aktif'],
            ['id_anggota' => 'A-009', 'nama' => 'I Ketut Suweta', 'alamat' => 'Desa Buruan No. 50', 'no_telepon' => '081234567809', 'status' => 'Aktif'],
            ['id_anggota' => 'A-010', 'nama' => 'Ni Wayan Sari', 'alamat' => 'Desa Buruan No. 55', 'no_telepon' => '081234567810', 'status' => 'Cuti'],
        ];

        foreach ($anggotaList as $data) {
            Anggota::updateOrCreate(
                ['id_anggota' => $data['id_anggota']],
                array_merge($data, ['created_by' => $admin->username])
            );
        }

        // 4. SEED SIMPANAN (10 Transaksi + Auto Jurnal)
        $simpananData = [
            ['id_simpanan' => 'SMP-20260811-0001', 'id_anggota' => 'A-001', 'jenis' => 'Pokok', 'nominal' => 500000.00, 'akun' => '201'],
            ['id_simpanan' => 'SMP-20260811-0002', 'id_anggota' => 'A-002', 'jenis' => 'Pokok', 'nominal' => 500000.00, 'akun' => '201'],
            ['id_simpanan' => 'SMP-20260811-0003', 'id_anggota' => 'A-003', 'jenis' => 'Pokok', 'nominal' => 500000.00, 'akun' => '201'],
            ['id_simpanan' => 'SMP-20260811-0004', 'id_anggota' => 'A-004', 'jenis' => 'Pokok', 'nominal' => 500000.00, 'akun' => '201'],
            ['id_simpanan' => 'SMP-20260811-0005', 'id_anggota' => 'A-005', 'jenis' => 'Pokok', 'nominal' => 500000.00, 'akun' => '201'],
            ['id_simpanan' => 'SMP-20260811-0006', 'id_anggota' => 'A-001', 'jenis' => 'Wajib', 'nominal' => 100000.00, 'akun' => '202'],
            ['id_simpanan' => 'SMP-20260811-0007', 'id_anggota' => 'A-002', 'jenis' => 'Wajib', 'nominal' => 100000.00, 'akun' => '202'],
            ['id_simpanan' => 'SMP-20260811-0008', 'id_anggota' => 'A-003', 'jenis' => 'Wajib', 'nominal' => 100000.00, 'akun' => '202'],
            ['id_simpanan' => 'SMP-20260811-0009', 'id_anggota' => 'A-001', 'jenis' => 'Sukarela', 'nominal' => 250000.00, 'akun' => '203'],
            ['id_simpanan' => 'SMP-20260811-0010', 'id_anggota' => 'A-002', 'jenis' => 'Sukarela', 'nominal' => 300000.00, 'akun' => '203'],
        ];

        foreach ($simpananData as $index => $item) {
            Simpanan::updateOrCreate(
                ['id_simpanan' => $item['id_simpanan']],
                [
                    'id_anggota' => $item['id_anggota'],
                    'jenis_simpanan' => $item['jenis'],
                    'nominal' => $item['nominal'],
                    'is_jurnaled' => true,
                    'created_by' => $admin->username,
                ]
            );

            $idJurnal = sprintf("JRN-20260811-%04d", $index + 1);
            JurnalUmum::updateOrCreate(
                ['id_jurnal' => $idJurnal],
                [
                    'tanggal' => now()->format('Y-m-d'),
                    'id_referensi' => $item['id_simpanan'],
                    'tipe_transaksi' => 'Simpanan',
                    'keterangan' => "Setoran Simpanan {$item['jenis']} a.n {$item['id_anggota']}",
                    'is_reversal' => false,
                    'created_by' => $admin->username,
                ]
            );

            JurnalDetail::create([
                'id_jurnal' => $idJurnal,
                'kode_akun' => '101',
                'posisi' => 'Debit',
                'nominal' => $item['nominal'],
            ]);

            JurnalDetail::create([
                'id_jurnal' => $idJurnal,
                'kode_akun' => $item['akun'],
                'posisi' => 'Kredit',
                'nominal' => $item['nominal'],
            ]);
        }

        // 5. SEED PINJAMAN (10 Data Pinjaman)
        for ($i = 1; $i <= 10; $i++) {
            $idPinjam = sprintf("PNJ-20260811-%04d", $i);
            $idAnggota = sprintf("A-%03d", $i);
            $jumlahPinjam = $i * 1000000.00;
            $tenor = 10;
            $pokokBulan = $jumlahPinjam / $tenor;
            $bungaBulan = $jumlahPinjam * 0.015;

            $status = $i <= 5 ? 'Berjalan' : ($i <= 8 ? 'Pengajuan' : 'Lunas');

            Pinjaman::updateOrCreate(
                ['id_pinjam' => $idPinjam],
                [
                    'id_anggota' => $idAnggota,
                    'jumlah_pinjam' => $jumlahPinjam,
                    'tenor' => $tenor,
                    'bunga_rate' => 1.50,
                    'pokok_per_bulan' => $pokokBulan,
                    'bunga_per_bulan' => $bungaBulan,
                    'sisa_tagihan' => $status === 'Lunas' ? 0.00 : $jumlahPinjam - $pokokBulan,
                    'status' => $status,
                    'is_jurnaled' => $status !== 'Pengajuan',
                    'created_by' => $admin->username,
                    'approved_by' => $status !== 'Pengajuan' ? $ketua->username : null,
                    'approved_at' => $status !== 'Pengajuan' ? now() : null,
                ]
            );

            if ($status !== 'Pengajuan') {
                $idJurnalPencairan = sprintf("JRN-20260811-%04d", $i + 10);
                JurnalUmum::updateOrCreate(
                    ['id_jurnal' => $idJurnalPencairan],
                    [
                        'tanggal' => now()->format('Y-m-d'),
                        'id_referensi' => $idPinjam,
                        'tipe_transaksi' => 'Pinjaman',
                        'keterangan' => "Pencairan Pinjaman a.n {$idAnggota}",
                        'is_reversal' => false,
                        'created_by' => $admin->username,
                    ]
                );

                JurnalDetail::create(['id_jurnal' => $idJurnalPencairan, 'kode_akun' => '103', 'posisi' => 'Debit', 'nominal' => $jumlahPinjam]);
                JurnalDetail::create(['id_jurnal' => $idJurnalPencairan, 'kode_akun' => '101', 'posisi' => 'Kredit', 'nominal' => $jumlahPinjam]);
            }
        }

        // 6. SEED ANGSURAN (10 Transaksi + Jurnal Split 3-Baris)
        for ($i = 1; $i <= 10; $i++) {
            $idAngsuran = sprintf("ANG-20260811-%04d", $i);
            $idPinjam = sprintf("PNJ-20260811-%04d", ($i % 5) + 1);
            $idAnggota = sprintf("A-%03d", ($i % 5) + 1);

            $bayarPokok = 100000.00 * $i;
            $bayarBunga = $bayarPokok * 0.015;
            $totalBayar = $bayarPokok + $bayarBunga;

            Angsuran::updateOrCreate(
                ['id_angsuran' => $idAngsuran],
                [
                    'id_pinjam' => $idPinjam,
                    'id_anggota' => $idAnggota,
                    'angsuran_ke' => 1,
                    'bayar_pokok' => $bayarPokok,
                    'bayar_bunga' => $bayarBunga,
                    'total_bayar' => $totalBayar,
                    'is_jurnaled' => true,
                    'created_by' => $admin->username,
                ]
            );

            $idJurnalAngsuran = sprintf("JRN-20260811-%04d", $i + 20);
            JurnalUmum::updateOrCreate(
                ['id_jurnal' => $idJurnalAngsuran],
                [
                    'tanggal' => now()->format('Y-m-d'),
                    'id_referensi' => $idAngsuran,
                    'tipe_transaksi' => 'Angsuran',
                    'keterangan' => "Angsuran ke-1 Pinjaman {$idPinjam} a.n {$idAnggota}",
                    'is_reversal' => false,
                    'created_by' => $admin->username,
                ]
            );

            JurnalDetail::create(['id_jurnal' => $idJurnalAngsuran, 'kode_akun' => '101', 'posisi' => 'Debit', 'nominal' => $totalBayar]);
            JurnalDetail::create(['id_jurnal' => $idJurnalAngsuran, 'kode_akun' => '103', 'posisi' => 'Kredit', 'nominal' => $bayarPokok]);
            JurnalDetail::create(['id_jurnal' => $idJurnalAngsuran, 'kode_akun' => '401', 'posisi' => 'Kredit', 'nominal' => $bayarBunga]);
        }
    }
}