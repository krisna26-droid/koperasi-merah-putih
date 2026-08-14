<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\Coa;
use App\Models\JurnalDetail;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BukuBesarController extends Controller
{
    public function index(Request $request): View
    {
        $coaList = Coa::all();
        $selectedAccount = $request->get('kode_akun', $coaList->first()?->kode_akun ?? '101');

        $account = Coa::where('kode_akun', $selectedAccount)->firstOrFail();

        $mutasi = JurnalDetail::with('header')
            ->where('kode_akun', $selectedAccount)
            ->whereHas('header')
            ->get()
            ->sortBy(fn($item) => $item->header->tanggal . $item->header->id_jurnal);

        // Kalkulasi Running Balance
        $runningBalance = 0.00;
        $ledgerData = [];

        foreach ($mutasi as $item) {
            $debit = $item->posisi === 'Debit' ? (float) $item->nominal : 0.00;
            $kredit = $item->posisi === 'Kredit' ? (float) $item->nominal : 0.00;

            if ($account->saldo_normal === 'Debit') {
                $runningBalance += ($debit - $kredit);
            } else {
                $runningBalance += ($kredit - $debit);
            }

            $ledgerData[] = [
                'tanggal' => $item->header->tanggal->format('d/m/Y'),
                'id_jurnal' => $item->header->id_jurnal,
                'keterangan' => $item->header->keterangan,
                'debit' => $debit,
                'kredit' => $kredit,
                'saldo' => $runningBalance,
            ];
        }

        return view('pengurus.buku-besar.index', compact('coaList', 'account', 'ledgerData'));
    }
}