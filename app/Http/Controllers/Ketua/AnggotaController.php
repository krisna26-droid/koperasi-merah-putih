<?php

namespace App\Http\Controllers\Ketua;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Angsuran;
use App\Models\Pinjaman;
use App\Models\Simpanan;
use Illuminate\Http\Request;

class AnggotaController extends Controller
{
    public function index(Request $request)
    {
        $totalAnggota = Anggota::count();
        $anggotaAktif = Anggota::where('status', 'Aktif')->count();
        $bergabungBulanIni = Anggota::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $query = Anggota::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('id_anggota', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%")
                  ->orWhere('no_telepon', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Urutkan berdasarkan created_at karena tabel tidak memiliki kolom auto-increment 'id'
        $anggotaList = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        // Generate Nomor Anggota Otomatis
        $lastAnggota = Anggota::orderBy('id_anggota', 'desc')->first();
        $nextIdNumber = $lastAnggota ? ((int) preg_replace('/[^0-9]/', '', $lastAnggota->id_anggota)) + 1 : 1;
        $generatedId = sprintf("AG-%05d", $nextIdNumber);

        return view('ketua.anggota.index', compact(
            'totalAnggota',
            'anggotaAktif',
            'bergabungBulanIni',
            'anggotaList',
            'generatedId'
        ));
    }

    public function show($id)
    {
        $anggota = Anggota::where('id_anggota', $id)->firstOrFail();

        // 1. Agregasi Metrik Finansial
        $totalSimpanan = (float) Simpanan::where('id_anggota', $id)->sum('nominal');
        $pinjamanAktif = Pinjaman::where('id_anggota', $id)->where('status', 'Berjalan')->first();
        $sisaPinjaman = $pinjamanAktif ? (float) $pinjamanAktif->sisa_tagihan : 0;
        $totalAngsuran = (float) Angsuran::where('id_anggota', $id)->sum('total_bayar');

        // 2. Data Tabel per Tab
        $simpananList = Simpanan::where('id_anggota', $id)->orderBy('created_at', 'desc')->get();
        $pinjamanList = Pinjaman::where('id_anggota', $id)->orderBy('created_at', 'desc')->get();
        $angsuranList = Angsuran::with('pinjaman')->where('id_anggota', $id)->orderBy('created_at', 'desc')->get();

        // 3. Gabungan Riwayat Seluruh Transaksi (Simpanan + Angsuran)
        $riwayatSimpanan = $simpananList->map(function ($item) {
            return [
                'tanggal' => $item->created_at,
                'kode' => $item->id_simpanan,
                'tipe' => 'Simpanan ' . $item->jenis_simpanan,
                'nominal' => $item->nominal,
                'status' => 'Selesai',
                'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200'
            ];
        });

        $riwayatAngsuran = $angsuranList->map(function ($item) {
            return [
                'tanggal' => $item->created_at,
                'kode' => $item->id_angsuran,
                'tipe' => 'Angsuran Pinjaman',
                'nominal' => $item->total_bayar,
                'status' => 'Selesai',
                'badge' => 'bg-blue-50 text-blue-700 border-blue-200'
            ];
        });

        $semuaTransaksi = $riwayatSimpanan->concat($riwayatAngsuran)->sortByDesc('tanggal');

        // 4. Riwayat Singkat untuk Tab Overview
        $simpananTerbaru = $simpananList->take(3);
        $pinjamanTerbaru = $pinjamanList->take(2);

        return view('ketua.anggota.show', compact(
            'anggota',
            'totalSimpanan',
            'pinjamanAktif',
            'sisaPinjaman',
            'totalAngsuran',
            'simpananList',
            'pinjamanList',
            'angsuranList',
            'semuaTransaksi',
            'simpananTerbaru',
            'pinjamanTerbaru'
        ));
    }
}
