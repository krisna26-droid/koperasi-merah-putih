<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Besar - Koperasi Merah Putih</title>
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
            <header class="bg-white border-b border-slate-100 py-3.5 px-8 flex items-center justify-between select-none font-sans">
                <div>
                    <div class="flex items-center space-x-2 text-xs text-slate-400">
                        <span>Koperasi Merah Putih</span>
                        <span>&rsaquo;</span>
                        <span class="text-slate-600 font-medium">Buku Besar</span>
                    </div>
                    <h2 class="text-base font-bold text-slate-800 mt-0.5">Buku Besar</h2>
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
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Buku Besar</h1>
                        <p class="text-xs text-slate-500 mt-1">Ringkasan pergerakan dan saldo setiap akun.</p>
                    </div>

                    <div class="flex items-center space-x-3">
                        <button type="button" onclick="window.print()" class="px-3.5 py-2 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-bold rounded-xl transition flex items-center space-x-2 shadow-sm">
                            <i class="fi fi-rr-print text-xs"></i>
                            <span>Cetak</span>
                        </button>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
                    <form method="GET" action="{{ route('ketua.laporan.buku-besar') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                        <div class="md:col-span-5">
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Pilih akun</label>
                            <select name="kode_akun" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition font-medium">
                                @foreach($coaList as $c)
                                    <option value="{{ $c->kode_akun }}" {{ $account->kode_akun == $c->kode_akun ? 'selected' : '' }}>
                                        {{ $c->kode_akun }} - {{ $c->nama_akun }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-4">
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Periode</label>
                            <input type="text" readonly value="{{ $startDate->translatedFormat('d') }} – {{ $endDate->translatedFormat('d F Y') }}" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-700 font-medium cursor-not-allowed">
                        </div>

                        <div class="md:col-span-3">
                            <button type="submit" class="w-full py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-xl transition flex items-center justify-center space-x-2 shadow-sm">
                                <i class="fi fi-rr-filter text-xs text-slate-400"></i>
                                <span>Terapkan filter</span>
                            </button>
                        </div>
                    </form>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-slate-400">Saldo awal</p>
                            <h3 class="text-2xl font-extrabold text-slate-800 mt-1">
                                Rp {{ number_format($saldoAwal, 0, ',', '.') }}
                            </h3>
                        </div>
                        <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
                            <i class="fi fi-rr-bank text-xl"></i>
                        </div>
                    </div>

                    <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-slate-400">Total debit periode</p>
                            <h3 class="text-2xl font-extrabold text-slate-800 mt-1">
                                Rp {{ number_format($totalDebitPeriode, 0, ',', '.') }}
                            </h3>
                        </div>
                        <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <i class="fi fi-rr-arrow-down-left text-xl"></i>
                        </div>
                    </div>

                    <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-slate-400">Saldo akhir</p>
                            <h3 class="text-2xl font-extrabold text-emerald-600 mt-1">
                                Rp {{ number_format($saldoAkhir, 0, ',', '.') }}
                            </h3>
                        </div>
                        <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                            <i class="fi fi-rr-stats text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider">BUKU BESAR</span>
                            <h3 class="text-base font-extrabold text-slate-800 mt-0.5">Akun: {{ $account->nama_akun }} ({{ $account->kode_akun }})</h3>
                        </div>
                        <span class="px-3 py-1 bg-blue-50 text-blue-700 text-xs font-bold rounded-full border border-blue-200/60">
                            {{ $startDate->translatedFormat('F Y') }}
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100 bg-slate-50/50 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                    <th class="py-3.5 px-6">Tanggal</th>
                                    <th class="py-3.5 px-6">Referensi</th>
                                    <th class="py-3.5 px-6">Keterangan</th>
                                    <th class="py-3.5 px-6 text-right">Debit</th>
                                    <th class="py-3.5 px-6 text-right">Kredit</th>
                                    <th class="py-3.5 px-6 text-right">Saldo</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-xs">
                                <tr class="bg-slate-50/40 font-semibold text-slate-600">
                                    <td class="py-4 px-6">{{ $startDate->translatedFormat('d F Y') }}</td>
                                    <td class="py-4 px-6 font-mono text-[11px] text-slate-400">SALDO-AWAL</td>
                                    <td class="py-4 px-6">Saldo awal bulan {{ $startDate->translatedFormat('F') }}</td>
                                    <td class="py-4 px-6 text-right text-slate-400">-</td>
                                    <td class="py-4 px-6 text-right text-slate-400">-</td>
                                    <td class="py-4 px-6 text-right font-extrabold text-slate-800 font-mono">
                                        Rp {{ number_format($saldoAwal, 0, ',', '.') }}
                                    </td>
                                </tr>

                                @forelse($ledgerData as $row)
                                    <tr class="hover:bg-slate-50/60 transition">
                                        <td class="py-4 px-6 text-slate-600 font-medium whitespace-nowrap">{{ $row['tanggal'] }}</td>
                                        <td class="py-4 px-6 font-semibold text-slate-500 whitespace-nowrap font-mono text-[11px]">{{ $row['referensi'] }}</td>
                                        <td class="py-4 px-6 text-slate-700 font-medium">{{ $row['keterangan'] }}</td>
                                        <td class="py-4 px-6 text-right font-bold text-slate-800 whitespace-nowrap font-mono">
                                            {{ $row['debit'] > 0 ? 'Rp ' . number_format($row['debit'], 0, ',', '.') : '-' }}
                                        </td>
                                        <td class="py-4 px-6 text-right font-bold text-slate-800 whitespace-nowrap font-mono">
                                            {{ $row['kredit'] > 0 ? 'Rp ' . number_format($row['kredit'], 0, ',', '.') : '-' }}
                                        </td>
                                        <td class="py-4 px-6 text-right font-extrabold text-slate-900 whitespace-nowrap font-mono">
                                            Rp {{ number_format($row['saldo'], 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    @if($saldoAwal == 0)
                                        <tr>
                                            <td colspan="6" class="py-8 text-center text-xs text-slate-400">
                                                Belum ada mutasi transaksi untuk akun ini pada periode terpilih.
                                            </td>
                                        </tr>
                                    @endif
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>