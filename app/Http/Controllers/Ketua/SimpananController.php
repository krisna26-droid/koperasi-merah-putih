<?php

namespace App\Http\Controllers\Ketua;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Simpanan;
use Illuminate\Http\Request;

class SimpananController extends Controller
{
    public function index(Request $request)
    {
        // 1. Data Statistik Simpanan
        $totalSimpanan = Simpanan::sum('nominal');
        $simpananPokok = Simpanan::where('jenis_simpanan', 'Pokok')->sum('nominal');
        $simpananWajib = Simpanan::where('jenis_simpanan', 'Wajib')->sum('nominal');
        $simpananSukarela = Simpanan::where('jenis_simpanan', 'Sukarela')->sum('nominal');

        // 2. Query Transaksi Simpanan
        $query = Simpanan::with('anggota');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('id_simpanan', 'like', "%{$search}%")
                  ->orWhere('id_anggota', 'like', "%{$search}%")
                  ->orWhereHas('anggota', function ($sq) use ($search) {
                      $sq->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('jenis')) {
            $query->where('jenis_simpanan', $request->input('jenis'));
        }

        $simpananList = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        $anggotaList = Anggota::where('status', 'Aktif')->orderBy('nama', 'asc')->get();

        return view('ketua.simpanan.index', compact(
            'totalSimpanan',
            'simpananPokok',
            'simpananWajib',
            'simpananSukarela',
            'simpananList',
            'anggotaList'
        ));
    }

}
