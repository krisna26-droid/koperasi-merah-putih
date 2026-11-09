<?php

namespace Database\Seeders;

use App\Models\Anggota;
use App\Models\Angsuran;
use App\Models\JurnalDetail;
use App\Models\JurnalUmum;
use App\Models\Pinjaman;
use App\Models\Simpanan;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CoaSeeder::class,
        ]);

        $anggota = $this->seedAnggota();

        $this->seedSimpanan($anggota);
        $this->seedPinjamanDanAngsuran($anggota);
    }

    /** @return array<int, string> */
    private function seedAnggota(): array
    {
        $anggotaIds = [];

        // 50 anggota Aktif dan 10 anggota Cuti untuk mencakup setiap status.
        for ($number = 1; $number <= 60; $number++) {
            $idAnggota = sprintf('AG-%05d', $number);
            $status = $number <= 50 ? 'Aktif' : 'Cuti';

            $record = Anggota::updateOrCreate(
                ['id_anggota' => $idAnggota],
                [
                    'nama' => sprintf('Anggota Demo %02d', $number),
                    'nik' => sprintf('5104%012d', $number),
                    'jenis_kelamin' => $number % 2 === 0 ? 'Perempuan' : 'Laki-laki',
                    'pekerjaan' => $number % 2 === 0 ? 'Pedagang' : 'Petani',
                    'alamat' => sprintf('Banjar Demo %02d, Desa Buruan', (($number - 1) % 10) + 1),
                    'no_telepon' => sprintf('08%010d', $number),
                    'email' => sprintf('anggota.demo.%02d@example.test', $number),
                    'status' => $status,
                    'created_by' => 'pengurus',
                ]
            );
            $this->setCreatedAt($record, Carbon::create(2026, 9, 1)->addDays($number - 1));

            $anggotaIds[$number] = $idAnggota;
        }

        return $anggotaIds;
    }

    /** @param array<int, string> $anggota */
    private function seedSimpanan(array $anggota): void
    {
        $akunSimpanan = [
            'Pokok' => '301',
            'Wajib' => '302',
            'Sukarela' => '303',
        ];
        $sequence = 0;

        // Sepuluh transaksi berjurnal untuk masing-masing jenis simpanan.
        foreach ($akunSimpanan as $jenis => $kodeAkun) {
            for ($number = 1; $number <= 10; $number++) {
                $sequence++;
                $idSimpanan = sprintf('SMP-20261001-%04d', $sequence);
                $idAnggota = $anggota[$sequence];
                $nominal = 100_000 + ($number * 25_000);

                $record = Simpanan::updateOrCreate(
                    ['id_simpanan' => $idSimpanan],
                    [
                        'id_anggota' => $idAnggota,
                        'jenis_simpanan' => $jenis,
                        'nominal' => $nominal,
                        'is_jurnaled' => true,
                        'created_by' => 'pengurus',
                    ]
                );
                $tanggalSimpanan = Carbon::create(2026, 10, 1)->addDays($sequence - 1);
                $this->setCreatedAt($record, $tanggalSimpanan);

                $idJurnal = sprintf('JRN-20261001-%04d', $sequence);
                $this->createJournal(
                    $idJurnal,
                    $idSimpanan,
                    'Simpanan',
                    "Setoran simpanan {$jenis} anggota {$idAnggota}",
                    'pengurus',
                    $tanggalSimpanan,
                    [
                        ['kode_akun' => '101', 'posisi' => 'Debit', 'nominal' => $nominal],
                        ['kode_akun' => $kodeAkun, 'posisi' => 'Kredit', 'nominal' => $nominal],
                    ]
                );
            }
        }

        // Sepuluh simpanan belum diposting untuk mengisi kondisi pending.
        for ($number = 1; $number <= 10; $number++) {
            $idSimpanan = sprintf('SMP-20261001-%04d', 30 + $number);
            $jenis = array_keys($akunSimpanan)[($number - 1) % count($akunSimpanan)];

            $record = Simpanan::updateOrCreate(
                ['id_simpanan' => $idSimpanan],
                [
                    'id_anggota' => $anggota[30 + $number],
                    'jenis_simpanan' => $jenis,
                    'nominal' => 150_000 + ($number * 10_000),
                    'is_jurnaled' => false,
                    'created_by' => 'pengurus',
                ]
            );
            $this->setCreatedAt($record, Carbon::create(2026, 10, 15)->addDays($number - 1));
        }
    }

    /** @param array<int, string> $anggota */
    private function seedPinjamanDanAngsuran(array $anggota): void
    {
        $statusPinjaman = ['Pengajuan', 'Disetujui', 'Ditolak', 'Berjalan', 'Lunas'];
        $nomorAngsuran = 0;
        $nomorJurnalPinjaman = 0;
        $nomorJurnalAngsuran = 0;

        // Sepuluh pinjaman pada setiap status yang tersedia di enum tb_pinjaman.
        foreach ($statusPinjaman as $indexStatus => $status) {
            for ($number = 1; $number <= 10; $number++) {
                $numberPinjaman = ($indexStatus * 10) + $number;
                $idPinjaman = sprintf('PNJ-20261002-%04d', $numberPinjaman);
                $idAnggota = $anggota[$numberPinjaman];
                $jumlahPinjam = 6_000_000 + ($numberPinjaman * 600_000);
                $tenor = 6;
                $pokokPerBulan = $jumlahPinjam / $tenor;
                $bungaPerBulan = round($jumlahPinjam * 0.015, 2);
                $jumlahAngsuran = match ($status) {
                    'Berjalan' => 2,
                    'Lunas' => $tenor,
                    default => 0,
                };
                $isJurnaled = in_array($status, ['Berjalan', 'Lunas'], true);
                $sisaTagihan = max(0, $jumlahPinjam - ($pokokPerBulan * $jumlahAngsuran));
                $createdAt = Carbon::create(2026, 10, 2)->addDays(($numberPinjaman - 1) % 30);
                $approved = in_array($status, ['Disetujui', 'Ditolak', 'Berjalan', 'Lunas'], true);

                $pinjaman = Pinjaman::updateOrCreate(
                    ['id_pinjam' => $idPinjaman],
                    [
                        'id_anggota' => $idAnggota,
                        'jumlah_pinjam' => $jumlahPinjam,
                        'tenor' => $tenor,
                        'bunga_rate' => 1.50,
                        'pokok_per_bulan' => $pokokPerBulan,
                        'bunga_per_bulan' => $bungaPerBulan,
                        'sisa_tagihan' => $sisaTagihan,
                        'status' => $status,
                        'is_jurnaled' => $isJurnaled,
                        'created_by' => 'pengurus',
                        'approved_by' => $approved ? 'ketua' : null,
                        'approved_at' => $approved ? $createdAt->copy()->addHours(2) : null,
                    ]
                );
                $this->setCreatedAt($pinjaman, $createdAt);

                if (!$isJurnaled) {
                    continue;
                }

                $nomorJurnalPinjaman++;
                $this->createJournal(
                    sprintf('JRN-20261002-%04d', $nomorJurnalPinjaman),
                    $idPinjaman,
                    'Pinjaman',
                    "Pencairan pinjaman {$idPinjaman} anggota {$idAnggota}",
                    'ketua',
                    $createdAt->copy()->addHours(2),
                    [
                        ['kode_akun' => '103', 'posisi' => 'Debit', 'nominal' => $jumlahPinjam],
                        ['kode_akun' => '101', 'posisi' => 'Kredit', 'nominal' => $jumlahPinjam],
                    ]
                );

                for ($angsuranKe = 1; $angsuranKe <= $jumlahAngsuran; $angsuranKe++) {
                    $nomorAngsuran++;
                    $nomorJurnalAngsuran++;
                    $idAngsuran = sprintf('ANG-20261003-%04d', $nomorAngsuran);
                    $tanggalAngsuran = Carbon::create(2026, 10, 4)
                        ->addDays(($numberPinjaman + $angsuranKe) % 30);
                    $totalBayar = $pokokPerBulan + $bungaPerBulan;

                    $angsuran = Angsuran::updateOrCreate(
                        ['id_angsuran' => $idAngsuran],
                        [
                            'id_pinjam' => $idPinjaman,
                            'id_anggota' => $idAnggota,
                            'angsuran_ke' => $angsuranKe,
                            'bayar_pokok' => $pokokPerBulan,
                            'bayar_bunga' => $bungaPerBulan,
                            'total_bayar' => $totalBayar,
                            'is_jurnaled' => true,
                            'created_by' => 'pengurus',
                        ]
                    );
                    $this->setCreatedAt($angsuran, $tanggalAngsuran);

                    $this->createJournal(
                        sprintf('JRN-20261003-%04d', $nomorJurnalAngsuran),
                        $idAngsuran,
                        'Angsuran',
                        "Angsuran ke-{$angsuranKe} pinjaman {$idPinjaman}",
                        'pengurus',
                        $tanggalAngsuran,
                        [
                            ['kode_akun' => '101', 'posisi' => 'Debit', 'nominal' => $totalBayar],
                            ['kode_akun' => '103', 'posisi' => 'Kredit', 'nominal' => $pokokPerBulan],
                            ['kode_akun' => '401', 'posisi' => 'Kredit', 'nominal' => $bungaPerBulan],
                        ]
                    );
                }
            }
        }
    }

    /** @param array<int, array{kode_akun: string, posisi: string, nominal: int|float}> $details */
    private function createJournal(
        string $idJurnal,
        string $referensi,
        string $tipe,
        string $keterangan,
        string $createdBy,
        Carbon $tanggal,
        array $details
    ): void {
        $jurnal = JurnalUmum::updateOrCreate(
            ['id_jurnal' => $idJurnal],
            [
                'tanggal' => $tanggal->toDateString(),
                'id_referensi' => $referensi,
                'tipe_transaksi' => $tipe,
                'keterangan' => $keterangan,
                'is_reversal' => false,
                'reversed_ref' => null,
                'created_by' => $createdBy,
            ]
        );
        $this->setCreatedAt($jurnal, $tanggal);

        foreach ($details as $detail) {
            JurnalDetail::updateOrCreate(
                [
                    'id_jurnal' => $idJurnal,
                    'kode_akun' => $detail['kode_akun'],
                    'posisi' => $detail['posisi'],
                ],
                ['nominal' => $detail['nominal']]
            );
        }
    }

    private function setCreatedAt(Model $model, Carbon $timestamp): void
    {
        $model->forceFill([
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ])->saveQuietly();
    }
}
