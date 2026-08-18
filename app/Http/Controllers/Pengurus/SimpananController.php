<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Simpanan;
use App\Services\SavingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        $simpanan = Simpanan::with('anggota')->latest()->get();
        $anggotaList = Anggota::where('status', 'Aktif')->orderBy('nama', 'asc')->get();

        return view('pengurus.simpanan.index', compact('simpanan', 'anggotaList'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'id_anggota' => 'required|exists:tb_anggota,id_anggota',
            'jenis_simpanan' => 'required|in:Pokok,Wajib,Sukarela',
            'nominal' => 'required|numeric|min:1000',
        ]);

        try {
            $this->savingsService->processDeposit(
                $request->id_anggota,
                $request->jenis_simpanan,
                (float) $request->nominal
            );

            return redirect()->route('pengurus.simpanan.index')->with('success', 'Setoran simpanan berhasil disimpan dan dijurnal otomatis.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}