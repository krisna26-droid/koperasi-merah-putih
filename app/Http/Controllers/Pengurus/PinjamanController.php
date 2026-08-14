<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePinjamanRequest;
use App\Models\Anggota;
use App\Models\Pinjaman;
use App\Services\LoanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Exception;

class PinjamanController extends Controller
{
    protected LoanService $loanService;

    public function __construct(LoanService $loanService)
    {
        $this->loanService = $loanService;
    }

    public function index(): View
    {
        $pinjaman = Pinjaman::with(['anggota', 'approver'])->latest()->paginate(10);
        $anggotaList = Anggota::where('status', 'Aktif')->get();
        return view('pengurus.pinjaman.index', compact('pinjaman', 'anggotaList'));
    }

    public function store(StorePinjamanRequest $request): RedirectResponse
    {
        try {
            $this->loanService->createApplication(
                $request->id_anggota,
                (float) $request->jumlah_pinjam,
                (int) $request->tenor
            );

            return redirect()->route('pengurus.pinjaman.index')->with('success', 'Pengajuan pinjaman berhasil dikirim ke Ketua Koperasi.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}