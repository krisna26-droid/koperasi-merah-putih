<?php

namespace App\Http\Controllers\Ketua;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Angsuran;
use App\Models\Pinjaman;
use App\Models\Simpanan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        $startThisMonth = $now->copy()->startOfMonth();
        $startLastMonth = $now->copy()->subMonth()->startOfMonth();
        $endLastMonth = $now->copy()->subMonth()->endOfMonth();

        // 1. Data Statistik Utama
        $totalAnggota = Anggota::count();
        $totalSimpanan = (float) Simpanan::sum('nominal');
        $totalPinjaman = (float) Pinjaman::where('status', 'Berjalan')->sum('jumlah_pinjam');
        $totalAngsuran = (float) Angsuran::sum('total_bayar');

        // 2. Kalkulasi Tren Persentase Pertumbuhan Bulanan (MoM)
        $anggotaLalu = Anggota::where('created_at', '<=', $endLastMonth)->count();
        $anggotaBulanIni = Anggota::whereBetween('created_at', [$startThisMonth, $now])->count();
        $trenAnggota = $this->calculateGrowth($anggotaBulanIni, $anggotaLalu);

        $simpananBulanIni = (float) Simpanan::whereBetween('created_at', [$startThisMonth, $now])->sum('nominal');
        $simpananBulanLalu = (float) Simpanan::whereBetween('created_at', [$startLastMonth, $endLastMonth])->sum('nominal');
        $trenSimpanan = $this->calculateGrowth($simpananBulanIni, $simpananBulanLalu);

        $pinjamanBulanIni = (float) Pinjaman::where('status', 'Berjalan')->whereBetween('created_at', [$startThisMonth, $now])->sum('jumlah_pinjam');
        $pinjamanBulanLalu = (float) Pinjaman::where('status', 'Berjalan')->whereBetween('created_at', [$startLastMonth, $endLastMonth])->sum('jumlah_pinjam');
        $trenPinjaman = $this->calculateGrowth($pinjamanBulanIni, $pinjamanBulanLalu);

        $angsuranBulanIni = (float) Angsuran::whereBetween('created_at', [$startThisMonth, $now])->sum('total_bayar');
        $angsuranBulanLalu = (float) Angsuran::whereBetween('created_at', [$startLastMonth, $endLastMonth])->sum('total_bayar');
        $trenAngsuran = $this->calculateGrowth($angsuranBulanIni, $angsuranBulanLalu);

        // 3. Ringkasan Keuangan 6 Bulan Terakhir (Grafik Batang)
        $chartData = [];
        $maxChartNominal = 1; // Menghindari division by zero

        for ($i = 5; $i >= 0; $i--) {
            $monthDate = $now->copy()->subMonths($i);
            $mStart = $monthDate->copy()->startOfMonth();
            $mEnd = $monthDate->copy()->endOfMonth();

            $nominalSimpanan = (float) Simpanan::whereBetween('created_at', [$mStart, $mEnd])->sum('nominal');
            $nominalPinjaman = (float) Pinjaman::where('status', 'Berjalan')->whereBetween('created_at', [$mStart, $mEnd])->sum('jumlah_pinjam');
            $nominalAngsuran = (float) Angsuran::whereBetween('created_at', [$mStart, $mEnd])->sum('total_bayar');

            $highest = max($nominalSimpanan, $nominalPinjaman, $nominalAngsuran);
            if ($highest > $maxChartNominal) {
                $maxChartNominal = $highest;
            }

            $chartData[] = [
                'label' => $monthDate->translatedFormat('M'),
                'simpanan' => $nominalSimpanan,
                'pinjaman' => $nominalPinjaman,
                'angsuran' => $nominalAngsuran,
            ];
        }

        // Kalkulasi persentase tinggi bar CSS (15% min - 100% max)
        foreach ($chartData as &$data) {
            $data['height_simpanan'] = $maxChartNominal > 0 && $data['simpanan'] > 0 
                ? max(15, round(($data['simpanan'] / $maxChartNominal) * 100)) 
                : 10;
        }

        // 4. Data Persetujuan & Transaksi
        $menungguPersetujuan = Pinjaman::with('anggota')
            ->where('status', 'Pengajuan')
            ->orderBy('created_at', 'desc')
            ->get();

        $transaksiTerbaru = Simpanan::with('anggota')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('ketua.dashboard', compact(
            'totalAnggota',
            'totalSimpanan',
            'totalPinjaman',
            'totalAngsuran',
            'trenAnggota',
            'trenSimpanan',
            'trenPinjaman',
            'trenAngsuran',
            'chartData',
            'menungguPersetujuan',
            'transaksiTerbaru'
        ));
    }

    private function calculateGrowth(float $current, float $previous): array
    {
        if ($previous == 0) {
            $percentage = $current > 0 ? 100.0 : 0.0;
        } else {
            $percentage = (($current - $previous) / $previous) * 100;
        }

        return [
            'value' => abs(round($percentage, 1)),
            'is_up' => $percentage >= 0,
            'label' => ($percentage >= 0 ? '↗ ' : '↘ ') . number_format(abs($percentage), 1, ',', '.') . '%'
        ];
    }
}