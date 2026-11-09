<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Angsuran - {{ $cooperativeSettings->name ?? 'Koperasi Merah Putih' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.1.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
        }
    </style>
</head>
<body class="bg-slate-50/60 antialiased">
    <div class="flex min-h-screen">
        <div class="no-print">
            @include('layouts.sidebar-pengurus')
        </div>

        <div class="flex-1 flex flex-col min-w-0">
            <x-page-header title="Laporan Angsuran" section="Laporan" print-hidden="true" />
            <main class="p-8 space-y-6">
                <!-- Toolbar Filter & Tombol Print -->
                <div class="no-print bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col md:flex-row md:items-end justify-between gap-4">
                    <form action="{{ route('pengurus.laporan.angsuran') }}" method="GET" class="flex flex-wrap items-end gap-3 text-xs">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Dari Tanggal</label>
                            <input type="date" name="start_date" value="{{ $startDate }}" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Sampai Tanggal</label>
                            <input type="date" name="end_date" value="{{ $endDate }}" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                        </div>
                        <button type="submit" class="px-4 py-2 bg-slate-900 text-white font-bold rounded-xl hover:bg-slate-800">
                            Filter
                        </button>
                    </form>

                    <div class="flex items-center space-x-2">
                        <a href="{{ route('pengurus.laporan.index') }}" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold text-xs rounded-xl hover:bg-slate-200">
                            Kembali
                        </a>
                        <x-export-buttons report="angsuran" />
                    </div>
                </div>

                <!-- Rekap Kop Surat -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm space-y-4">
                    <div class="text-center pb-4 border-b border-slate-100">
                        <x-cooperative-print-brand />
                        <h2 class="text-lg font-extrabold text-slate-900">{{ strtoupper($cooperativeSettings->name) }} · {{ $cooperativeSettings->address }}</h2>
                        <h3 class="text-sm font-bold text-slate-700 mt-0.5">LAPORAN PENERIMAAN ANGSURAN PINJAMAN</h3>
                        <p class="text-xs text-slate-400 mt-1">Periode: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase text-[10px]">
                                    <th class="p-3">ID Angsuran</th>
                                    <th class="p-3">Tanggal</th>
                                    <th class="p-3">ID Pinjam</th>
                                    <th class="p-3">Anggota</th>
                                    <th class="p-3 text-right">Pokok (Rp)</th>
                                    <th class="p-3 text-right">Bunga (Rp)</th>
                                    <th class="p-3 text-right">Total Bayar (Rp)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($data as $row)
                                    <tr>
                                        <td class="p-3 font-mono font-bold text-slate-700">{{ $row->id_angsuran }}</td>
                                        <td class="p-3 font-mono">{{ $row->created_at->format('d/m/Y') }}</td>
                                        <td class="p-3 font-mono">{{ $row->id_pinjam }}</td>
                                        <td class="p-3 font-bold text-slate-800">{{ $row->pinjaman->anggota->nama ?? '-' }}</td>
                                        <td class="p-3 text-right font-mono">{{ number_format($row->bayar_pokok, 0, ',', '.') }}</td>
                                        <td class="p-3 text-right font-mono">{{ number_format($row->bayar_bunga, 0, ',', '.') }}</td>
                                        <td class="p-3 text-right font-mono font-bold text-emerald-700">{{ number_format($row->total_bayar, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="p-4 text-center text-slate-400">Tidak ada catatan pembayaran angsuran pada periode ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="bg-slate-50 font-bold border-t border-slate-200 text-slate-800">
                                    <td colspan="4" class="p-3 text-right">TOTAL PENERIMAAN:</td>
                                    <td class="p-3 text-right font-mono">Rp {{ number_format($totalPokok, 0, ',', '.') }}</td>
                                    <td class="p-3 text-right font-mono">Rp {{ number_format($totalBunga, 0, ',', '.') }}</td>
                                    <td class="p-3 text-right font-mono text-emerald-700">Rp {{ number_format($totalBayar, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>