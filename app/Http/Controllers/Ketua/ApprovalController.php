<?php

namespace App\Http\Controllers\Ketua;

use App\Http\Controllers\Controller;
use App\Models\Pinjaman;
use App\Services\LoanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Exception;

class ApprovalController extends Controller
{
    protected LoanService $loanService;

    public function __construct(LoanService $loanService)
    {
        $this->loanService = $loanService;
    }

    public function index(): View
    {
        $pengajuan = Pinjaman::with('anggota')->where('status', 'Pengajuan')->latest()->paginate(10);
        return view('ketua.approval.index', compact('pengajuan'));
    }

    public function approve(Request $request, string $id): RedirectResponse
    {
        try {
            $this->loanService->processApproval($id, 'Approve');
            return redirect()->back()->with('success', "Pinjaman [{$id}] berhasil disetujui & dicairkan.");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, string $id): RedirectResponse
    {
        try {
            $this->loanService->processApproval($id, 'Reject');
            return redirect()->back()->with('success', "Pinjaman [{$id}] telah ditolak.");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}