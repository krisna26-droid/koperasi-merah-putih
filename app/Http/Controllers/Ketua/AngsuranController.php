<?php

namespace App\Http\Controllers\Ketua;

use App\Http\Controllers\Controller;
use App\Models\Angsuran;
use App\Models\Pinjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AngsuranController extends Controller
{
    public function index(Request $request)
    {
        // 1. Data Statistik Angsuran
        $angsuranHariIni = Angsuran::whereDate('created_at', now()->format('Y-m-d'))->count();
        $totalTerkumpul = Angsuran::sum('total_bayar');
        $outstanding = Pinjaman::where('status', 'Berjalan')->sum('sisa_tagihan');
        $pembayaranTerlambat = 0; // Default 0 jika skema belum menggunakan fitur jatuh tempo (due date)

        // 2. Query Daftar Angsuran
        $query = Angsuran::with(['anggota', 'pinjaman']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('id_angsuran', 'like', "%{$search}%")
                  ->orWhere('id_pinjam', 'like', "%{$search}%")
                  ->orWhereHas('anggota', function ($sq) use ($search) {
                      $sq->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        $angsuranList = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('ketua.angsuran.index', compact(
            'angsuranHariIni',
            'totalTerkumpul',
            'outstanding',
            'pembayaranTerlambat',
            'angsuranList'
        ));
    }
}