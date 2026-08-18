<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pusat Laporan - Koperasi Merah Putih</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.1.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.1.0/uicons-solid-rounded/css/uicons-solid-rounded.css'>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50/60 antialiased">
    <div class="flex min-h-screen">
        @include('layouts.sidebar-ketua')

        <div class="flex-1 flex flex-col min-w-0">
            <!-- Header Navbar -->
            <header class="bg-white border-b border-slate-100 py-3.5 px-8 flex items-center justify-between select-none font-sans">
                <div>
                    <div class="flex items-center space-x-2 text-xs text-slate-400">
                        <span>Koperasi Merah Putih</span>
                        <span>&rsaquo;</span>
                        <span class="text-slate-600 font-medium">Laporan</span>
                    </div>
                    <h2 class="text-base font-bold text-slate-800 mt-0.5">Laporan</h2>
                </div>

                <div class="flex items-center space-x-5">
                    <button class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-50 rounded-full transition-colors">
                        <i class="fi fi-rr-search flex items-center text-sm"></i>
                    </button>
                    <div class="h-6 w-px bg-slate-100"></div>
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-xs ring-2 ring-emerald-50">
                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                        </div>
                        <div class="text-left">
                            <p class="text-xs font-bold text-slate-800 leading-tight">{{ Auth::user()->name }}</p>
                            <p class="text-[10px] text-slate-400">Ketua Koperasi</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="ml-2">
                            @csrf
                            <button type="submit" class="text-slate-400 hover:text-rose-600 p-1.5 rounded-lg transition-colors flex items-center" title="Keluar">
                                <i class="fi fi-rr-sign-out-alt text-sm"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="p-8 space-y-6">
                <!-- Title Header & Filter Periode Button -->
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Pusat Laporan</h1>
                        <p class="text-xs text-slate-500 mt-1">Akses, pratinjau, dan ekspor laporan koperasi.</p>
                    </div>

                    <button type="button" class="px-3.5 py-2 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-bold rounded-xl transition flex items-center space-x-2 shadow-sm">
                        <i class="fi fi-rr-calendar text-xs text-slate-400"></i>
                        <span>Pilih periode</span>
                    </button>
                </div>

                <!-- Badges Filter Aktif -->
                <div class="flex items-center space-x-2.5">
                    <span class="px-3 py-1 bg-blue-50 text-blue-600 text-xs font-bold rounded-full border border-blue-100">
                        Periode aktif: {{ $periodeAktif }}
                    </span>
                    <span class="text-xs text-slate-400 font-medium">
                        Terakhir diperbarui: hari ini, {{ $terakhirDiperbarui }}
                    </span>
                </div>

                <!-- Grid Kartu Laporan (6 Modul) -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    <!-- 1. Laporan Anggota -->
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between hover:border-slate-200 transition">
                        <div>
                            <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-4">
                                <i class="fi fi-rr-users text-xl"></i>
                            </div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">LAPORAN ANGGOTA</span>
                            <h3 class="text-base font-extrabold text-slate-800 mt-1">Daftar anggota</h3>
                            <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                                Daftar lengkap anggota berdasarkan status dan periode.
                            </p>
                        </div>
                        <div class="flex items-center space-x-3 mt-6 pt-4 border-t border-slate-50">
                            <a href="{{ route('ketua.laporan.anggota') }}" class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold rounded-xl transition flex items-center space-x-1.5">
                                <i class="fi fi-rr-eye text-xs"></i>
                                <span>Pratinjau</span>
                            </a>
                            <a href="{{ route('ketua.laporan.anggota') }}" class="px-3 py-1.5 bg-slate-50 hover:bg-slate-100 text-slate-600 text-xs font-semibold rounded-xl transition flex items-center space-x-1.5">
                                <i class="fi fi-rr-download text-xs text-slate-400"></i>
                                <span>Export</span>
                            </a>
                        </div>
                    </div>

                    <!-- 2. Laporan Simpanan -->
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between hover:border-slate-200 transition">
                        <div>
                            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-4">
                                <i class="fi fi-rr-envelope text-xl"></i>
                            </div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">LAPORAN SIMPANAN</span>
                            <h3 class="text-base font-extrabold text-slate-800 mt-1">Rekap simpanan</h3>
                            <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                                Ringkasan simpanan pokok, wajib, dan sukarela.
                            </p>
                        </div>
                        <div class="flex items-center space-x-3 mt-6 pt-4 border-t border-slate-50">
                            <a href="{{ route('ketua.laporan.simpanan') }}" class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold rounded-xl transition flex items-center space-x-1.5">
                                <i class="fi fi-rr-eye text-xs"></i>
                                <span>Pratinjau</span>
                            </a>
                            <a href="{{ route('ketua.laporan.simpanan') }}" class="px-3 py-1.5 bg-slate-50 hover:bg-slate-100 text-slate-600 text-xs font-semibold rounded-xl transition flex items-center space-x-1.5">
                                <i class="fi fi-rr-download text-xs text-slate-400"></i>
                                <span>Export</span>
                            </a>
                        </div>
                    </div>

                    <!-- 3. Laporan Pinjaman -->
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between hover:border-slate-200 transition">
                        <div>
                            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center mb-4">
                                <i class="fi fi-rr-handshake text-xl"></i>
                            </div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">LAPORAN PINJAMAN</span>
                            <h3 class="text-base font-extrabold text-slate-800 mt-1">Pinjaman aktif</h3>
                            <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                                Daftar pinjaman aktif beserta sisa kewajiban.
                            </p>
                        </div>
                        <div class="flex items-center space-x-3 mt-6 pt-4 border-t border-slate-50">
                            <a href="{{ route('ketua.laporan.pinjaman') }}" class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold rounded-xl transition flex items-center space-x-1.5">
                                <i class="fi fi-rr-eye text-xs"></i>
                                <span>Pratinjau</span>
                            </a>
                            <a href="{{ route('ketua.laporan.pinjaman') }}" class="px-3 py-1.5 bg-slate-50 hover:bg-slate-100 text-slate-600 text-xs font-semibold rounded-xl transition flex items-center space-x-1.5">
                                <i class="fi fi-rr-download text-xs text-slate-400"></i>
                                <span>Export</span>
                            </a>
                        </div>
                    </div>

                    <!-- 4. Laporan Angsuran -->
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between hover:border-slate-200 transition">
                        <div>
                            <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-4">
                                <i class="fi fi-rr-receipt text-xl"></i>
                            </div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">LAPORAN ANGSURAN</span>
                            <h3 class="text-base font-extrabold text-slate-800 mt-1">Rekap angsuran</h3>
                            <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                                Rekap penerimaan angsuran pada periode terpilih.
                            </p>
                        </div>
                        <div class="flex items-center space-x-3 mt-6 pt-4 border-t border-slate-50">
                            <a href="{{ route('ketua.laporan.angsuran') }}" class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold rounded-xl transition flex items-center space-x-1.5">
                                <i class="fi fi-rr-eye text-xs"></i>
                                <span>Pratinjau</span>
                            </a>
                            <a href="{{ route('ketua.laporan.angsuran') }}" class="px-3 py-1.5 bg-slate-50 hover:bg-slate-100 text-slate-600 text-xs font-semibold rounded-xl transition flex items-center space-x-1.5">
                                <i class="fi fi-rr-download text-xs text-slate-400"></i>
                                <span>Export</span>
                            </a>
                        </div>
                    </div>

                    <!-- 5. Laporan Jurnal Umum -->
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between hover:border-slate-200 transition">
                        <div>
                            <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center mb-4">
                                <i class="fi fi-rr-book-alt text-xl"></i>
                            </div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">LAPORAN AKUNTANSI</span>
                            <h3 class="text-base font-extrabold text-slate-800 mt-1">Jurnal umum</h3>
                            <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                                Catatan transaksi jurnal umum dan keseimbangannya.
                            </p>
                        </div>
                        <div class="flex items-center space-x-3 mt-6 pt-4 border-t border-slate-50">
                            <a href="{{ route('ketua.laporan.jurnal') }}" class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold rounded-xl transition flex items-center space-x-1.5">
                                <i class="fi fi-rr-eye text-xs"></i>
                                <span>Pratinjau</span>
                            </a>
                            <a href="{{ route('ketua.laporan.jurnal') }}" class="px-3 py-1.5 bg-slate-50 hover:bg-slate-100 text-slate-600 text-xs font-semibold rounded-xl transition flex items-center space-x-1.5">
                                <i class="fi fi-rr-download text-xs text-slate-400"></i>
                                <span>Export</span>
                            </a>
                        </div>
                    </div>

                    <!-- 6. Laporan Buku Besar -->
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between hover:border-slate-200 transition">
                        <div>
                            <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center mb-4">
                                <i class="fi fi-rr-document text-xl"></i>
                            </div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">LAPORAN AKUNTANSI</span>
                            <h3 class="text-base font-extrabold text-slate-800 mt-1">Buku besar</h3>
                            <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                                Pergerakan saldo pada setiap akun perkiraan.
                            </p>
                        </div>
                        <div class="flex items-center space-x-3 mt-6 pt-4 border-t border-slate-50">
                            <a href="{{ route('ketua.laporan.buku-besar') }}" class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold rounded-xl transition flex items-center space-x-1.5">
                                <i class="fi fi-rr-eye text-xs"></i>
                                <span>Pratinjau</span>
                            </a>
                            <a href="{{ route('ketua.laporan.buku-besar') }}" class="px-3 py-1.5 bg-slate-50 hover:bg-slate-100 text-slate-600 text-xs font-semibold rounded-xl transition flex items-center space-x-1.5">
                                <i class="fi fi-rr-download text-xs text-slate-400"></i>
                                <span>Export</span>
                            </a>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>