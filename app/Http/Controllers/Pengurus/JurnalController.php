<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\Coa;
use App\Models\JurnalDetail;
use App\Models\JurnalUmum;
use App\Services\AccountingService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
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

    public function index(Request $request): View
    {
        $now = Carbon::now();

        // 1. Variabel Statistik 3 Kartu Metrik
        $totalJurnalBulanIni = JurnalUmum::whereMonth('tanggal', $now->month)
            ->whereYear('tanggal', $now->year)
            ->count();

        $totalDebit = (float) JurnalDetail::where('posisi', 'Debit')->sum('nominal');
        $totalKredit = (float) JurnalDetail::where('posisi', 'Kredit')->sum('nominal');

        // 2. Query Detail Jurnal dengan Filter
        $query = JurnalDetail::with(['header', 'coa']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('id_jurnal', 'like', "%{$search}%")
                  ->orWhereHas('header', function ($jq) use ($search) {
                      $jq->where('keterangan', 'like', "%{$search}%");
                  })
                  ->orWhereHas('coa', function ($cq) use ($search) {
                      $cq->where('nama_akun', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('kode_akun')) {
            $query->where('kode_akun', $request->input('kode_akun'));
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereHas('header', function ($jq) use ($request) {
                $jq->whereBetween('tanggal', [$request->start_date, $request->end_date]);
            });
        }

        $jurnalDetails = $query->orderBy('id_jurnal', 'desc')
            ->orderBy('posisi', 'asc')
            ->paginate(15)
            ->withQueryString();

        $daftarAkun = Coa::orderBy('kode_akun', 'asc')->get();

        return view('pengurus.jurnal.index', compact(
            'totalJurnalBulanIni',
            'totalDebit',
            'totalKredit',
            'jurnalDetails',
            'daftarAkun'
        ));
    }

    public function getDetail(string $id): JsonResponse
    {
        $jurnal = JurnalUmum::with(['details.coa'])->where('id_jurnal', $id)->firstOrFail();

        $totalDebit = (float) $jurnal->details->where('posisi', 'Debit')->sum('nominal');
        $totalKredit = (float) $jurnal->details->where('posisi', 'Kredit')->sum('nominal');
        $isBalanced = round($totalDebit, 2) === round($totalKredit, 2);

        return response()->json([
            'id_jurnal' => $jurnal->id_jurnal,
            'tanggal' => Carbon::parse($jurnal->tanggal)->translatedFormat('d F Y'),
            'keterangan' => $jurnal->keterangan,
            'is_reversal' => $jurnal->is_reversal,
            'total_debit' => number_format($totalDebit, 0, ',', '.'),
            'total_kredit' => number_format($totalKredit, 0, ',', '.'),
            'is_balanced' => $isBalanced,
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

    public function reversal(Request $request, string $id): RedirectResponse
    {
        try {
            $alasan = $request->input('alasan', 'Koreksi Transaksi Pengurus');
            $this->accountingService->reverseJournal($id, $alasan);

            return redirect()->route('pengurus.jurnal.index')->with('success', "Jurnal {$id} berhasil di-reversal dan dibuatkan jurnal pembalik.");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}