<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\Coa;
use App\Models\JurnalUmum;
use App\Services\AccountingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Exception;

class JurnalController extends Controller
{
    protected AccountingService $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    public function index(): View
    {
        $jurnal = JurnalUmum::with(['details.coa', 'creator'])->latest('tanggal')->latest('id_jurnal')->paginate(10);
        $coaList = Coa::all();
        return view('pengurus.jurnal.index', compact('jurnal', 'coaList'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'tanggal' => ['required', 'date'],
            'keterangan' => ['required', 'string'],
            'details' => ['required', 'array', 'min:2'],
            'details.*.kode_akun' => ['required', 'exists:tb_coa,kode_akun'],
            'details.*.posisi' => ['required', 'in:Debit,Kredit'],
            'details.*.nominal' => ['required', 'numeric', 'gt:0'],
        ]);

        try {
            $this->accountingService->createJournalEntry(
                $request->tanggal,
                null,
                'Manual',
                $request->keterangan,
                $request->details
            );

            return redirect()->route('pengurus.jurnal.index')->with('success', 'Entri jurnal manual berhasil diposting.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function reversal(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'alasan' => ['required', 'string', 'max:255'],
        ]);

        try {
            $this->accountingService->reverseJournal($id, $request->alasan);
            return redirect()->route('pengurus.jurnal.index')->with('success', "Jurnal [{$id}] berhasil direversal.");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}