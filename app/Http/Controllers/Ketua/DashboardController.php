<?php

namespace App\Http\Controllers\Ketua;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Angsuran;
use App\Models\Pinjaman;
use App\Models\Simpanan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Data Statistik Utama
        $totalAnggota = Anggota::count();
        $totalSimpanan = Simpanan::sum('nominal');
        $totalPinjaman = Pinjaman::where('status', 'Berjalan')->sum('jumlah_pinjam');
        $totalAngsuran = Angsuran::sum('total_bayar');

        // 2. Daftar Pinjaman Menunggu Persetujuan
        $menungguPersetujuan = Pinjaman::with('anggota')
            ->where('status', 'Pengajuan')
            ->latest()
            ->get();

        // 3. Transaksi Terbaru (Gabungan Simpanan & Angsuran)
        $transaksiTerbaru = Simpanan::with('anggota')
            ->latest()
            ->take(5)
            ->get();

        return view('ketua.dashboard', compact(
            'totalAnggota',
            'totalSimpanan',
            'totalPinjaman',
            'totalAngsuran',
            'menungguPersetujuan',
            'transaksiTerbaru'
        ));
    }
}