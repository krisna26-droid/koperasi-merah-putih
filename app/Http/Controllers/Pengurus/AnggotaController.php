<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAnggotaRequest;
use App\Models\Anggota;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AnggotaController extends Controller
{
    public function index(): View
    {
        $anggota = Anggota::with('creator')->latest()->paginate(10);
        return view('pengurus.anggota.index', compact('anggota'));
    }

    public function store(StoreAnggotaRequest $request): RedirectResponse
    {
        Anggota::create([
            'id_anggota' => $request->id_anggota,
            'nama' => $request->nama,
            'alamat' => $request->alamat,
            'no_telepon' => $request->no_telepon,
            'status' => $request->status,
            'created_by' => Auth::user()->username,
        ]);

        return redirect()->route('pengurus.anggota.index')->with('success', 'Data Anggota berhasil ditambahkan.');
    }
}