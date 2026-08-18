<?php

namespace App\Http\Controllers\Ketua;

use App\Http\Controllers\Controller;
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
        // 1. Data Riwayat & Pengajuan Pinjaman
        $pinjaman = Pinjaman::with(['anggota', 'approver'])
            ->orderByRaw("FIELD(status, 'Pengajuan', 'Berjalan', 'Disetujui', 'Lunas', 'Ditolak')")
            ->latest()
            ->paginate(10);

        // Alias variabel untuk kompatibilitas view
        $pinjamanList = $pinjaman;

        // 2. Variabel Statistik 4 Stat Cards di Blade View
        $pinjamanAktif = Pinjaman::where('status', 'Berjalan')->count();
        $totalOutstanding = (float) Pinjaman::where('status', 'Berjalan')->sum('sisa_tagihan');
        $pengajuanPending = Pinjaman::where('status', 'Pengajuan')->count();
        $pinjamanLunas = Pinjaman::where('status', 'Lunas')->count();
        $totalPenyaluran = (float) Pinjaman::whereIn('status', ['Berjalan', 'Lunas'])->sum('jumlah_pinjam');

        return view('ketua.pinjaman.index', compact(
            'pinjaman',
            'pinjamanList',
            'pinjamanAktif',
            'totalOutstanding',
            'pengajuanPending',
            'pinjamanLunas',
            'totalPenyaluran'
        ));
    }

    public function show(string $id): View
    {
        $pinjaman = Pinjaman::with(['anggota', 'approver', 'angsuran'])->findOrFail($id);

        $riwayatPinjaman = Pinjaman::where('id_anggota', $pinjaman->id_anggota)
            ->where('id_pinjam', '!=', $id)
            ->latest()
            ->take(5)
            ->get();

        return view('ketua.pinjaman.show', compact('pinjaman', 'riwayatPinjaman'));
    }

    public function approve(string $id): RedirectResponse
    {
        try {
            $this->loanService->processApproval($id, 'Approve');
            return redirect()->route('ketua.pinjaman.index')->with('success', 'Pengajuan pinjaman berhasil disetujui dan dicairkan.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject(string $id): RedirectResponse
    {
        try {
            $this->loanService->processApproval($id, 'Reject');
            return redirect()->route('ketua.pinjaman.index')->with('success', 'Pengajuan pinjaman telah ditolak.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}