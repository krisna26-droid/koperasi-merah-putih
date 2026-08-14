<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSimpananRequest;
use App\Models\Anggota;
use App\Models\Simpanan;
use App\Services\SavingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Exception;

class SimpananController extends Controller
{
    protected SavingsService $savingsService;

    public function __construct(SavingsService $savingsService)
    {
        $this->savingsService = $savingsService;
    }

    public function index(): View
    {
        $simpanan = Simpanan::with('anggota')->latest()->paginate(10);
        $anggotaList = Anggota::where('status', 'Aktif')->get();
        return view('pengurus.simpanan.index', compact('simpanan', 'anggotaList'));
    }

    public function store(StoreSimpananRequest $request): RedirectResponse
    {
        try {
            $this->savingsService->processDeposit(
                $request->id_anggota,
                $request->jenis_simpanan,
                (float) $request->nominal
            );

            return redirect()->route('pengurus.simpanan.index')->with('success', 'Setoran simpanan berhasil diproses dan dijurnal.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}