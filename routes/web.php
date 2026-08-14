<?php

use App\Http\Controllers\Ketua\ApprovalController as KetuaApprovalController;
use App\Http\Controllers\Ketua\LaporanController as KetuaLaporanController;
use App\Http\Controllers\Ketua\DashboardController as KetuaDashboardController;
use App\Http\Controllers\Pengurus\AnggotaController;
use App\Http\Controllers\Pengurus\AngsuranController;
use App\Http\Controllers\Pengurus\BukuBesarController;
use App\Http\Controllers\Pengurus\JurnalController;
use App\Http\Controllers\Pengurus\PinjamanController;
use App\Http\Controllers\Pengurus\SimpananController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    $user = Auth::user();
    if ($user->peran === 'Admin') {
        return redirect()->route('pengurus.dashboard');
    }
    if ($user->peran === 'Ketua') {
        return redirect()->route('ketua.dashboard');
    }
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Admin / Pengurus Routes
Route::middleware(['auth', 'role:Admin'])->prefix('pengurus')->name('pengurus.')->group(function () {
    Route::get('/dashboard', function () {
        return view('pengurus.dashboard');
    })->name('dashboard');

    Route::resource('anggota', AnggotaController::class)->only(['index', 'store']);
    Route::resource('simpanan', SimpananController::class)->only(['index', 'store']);
    Route::resource('pinjaman', PinjamanController::class)->only(['index', 'store']);
    Route::resource('angsuran', AngsuranController::class)->only(['index', 'store']);

    // Modul Akuntansi Pengurus
    Route::get('/jurnal', [JurnalController::class, 'index'])->name('jurnal.index');
    Route::post('/jurnal', [JurnalController::class, 'store'])->name('jurnal.store');
    Route::post('/jurnal/{id}/reversal', [JurnalController::class, 'reversal'])->name('jurnal.reversal');
    Route::get('/buku-besar', [BukuBesarController::class, 'index'])->name('buku-besar.index');
});

// Ketua Koperasi Routes
Route::middleware(['auth', 'role:Ketua'])->prefix('ketua')->name('ketua.')->group(function () {
    Route::get('/dashboard', function () {
        return view('ketua.dashboard');
    })->name('dashboard');

    Route::get('/approval', [KetuaApprovalController::class, 'index'])->name('approval.index');
    Route::post('/approval/{id}/approve', [KetuaApprovalController::class, 'approve'])->name('approval.approve');
    Route::post('/approval/{id}/reject', [KetuaApprovalController::class, 'reject'])->name('approval.reject');

    // Laporan Ketua Koperasi
    Route::get('/laporan/anggota', [KetuaLaporanController::class, 'anggota'])->name('laporan.anggota');
    Route::get('/laporan/simpanan', [KetuaLaporanController::class, 'simpanan'])->name('laporan.simpanan');
    Route::get('/laporan/pinjaman', [KetuaLaporanController::class, 'pinjaman'])->name('laporan.pinjaman');
    Route::get('/laporan/jurnal-umum', [KetuaLaporanController::class, 'jurnalUmum'])->name('laporan.jurnal');
    Route::get('/laporan/buku-besar', [KetuaLaporanController::class, 'bukuBesar'])->name('laporan.buku-besar');

    Route::get('/dashboard', [KetuaDashboardController::class, 'index'])->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';