<?php

namespace App\Http\Controllers\Ketua;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Angsuran;
use App\Models\Coa;
use App\Models\JurnalDetail;
use App\Models\JurnalUmum;
use App\Models\Pinjaman;
use App\Models\Simpanan;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaporanController extends Controller
{
    /**
     * Tampilan Hub Pusat Laporan
     */
    public function index(): View
    {
        $periodeAktif = Carbon::now()->translatedFormat('F Y');$terakhirDiperbarui = Carbon::now()->translatedFormat('H:i');

        $totalSimpanan = (float) Simpanan::sum('nominal');$totalPinjaman = (float) Pinjaman::whereIn('status', ['Berjalan', 'Lunas'])->sum('jumlah_pinjam');
        $totalAngsuran = (float) Angsuran::sum('total_bayar');$totalAnggota = Anggota::count();

        return view('ketua.laporan.index', compact(
            'periodeAktif',
            'terakhirDiperbarui',
            'totalSimpanan',
            'totalPinjaman',
            'totalAngsuran',
            'totalAnggota'
        ));
    }

    public function anggota(Request $request): View
    {
        $status = $request->get('status');$query = Anggota::query();

        if ($status) {
            $query->where('status',$status);
        }

        $anggota =$query->orderBy('created_at', 'desc')->get();
        $totalAktif = Anggota::where('status', 'Aktif')->count();$totalCuti = Anggota::where('status', 'Cuti')->count();

        return view('ketua.laporan.anggota', compact('anggota', 'status', 'totalAktif', 'totalCuti'));
    }

    public function simpanan(Request $request): View
    {
        $startDate =$request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate =$request->get('end_date', Carbon::now()->toDateString());
        $jenis =$request->get('jenis_simpanan');

        $query = Simpanan::with('anggota')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate);

        if ($jenis) {
            $query->where('jenis_simpanan',$jenis);
        }

        $simpanan =$query->latest()->get();
        $totalNominal =$simpanan->sum('nominal');

        return view('ketua.laporan.simpanan', compact('simpanan', 'startDate', 'endDate', 'jenis', 'totalNominal'));
    }

    public function pinjaman(Request $request): View
    {
        $startDate =$request->get('start_date', Carbon::now()->startOfYear()->toDateString());
        $endDate =$request->get('end_date', Carbon::now()->toDateString());
        $status =$request->get('status');

        $query = Pinjaman::with(['anggota', 'approver'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate);

        if ($status) {
            $query->where('status',$status);
        }

        $pinjaman =$query->latest()->get();
        $totalPenyaluran =$pinjaman->whereIn('status', ['Berjalan', 'Lunas'])->sum('jumlah_pinjam');
        $totalSisaTagihan =$pinjaman->where('status', 'Berjalan')->sum('sisa_tagihan');

        return view('ketua.laporan.pinjaman', compact('pinjaman', 'startDate', 'endDate', 'status', 'totalPenyaluran', 'totalSisaTagihan'));
    }

    public function angsuran(Request $request): View
    {
        $startDate =$request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate =$request->get('end_date', Carbon::now()->toDateString());

        $angsuran = Angsuran::with(['pinjaman.anggota'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->latest()
            ->get();

        $totalPokok =$angsuran->sum('bayar_pokok');
        $totalBunga =$angsuran->sum('bayar_bunga');
        $totalBayar =$angsuran->sum('total_bayar');

        return view('ketua.laporan.angsuran', compact('angsuran', 'startDate', 'endDate', 'totalPokok', 'totalBunga', 'totalBayar'));
    }

    public function jurnalUmum(Request $request): View
    {
        $now = Carbon::now();

        $totalJurnalBulanIni = JurnalUmum::whereMonth('tanggal',$now->month)
            ->whereYear('tanggal', $now->year)
            ->count();

        $totalDebit = (float) JurnalDetail::where('posisi', 'Debit')->sum('nominal');$totalKredit = (float) JurnalDetail::where('posisi', 'Kredit')->sum('nominal');

        $query = JurnalDetail::with(['header', 'coa']);

        if ($request->filled('search')) {$search = $request->input('search');$query->where(function ($q) use ($search) {
                $q->where('id_jurnal', 'like', "\%{$search}%")
                  ->orWhereHas('header', function ($jq) use ($search) {
                      $jq->where('keterangan', 'like', "\%{$search}%");
                  })
                  ->orWhereHas('coa', function ($cq) use ($search) {
                      $cq->where('nama_akun', 'like', "\%{$search}%");
                  });
            });
        }

        if ($request->filled('kode_akun')) {
            $query->where('kode_akun',$request->input('kode_akun'));
        }

        if ($request->filled('start_date') &&$request->filled('end_date')) {
            $query->whereHas('header', function ($jq) use ($request) {$jq->whereBetween('tanggal', [$request->start_date,$request->end_date]);
            });
        }

        $jurnalDetails =$query->orderBy('id_jurnal', 'desc')
            ->orderBy('posisi', 'asc')
            ->paginate(15)
            ->withQueryString();

        $daftarAkun = Coa::orderBy('kode_akun', 'asc')->get();

        return view('ketua.laporan.jurnal', compact(
            'totalJurnalBulanIni',
            'totalDebit',
            'totalKredit',
            'jurnalDetails',
            'daftarAkun'
        ));
    }

    public function getJurnalDetail(string $id): JsonResponse
    {
        $jurnal = JurnalUmum::with(['details.coa'])->where('id_jurnal', $id)->firstOrFail();$totalDebit = (float) $jurnal->details->where('posisi', 'Debit')->sum('nominal');$totalKredit = (float) $jurnal->details->where('posisi', 'Kredit')->sum('nominal');$isBalanced = round($totalDebit, 2) === round($totalKredit, 2);

        return response()->json([
            'id_jurnal' => $jurnal->id_jurnal,
            'tanggal' => Carbon::parse($jurnal->tanggal)->translatedFormat('d F Y'),
            'keterangan' => $jurnal->keterangan,
            'total_debit' => number_format($totalDebit, 0, ',', '.'),
            'total_kredit' => number_format($totalKredit, 0, ',', '.'),             'is_balanced' =>$isBalanced,
            'details' => $jurnal->details->map(function ($d) {
                return [
                    'nama_akun' => $d->coa->nama_akun ?? $d->kode_akun,
                    'kode_akun' => $d->kode_akun,
                    'posisi' => $d->posisi,
                    'debit' => $d->posisi === 'Debit' ? 'Rp ' . number_format($d->nominal, 0, ',', '.') : '-',
                    'kredit' => $d->posisi === 'Kredit' ? 'Rp ' . number_format($d->nominal, 0, ',', '.') : '-',
                ];
            })
        ]);
    }

    public function bukuBesar(Request $request): View
    {
        $coaList = Coa::orderBy('kode_akun', 'asc')->get();$selectedAccount = $request->get('kode_akun',$coaList->first()?->kode_akun ?? '101');
        $account = Coa::where('kode_akun',$selectedAccount)->firstOrFail();

        $month = $request->get('month', now()->month);$year = $request->get('year', now()->year);$startDate = Carbon::createFromDate($year,$month, 1)->startOfMonth();
        $endDate =$startDate->copy()->endOfMonth();

        $mutasiLalu = JurnalDetail::where('kode_akun',$selectedAccount)
            ->whereHas('header', function ($q) use ($startDate) {
                $q->where('tanggal', '<',$startDate->format('Y-m-d'));
            })
            ->get();

        $saldoAwal = 0.00;
        foreach ($mutasiLalu as $item) {$deb = $item->posisi === 'Debit' ? (float)$item->nominal : 0.00;
            $kre = $item->posisi === 'Kredit' ? (float)$item->nominal : 0.00;

            if ($account->saldo_normal === 'Debit') {$saldoAwal += ($deb -$kre);
            } else {
                $saldoAwal += ($kre -$deb);
            }
        }

        $mutasi = JurnalDetail::with('header')
            ->where('kode_akun', $selectedAccount)
            ->whereHas('header', function ($q) use ($startDate, $endDate) {$q->whereBetween('tanggal', [$startDate->format('Y-m-d'),$endDate->format('Y-m-d')]);
            })
            ->get()
            ->sortBy(function ($item) {
                $tgl = Carbon::parse($item->header->tanggal)->format('Y-m-d');
                return $tgl .$item->header->id_jurnal;
            });

        $runningBalance =$saldoAwal;
        $totalDebitPeriode = 0.00;
        $totalKreditPeriode = 0.00;
        $ledgerData = [];

        foreach ($mutasi as $item) {$debit = $item->posisi === 'Debit' ? (float)$item->nominal : 0.00;
            $kredit = $item->posisi === 'Kredit' ? (float)$item->nominal : 0.00;

            $totalDebitPeriode +=$debit;
            $totalKreditPeriode +=$kredit;

            if ($account->saldo_normal === 'Debit') {$runningBalance += ($debit -$kredit);
            } else {
                $runningBalance += ($kredit -$debit);
            }

            $ledgerData[] = [
                'tanggal' => Carbon::parse($item->header->tanggal)->translatedFormat('d F Y'),
                'referensi' => $item->header->id_referensi ?? $item->header->id_jurnal,
                'keterangan' => $item->header->keterangan,
                'debit' => $debit,
                'kredit' => $kredit,
                'saldo' => $runningBalance,
            ];
        }

        $saldoAkhir =$runningBalance;

        return view('ketua.laporan.buku-besar', compact(
            'coaList',
            'account',
            'saldoAwal',
            'totalDebitPeriode',
            'saldoAkhir',
            'ledgerData',
            'startDate',
            'endDate'
        ));
    }
    public function neracaSaldo(Request $request, \App\Services\AccountingService $accountingService): View
    {
        $endDate = $request->get('end_date', Carbon::now()->toDateString());
        $data = $accountingService->getTrialBalance($endDate);

        return view('ketua.laporan.neraca-saldo', compact('data', 'endDate'));
    }

    public function labaRugi(Request $request, \App\Services\AccountingService $accountingService): View
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfYear()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());
        $data = $accountingService->getIncomeStatement($startDate, $endDate);

        return view('ketua.laporan.laba-rugi', compact('data', 'startDate', 'endDate'));
    }
}