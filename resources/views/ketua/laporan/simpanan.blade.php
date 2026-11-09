<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Simpanan - {{ $cooperativeSettings->name ?? 'Koperasi Merah Putih' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.1.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.1.0/uicons-solid-rounded/css/uicons-solid-rounded.css'>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            .print-border { border: 1px solid #cbd5e1 !important; }
        }
    </style>
</head>
<body class="bg-slate-50/60 antialiased">
    <div class="flex min-h-screen">
        <!-- Sidebar Navigation -->
        <div class="no-print">
            @include('layouts.sidebar-ketua')
        </div>

        <div class="flex-1 flex flex-col min-w-0">
            <!-- Header Navbar -->
            <x-page-header title="Laporan Simpanan" section="Laporan" print-hidden="true" />

            <main class="p-8 space-y-6">
                <!-- Title Header & Buttons -->
                <div class="no-print flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('ketua.laporan.index') }}" class="p-1.5 text-slate-400 hover:text-slate-700 rounded-lg hover:bg-slate-100 transition">
                                <i class="fi fi-rr-arrow-left text-sm flex items-center"></i>
                            </a>
                            <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Rekap Simpanan Anggota</h1>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Laporan rincian simpanan pokok, wajib, dan sukarela anggota koperasi.</p>
                    </div>

                    <div class="flex items-center space-x-3">
                        <x-export-buttons report="simpanan" />
                    </div>
                </div>

                <!-- Lembar Laporan -->
                <div class="bg-white p-8 rounded-2xl border border-slate-100 shadow-sm print:p-0 print:border-none print:shadow-none">
                    <!-- Kop Surat -->
                    <div class="text-center pb-6 border-b border-slate-200 mb-6">
                        <x-cooperative-print-brand />
                        <h2 class="text-lg font-extrabold text-slate-900 uppercase tracking-tight">{{ $cooperativeSettings->name ?? 'Koperasi Merah Putih' }}</h2>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $cooperativeSettings->address }}</p>
                        <h3 class="text-sm font-bold text-slate-800 mt-3 underline uppercase">Laporan Mutasi Simpanan</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}</p>
                    </div>

                    <!-- Tabel Data Simpanan -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse border border-slate-200">
                            <thead>
                                <tr class="bg-slate-50 text-[11px] font-bold text-slate-600 uppercase tracking-wider border-b border-slate-200">
                                    <th class="py-3 px-4 border border-slate-200 text-center w-12">No</th>
                                    <th class="py-3 px-4 border border-slate-200">ID / Anggota</th>
                                    <th class="py-3 px-4 border border-slate-200">Jenis Simpanan</th>
                                    <th class="py-3 px-4 border border-slate-200">Tanggal Transaksi</th>
                                    <th class="py-3 px-4 border border-slate-200 text-right">Nominal (Rp)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 text-xs">
                                @forelse($simpanan as $idx => $s)
                                    <tr class="hover:bg-slate-50/50">
                                        <td class="py-3 px-4 border border-slate-200 text-center text-slate-500">{{ $idx + 1 }}</td>
                                        <td class="py-3 px-4 border border-slate-200">
                                            <p class="font-bold text-slate-800">{{ $s->anggota->nama_lengkap ?? '-' }}</p>
                                            <p class="text-[11px] text-slate-400 font-mono">{{ $s->no_anggota ?? ($s->anggota->no_anggota ?? '-') }}</p>
                                        </td>
                                        <td class="py-3 px-4 border border-slate-200">
                                            <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-lg text-[11px] font-semibold">
                                                {{ $s->jenis_simpanan ?? 'Simpanan' }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 border border-slate-200 text-slate-600">
                                            {{ \Carbon\Carbon::parse($s->tanggal ?? $s->created_at)->translatedFormat('d F Y') }}
                                        </td>
                                        <td class="py-3 px-4 border border-slate-200 text-right font-extrabold text-slate-900">
                                            Rp {{ number_format($s->nominal ?? $s->jumlah ?? 0, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-8 text-center text-slate-400">
                                            Belum ada catatan simpanan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($simpanan->isNotEmpty())
                                <tfoot>
                                    <tr class="bg-slate-50 font-extrabold text-xs text-slate-900 border-t-2 border-slate-300">
                                        <td colspan="4" class="py-3 px-4 border border-slate-200 text-center uppercase tracking-wider">
                                            Total Keseluruhan Simpanan
                                        </td>
                                        <td class="py-3 px-4 border border-slate-200 text-right">
                                            Rp {{ number_format($simpanan->sum(fn($i) => $i->nominal ?? $i->jumlah ?? 0), 0, ',', '.') }}
                                        </td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>

                    <!-- Tanda Tangan Cetak -->
                    <div class="hidden print:grid grid-cols-2 gap-8 mt-12 pt-6 text-xs text-center">
                        <div>
                            <p class="text-slate-500">Mengetahui,</p>
                            <p class="font-bold text-slate-800 mt-1">Pengurus Koperasi</p>
                            <div class="h-16"></div>
                            <p class="font-bold text-slate-800 underline">( ........................................ )</p>
                        </div>
                        <div>
                            <p class="text-slate-500">Gianyar, {{ now()->translatedFormat('d F Y') }}</p>
                            <p class="font-bold text-slate-800 mt-1">Ketua Koperasi</p>
                            <div class="h-16"></div>
                            <p class="font-bold text-slate-800 underline">{{ Auth::user()->name }}</p>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>