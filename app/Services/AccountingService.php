<?php

namespace App\Services;

use App\Models\Coa;
use App\Models\JurnalDetail;
use App\Models\JurnalUmum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class AccountingService
{
    /**
     * Memposting entri jurnal otomatis dengan validasi keseimbangan Debit = Kredit.
     */
    public function createJournalEntry(
        string $tanggal,
        ?string $idReferensi,
        string $tipeTransaksi,
        string $keterangan,
        array $details,
        bool $isReversal = false,
        ?string $reversedRef = null
    ): JurnalUmum {
        return DB::transaction(function () use ($tanggal, $idReferensi, $tipeTransaksi, $keterangan, $details, $isReversal, $reversedRef) {
            // 1. Validasi Keseimbangan Total Debit == Total Kredit
            $totalDebit = 0.00;
            $totalKredit = 0.00;

            foreach ($details as $detail) {
                if ($detail['posisi'] === 'Debit') {
                    $totalDebit += (float) $detail['nominal'];
                } elseif ($detail['posisi'] === 'Kredit') {
                    $totalKredit += (float) $detail['nominal'];
                }
            }

            // Pembulatan 2 desimal untuk menghindari floating point error
            if (round($totalDebit, 2) !== round($totalKredit, 2)) {
                throw new Exception("Transaksi Jurnal Tidak Balance! Total Debit (Rp " . number_format($totalDebit, 2) . ") != Total Kredit (Rp " . number_format($totalKredit, 2) . ")");
            }

            // 2. Generate ID Jurnal Unik (Format: JRN-YYYYMMDD-XXXX)
            $datePrefix = date('Ymd', strtotime($tanggal));
            $lastJournal = JurnalUmum::where('id_jurnal', 'LIKE', "JRN-{$datePrefix}-%")
                ->orderBy('id_jurnal', 'desc')
                ->first();

            $nextSequence = $lastJournal ? ((int) substr($lastJournal->id_jurnal, -4)) + 1 : 1;
            $idJurnal = sprintf("JRN-%s-%04d", $datePrefix, $nextSequence);

            // 3. Simpan Header Jurnal
            $header = JurnalUmum::create([
                'id_jurnal' => $idJurnal,
                'tanggal' => $tanggal,
                'id_referensi' => $idReferensi,
                'tipe_transaksi' => $tipeTransaksi,
                'keterangan' => $keterangan,
                'is_reversal' => $isReversal,
                'reversed_ref' => $reversedRef,
                'created_by' => Auth::check() ? Auth::user()->username : 'system',
            ]);

            // 4. Simpan Line Details Jurnal
            foreach ($details as $detail) {
                // Pastikan akun terdaftar di CoA
                $coaExists = Coa::where('kode_akun', $detail['kode_akun'])->exists();
                if (!$coaExists) {
                    throw new Exception("Kode Akun [{$detail['kode_akun']}] tidak ditemukan pada Chart of Accounts!");
                }

                JurnalDetail::create([
                    'id_jurnal' => $header->id_jurnal,
                    'kode_akun' => $detail['kode_akun'],
                    'posisi' => $detail['posisi'],
                    'nominal' => $detail['nominal'],
                ]);
            }

            return $header;
        });
    }

    /**
     * Eksekusi Reversal Engine: Membalikkan posisi Debit/Kredit jurnal lama.
     */
    public function reverseJournal(string $idJurnalLama, string $alasanReversal): JurnalUmum
    {
        return DB::transaction(function () use ($idJurnalLama, $alasanReversal) {
            $jurnalLama = JurnalUmum::with('details')->findOrFail($idJurnalLama);

            if ($jurnalLama->is_reversal) {
                throw new Exception("Jurnal [{$idJurnalLama}] sudah pernah direversal!");
            }

            // Susun detail kebalikan (180 derajat)
            $reversedDetails = [];
            foreach ($jurnalLama->details as $detail) {
                $reversedDetails[] = [
                    'kode_akun' => $detail->kode_akun,
                    'posisi' => $detail->posisi === 'Debit' ? 'Kredit' : 'Debit',
                    'nominal' => $detail->nominal,
                ];
            }

            // Tandai jurnal lama
            $jurnalLama->update(['is_reversal' => true]);

            // Terbitkan jurnal pembalik baru
            return $this->createJournalEntry(
                now()->format('Y-m-d'),
                $jurnalLama->id_referensi,
                'Reversal',
                "REVERSAL: " . $alasanReversal . " (Ref: {$jurnalLama->id_jurnal})",
                $reversedDetails,
                true,
                $jurnalLama->id_jurnal
            );
        });
    }
}