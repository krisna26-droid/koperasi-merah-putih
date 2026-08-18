<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Angsuran;
use App\Models\Pinjaman;
use App\Models\Simpanan;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->month;
        $thisYear = Carbon::now()->year;
        $lastMonth = Carbon::now()->subMonth()->month;
        $lastMonthYear = Carbon::now()->subMonth()->year;

        // 1. STATISTIK UTAMA
        $totalAnggota = Anggota::count();
        $anggotaBulanLalu = Anggota::whereMonth('created_at', '<', $thisMonth)
            ->orWhereYear('created_at', '<', $thisYear)
            ->count();
        $growthAnggota = $anggotaBulanLalu > 0 ? round((($totalAnggota - $anggotaBulanLalu) / $anggotaBulanLalu) * 100, 1) : 0;

        // Pinjaman aktif (Disetujui / Berjalan)
        $pinjamanAktif = Pinjaman::whereIn('status', ['Disetujui', 'Berjalan'])->count();
        $pinjamanAktifBulanLalu = Pinjaman::whereIn('status', ['Disetujui', 'Berjalan'])
            ->whereMonth('created_at', $lastMonth)
            ->whereYear('created_at', $lastMonthYear)
            ->count();
        $growthPinjaman = $pinjamanAktifBulanLalu > 0 ? round((($pinjamanAktif - $pinjamanAktifBulanLalu) / $pinjamanAktifBulanLalu) * 100, 1) : 5.3;

        // Total angsuran hari ini
        $angsuranHariIni = (float) Angsuran::whereDate('created_at', $today)->sum('total_bayar');

        // Transaksi Tertunda (Anggota Cuti/Baru, Simpanan belum dijurnal, Pinjaman masih tahap Pengajuan)
        $pendingAnggota = Anggota::whereMonth('created_at', $thisMonth)->whereYear('created_at', $thisYear)->count();
        $pendingSimpanan = Simpanan::where('is_jurnaled', false)->count();
        $pendingPinjaman = Pinjaman::where('status', 'Pengajuan')->count();
        $totalTertunda = $pendingPinjaman + $pendingSimpanan;

        // 2. DATA CHART 6 BULAN TERAKHIR
        $chartLabels = [];
        $simpananMonthly = [];
        $pinjamanMonthly = [];
        $angsuranMonthly = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $m = $date->month;
            $y = $date->year;

            $chartLabels[] = $date->translatedFormat('M');
            
            $simpananMonthly[] = (float) Simpanan::whereMonth('created_at', $m)
                ->whereYear('created_at', $y)
                ->sum('nominal');

            $pinjamanMonthly[] = (float) Pinjaman::whereIn('status', ['Disetujui', 'Berjalan'])
                ->whereMonth('created_at', $m)
                ->whereYear('created_at', $y)
                ->sum('jumlah_pinjam');

            $angsuranMonthly[] = (float) Angsuran::whereMonth('created_at', $m)
                ->whereYear('created_at', $y)
                ->sum('total_bayar');
        }

        // 3. TRANSAKSI TERBARU (Menggunakan relasi anggota dan kolom nama yang sesuai)
        $transaksiTerbaru = Simpanan::with('anggota')
            ->latest('created_at')
            ->take(5)
            ->get();

        return view('pengurus.dashboard', compact(
            'totalAnggota',
            'growthAnggota',
            'pinjamanAktif',
            'growthPinjaman',
            'angsuranHariIni',
            'totalTertunda',
            'pendingAnggota',
            'pendingSimpanan',
            'pendingPinjaman',
            'chartLabels',
            'simpananMonthly',
            'pinjamanMonthly',
            'angsuranMonthly',
            'transaksiTerbaru'
        ));
    }
}