<?php

use App\Http\Controllers\Ketua\ApprovalController as KetuaApprovalController;
use App\Http\Controllers\Ketua\AnggotaController as KetuaAnggotaController;
use App\Http\Controllers\Ketua\AngsuranController as KetuaAngsuranController;
use App\Http\Controllers\Ketua\DashboardController as KetuaDashboardController;
use App\Http\Controllers\Ketua\LaporanController as KetuaLaporanController;
use App\Http\Controllers\Ketua\PinjamanController as KetuaPinjamanController;
use App\Http\Controllers\Ketua\SimpananController as KetuaSimpananController;

use App\Http\Controllers\Pengurus\DashboardController as PengurusDashboardController;
use App\Http\Controllers\Pengurus\LaporanController;
use App\Http\Controllers\Pengurus\AnggotaController;
use App\Http\Controllers\Pengurus\AngsuranController;
use App\Http\Controllers\Pengurus\BukuBesarController;
use App\Http\Controllers\Pengurus\JurnalController;
use App\Http\Controllers\Pengurus\PinjamanController;
use App\Http\Controllers\Pengurus\SimpananController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\CooperativeLogoController;
use App\Http\Controllers\Ketua\CooperativeSettingController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect Root ke Login
Route::get('/', function () {
    return redirect()->route('login');
});
Route::get('/cooperative-logo', CooperativeLogoController::class)->name('cooperative.logo');

// Dashboard Router Berdasarkan Role
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

/*
|--------------------------------------------------------------------------
| Admin / Pengurus Routes (Operasional & Input Data)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:Admin'])->prefix('pengurus')->name('pengurus.')->group(function () {
    Route::get('/export/{report}/{format}', ExportController::class)->name('export');
    Route::get('/log-aktivitas', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('/dashboard', [PengurusDashboardController::class, 'index'])->name('dashboard');

    // Modul Operasional Pengurus
    Route::resource('anggota', AnggotaController::class)->only(['index', 'store']);
    Route::resource('simpanan', SimpananController::class)->only(['index', 'store']);
    Route::resource('pinjaman', PinjamanController::class)->only(['index', 'store']);
    Route::resource('angsuran', AngsuranController::class)->only(['index', 'store']);

    // Modul Akuntansi Pengurus
    Route::get('/jurnal', [JurnalController::class, 'index'])->name('jurnal.index');
    Route::post('/jurnal', [JurnalController::class, 'store'])->name('jurnal.store');
    Route::post('/jurnal/{id}/reversal', [JurnalController::class, 'reversal'])->name('jurnal.reversal');
    Route::get('/buku-besar', [BukuBesarController::class, 'index'])->name('buku-besar.index');
    Route::get('/jurnal/{id}/detail', [JurnalController::class, 'getDetail'])->name('jurnal.detail');

    // Menu Laporan Pengurus
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/', [LaporanController::class, 'index'])->name('index');
        Route::get('/simpanan', [LaporanController::class, 'simpanan'])->name('simpanan');
        Route::get('/pinjaman', [LaporanController::class, 'pinjaman'])->name('pinjaman');
        Route::get('/angsuran', [LaporanController::class, 'angsuran'])->name('angsuran');
    });

    // Di dalam grup Pengurus: Route::middleware(['auth', 'role:Admin'])->prefix('pengurus')->name('pengurus.')...
    Route::get('/neraca-saldo', [\App\Http\Controllers\Pengurus\AkuntansiLanjutanController::class, 'neracaSaldo'])->name('neraca-saldo.index');
    Route::get('/laba-rugi', [\App\Http\Controllers\Pengurus\AkuntansiLanjutanController::class, 'labaRugi'])->name('laba-rugi.index');
    });

/*
|--------------------------------------------------------------------------
| Ketua Koperasi Routes (Monitoring, Audit & Approval)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:Ketua'])->prefix('ketua')->name('ketua.')->group(function () {
    Route::get('/export/{report}/{format}', ExportController::class)->name('export');
    Route::get('/log-aktivitas', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('/pengaturan-identitas', [CooperativeSettingController::class, 'edit'])->name('settings.identity.edit');
    Route::put('/pengaturan-identitas', [CooperativeSettingController::class, 'update'])->name('settings.identity.update');
    Route::get('/dashboard', [KetuaDashboardController::class, 'index'])->name('dashboard');

    // Monitoring read-only untuk anggota.
    Route::prefix('anggota')->name('anggota.')->group(function () {
        Route::get('/', [KetuaAnggotaController::class, 'index'])->name('index');
        Route::get('/{id}', [KetuaAnggotaController::class, 'show'])->name('show');
    });

    // Monitoring read-only untuk simpanan.
    Route::prefix('simpanan')->name('simpanan.')->group(function () {
        Route::get('/', [KetuaSimpananController::class, 'index'])->name('index');
    });

    // Ketua dapat memeriksa pinjaman dan menyetujui atau menolaknya.
    Route::prefix('pinjaman')->name('pinjaman.')->group(function () {
        Route::get('/', [KetuaPinjamanController::class, 'index'])->name('index');
        Route::get('/{id}', [KetuaPinjamanController::class, 'show'])->name('show');
        Route::post('/{id}/approve', [KetuaPinjamanController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [KetuaPinjamanController::class, 'reject'])->name('reject');
    });

    // Approval panel hanya menangani keputusan atas pinjaman.
    Route::get('/approval', [KetuaApprovalController::class, 'index'])->name('approval.index');
    Route::post('/approval/{id}/approve', [KetuaApprovalController::class, 'approve'])->name('approval.approve');
    Route::post('/approval/{id}/reject', [KetuaApprovalController::class, 'reject'])->name('approval.reject');

    Route::get('/angsuran', [KetuaAngsuranController::class, 'index'])->name('angsuran.index');

    // Akuntansi & Laporan Ketua
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/', [KetuaLaporanController::class, 'index'])->name('index');
        Route::get('/jurnal-umum', [KetuaLaporanController::class, 'jurnalUmum'])->name('jurnal');
        Route::get('/jurnal-umum/{id}/detail', [KetuaLaporanController::class, 'getJurnalDetail'])->name('jurnal.detail');
        Route::get('/buku-besar', [KetuaLaporanController::class, 'bukuBesar'])->name('buku-besar');
        Route::get('/simpanan', [KetuaLaporanController::class, 'simpanan'])->name('simpanan');
        Route::get('/pinjaman', [KetuaLaporanController::class, 'pinjaman'])->name('pinjaman');
        Route::get('/angsuran', [KetuaLaporanController::class, 'angsuran'])->name('angsuran');
        Route::get('/anggota', [KetuaLaporanController::class, 'anggota'])->name('anggota');
    });

    // Di dalam grup Ketua: Route::middleware(['auth', 'role:Ketua'])->prefix('ketua')->name('ketua.')->prefix('laporan')->name('laporan.')...
    Route::get('/neraca-saldo', [\App\Http\Controllers\Ketua\LaporanController::class, 'neracaSaldo'])->name('neraca-saldo');
    Route::get('/laba-rugi', [\App\Http\Controllers\Ketua\LaporanController::class, 'labaRugi'])->name('laba-rugi');
    });

/*
|--------------------------------------------------------------------------
| User Profile Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::middleware('role:Admin')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });
});

require __DIR__.'/auth.php';
