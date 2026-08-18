<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\Simpanan;
use App\Models\Pinjaman;
use App\Models\Angsuran;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function index(): View
    {
        $totalSimpanan = (float) Simpanan::sum('nominal');
        $totalPinjaman = (float) Pinjaman::whereIn('status', ['Berjalan', 'Lunas'])->sum('jumlah_pinjam');
        $totalAngsuran = (float) Angsuran::sum('total_bayar');

        return view('pengurus.laporan.index', compact('totalSimpanan', 'totalPinjaman', 'totalAngsuran'));
    }

    public function simpanan(Request $request): View
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());
        $jenis = $request->get('jenis_simpanan');

        $query = Simpanan::with('anggota')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate);

        if ($jenis) {
            $query->where('jenis_simpanan', $jenis);
        }

        $data = $query->latest()->get();
        $totalNominal = $data->sum('nominal');

        return view('pengurus.laporan.simpanan', compact('data', 'startDate', 'endDate', 'jenis', 'totalNominal'));
    }

    public function pinjaman(Request $request): View
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfYear()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());
        $status = $request->get('status');

        $query = Pinjaman::with('anggota')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate);

        if ($status) {
            $query->where('status', $status);
        }

        $data = $query->latest()->get();
        $totalPenyaluran = $data->whereIn('status', ['Berjalan', 'Lunas'])->sum('jumlah_pinjam');
        $totalSisaTagihan = $data->where('status', 'Berjalan')->sum('sisa_tagihan');

        return view('pengurus.laporan.pinjaman', compact('data', 'startDate', 'endDate', 'status', 'totalPenyaluran', 'totalSisaTagihan'));
    }

    public function angsuran(Request $request): View
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());

        $data = Angsuran::with('pinjaman.anggota')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->latest()
            ->get();

        $totalPokok = $data->sum('bayar_pokok');
        $totalBunga = $data->sum('bayar_bunga');
        $totalBayar = $data->sum('total_bayar');

        return view('pengurus.laporan.angsuran', compact('data', 'startDate', 'endDate', 'totalPokok', 'totalBunga', 'totalBayar'));
    }
}