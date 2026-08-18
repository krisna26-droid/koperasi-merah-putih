<?php

namespace App\Services;

use App\Models\Anggota;
use App\Models\Simpanan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class SavingsService
{
    protected AccountingService $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    /**
     * Memproses setoran simpanan dan otomatis memicu pencatatan jurnal
     */
    public function processDeposit(string $idAnggota, string $jenisSimpanan, float $nominal): Simpanan
    {
        return DB::transaction(function () use ($idAnggota, $jenisSimpanan, $nominal) {
            // 1. Validasi Anggota
            $anggota = Anggota::findOrFail($idAnggota);
            if ($anggota->status !== 'Aktif') {
                throw new Exception("Transaksi ditolak: Anggota [{$idAnggota}] tidak berstatus Aktif!");
            }

            if ($nominal <= 0) {
                throw new Exception("Nominal setoran harus lebih besar dari 0!");
            }

            // 2. Generate ID Simpanan Unik (SMP-YYYYMMDD-XXXX)
            $datePrefix = date('Ymd');
            $lastRecord = Simpanan::where('id_simpanan', 'LIKE', "SMP-{$datePrefix}-%")
                ->orderBy('id_simpanan', 'desc')
                ->first();

            $nextSequence = $lastRecord ? ((int) substr($lastRecord->id_simpanan, -4)) + 1 : 1;
            $idSimpanan = sprintf("SMP-%s-%04d", $datePrefix, $nextSequence);

            // 3. Simpan Record Simpanan
            $simpanan = Simpanan::create([
                'id_simpanan' => $idSimpanan,
                'id_anggota' => $idAnggota,
                'jenis_simpanan' => $jenisSimpanan,
                'nominal' => $nominal,
                'is_jurnaled' => false,
                'created_by' => Auth::check() ? Auth::user()->username : 'system',
            ]);

            // 4. Pos Akun Kredit (Simpanan Pokok: 301 / Wajib: 302 / Sukarela: 303 atau 201/202/203 sesuai CoA Anda)
            $kodeAkunKredit = match ($jenisSimpanan) {
                'Pokok' => '301',
                'Wajib' => '302',
                'Sukarela' => '303',
                default => throw new Exception("Jenis simpanan tidak valid!"),
            };

            // 5. Eksekusi Jurnal: Kas (101) Debit, Simpanan Kredit
            $journalDetails = [
                ['kode_akun' => '101', 'posisi' => 'Debit', 'nominal' => $nominal],
                ['kode_akun' => $kodeAkunKredit, 'posisi' => 'Kredit', 'nominal' => $nominal],
            ];

            $this->accountingService->createJournalEntry(
                now()->format('Y-m-d'),
                $simpanan->id_simpanan,
                'Simpanan',
                "Setoran Simpanan {$jenisSimpanan} a.n {$anggota->nama} ({$anggota->id_anggota})",
                $journalDetails
            );

            $simpanan->update(['is_jurnaled' => true]);

            return $simpanan;
        });
    }
}