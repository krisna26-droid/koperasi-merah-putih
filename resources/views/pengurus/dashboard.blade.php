<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin Pengurus - {{ $cooperativeSettings->name ?? 'Koperasi Merah Putih' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.1.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.1.0/uicons-solid-rounded/css/uicons-solid-rounded.css'>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50/60 antialiased">
    <div class="flex min-h-screen">
        @include('layouts.sidebar-pengurus')

        <div class="flex-1 flex flex-col min-w-0">
            <!-- Header Navbar -->
            <x-page-header title="Ringkasan hari ini" section="Dashboard" />

            <main class="p-8 space-y-6">
                <!-- Welcome Banner & Tanggal -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider">
                            {{ now()->translatedFormat('l, d F Y') }}
                        </span>
                        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight mt-0.5">
                            Selamat datang, Bapak {{ explode(' ', Auth::user()->name)[0] }}
                        </h1>
                        <p class="text-xs text-slate-500 mt-1">Berikut ringkasan aktivitas koperasi hari ini.</p>
                    </div>

                    <div class="px-3.5 py-2 bg-white border border-slate-200 text-slate-700 text-xs font-bold rounded-xl transition flex items-center space-x-2 shadow-sm w-fit">
                        <i class="fi fi-rr-calendar text-xs text-slate-400"></i>
                        <span>{{ now()->translatedFormat('d F Y') }}</span>
                    </div>
                </div>

                <!-- 4 Stat Cards Utama -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Total Anggota -->
                    <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <p class="text-xs font-semibold text-slate-400">Total anggota</p>
                            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                                <i class="fi fi-rr-users text-lg"></i>
                            </div>
                        </div>
                        <div class="mt-2">
                            <h3 class="text-2xl font-extrabold text-slate-800">{{ number_format($totalAnggota) }}</h3>
                            <p class="text-[11px] text-emerald-600 font-semibold mt-1 flex items-center space-x-1">
                                <i class="fi fi-rr-arrow-trend-up text-[10px]"></i>
                                <span>{{ $growthAnggota }}% dari bulan lalu</span>
                            </p>
                        </div>
                    </div>

                    <!-- Pinjaman Aktif -->
                    <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <p class="text-xs font-semibold text-slate-400">Pinjaman aktif</p>
                            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                                <i class="fi fi-rr-handshake text-lg"></i>
                            </div>
                        </div>
                        <div class="mt-2">
                            <h3 class="text-2xl font-extrabold text-slate-800">{{ number_format($pinjamanAktif) }}</h3>
                            <p class="text-[11px] text-emerald-600 font-semibold mt-1 flex items-center space-x-1">
                                <i class="fi fi-rr-arrow-trend-up text-[10px]"></i>
                                <span>{{ $growthPinjaman }}% dari bulan lalu</span>
                            </p>
                        </div>
                    </div>

                    <!-- Angsuran Hari Ini -->
                    <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <p class="text-xs font-semibold text-slate-400">Angsuran hari ini</p>
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                <i class="fi fi-rr-money-bill-wave text-lg"></i>
                            </div>
                        </div>
                        <div class="mt-2">
                            <h3 class="text-2xl font-extrabold text-slate-800">
                                @if($angsuranHariIni >= 1000000)
                                    Rp {{ number_format($angsuranHariIni / 1000000, 2, ',', '.') }} Jt
                                @else
                                    Rp {{ number_format($angsuranHariIni, 0, ',', '.') }}
                                @endif
                            </h3>
                            <p class="text-[11px] text-emerald-600 font-semibold mt-1 flex items-center space-x-1">
                                <i class="fi fi-rr-arrow-trend-up text-[10px]"></i>
                                <span>11,2% dari bulan lalu</span>
                            </p>
                        </div>
                    </div>

                    <!-- Transaksi Tertunda -->
                    <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <p class="text-xs font-semibold text-slate-400">Transaksi tertunda</p>
                            <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
                                <i class="fi fi-rr-clipboard-list text-lg"></i>
                            </div>
                        </div>
                        <div class="mt-2">
                            <h3 class="text-2xl font-extrabold text-slate-800">{{ number_format($totalTertunda) }}</h3>
                            <p class="text-[11px] text-slate-400 font-medium mt-1">Perlu ditinjau hari ini</p>
                        </div>
                    </div>
                </div>

                <!-- Grid Baris 2: Chart Aktivitas & Tugas Tertunda -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    <!-- Chart Aktivitas Transaksi (8 Kolom) -->
                    <div class="lg:col-span-8 bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-base font-extrabold text-slate-800">Aktivitas transaksi</h3>
                                <p class="text-xs text-slate-400 mt-0.5">Performa 6 bulan terakhir</p>
                            </div>
                            <div class="flex items-center space-x-4 text-xs font-medium">
                                <div class="flex items-center space-x-1.5">
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                    <span class="text-slate-600">Simpanan</span>
                                </div>
                                <div class="flex items-center space-x-1.5">
                                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                                    <span class="text-slate-600">Pinjaman</span>
                                </div>
                                <div class="flex items-center space-x-1.5">
                                    <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                                    <span class="text-slate-600">Angsuran</span>
                                </div>
                            </div>
                        </div>

                        <div class="h-64 w-full">
                            <canvas id="transaksiChart"></canvas>
                        </div>
                    </div>

                    <!-- Tugas yang Perlu Diselesaikan (4 Kolom) -->
                    <div class="lg:col-span-4 bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base font-extrabold text-slate-800">Tugas yang perlu diselesaikan</h3>
                            <span class="px-2.5 py-1 bg-amber-50 text-amber-700 text-[10px] font-bold rounded-full border border-amber-200/60">
                                {{ $totalTertunda }} tugas
                            </span>
                        </div>
                        <p class="text-xs text-slate-400 -mt-2">Prioritas pekerjaan hari ini</p>

                        <div class="space-y-3">
                            <!-- Tugas Anggota Baru -->
                            <a href="{{ route('pengurus.anggota.index') }}" class="p-3.5 bg-slate-50 hover:bg-slate-100/80 rounded-xl flex items-center justify-between transition group">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-xl bg-blue-100/70 text-blue-700 flex items-center justify-center">
                                        <i class="fi fi-rr-users text-sm"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-bold text-slate-800">Data anggota baru</h4>
                                        <p class="text-[11px] text-slate-400">{{ $pendingAnggota }} anggota terdaftar bulan ini</p>
                                    </div>
                                </div>
                                <i class="fi fi-rr-angle-small-right text-slate-400 group-hover:translate-x-0.5 transition-transform"></i>
                            </a>

                            <!-- Tugas Validasi Simpanan -->
                            <a href="{{ route('pengurus.simpanan.index') }}" class="p-3.5 bg-slate-50 hover:bg-slate-100/80 rounded-xl flex items-center justify-between transition group">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-xl bg-amber-100/70 text-amber-700 flex items-center justify-center">
                                        <i class="fi fi-rr-envelope text-sm"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-bold text-slate-800">Transaksi simpanan</h4>
                                        <p class="text-[11px] text-slate-400">{{ $pendingSimpanan }} transaksi belum dijurnal</p>
                                    </div>
                                </div>
                                <i class="fi fi-rr-angle-small-right text-slate-400 group-hover:translate-x-0.5 transition-transform"></i>
                            </a>

                            <!-- Tugas Pengajuan Pinjaman -->
                            <a href="{{ route('pengurus.pinjaman.index') }}" class="p-3.5 bg-slate-50 hover:bg-slate-100/80 rounded-xl flex items-center justify-between transition group">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-xl bg-orange-100/70 text-orange-700 flex items-center justify-center">
                                        <i class="fi fi-rr-handshake text-sm"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-bold text-slate-800">Pengajuan pinjaman</h4>
                                        <p class="text-[11px] text-slate-400">{{ $pendingPinjaman }} pengajuan perlu diproses</p>
                                    </div>
                                </div>
                                <i class="fi fi-rr-angle-small-right text-slate-400 group-hover:translate-x-0.5 transition-transform"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Grid Baris 3: Transaksi Terbaru & Akses Cepat -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    <!-- Transaksi Terbaru (7 Kolom) -->
                    <div class="lg:col-span-7 bg-white p-6 rounded-2xl border border-slate-100 shadow-sm space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-base font-extrabold text-slate-800">Transaksi terbaru</h3>
                                <p class="text-xs text-slate-400 mt-0.5">Aktivitas operasional terkini</p>
                            </div>
                            <a href="{{ route('pengurus.simpanan.index') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700">
                                Lihat semua
                            </a>
                        </div>

                        <div class="space-y-3">
                            @forelse($transaksiTerbaru as $trx)
                                <div class="flex items-center justify-between p-3 rounded-xl hover:bg-slate-50 transition border border-slate-50">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                            <i class="fi fi-rr-envelope text-sm"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-xs font-bold text-slate-800">{{ $trx->anggota->nama ?? 'Anggota' }}</h4>
                                            <p class="text-[11px] text-slate-400">{{ $trx->jenis_simpanan }} · {{ \Carbon\Carbon::parse($trx->created_at)->translatedFormat('d F Y') }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xs font-extrabold text-slate-800">+Rp {{ number_format($trx->nominal, 0, ',', '.') }}</p>
                                        <span class="inline-block px-2 py-0.5 mt-0.5 text-[10px] font-bold rounded-full {{ $trx->is_jurnaled ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                            {{ $trx->is_jurnaled ? 'Selesai' : 'Pending' }}
                                        </span>
                                    </div>
                                </div>
                            @empty
                                <div class="p-6 text-center text-xs text-slate-400">
                                    Belum ada catatan transaksi operasional terkini.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Akses Cepat (5 Kolom) -->
                    <div class="lg:col-span-5 bg-white p-6 rounded-2xl border border-slate-100 shadow-sm space-y-4">
                        <div>
                            <h3 class="text-base font-extrabold text-slate-800">Akses cepat</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Mulai pekerjaan yang sering digunakan</p>
                        </div>

                        <div class="grid grid-cols-2 gap-3.5 pt-2">
                            <!-- Tambah Anggota -->
                            <a href="{{ route('pengurus.anggota.index') }}" class="p-4 rounded-xl border border-slate-100 bg-slate-50/50 hover:bg-emerald-50/50 hover:border-emerald-200 transition group flex flex-col justify-between">
                                <div class="w-9 h-9 rounded-xl bg-white text-slate-600 group-hover:text-emerald-600 flex items-center justify-center shadow-sm">
                                    <i class="fi fi-rr-user-add text-sm"></i>
                                </div>
                                <h4 class="text-xs font-bold text-slate-800 group-hover:text-emerald-800 mt-3">Tambah anggota</h4>
                            </a>

                            <!-- Catat Simpanan -->
                            <a href="{{ route('pengurus.simpanan.index') }}" class="p-4 rounded-xl border border-slate-100 bg-slate-50/50 hover:bg-emerald-50/50 hover:border-emerald-200 transition group flex flex-col justify-between">
                                <div class="w-9 h-9 rounded-xl bg-white text-slate-600 group-hover:text-emerald-600 flex items-center justify-center shadow-sm">
                                    <i class="fi fi-rr-envelope text-sm"></i>
                                </div>
                                <h4 class="text-xs font-bold text-slate-800 group-hover:text-emerald-800 mt-3">Catat simpanan</h4>
                            </a>

                            <!-- Ajukan Pinjaman -->
                            <a href="{{ route('pengurus.pinjaman.index') }}" class="p-4 rounded-xl border border-slate-100 bg-slate-50/50 hover:bg-emerald-50/50 hover:border-emerald-200 transition group flex flex-col justify-between">
                                <div class="w-9 h-9 rounded-xl bg-white text-slate-600 group-hover:text-emerald-600 flex items-center justify-center shadow-sm">
                                    <i class="fi fi-rr-handshake text-sm"></i>
                                </div>
                                <h4 class="text-xs font-bold text-slate-800 group-hover:text-emerald-800 mt-3">Ajukan pinjaman</h4>
                            </a>

                            <!-- Catat Angsuran -->
                            <a href="{{ route('pengurus.angsuran.index') }}" class="p-4 rounded-xl border border-slate-100 bg-slate-50/50 hover:bg-emerald-50/50 hover:border-emerald-200 transition group flex flex-col justify-between">
                                <div class="w-9 h-9 rounded-xl bg-white text-slate-600 group-hover:text-emerald-600 flex items-center justify-center shadow-sm">
                                    <i class="fi fi-rr-receipt text-sm"></i>
                                </div>
                                <h4 class="text-xs font-bold text-slate-800 group-hover:text-emerald-800 mt-3">Catat angsuran</h4>
                            </a>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Script Render Chart.js -->
    <script>
        const ctx = document.getElementById('transaksiChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($chartLabels),
                datasets: [
                    {
                        label: 'Simpanan',
                        data: @json($simpananMonthly),
                        backgroundColor: '#10b981',
                        borderRadius: 6,
                        barPercentage: 0.7,
                        categoryPercentage: 0.6
                    },
                    {
                        label: 'Pinjaman',
                        data: @json($pinjamanMonthly),
                        backgroundColor: '#3b82f6',
                        borderRadius: 6,
                        barPercentage: 0.7,
                        categoryPercentage: 0.6
                    },
                    {
                        label: 'Angsuran',
                        data: @json($angsuranMonthly),
                        backgroundColor: '#fbbf24',
                        borderRadius: 6,
                        barPercentage: 0.7,
                        categoryPercentage: 0.6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Plus Jakarta Sans', size: 11 }, color: '#94a3b8' }
                    },
                    y: {
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            font: { family: 'Plus Jakarta Sans', size: 10 },
                            color: '#94a3b8',
                            callback: function(value) {
                                if (value >= 1000000000) return 'Rp ' + (value / 1000000000) + ' M';
                                if (value >= 1000000) return 'Rp ' + (value / 1000000) + ' Jt';
                                return 'Rp ' + value;
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>