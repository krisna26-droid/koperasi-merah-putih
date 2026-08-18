<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Ketua - Koperasi Merah Putih</title>
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
            @include('layouts.navbar-ketua')

            <main class="p-8 space-y-6">
                <!-- Header Greeting -->
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider">
                            {{ strtoupper(\Carbon\Carbon::now()->translatedFormat('l, d F Y')) }}
                        </span>
                        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight mt-0.5">
                            Selamat datang, {{ Auth::user()->name }}
                        </h1>
                        <p class="text-xs text-slate-500 mt-1">Pantau kesehatan koperasi dan persetujuan yang membutuhkan perhatian.</p>
                    </div>

                    <div class="flex items-center space-x-2 bg-white px-3.5 py-2 rounded-xl border border-slate-200/80 shadow-sm text-xs font-semibold text-slate-700">
                        <i class="fi fi-rr-calendar text-slate-400 text-sm flex items-center"></i>
                        <span>{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</span>
                    </div>
                </div>

                <!-- 4 Top Stat Cards (Dinamis Nilai & Tren MoM) -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Total Anggota -->
                    <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs font-semibold text-slate-400">Total anggota</p>
                                <h3 class="text-2xl font-extrabold text-slate-800 mt-1">{{ number_format($totalAnggota) }}</h3>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                                <i class="fi fi-rr-users text-lg"></i>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-[11px] font-semibold {{ $trenAnggota['is_up'] ? 'text-emerald-600' : 'text-rose-600' }}">
                            <span>{{ $trenAnggota['label'] }}</span>
                            <span class="text-slate-400 font-normal ml-1">dari bulan lalu</span>
                        </div>
                    </div>

                    <!-- Total Simpanan -->
                    <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs font-semibold text-slate-400">Total simpanan</p>
                                <h3 class="text-2xl font-extrabold text-slate-800 mt-1">
                                    @if($totalSimpanan >= 1000000000)
                                        Rp {{ number_format($totalSimpanan / 1000000000, 2, ',', '.') }} M
                                    @else
                                        Rp {{ number_format($totalSimpanan / 1000000, 1, ',', '.') }} Jt
                                    @endif
                                </h3>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                <i class="fi fi-rr-envelope text-lg"></i>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-[11px] font-semibold {{ $trenSimpanan['is_up'] ? 'text-emerald-600' : 'text-rose-600' }}">
                            <span>{{ $trenSimpanan['label'] }}</span>
                            <span class="text-slate-400 font-normal ml-1">dari bulan lalu</span>
                        </div>
                    </div>

                    <!-- Total Pinjaman -->
                    <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs font-semibold text-slate-400">Total pinjaman</p>
                                <h3 class="text-2xl font-extrabold text-slate-800 mt-1">
                                    Rp {{ number_format($totalPinjaman / 1000000, 1, ',', '.') }} Jt
                                </h3>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                                <i class="fi fi-rr-usd-circle text-lg"></i>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-[11px] font-semibold {{ $trenPinjaman['is_up'] ? 'text-emerald-600' : 'text-rose-600' }}">
                            <span>{{ $trenPinjaman['label'] }}</span>
                            <span class="text-slate-400 font-normal ml-1">dari bulan lalu</span>
                        </div>
                    </div>

                    <!-- Total Angsuran -->
                    <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs font-semibold text-slate-400">Total angsuran</p>
                                <h3 class="text-2xl font-extrabold text-slate-800 mt-1">
                                    Rp {{ number_format($totalAngsuran / 1000000, 1, ',', '.') }} Jt
                                </h3>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
                                <i class="fi fi-rr-receipt text-lg"></i>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-[11px] font-semibold {{ $trenAngsuran['is_up'] ? 'text-emerald-600' : 'text-rose-600' }}">
                            <span>{{ $trenAngsuran['label'] }}</span>
                            <span class="text-slate-400 font-normal ml-1">dari bulan lalu</span>
                        </div>
                    </div>
                </div>

                <!-- Middle Section: Menunggu Persetujuan & Grafik Dinamis -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    <!-- Sisi Kiri: Menunggu Persetujuan -->
                    <div class="lg:col-span-7 bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-sm font-bold text-slate-800">Menunggu persetujuan</h3>
                                    <p class="text-xs text-slate-400 mt-0.5">Pengajuan pinjaman yang membutuhkan keputusan Anda</p>
                                </div>
                                <span class="bg-amber-50 text-amber-700 text-[11px] font-bold px-2.5 py-1 rounded-full border border-amber-200/60">
                                    {{ $menungguPersetujuan->count() }} pengajuan
                                </span>
                            </div>

                            <div class="mt-6 space-y-4">
                                @forelse($menungguPersetujuan as $pengajuan)
                                    <div class="flex items-center justify-between p-4 rounded-xl bg-slate-50/50 border border-slate-100">
                                        <div class="flex items-center space-x-3.5">
                                            <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-xs">
                                                {{ strtoupper(substr($pengajuan->anggota->nama, 0, 2)) }}
                                            </div>
                                            <div>
                                                <h4 class="text-xs font-bold text-slate-800">{{ $pengajuan->anggota->nama }}</h4>
                                                <p class="text-[11px] text-slate-400">{{ $pengajuan->id_pinjam }} · {{ $pengajuan->created_at->translatedFormat('d F Y') }}</p>
                                                <p class="text-[11px] text-slate-500 font-medium mt-0.5">Modal usaha · tenor {{ $pengajuan->tenor }} bulan</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-extrabold text-slate-800">Rp {{ number_format($pengajuan->jumlah_pinjam / 1000000, 1, ',', '.') }} Jt</p>
                                            <div class="flex items-center space-x-2 mt-2">
                                                <form action="{{ route('ketua.pinjaman.approve', $pengajuan->id_pinjam) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-bold px-3 py-1 rounded-lg shadow-sm transition">
                                                        ✓ Setujui
                                                    </button>
                                                </form>
                                                <form action="{{ route('ketua.pinjaman.reject', $pengajuan->id_pinjam) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="bg-white hover:bg-slate-100 text-slate-600 border border-slate-200 text-[11px] font-semibold px-3 py-1 rounded-lg transition">
                                                        ✕ Tolak
                                                    </button>
                                                </form>
                                                <a href="{{ route('ketua.pinjaman.show', $pengajuan->id_pinjam) }}" class="text-[11px] font-semibold text-slate-400 hover:text-slate-600 px-1">Detail</a>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="p-8 text-center text-xs text-slate-400 bg-slate-50/50 rounded-xl border border-dashed border-slate-200">
                                        Tidak ada permohonan pinjaman baru yang menunggu persetujuan.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Sisi Kanan: Grafik Batang 6 Bulan Dinamis -->
                    <div class="lg:col-span-5 bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-sm font-bold text-slate-800">Ringkasan keuangan</h3>
                                    <p class="text-xs text-slate-400 mt-0.5">Performa 6 bulan terakhir</p>
                                </div>
                            </div>

                            <div class="flex items-center space-x-4 mt-4 text-[11px] font-semibold text-slate-500">
                                <div class="flex items-center space-x-1.5">
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                    <span>Simpanan Masuk</span>
                                </div>
                            </div>

                            <!-- Bar Chart CSS Dinamis Proporsional -->
                            <div class="mt-6 space-y-2">
                                <div class="flex items-end justify-between h-40 pt-4 border-b border-slate-100 px-2 gap-2">
                                    @foreach($chartData as $cd)
                                        <div class="w-full flex flex-col items-center group relative h-full justify-end">
                                            <!-- Tooltip Nilai -->
                                            <div class="absolute -top-7 opacity-0 group-hover:opacity-100 transition-opacity bg-slate-800 text-white text-[10px] font-bold px-2 py-0.5 rounded shadow pointer-events-none whitespace-nowrap z-20">
                                                Rp {{ number_format($cd['simpanan'], 0, ',', '.') }}
                                            </div>
                                            <div class="w-full bg-emerald-400 hover:bg-emerald-500 rounded-t transition-all duration-300"
                                                 style="height: {{ $cd['height_simpanan'] }}%;"></div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="flex justify-between text-[11px] text-slate-400 font-semibold px-2">
                                    @foreach($chartData as $cd)
                                        <span>{{ $cd['label'] }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Section: Transaksi & Akses Cepat -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    <div class="lg:col-span-7 bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-sm font-bold text-slate-800">Transaksi terbaru</h3>
                                <p class="text-xs text-slate-400 mt-0.5">Aktivitas operasional terkini</p>
                            </div>
                        </div>

                        <div class="space-y-3">
                            @forelse($transaksiTerbaru as $tx)
                                <div class="flex items-center justify-between py-2.5 border-b border-slate-50 last:border-none">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                            <i class="fi fi-rr-envelope text-sm"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-xs font-bold text-slate-800">{{ $tx->anggota->nama ?? 'Anggota' }}</h4>
                                            <p class="text-[11px] text-slate-400">{{ $tx->jenis_simpanan }} · {{ $tx->id_simpanan }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xs font-extrabold text-slate-800">+Rp {{ number_format($tx->nominal, 0, ',', '.') }}</p>
                                        <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md">Selesai</span>
                                    </div>
                                </div>
                            @empty
                                <p class="text-xs text-slate-400 py-4 text-center">Belum ada riwayat transaksi.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="lg:col-span-5 bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between">
                        <div>
                            <h3 class="text-sm font-bold text-slate-800">Akses cepat</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Mulai pekerjaan yang sering digunakan</p>

                            <div class="grid grid-cols-2 gap-3 mt-4">
                                <a href="{{ route('ketua.pinjaman.index') }}" class="p-4 rounded-xl border border-slate-100 bg-slate-50/50 hover:bg-slate-100/60 transition block text-left">
                                    <div class="w-8 h-8 rounded-lg bg-white border border-slate-200/60 text-slate-600 flex items-center justify-center mb-3">
                                        <i class="fi fi-rr-checkbox text-sm"></i>
                                    </div>
                                    <p class="text-xs font-bold text-slate-800">Review pinjaman</p>
                                </a>

                                <a href="{{ route('ketua.anggota.index') }}" class="p-4 rounded-xl border border-slate-100 bg-slate-50/50 hover:bg-slate-100/60 transition block text-left">
                                    <div class="w-8 h-8 rounded-lg bg-white border border-slate-200/60 text-slate-600 flex items-center justify-center mb-3">
                                        <i class="fi fi-rr-users text-sm"></i>
                                    </div>
                                    <p class="text-xs font-bold text-slate-800">Lihat anggota</p>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>