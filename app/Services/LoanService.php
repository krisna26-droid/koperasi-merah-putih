<?php

namespace App\Services;

use App\Models\Anggota;
use App\Models\Angsuran;
use App\Models\Pinjaman;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class LoanService
{
    protected AccountingService $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    /**
     * Memproses Pengajuan Pinjaman Baru (Dengan Single Active Loan Pre-Check).
     */
    public function createApplication(string $idAnggota, float $jumlahPinjam, int $tenor): Pinjaman
    {
        // 1. Validasi Keaktifan Anggota
        $anggota = Anggota::findOrFail($idAnggota);
        if ($anggota->status !== 'Aktif') {
            throw new Exception("Pengajuan ditolak: Anggota [{$idAnggota}] berstatus tidak aktif!");
        }

        // 2. ATURAN SINGLE ACTIVE LOAN (BR-002): Cek Pinjaman Aktif
        $hasActiveLoan = Pinjaman::where('id_anggota', $idAnggota)
            ->where('status', 'Berjalan')
            ->exists();

        if ($hasActiveLoan) {
            throw new Exception("Pengajuan ditolak: Anggota [{$idAnggota}] masih memiliki pinjaman yang berstatus 'Berjalan'!");
        }

        if ($jumlahPinjam <= 0 || $tenor <= 0) {
            throw new Exception("Jumlah pinjaman dan tenor harus lebih besar dari 0!");
        }

        // 3. KALKULASI FLAT 1.5% PER BULAN (BR-001)
        $rateBunga = 1.50; // Flat 1.5%
        $pokokPerBulan = round($jumlahPinjam / $tenor, 2);
        $bungaPerBulan = round($jumlahPinjam * ($rateBunga / 100), 2);

        // 4. Generate ID Pinjam Unik (PNJ-YYYYMMDD-XXXX)
        $datePrefix = date('Ymd');
        $lastRecord = Pinjaman::where('id_pinjam', 'LIKE', "PNJ-{$datePrefix}-%")
            ->orderBy('id_pinjam', 'desc')
            ->first();

        $nextSequence = $lastRecord ? ((int) substr($lastRecord->id_pinjam, -4)) + 1 : 1;
        $idPinjam = sprintf("PNJ-%s-%04d", $datePrefix, $nextSequence);

        // 5. Simpan Record Pengajuan
        return Pinjaman::create([
            'id_pinjam' => $idPinjam,
            'id_anggota' => $idAnggota,
            'jumlah_pinjam' => $jumlahPinjam,
            'tenor' => $tenor,
            'bunga_rate' => $rateBunga,
            'pokok_per_bulan' => $pokokPerBulan,
            'bunga_per_bulan' => $bungaPerBulan,
            'sisa_tagihan' => $jumlahPinjam,
            'status' => 'Pengajuan',
            'is_jurnaled' => false,
            'created_by' => Auth::check() ? Auth::user()->username : 'system',
        ]);
    }

    /**
     * Persetujuan / Penolakan Pinjaman oleh Ketua Koperasi.
     */
    public function processApproval(string $idPinjam, string $action): Pinjaman
    {
        return DB::transaction(function () use ($idPinjam, $action) {
            $pinjaman = Pinjaman::findOrFail($idPinjam);

            if ($pinjaman->status !== 'Pengajuan') {
                throw new Exception("Pinjaman [{$idPinjam}] tidak berstatus 'Pengajuan'!");
            }

            if ($action === 'Reject') {
                $pinjaman->update([
                    'status' => 'Ditolak',
                    'approved_by' => Auth::check() ? Auth::user()->username : 'ketua',
                    'approved_at' => now(),
                ]);
                return $pinjaman;
            }

            if ($action === 'Approve') {
                // Ubah status ke Disetujui & langsung eksekusi Pencairan (Disbursal)
                $pinjaman->update([
                    'status' => 'Berjalan',
                    'approved_by' => Auth::check() ? Auth::user()->username : 'ketua',
                    'approved_at' => now(),
                ]);

                // Auto-Journal Pencairan Pinjaman (Debit Piutang 103, Kredit Kas 101)
                $journalDetails = [
                    ['kode_akun' => '103', 'posisi' => 'Debit', 'nominal' => $pinjaman->jumlah_pinjam],
                    ['kode_akun' => '101', 'posisi' => 'Kredit', 'nominal' => $pinjaman->jumlah_pinjam],
                ];

                $this->accountingService->createJournalEntry(
                    now()->format('Y-m-d'),
                    $pinjaman->id_pinjam,
                    'Pinjaman',
                    "Pencairan Pinjaman a.n {$pinjaman->anggota->nama} ({$pinjaman->id_pinjam})",
                    $journalDetails
                );

                $pinjaman->update(['is_jurnaled' => true]);

                return $pinjaman;
            }

            throw new Exception("Aksi approval tidak dikenal!");
        });
    }

    /**
     * Pembayaran Angsuran Kredit dengan Penjurnalan Split 3-Baris.
     */
    public function processInstallmentPayment(string $idPinjam): Angsuran
    {
        return DB::transaction(function () use ($idPinjam) {
            $pinjaman = Pinjaman::with('anggota')->findOrFail($idPinjam);

            if ($pinjaman->status !== 'Berjalan') {
                throw new Exception("Transaksi Angsuran ditolak: Pinjaman [{$idPinjam}] tidak berstatus 'Berjalan'!");
            }

            $bayarPokok = $pinjaman->pokok_per_bulan;
            $bayarBunga = $pinjaman->bunga_per_bulan;
            $totalBayar = round($bayarPokok + $bayarBunga, 2);

            // Hitung urutan angsuran ke-N
            $lastAngsuranCount = Angsuran::where('id_pinjam', $idPinjam)->count();
            $angsuranKe = $lastAngsuranCount + 1;

            // Generate ID Angsuran Unik (ANG-YYYYMMDD-XXXX)
            $datePrefix = date('Ymd');
            $lastRecord = Angsuran::where('id_angsuran', 'LIKE', "ANG-{$datePrefix}-%")
                ->orderBy('id_angsuran', 'desc')
                ->first();

            $nextSequence = $lastRecord ? ((int) substr($lastRecord->id_angsuran, -4)) + 1 : 1;
            $idAngsuran = sprintf("ANG-%s-%04d", $datePrefix, $nextSequence);

            // 1. Simpan Transaksi Angsuran
            $angsuran = Angsuran::create([
                'id_angsuran' => $idAngsuran,
                'id_pinjam' => $idPinjam,
                'id_anggota' => $pinjaman->id_anggota,
                'angsuran_ke' => $angsuranKe,
                'bayar_pokok' => $bayarPokok,
                'bayar_bunga' => $bayarBunga,
                'total_bayar' => $totalBayar,
                'is_jurnaled' => false,
                'created_by' => Auth::check() ? Auth::user()->username : 'system',
            ]);

            // 2. Potong Sisa Tagihan Pokok
            $sisaTagihanBaru = max(0.00, round($pinjaman->sisa_tagihan - $bayarPokok, 2));
            $pinjaman->sisa_tagihan = $sisaTagihanBaru;

            // Auto-Pelunasan (BR-008)
            if ($sisaTagihanBaru == 0.00) {
                $pinjaman->status = 'Lunas';
            }
            $pinjaman->save();

            // 3. JURNAL SPLIT 3-BARIS (BR-004)
            $journalDetails = [
                ['kode_akun' => '101', 'posisi' => 'Debit',  'nominal' => $totalBayar],
                ['kode_akun' => '103', 'posisi' => 'Kredit', 'nominal' => $bayarPokok],
                ['kode_akun' => '401', 'posisi' => 'Kredit', 'nominal' => $bayarBunga],
            ];

            $this->accountingService->createJournalEntry(
                now()->format('Y-m-d'),
                $angsuran->id_angsuran,
                'Angsuran',
                "Angsuran ke-{$angsuranKe} Pinjaman {$idPinjam} a.n {$pinjaman->anggota->nama}",
                $journalDetails
            );

            $angsuran->update(['is_jurnaled' => true]);

            return $angsuran;
        });
    }
}