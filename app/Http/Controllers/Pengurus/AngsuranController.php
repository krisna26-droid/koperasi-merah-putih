<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\Angsuran;
use App\Models\Pinjaman;
use App\Services\LoanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        $angsuran = Angsuran::latest()->paginate(10);
        $pinjamanBerjalan = Pinjaman::with('anggota')
            ->where('status', 'Berjalan')
            ->orderBy('id_pinjam', 'asc')
            ->get();

        return view('pengurus.angsuran.index', compact('angsuran', 'pinjamanBerjalan'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'id_pinjam' => 'required|exists:tb_pinjaman,id_pinjam',
        ]);

        try {
            $this->loanService->processInstallmentPayment($request->id_pinjam);
            return redirect()->route('pengurus.angsuran.index')->with('success', 'Pembayaran angsuran berhasil dicatat dan diposting ke jurnal umum.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}