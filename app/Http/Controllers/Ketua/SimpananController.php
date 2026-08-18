<?php

namespace App\Http\Controllers\Ketua;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\JurnalDetail;
use App\Models\JurnalUmum;
use App\Models\Simpanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    public function store(Request $request)
    {
        $request->validate([
            'id_anggota' => 'required|exists:tb_anggota,id_anggota',
            'jenis_simpanan' => 'required|in:Pokok,Wajib,Sukarela',
            'nominal' => 'required|numeric|min:1000',
            'tanggal' => 'nullable|date',
            'keterangan' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request) {
            $tanggal = $request->input('tanggal', now()->format('Y-m-d'));
            $datePrefix = date('Ymd', strtotime($tanggal));

            // Generate ID Simpanan Unik
            $countToday = Simpanan::whereDate('created_at', $tanggal)->count() + 1;
            $idSimpanan = sprintf("SP-%s-%03d", $datePrefix, $countToday);

            // 1. Simpan Transaksi Simpanan
            $simpanan = Simpanan::create([
                'id_simpanan' => $idSimpanan,
                'id_anggota' => $request->id_anggota,
                'jenis_simpanan' => $request->jenis_simpanan,
                'nominal' => $request->nominal,
                'is_jurnaled' => true,
                'created_by' => Auth::user()->username ?? 'ketua',
                'created_at' => $tanggal . ' ' . now()->format('H:i:s'),
            ]);

            // 2. Tentukan Kode Akun Simpanan
            $akunKredit = match ($request->jenis_simpanan) {
                'Pokok' => '201',
                'Wajib' => '202',
                'Sukarela' => '203',
            };

            // 3. Catat Jurnal Umum Otomatis
            $countJurnal = JurnalUmum::whereDate('created_at', $tanggal)->count() + 1;
            $idJurnal = sprintf("JRN-%s-%04d", $datePrefix, $countJurnal);

            JurnalUmum::create([
                'id_jurnal' => $idJurnal,
                'tanggal' => $tanggal,
                'id_referensi' => $idSimpanan,
                'tipe_transaksi' => 'Simpanan',
                'keterangan' => $request->keterangan ?? "Setoran Simpanan {$request->jenis_simpanan} a.n {$request->id_anggota}",
                'is_reversal' => false,
                'created_by' => Auth::user()->username ?? 'ketua',
            ]);

            // Debit: Kas (101)
            JurnalDetail::create([
                'id_jurnal' => $idJurnal,
                'kode_akun' => '101',
                'posisi' => 'Debit',
                'nominal' => $request->nominal,
            ]);

            // Kredit: Akun Simpanan Terkait
            JurnalDetail::create([
                'id_jurnal' => $idJurnal,
                'kode_akun' => $akunKredit,
                'posisi' => 'Kredit',
                'nominal' => $request->nominal,
            ]);
        });

        return redirect()->route('ketua.simpanan.index')->with('success', 'Transaksi simpanan berhasil dicatat.');
    }
}