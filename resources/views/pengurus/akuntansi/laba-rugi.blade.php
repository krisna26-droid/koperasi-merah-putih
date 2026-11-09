<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Sisa Hasil Usaha (SHU) - {{ $cooperativeSettings->name ?? 'Koperasi Merah Putih' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.1.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        @media print { .no-print { display: none !important; } body { background: white !important; } }
    </style>
</head>
<body class="bg-slate-50/60 antialiased">
    <div class="flex min-h-screen">
        <div class="no-print">
            @include('layouts.sidebar-pengurus')
        </div>

        <div class="flex-1 flex flex-col min-w-0">
            <x-page-header title="Laporan Sisa Hasil Usaha" section="Akuntansi" print-hidden="true" />
            <main class="p-8 space-y-6">
                <!-- Toolbar Filter & Print -->
                <div class="no-print bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col md:flex-row md:items-end justify-between gap-4">
                    <form action="{{ route('pengurus.laba-rugi.index') }}" method="GET" class="flex flex-wrap items-end gap-3 text-xs">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Dari Tanggal</label>
                            <input type="date" name="start_date" value="{{ $startDate }}" class="px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Sampai Tanggal</label>
                            <input type="date" name="end_date" value="{{ $endDate }}" class="px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                        </div>
                        <button type="submit" class="px-4 py-2 bg-slate-900 text-white font-bold rounded-xl hover:bg-slate-800 transition">
                            Filter Periode
                        </button>
                    </form>

                    <x-export-buttons report="laba-rugi" />
                </div>

                <!-- Format Laporan Laba Rugi / SHU Standar Akuntansi Koperasi -->
                <div class="bg-white p-8 rounded-2xl border border-slate-100 shadow-sm max-w-4xl mx-auto space-y-6">
                    <div class="text-center pb-4 border-b border-slate-200">
                        <x-cooperative-print-brand />
                        <h2 class="text-lg font-extrabold text-slate-900">{{ strtoupper($cooperativeSettings->name) }} · {{ $cooperativeSettings->address }}</h2>
                        <h3 class="text-sm font-bold text-slate-700 mt-0.5">LAPORAN PERHITUNGAN HASIL USAHA (SISA HASIL USAHA)</h3>
                        <p class="text-xs text-slate-400 mt-1">Periode: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}</p>
                    </div>

                    <!-- 1. Bagian Pendapatan Operasional -->
                    <div class="space-y-3">
                        <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider border-b pb-1 border-slate-100">I. PENDAPATAN OPERASIONAL</h4>
                        <div class="space-y-2 text-xs">
                            @forelse($data['pendapatan'] as $p)
                                <div class="flex justify-between items-center py-1">
                                    <span class="text-slate-700 pl-4">{{ $p['kode_akun'] }} &mdash; {{ $p['nama_akun'] }}</span>
                                    <span class="font-mono font-medium text-slate-800">Rp {{ number_format($p['nominal'], 0, ',', '.') }}</span>
                                </div>
                            @empty
                                <p class="text-xs text-slate-400 italic pl-4">Tidak ada pendapatan pada periode ini.</p>
                            @endforelse
                        </div>
                        <div class="flex justify-between items-center py-2 border-t border-slate-100 text-xs font-bold text-slate-800 bg-slate-50/50 px-3 rounded-lg">
                            <span>TOTAL PENDAPATAN OPERASIONAL</span>
                            <span class="font-mono text-emerald-700">Rp {{ number_format($data['total_pendapatan'], 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- 2. Bagian Beban Operasional -->
                    <div class="space-y-3">
                        <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider border-b pb-1 border-slate-100">II. BEBAN OPERASIONAL</h4>
                        <div class="space-y-2 text-xs">
                            @forelse($data['beban'] as $b)
                                <div class="flex justify-between items-center py-1">
                                    <span class="text-slate-700 pl-4">{{ $b['kode_akun'] }} &mdash; {{ $b['nama_akun'] }}</span>
                                    <span class="font-mono font-medium text-slate-800">Rp {{ number_format($b['nominal'], 0, ',', '.') }}</span>
                                </div>
                            @empty
                                <p class="text-xs text-slate-400 italic pl-4">Tidak ada pencatatan beban operasional.</p>
                            @endforelse
                        </div>
                        <div class="flex justify-between items-center py-2 border-t border-slate-100 text-xs font-bold text-slate-800 bg-slate-50/50 px-3 rounded-lg">
                            <span>TOTAL BEBAN OPERASIONAL</span>
                            <span class="font-mono text-rose-700">Rp {{ number_format($data['total_beban'], 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- 3. Sisa Hasil Usaha (SHU) Bersih -->
                    <div class="p-4 rounded-xl border border-emerald-200 bg-emerald-50/60 flex justify-between items-center text-sm font-extrabold text-emerald-950">
                        <span>SISA HASIL USAHA (SHU) BERSIH TAHUN BERJALAN</span>
                        <span class="font-mono text-base text-emerald-700">Rp {{ number_format($data['shu_bersih'], 0, ',', '.') }}</span>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>