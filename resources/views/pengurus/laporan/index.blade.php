<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pusat Laporan Operasional - Koperasi Merah Putih</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.1.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50/60 antialiased">
    <div class="flex min-h-screen">
        @include('layouts.sidebar-pengurus')

        <div class="flex-1 flex flex-col min-w-0">
            <!-- Header Navbar -->
            <header class="bg-white border-b border-slate-100 py-3.5 px-8 flex items-center justify-between select-none">
                <div class="flex items-center space-x-2 text-xs text-slate-400">
                    <span>Koperasi Merah Putih</span>
                    <span>&rsaquo;</span>
                    <span class="text-slate-600 font-medium">Laporan Operasional</span>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-xs ring-2 ring-emerald-50">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <div class="text-left">
                        <p class="text-xs font-bold text-slate-800 leading-tight">{{ Auth::user()->name }}</p>
                        <p class="text-[10px] text-slate-400">Admin Pengurus</p>
                    </div>
                </div>
            </header>

            <main class="p-8 space-y-6">
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Pusat Laporan Operasional</h1>
                    <p class="text-xs text-slate-500 mt-1">Pilih rekap laporan operasional untuk ditinjau dan dicetak.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Kartu Laporan Simpanan -->
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between space-y-4 hover:border-emerald-500/30 transition">
                        <div class="space-y-2">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg">
                                <i class="fi fi-rr-envelope"></i>
                            </div>
                            <h3 class="text-base font-bold text-slate-800">Laporan Simpanan</h3>
                            <p class="text-xs text-slate-400 leading-relaxed">Rekap setoran simpanan pokok, wajib, dan sukarela anggota.</p>
                        </div>
                        <div class="pt-4 border-t border-slate-50 flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-800">Rp {{ number_format($totalSimpanan, 0, ',', '.') }}</span>
                            <a href="{{ route('pengurus.laporan.simpanan') }}" class="px-3.5 py-1.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition">
                                Buka Rekap &rarr;
                            </a>
                        </div>
                    </div>

                    <!-- Kartu Laporan Pinjaman -->
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between space-y-4 hover:border-emerald-500/30 transition">
                        <div class="space-y-2">
                            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg">
                                <i class="fi fi-rr-dollar"></i>
                            </div>
                            <h3 class="text-base font-bold text-slate-800">Laporan Pinjaman</h3>
                            <p class="text-xs text-slate-400 leading-relaxed">Rekap penyaluran plafond kredit dan sisa kewajiban anggota.</p>
                        </div>
                        <div class="pt-4 border-t border-slate-50 flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-800">Rp {{ number_format($totalPinjaman, 0, ',', '.') }}</span>
                            <a href="{{ route('pengurus.laporan.pinjaman') }}" class="px-3.5 py-1.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition">
                                Buka Rekap &rarr;
                            </a>
                        </div>
                    </div>

                    <!-- Kartu Laporan Angsuran -->
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between space-y-4 hover:border-emerald-500/30 transition">
                        <div class="space-y-2">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
                                <i class="fi fi-rr-receipt"></i>
                            </div>
                            <h3 class="text-base font-bold text-slate-800">Laporan Angsuran</h3>
                            <p class="text-xs text-slate-400 leading-relaxed">Rekap pelunasan cicilan pokok dan pendapatan bunga kredit.</p>
                        </div>
                        <div class="pt-4 border-t border-slate-50 flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-800">Rp {{ number_format($totalAngsuran, 0, ',', '.') }}</span>
                            <a href="{{ route('pengurus.laporan.angsuran') }}" class="px-3.5 py-1.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition">
                                Buka Rekap &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>