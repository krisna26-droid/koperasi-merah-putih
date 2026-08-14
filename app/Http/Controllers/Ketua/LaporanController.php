<?php

namespace App\Http\Controllers\Ketua;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Coa;
use App\Models\JurnalDetail;
use App\Models\JurnalUmum;
use App\Models\Pinjaman;
use App\Models\Simpanan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaporanController extends Controller
{
    public function anggota(): View
    {
        $anggota = Anggota::latest()->get();
        return view('ketua.laporan.anggota', compact('anggota'));
    }

    public function simpanan(): View
    {
        $simpanan = Simpanan::with('anggota')->latest()->get();
        return view('ketua.laporan.simpanan', compact('simpanan'));
    }

    public function pinjaman(): View
    {
        $pinjaman = Pinjaman::with('anggota')->latest()->get();
        return view('ketua.laporan.pinjaman', compact('pinjaman'));
    }

    public function jurnalUmum(): View
    {
        $jurnal = JurnalUmum::with('details.coa')->latest('tanggal')->get();
        return view('ketua.laporan.jurnal', compact('jurnal'));
    }

    public function bukuBesar(Request $request): View
    {
        $coaList = Coa::all();
        $selectedAccount = $request->get('kode_akun', $coaList->first()?->kode_akun ?? '101');
        $account = Coa::where('kode_akun', $selectedAccount)->firstOrFail();

        $mutasi = JurnalDetail::with('header')
            ->where('kode_akun', $selectedAccount)
            ->whereHas('header')
            ->get()
            ->sortBy(fn($item) => $item->header->tanggal . $item->header->id_jurnal);

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

        return view('ketua.laporan.buku-besar', compact('coaList', 'account', 'ledgerData'));
    }
}