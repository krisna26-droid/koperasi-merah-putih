<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\Coa;
use App\Models\JurnalDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BukuBesarController extends Controller
{
    public function index(Request $request): View
    {
        $coaList = Coa::orderBy('kode_akun', 'asc')->get();$selectedAccount = $request->get('kode_akun',$coaList->first()?->kode_akun ?? '101');
        $account = Coa::where('kode_akun',$selectedAccount)->firstOrFail();

        $month = $request->get('month', now()->month);$year = $request->get('year', now()->year);$startDate = Carbon::createFromDate($year,$month, 1)->startOfMonth();
        $endDate =$startDate->copy()->endOfMonth();

        // 1. Kalkulasi Saldo Awal (Mutasi sebelum tanggal $startDate)
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

        // 2. Ambil Mutasi Periode Terpilih
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

        return view('pengurus.buku-besar.index', compact(
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
}