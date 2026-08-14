<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAngsuranRequest;
use App\Models\Angsuran;
use App\Models\Pinjaman;
use App\Services\LoanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Exception;

class AngsuranController extends Controller
{
    protected LoanService $loanService;

    public function __construct(LoanService $loanService)
    {
        $this->loanService = $loanService;
    }

    public function index(): View
    {
        $angsuran = Angsuran::with(['pinjaman', 'anggota'])->latest()->paginate(10);
        $pinjamanBerjalan = Pinjaman::with('anggota')->where('status', 'Berjalan')->get();
        return view('pengurus.angsuran.index', compact('angsuran', 'pinjamanBerjalan'));
    }

    public function store(StoreAngsuranRequest $request): RedirectResponse
    {
        try {
            $this->loanService->processInstallmentPayment($request->id_pinjam);
            return redirect()->route('pengurus.angsuran.index')->with('success', 'Pembayaran angsuran berhasil diproses dan dijurnal 3-baris.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}