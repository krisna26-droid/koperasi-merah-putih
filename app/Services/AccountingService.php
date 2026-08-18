<?php

namespace App\Services;

use App\Models\JurnalUmum;
use App\Models\JurnalDetail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class AccountingService
{
    /**
     * Membuat Header dan Detail Jurnal Umum
     */
    public function createJournalEntry(string $tanggal, ?string $referensi, string $tipeTransaksi, string $keterangan, array $details): JurnalUmum
    {
        return DB::transaction(function () use ($tanggal, $referensi, $tipeTransaksi, $keterangan, $details) {
            // Validasi Keseimbangan (Debet = Kredit)
            $totalDebit = 0;
            $totalKredit = 0;

            foreach ($details as $row) {
                if ($row['posisi'] === 'Debit') {
                    $totalDebit += (float) $row['nominal'];
                } elseif ($row['posisi'] === 'Kredit') {
                    $totalKredit += (float) $row['nominal'];
                }
            }

            if (round($totalDebit, 2) !== round($totalKredit, 2)) {
                throw new Exception("Jurnal tidak seimbang: Total Debit (Rp " . number_format($totalDebit) . ") != Total Kredit (Rp " . number_format($totalKredit) . ").");
            }

            // Generate ID Jurnal: JRN-YYYYMMDD-XXXX
            $dateFormatted = Carbon::parse($tanggal)->format('Ymd');
            $countToday = JurnalUmum::whereDate('tanggal', Carbon::parse($tanggal))->count() + 1;
            $idJurnal = 'JRN-' . $dateFormatted . '-' . str_pad((string) $countToday, 4, '0', STR_PAD_LEFT);

            // 1. Simpan Header Jurnal
            $jurnal = JurnalUmum::create([
                'id_jurnal' => $idJurnal,
                'tanggal' => $tanggal,
                'referensi_transaksi' => $referensi,
                'tipe_transaksi' => $tipeTransaksi,
                'keterangan' => $keterangan,
                'is_reversal' => false,
            ]);

            // 2. Simpan Detail Jurnal
            foreach ($details as $item) {
                JurnalDetail::create([
                    'id_jurnal' => $idJurnal,
                    'kode_akun' => $item['kode_akun'],
                    'posisi' => $item['posisi'],
                    'nominal' => $item['nominal'],
                ]);
            }

            return $jurnal;
        });
    }

    /**
     * Membalik jurnal (Reversal) secara otomatis dengan menukar Debit <-> Kredit
     */
    public function reverseJournal(string $idJurnal, string $alasan = 'Koreksi Transaksi'): JurnalUmum
    {
        return DB::transaction(function () use ($idJurnal, $alasan) {
            $originalJournal = JurnalUmum::with('details')->findOrFail($idJurnal);

            if ($originalJournal->is_reversal) {
                throw new Exception("Jurnal ini sudah pernah di-reversal sebelumnya.");
            }

            // Tandai jurnal lama sebagai reversed
            $originalJournal->update(['is_reversal' => true]);

            // Siapkan detail pembalik (Tukar Debit jadi Kredit, Kredit jadi Debit)
            $reversedDetails = [];
            foreach ($originalJournal->details as $item) {
                $reversedDetails[] = [
                    'kode_akun' => $item->kode_akun,
                    'posisi' => $item->posisi === 'Debit' ? 'Kredit' : 'Debit',
                    'nominal' => $item->nominal,
                ];
            }

            // Buat entri jurnal pembalik baru
            return $this->createJournalEntry(
                Carbon::now()->format('Y-m-d'),
                $originalJournal->id_jurnal,
                'Reversal',
                "Reversal {$originalJournal->id_jurnal}: {$alasan} ({$originalJournal->keterangan})",
                $reversedDetails
            );
        });
    }
    public function getTrialBalance(?string $endDate = null): array
    {
        $endDate = $endDate ?? Carbon::now()->toDateString();
        $coaList = \App\Models\Coa::orderBy('kode_akun', 'asc')->get();

        $rows = [];
        $totalDebit = 0.00;
        $totalKredit = 0.00;

        foreach ($coaList as $coa) {
            $mutasi = \App\Models\JurnalDetail::where('kode_akun', $coa->kode_akun)
                ->whereHas('header', function ($q) use ($endDate) {
                    $q->whereDate('tanggal', '<=', $endDate);
                })
                ->get();

            $sumDebit = (float) $mutasi->where('posisi', 'Debit')->sum('nominal');
            $sumKredit = (float) $mutasi->where('posisi', 'Kredit')->sum('nominal');

            $saldoAkhir = 0.00;
            $posisiDebit = 0.00;
            $posisiKredit = 0.00;

            if ($coa->saldo_normal === 'Debit') {
                $saldoAkhir = $sumDebit - $sumKredit;
                if ($saldoAkhir >= 0) {
                    $posisiDebit = $saldoAkhir;
                } else {
                    $posisiKredit = abs($saldoAkhir);
                }
            } else {
                $saldoAkhir = $sumKredit - $sumDebit;
                if ($saldoAkhir >= 0) {
                    $posisiKredit = $saldoAkhir;
                } else {
                    $posisiDebit = abs($saldoAkhir);
                }
            }

            $totalDebit += $posisiDebit;
            $totalKredit += $posisiKredit;

            $rows[] = [
                'kode_akun' => $coa->kode_akun,
                'nama_akun' => $coa->nama_akun,
                'tipe_akun' => $coa->tipe_akun ?? '-',
                'saldo_normal' => $coa->saldo_normal,
                'debit' => $posisiDebit,
                'kredit' => $posisiKredit,
            ];
        }

        return [
            'rows' => $rows,
            'total_debit' => $totalDebit,
            'total_kredit' => $totalKredit,
            'is_balanced' => round($totalDebit, 2) === round($totalKredit, 2),
            'end_date' => $endDate
        ];
    }

    /**
     * Menghitung Laporan Laba Rugi / Sisa Hasil Usaha (SHU)
     */
    public function getIncomeStatement(string $startDate, string $endDate): array
    {
        // 1. Pendapatan (Akun diawali '4', e.g. 401 Pendapatan Bunga Pinjaman)
        $pendapatanDetails = \App\Models\JurnalDetail::with('coa')
            ->where('kode_akun', 'like', '4%')
            ->whereHas('header', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('tanggal', [$startDate, $endDate]);
            })
            ->get();

        $pendapatanGrouped = [];
        $totalPendapatan = 0.00;

        foreach ($pendapatanDetails as $d) {
            $nom = $d->posisi === 'Kredit' ? (float) $d->nominal : -(float) $d->nominal;
            if (!isset($pendapatanGrouped[$d->kode_akun])) {
                $pendapatanGrouped[$d->kode_akun] = [
                    'kode_akun' => $d->kode_akun,
                    'nama_akun' => $d->coa->nama_akun ?? 'Pendapatan Lain',
                    'nominal' => 0.00
                ];
            }
            $pendapatanGrouped[$d->kode_akun]['nominal'] += $nom;
            $totalPendapatan += $nom;
        }

        // 2. Beban Operasional (Akun diawali '5', e.g. 501 Beban Operasional)
        $bebanDetails = \App\Models\JurnalDetail::with('coa')
            ->where('kode_akun', 'like', '5%')
            ->whereHas('header', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('tanggal', [$startDate, $endDate]);
            })
            ->get();

        $bebanGrouped = [];
        $totalBeban = 0.00;

        foreach ($bebanDetails as $d) {
            $nom = $d->posisi === 'Debit' ? (float) $d->nominal : -(float) $d->nominal;
            if (!isset($bebanGrouped[$d->kode_akun])) {
                $bebanGrouped[$d->kode_akun] = [
                    'kode_akun' => $d->kode_akun,
                    'nama_akun' => $d->coa->nama_akun ?? 'Beban Operasional',
                    'nominal' => 0.00
                ];
            }
            $bebanGrouped[$d->kode_akun]['nominal'] += $nom;
            $totalBeban += $nom;
        }

        $shuBersih = $totalPendapatan - $totalBeban;

        return [
            'pendapatan' => array_values($pendapatanGrouped),
            'total_pendapatan' => $totalPendapatan,
            'beban' => array_values($bebanGrouped),
            'total_beban' => $totalBeban,
            'shu_bersih' => $shuBersih,
            'start_date' => $startDate,
            'end_date' => $endDate
        ];
    }
}