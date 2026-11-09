<?php

namespace App\Services;

use App\Models\Anggota;
use App\Models\Angsuran;
use App\Models\Coa;
use App\Models\JurnalDetail;
use App\Models\Pinjaman;
use App\Models\Simpanan;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ReportExportService
{
    public function __construct(private readonly AccountingService $accountingService)
    {
    }

    /** @return array{title: string, headers: list<string>, rows: list<list<mixed>>} */
    public function make(string $report, Request $request): array
    {
        return match ($report) {
            'anggota' => $this->anggota($request),
            'simpanan' => $this->simpanan($request),
            'pinjaman' => $this->pinjaman($request),
            'angsuran' => $this->angsuran($request),
            'jurnal' => $this->jurnal($request),
            'buku-besar' => $this->bukuBesar($request),
            'neraca-saldo' => $this->neracaSaldo($request),
            'laba-rugi' => $this->labaRugi($request),
            default => abort(404, 'Jenis laporan tidak tersedia untuk ekspor.'),
        };
    }

    /** @return array{title: string, headers: list<string>, rows: list<list<mixed>>} */
    private function anggota(Request $request): array
    {
        $query = Anggota::query()->when($request->filled('status'), fn (Builder $q) => $q->where('status', $request->input('status')));

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(fn (Builder $q) => $q
                ->where('id_anggota', 'like', "%{$search}%")
                ->orWhere('nama', 'like', "%{$search}%")
                ->orWhere('alamat', 'like', "%{$search}%")
                ->orWhere('no_telepon', 'like', "%{$search}%"));
        }

        $rows = $query->with('creator')->orderBy('id_anggota')->get()->map(fn (Anggota $anggota) => [
            $anggota->id_anggota,
            $anggota->nama,
            $anggota->nik,
            $anggota->jenis_kelamin,
            $anggota->pekerjaan,
            $anggota->alamat,
            $anggota->no_telepon,
            $anggota->email,
            $anggota->status,
            $anggota->created_at?->format('Y-m-d'),
            $anggota->creator?->name ?? $anggota->created_by,
        ])->all();

        return [
            'title' => 'Data Anggota',
            'headers' => ['ID Anggota', 'Nama', 'NIK', 'Jenis Kelamin', 'Pekerjaan', 'Alamat', 'Telepon', 'Email', 'Status', 'Tanggal Bergabung', 'Ditambahkan oleh'],
            'rows' => $rows,
        ];
    }

    /** @return array{title: string, headers: list<string>, rows: list<list<mixed>>} */
    private function simpanan(Request $request): array
    {
        $query = Simpanan::with(['anggota', 'creator']);
        $this->applyDateRange($query, $request, 'created_at');
        $jenis = $request->input('jenis_simpanan', $request->input('jenis'));
        if ($jenis) {
            $query->where('jenis_simpanan', $jenis);
        }
        $this->applySearch($query, $request, ['id_simpanan', 'id_anggota'], 'anggota', ['nama']);

        return [
            'title' => $this->periodTitle('Laporan Simpanan', $request),
            'headers' => ['ID Simpanan', 'Tanggal', 'ID Anggota', 'Nama Anggota', 'Jenis Simpanan', 'Nominal (Rp)', 'Status Jurnal', 'Dicatat oleh'],
            'rows' => $query->latest('created_at')->get()->map(fn (Simpanan $item) => [
                $item->id_simpanan,
                $item->created_at?->format('Y-m-d'),
                $item->id_anggota,
                $item->anggota?->nama,
                $item->jenis_simpanan,
                (float) $item->nominal,
                $item->is_jurnaled ? 'Terjurnal' : 'Belum dijurnal',
                $item->creator?->name ?? $item->created_by,
            ])->all(),
        ];
    }

    /** @return array{title: string, headers: list<string>, rows: list<list<mixed>>} */
    private function pinjaman(Request $request): array
    {
        $query = Pinjaman::with(['anggota', 'creator', 'approver']);
        $this->applyDateRange($query, $request, 'created_at');
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        $this->applySearch($query, $request, ['id_pinjam', 'id_anggota'], 'anggota', ['nama']);

        return [
            'title' => $this->periodTitle('Laporan Pinjaman', $request),
            'headers' => ['ID Pinjaman', 'Tanggal Pengajuan', 'ID Anggota', 'Nama Anggota', 'Jumlah (Rp)', 'Tenor (Bulan)', 'Bunga (%)', 'Pokok/Bulan (Rp)', 'Bunga/Bulan (Rp)', 'Sisa Tagihan (Rp)', 'Status', 'Pengaju', 'Penyetuju', 'Tanggal Keputusan'],
            'rows' => $query->latest('created_at')->get()->map(fn (Pinjaman $item) => [
                $item->id_pinjam,
                $item->created_at?->format('Y-m-d'),
                $item->id_anggota,
                $item->anggota?->nama,
                (float) $item->jumlah_pinjam,
                (int) $item->tenor,
                (float) $item->bunga_rate,
                (float) $item->pokok_per_bulan,
                (float) $item->bunga_per_bulan,
                (float) $item->sisa_tagihan,
                $item->status,
                $item->creator?->name,
                $item->approver?->name,
                $item->approved_at?->format('Y-m-d H:i'),
            ])->all(),
        ];
    }

    /** @return array{title: string, headers: list<string>, rows: list<list<mixed>>} */
    private function angsuran(Request $request): array
    {
        $query = Angsuran::with(['pinjaman', 'anggota', 'creator']);
        $this->applyDateRange($query, $request, 'created_at');
        $this->applySearch($query, $request, ['id_angsuran', 'id_pinjam', 'id_anggota'], 'anggota', ['nama']);

        return [
            'title' => $this->periodTitle('Laporan Angsuran', $request),
            'headers' => ['ID Angsuran', 'Tanggal Bayar', 'ID Pinjaman', 'ID Anggota', 'Nama Anggota', 'Angsuran Ke', 'Pokok (Rp)', 'Bunga (Rp)', 'Total Bayar (Rp)', 'Status Jurnal', 'Dicatat oleh'],
            'rows' => $query->latest('created_at')->get()->map(fn (Angsuran $item) => [
                $item->id_angsuran,
                $item->created_at?->format('Y-m-d'),
                $item->id_pinjam,
                $item->id_anggota,
                $item->anggota?->nama,
                (int) $item->angsuran_ke,
                (float) $item->bayar_pokok,
                (float) $item->bayar_bunga,
                (float) $item->total_bayar,
                $item->is_jurnaled ? 'Terjurnal' : 'Belum dijurnal',
                $item->creator?->name ?? $item->created_by,
            ])->all(),
        ];
    }

    /** @return array{title: string, headers: list<string>, rows: list<list<mixed>>} */
    private function jurnal(Request $request): array
    {
        $query = JurnalDetail::query();
        if ($request->filled('kode_akun')) {
            $query->where('kode_akun', $request->input('kode_akun'));
        }
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereHas('header', fn ($q) => $q->whereBetween('tanggal', [$request->input('start_date'), $request->input('end_date')]));
        }
        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(fn ($q) => $q
                ->where('id_jurnal', 'like', "%{$search}%")
                ->orWhereHas('header', fn ($header) => $header->where('keterangan', 'like', "%{$search}%"))
                ->orWhereHas('coa', fn ($coa) => $coa->where('nama_akun', 'like', "%{$search}%")));
        }

        $journalIds = $query->pluck('id_jurnal')->unique()->values();
        $journals = \App\Models\JurnalUmum::with(['details.coa', 'creator'])
            ->whereIn('id_jurnal', $journalIds)
            ->get()
            ->sortBy(fn ($journal) => ($journal->tanggal?->format('Y-m-d') ?? '') . $journal->id_jurnal);

        $rows = [];
        foreach ($journals as $journal) {
            $details = $journal->details->sortBy(fn (JurnalDetail $item) => $item->posisi === 'Debit' ? 0 : 1);
            $totalDebit = (float) $details->where('posisi', 'Debit')->sum('nominal');
            $totalKredit = (float) $details->where('posisi', 'Kredit')->sum('nominal');
            $balanced = round($totalDebit, 2) === round($totalKredit, 2);

            foreach ($details as $item) {
                $rows[] = [
                    $journal->id_jurnal,
                    $journal->tanggal?->format('Y-m-d'),
                    $journal->id_referensi,
                    $journal->tipe_transaksi,
                    $journal->keterangan,
                    $item->kode_akun,
                    ($item->posisi === 'Kredit' ? '    ' : '') . ($item->coa?->nama_akun ?? $item->kode_akun),
                    $item->posisi,
                    $item->posisi === 'Debit' ? (float) $item->nominal : '',
                    $item->posisi === 'Kredit' ? (float) $item->nominal : '',
                    '',
                    '',
                    '',
                    $journal->creator?->name ?? $journal->created_by,
                    $journal->is_reversal ? 'Ya' : 'Tidak',
                ];
            }

            $rows[] = [
                $journal->id_jurnal,
                $journal->tanggal?->format('Y-m-d'),
                $journal->id_referensi,
                $journal->tipe_transaksi,
                'TOTAL JURNAL',
                '',
                '',
                '',
                $totalDebit,
                $totalKredit,
                $totalDebit,
                $totalKredit,
                $balanced ? 'SEIMBANG' : 'TIDAK SEIMBANG',
                $journal->creator?->name ?? $journal->created_by,
                $journal->is_reversal ? 'Ya' : 'Tidak',
            ];
        }

        return [
            'title' => $this->periodTitle('Jurnal Umum', $request),
            'headers' => ['ID Jurnal', 'Tanggal', 'Referensi', 'Tipe Transaksi', 'Keterangan', 'Kode Akun', 'Nama Akun', 'Posisi', 'Debit (Rp)', 'Kredit (Rp)', 'Total Debit (Rp)', 'Total Kredit (Rp)', 'Balance', 'Dicatat oleh', 'Reversal'],
            'rows' => $rows,
        ];
    }

    /** @return array{title: string, headers: list<string>, rows: list<list<mixed>>} */
    private function bukuBesar(Request $request): array
    {
        $account = Coa::findOrFail($request->input('kode_akun', Coa::orderBy('kode_akun')->value('kode_akun')));
        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);
        abort_unless(checkdate($month, 1, $year), 422, 'Bulan atau tahun buku besar tidak valid.');
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $previous = JurnalDetail::with('header.creator')->where('kode_akun', $account->kode_akun)
            ->whereHas('header', fn ($q) => $q->where('tanggal', '<', $startDate->toDateString()))->get();
        $opening = $previous->sum(fn (JurnalDetail $item) => $this->signedAmount($item, $account->saldo_normal));
        $running = (float) $opening;
        $rows = [[$startDate->toDateString(), '', 'Saldo awal', '', '', (float) $running, '']];
        $totalDebit = 0.0;
        $totalCredit = 0.0;

        $entries = JurnalDetail::with('header.creator')->where('kode_akun', $account->kode_akun)
            ->whereHas('header', fn ($q) => $q->whereBetween('tanggal', [$startDate->toDateString(), $endDate->toDateString()]))
            ->get()->sortBy(fn (JurnalDetail $item) => ($item->header?->tanggal?->format('Y-m-d') ?? '') . $item->id_jurnal);

        foreach ($entries as $item) {
            $debit = $item->posisi === 'Debit' ? (float) $item->nominal : 0.0;
            $credit = $item->posisi === 'Kredit' ? (float) $item->nominal : 0.0;
            $totalDebit += $debit;
            $totalCredit += $credit;
            $running += $this->signedAmount($item, $account->saldo_normal);
            $rows[] = [
                $item->header?->tanggal?->format('Y-m-d'),
                $item->header?->id_referensi ?? $item->id_jurnal,
                $item->header?->keterangan,
                $debit,
                $credit,
                $running,
                $item->header?->creator?->name ?? $item->header?->created_by,
            ];
        }
        $rows[] = [$endDate->toDateString(), '', 'TOTAL MUTASI', $totalDebit, $totalCredit, (float) $running, ''];
        $rows[] = [$endDate->toDateString(), '', 'Saldo akhir', '', '', (float) $running, ''];

        return [
            'title' => 'Buku Besar ' . $account->kode_akun . ' ' . $account->nama_akun . ' - ' . $startDate->translatedFormat('F Y'),
            'headers' => ['Tanggal', 'Referensi', 'Keterangan', 'Debit (Rp)', 'Kredit (Rp)', 'Saldo (Rp)', 'Dicatat oleh'],
            'rows' => $rows,
        ];
    }

    /** @return array{title: string, headers: list<string>, rows: list<list<mixed>>} */
    private function neracaSaldo(Request $request): array
    {
        $endDate = $request->input('end_date', now()->toDateString());
        $data = $this->accountingService->getTrialBalance($endDate);
        $rows = array_map(fn (array $row) => [
            $row['kode_akun'], $row['nama_akun'], $row['saldo_normal'], (float) $row['debit'], (float) $row['kredit'], '',
        ], $data['rows']);
        $rows[] = ['', 'TOTAL', '', (float) $data['total_debit'], (float) $data['total_kredit'], $data['is_balanced'] ? 'SEIMBANG' : 'TIDAK SEIMBANG'];

        return ['title' => 'Neraca Saldo sampai ' . $endDate, 'headers' => ['Kode Akun', 'Nama Akun', 'Saldo Normal', 'Debit (Rp)', 'Kredit (Rp)', 'Balance'], 'rows' => $rows];
    }

    /** @return array{title: string, headers: list<string>, rows: list<list<mixed>>} */
    private function labaRugi(Request $request): array
    {
        $startDate = $request->input('start_date', now()->startOfYear()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        $data = $this->accountingService->getIncomeStatement($startDate, $endDate);
        $rows = [];

        foreach ($data['pendapatan'] as $item) {
            $rows[] = ['Pendapatan', $item['kode_akun'], '    ' . $item['nama_akun'], (float) $item['nominal']];
        }
        $rows[] = ['Total Pendapatan', '', '', (float) $data['total_pendapatan']];
        foreach ($data['beban'] as $item) {
            $rows[] = ['Beban', $item['kode_akun'], '    ' . $item['nama_akun'], (float) $item['nominal']];
        }
        $rows[] = ['Total Beban', '', '', (float) $data['total_beban']];
        $rows[] = ['SHU Bersih', '', '', (float) $data['shu_bersih']];

        return [
            'title' => "Laporan Laba Rugi {$startDate} s.d. {$endDate}",
            'headers' => ['Kelompok', 'Kode Akun', 'Nama Akun', 'Nominal (Rp)'],
            'rows' => $rows,
        ];
    }

    private function applyDateRange(Builder $query, Request $request, string $column): void
    {
        if ($request->filled('start_date')) {
            $query->whereDate($column, '>=', $request->input('start_date'));
        }
        if ($request->filled('end_date')) {
            $query->whereDate($column, '<=', $request->input('end_date'));
        }
    }

    /** @param list<string> $columns @param list<string> $relationColumns */
    private function applySearch(Builder $query, Request $request, array $columns, ?string $relation = null, array $relationColumns = []): void
    {
        if (!$request->filled('search')) {
            return;
        }

        $search = $request->string('search')->toString();
        $query->where(function (Builder $builder) use ($columns, $search, $relation, $relationColumns): void {
            foreach ($columns as $index => $column) {
                $method = $index === 0 ? 'where' : 'orWhere';
                $builder->{$method}($column, 'like', "%{$search}%");
            }
            if ($relation !== null && $relationColumns !== []) {
                $builder->orWhereHas($relation, function ($related) use ($relationColumns, $search): void {
                    foreach ($relationColumns as $index => $column) {
                        $method = $index === 0 ? 'where' : 'orWhere';
                        $related->{$method}($column, 'like', "%{$search}%");
                    }
                });
            }
        });
    }

    private function signedAmount(JurnalDetail $detail, string $normalBalance): float
    {
        $debit = $detail->posisi === 'Debit' ? (float) $detail->nominal : 0.0;
        $credit = $detail->posisi === 'Kredit' ? (float) $detail->nominal : 0.0;

        return $normalBalance === 'Debit' ? $debit - $credit : $credit - $debit;
    }

    private function periodTitle(string $title, Request $request): string
    {
        if (!$request->filled('start_date') && !$request->filled('end_date')) {
            return $title;
        }

        return $title . ' (' . ($request->input('start_date') ?: 'awal data') . ' s.d. ' . ($request->input('end_date') ?: 'hari ini') . ')';
    }
}
