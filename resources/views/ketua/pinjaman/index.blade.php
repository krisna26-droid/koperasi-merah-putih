<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pinjaman - {{ $cooperativeSettings->name ?? 'Koperasi Merah Putih' }}</title>
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
            <x-page-header title="Pinjaman" />

            <main class="p-8 space-y-6">
                <!-- Notifications -->
                @if(session('success'))
                    <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold rounded-xl flex items-center space-x-2">
                        <i class="fi fi-rr-check-circle text-base"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif
                @if(session('error'))
                    <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold rounded-xl flex items-center space-x-2">
                        <i class="fi fi-rr-cross-circle text-base"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                <!-- Title Header (Tanpa Tombol Ajukan Pinjaman) -->
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Manajemen Pinjaman</h1>
                    <p class="text-xs text-slate-500 mt-1">Kelola pengajuan dan portofolio pinjaman anggota.</p>
                </div>

                <!-- 4 Stat Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Pinjaman Aktif -->
                    <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-slate-400">Pinjaman aktif</p>
                            <h3 class="text-2xl font-extrabold text-slate-800 mt-1">{{ number_format($pinjamanAktif) }}</h3>
                        </div>
                        <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <i class="fi fi-rr-hand-holding-usd text-xl"></i>
                        </div>
                    </div>

                    <!-- Total Outstanding -->
                    <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-slate-400">Total outstanding</p>
                            <h3 class="text-2xl font-extrabold text-slate-800 mt-1">
                                @if($totalOutstanding >= 1000000000)
                                    Rp {{ number_format($totalOutstanding / 1000000000, 2, ',', '.') }} M
                                @else
                                    Rp {{ number_format($totalOutstanding / 1000000, 1, ',', '.') }} Jt
                                @endif
                            </h3>
                        </div>
                        <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                            <i class="fi fi-rr-dollar text-xl"></i>
                        </div>
                    </div>

                    <!-- Pengajuan Pending -->
                    <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-slate-400">Pengajuan pending</p>
                            <h3 class="text-2xl font-extrabold text-slate-800 mt-1">{{ number_format($pengajuanPending) }}</h3>
                        </div>
                        <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                            <i class="fi fi-rr-document-signed text-xl"></i>
                        </div>
                    </div>

                    <!-- Pinjaman Lunas -->
                    <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-slate-400">Pinjaman lunas</p>
                            <h3 class="text-2xl font-extrabold text-slate-800 mt-1">{{ number_format($pinjamanLunas) }}</h3>
                        </div>
                        <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
                            <i class="fi fi-rr-checkbox text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Table Card Container -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                        <form method="GET" action="{{ route('ketua.pinjaman.index') }}" class="flex-1 w-full flex items-center gap-2">
                            <div class="relative flex-1">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <i class="fi fi-rr-search text-xs flex items-center"></i>
                                </div>
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari anggota atau nomor pinjaman..." class="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition placeholder:text-slate-400">
                            </div>
                            <button type="submit" class="px-3.5 py-2 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold rounded-xl transition flex items-center space-x-2 shadow-sm">
                                <i class="fi fi-rr-filter text-xs text-slate-400"></i>
                                <span>Filter</span>
                            </button>
                        </form>
                        <x-export-buttons report="pinjaman" />
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100 bg-slate-50/50 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                    <th class="py-3.5 px-6">No. Pinjaman</th>
                                    <th class="py-3.5 px-6">Anggota</th>
                                    <th class="py-3.5 px-6">Tanggal</th>
                                    <th class="py-3.5 px-6">Pokok</th>
                                    <th class="py-3.5 px-6">Angsuran</th>
                                    <th class="py-3.5 px-6">Outstanding</th>
                                    <th class="py-3.5 px-6 text-center">Status</th>
                                    <th class="py-3.5 px-6 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-xs">
                                @forelse($pinjamanList as $item)
                                    <tr class="hover:bg-slate-50/60 transition">
                                        <td class="py-4 px-6 font-semibold text-slate-700">
                                            {{ $item->id_pinjam }}
                                        </td>
                                        <td class="py-4 px-6">
                                            <div class="flex items-center space-x-3">
                                                <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-[11px]">
                                                    {{ strtoupper(substr($item->anggota->nama ?? 'A', 0, 2)) }}
                                                </div>
                                                <span class="font-bold text-slate-800">{{ $item->anggota->nama ?? '-' }}</span>
                                            </div>
                                        </td>
                                        <td class="py-4 px-6 text-slate-600 font-medium">
                                            {{ $item->created_at->translatedFormat('d F Y') }}
                                        </td>
                                        <td class="py-4 px-6 font-bold text-slate-800">
                                            Rp {{ number_format($item->jumlah_pinjam, 0, ',', '.') }}
                                        </td>
                                        <td class="py-4 px-6 text-slate-600 font-medium">
                                            Rp {{ number_format($item->pokok_per_bulan + $item->bunga_per_bulan, 0, ',', '.') }}
                                        </td>
                                        <td class="py-4 px-6 font-bold text-slate-800">
                                            Rp {{ number_format($item->sisa_tagihan, 0, ',', '.') }}
                                        </td>
                                        <td class="py-4 px-6 text-center">
                                            @if($item->status === 'Pengajuan')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                    Menunggu Persetujuan
                                                </span>
                                            @elseif($item->status === 'Berjalan')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    Aktif
                                                </span>
                                            @elseif($item->status === 'Lunas')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                                    Lunas
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                    Ditolak
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-6 text-center">
                                            <a href="{{ route('ketua.pinjaman.show', $item->id_pinjam) }}" class="p-1.5 text-slate-400 hover:text-emerald-600 rounded-lg hover:bg-emerald-50 transition inline-flex items-center" title="Lihat Detail & Persetujuan">
                                                <i class="fi fi-rr-eye text-sm"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="py-8 text-center text-slate-400">
                                            Tidak ada riwayat pinjaman.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($pinjamanList->hasPages())
                        <div class="p-4 border-t border-slate-100 bg-white">
                            {{ $pinjamanList->links() }}
                        </div>
                    @endif
                </div>
            </main>
        </div>
    </div>
</body> 
</html>