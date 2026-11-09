<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemantauan Simpanan - {{ $cooperativeSettings->name ?? 'Koperasi Merah Putih' }}</title>
    <!-- Fonts & Icons -->
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
            <!-- Navbar Header -->
            <x-page-header title="Simpanan" />

            <main class="p-8 space-y-6">
                <!-- Notifications -->
                @if(session('success'))
                    <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold rounded-xl flex items-center space-x-2">
                        <i class="fi fi-rr-check-circle text-base"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                <!-- Title & Button Trigger Modal -->
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Pemantauan Simpanan</h1>
                        <p class="text-xs text-slate-500 mt-1">Tinjau simpanan pokok, wajib, dan sukarela anggota.</p>
                    </div>

                </div>

                <!-- 4 Stat Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
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
                        <div class="mt-4 flex items-center text-[11px] font-semibold text-emerald-600">
                            <span>↗ 12,5%</span>
                            <span class="text-slate-400 font-normal ml-1">dari bulan lalu</span>
                        </div>
                    </div>

                    <!-- Simpanan Pokok -->
                    <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs font-semibold text-slate-400">Simpanan pokok</p>
                                <h3 class="text-2xl font-extrabold text-slate-800 mt-1">
                                    Rp {{ number_format($simpananPokok / 1000000, 1, ',', '.') }} Jt
                                </h3>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                                <i class="fi fi-rr-bank text-lg"></i>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-[11px] font-semibold text-emerald-600">
                            <span>↗ 4,2%</span>
                            <span class="text-slate-400 font-normal ml-1">dari bulan lalu</span>
                        </div>
                    </div>

                    <!-- Simpanan Wajib -->
                    <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs font-semibold text-slate-400">Simpanan wajib</p>
                                <h3 class="text-2xl font-extrabold text-slate-800 mt-1">
                                    Rp {{ number_format($simpananWajib / 1000000, 1, ',', '.') }} Jt
                                </h3>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                                <i class="fi fi-rr-usd-circle text-lg"></i>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-[11px] font-semibold text-emerald-600">
                            <span>↗ 8,2%</span>
                            <span class="text-slate-400 font-normal ml-1">dari bulan lalu</span>
                        </div>
                    </div>

                    <!-- Simpanan Sukarela -->
                    <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs font-semibold text-slate-400">Simpanan sukarela</p>
                                <h3 class="text-2xl font-extrabold text-slate-800 mt-1">
                                    Rp {{ number_format($simpananSukarela / 1000000, 1, ',', '.') }} Jt
                                </h3>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
                                <i class="fi fi-rr-sparkles text-lg"></i>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-[11px] font-semibold text-emerald-600">
                            <span>↗ 16,4%</span>
                            <span class="text-slate-400 font-normal ml-1">dari bulan lalu</span>
                        </div>
                    </div>
                </div>

                <!-- Table Card Container -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                        <form method="GET" action="{{ route('ketua.simpanan.index') }}" class="flex-1 w-full flex items-center gap-2">
                            <div class="relative flex-1">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <i class="fi fi-rr-search text-xs flex items-center"></i>
                                </div>
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari anggota atau nomor transaksi..." class="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition placeholder:text-slate-400">
                            </div>
                            <button type="submit" class="px-3.5 py-2 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold rounded-xl transition flex items-center space-x-2 shadow-sm">
                                <i class="fi fi-rr-filter text-xs text-slate-400"></i>
                                <span>Filter</span>
                            </button>
                        </form>
                        <x-export-buttons report="simpanan" />
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100 bg-slate-50/50 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                    <th class="py-3.5 px-6">Tanggal</th>
                                    <th class="py-3.5 px-6">No. Transaksi</th>
                                    <th class="py-3.5 px-6">Anggota</th>
                                    <th class="py-3.5 px-6 text-center">Jenis</th>
                                    <th class="py-3.5 px-6">Jumlah</th>
                                    <th class="py-3.5 px-6 text-center">Status</th>
                                    <th class="py-3.5 px-6">Petugas</th>
                                    <th class="py-3.5 px-6 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-xs">
                                @forelse($simpananList as $item)
                                    <tr class="hover:bg-slate-50/60 transition">
                                        <td class="py-4 px-6 text-slate-600 font-medium">
                                            {{ $item->created_at->translatedFormat('d F Y') }}
                                        </td>
                                        <td class="py-4 px-6 font-semibold text-slate-700">
                                            {{ $item->id_simpanan }}
                                        </td>
                                        <td class="py-4 px-6 font-bold text-slate-800">
                                            {{ $item->anggota->nama ?? '-' }}
                                        </td>
                                        <td class="py-4 px-6 text-center">
                                            @if($item->jenis_simpanan === 'Wajib')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    Wajib
                                                </span>
                                            @elseif($item->jenis_simpanan === 'Pokok')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                                    Pokok
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                                    Sukarela
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-6 font-bold text-slate-800">
                                            Rp {{ number_format($item->nominal, 0, ',', '.') }}
                                        </td>
                                        <td class="py-4 px-6 text-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Selesai
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 text-slate-600">
                                            {{ $item->created_by }}
                                        </td>
                                        <td class="py-4 px-6 text-center">
                                            <button type="button" class="text-slate-400 hover:text-slate-600">
                                                •••
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="py-8 text-center text-slate-400">
                                            Tidak ada riwayat transaksi simpanan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($simpananList->hasPages())
                        <div class="p-4 border-t border-slate-100 bg-white">
                            {{ $simpananList->links() }}
                        </div>
                    @endif
                </div>
            </main>
        </div>
    </div>

</body>
</html>
