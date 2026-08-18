<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Neraca Saldo (Trial Balance) - Koperasi Merah Putih</title>
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
            <main class="p-8 space-y-6">
                <!-- Toolbar Filter & Cetak -->
                <div class="no-print bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col md:flex-row md:items-end justify-between gap-4">
                    <form action="{{ route('pengurus.neraca-saldo.index') }}" method="GET" class="flex items-end gap-3 text-xs">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Posisi Per Tanggal (*Cut-off*)</label>
                            <input type="date" name="end_date" value="{{ $endDate }}" class="px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white text-xs">
                        </div>
                        <button type="submit" class="px-4 py-2 bg-slate-900 text-white font-bold rounded-xl hover:bg-slate-800 transition">
                            Tampilkan
                        </button>
                    </form>

                    <button onclick="window.print()" class="px-4 py-2 bg-emerald-600 text-white font-bold text-xs rounded-xl hover:bg-emerald-700 flex items-center space-x-1.5 transition">
                        <i class="fi fi-rr-print"></i>
                        <span>Cetak Neraca Saldo</span>
                    </button>
                </div>

                <!-- Status Validasi Balance -->
                <div class="p-4 rounded-2xl border flex items-center justify-between {{ $data['is_balanced'] ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-rose-50 border-rose-200 text-rose-800' }}">
                    <div class="flex items-center space-x-2 text-xs font-bold">
                        <i class="fi {{ $data['is_balanced'] ? 'fi-rr-check-circle' : 'fi-rr-cross-circle' }} text-base"></i>
                        <span>{{ $data['is_balanced'] ? 'Neraca Saldo Seimbang (Debit = Kredit)' : 'PERINGATAN: Terjadi Ketidakseimbangan pada Buku Besar!' }}</span>
                    </div>
                    <span class="text-xs font-mono font-bold">Total: Rp {{ number_format($data['total_debit'], 0, ',', '.') }}</span>
                </div>

                <!-- Tabel Neraca Saldo -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm space-y-4">
                    <div class="text-center pb-4 border-b border-slate-100">
                        <h2 class="text-lg font-extrabold text-slate-900">KOPERASI MERAH PUTIH DESA BURUAN</h2>
                        <h3 class="text-sm font-bold text-slate-700 mt-0.5">NERACA SALDO (TRIAL BALANCE)</h3>
                        <p class="text-xs text-slate-400 mt-1">Per {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase text-[10px]">
                                    <th class="p-3">Kode Akun</th>
                                    <th class="p-3">Nama Akun Perkiraan</th>
                                    <th class="p-3 text-center">Saldo Normal</th>
                                    <th class="p-3 text-right">Debit (Rp)</th>
                                    <th class="p-3 text-right">Kredit (Rp)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-mono">
                                @foreach($data['rows'] as $row)
                                    <tr class="hover:bg-slate-50/50">
                                        <td class="p-3 font-bold text-slate-700">{{ $row['kode_akun'] }}</td>
                                        <td class="p-3 font-sans font-medium text-slate-800">{{ $row['nama_akun'] }}</td>
                                        <td class="p-3 text-center font-sans">
                                            <span class="px-2 py-0.5 bg-slate-100 text-slate-600 rounded text-[10px]">{{ $row['saldo_normal'] }}</span>
                                        </td>
                                        <td class="p-3 text-right {{ $row['debit'] > 0 ? 'font-bold text-slate-800' : 'text-slate-300' }}">
                                            {{ $row['debit'] > 0 ? number_format($row['debit'], 0, ',', '.') : '-' }}
                                        </td>
                                        <td class="p-3 text-right {{ $row['kredit'] > 0 ? 'font-bold text-slate-800' : 'text-slate-300' }}">
                                            {{ $row['kredit'] > 0 ? number_format($row['kredit'], 0, ',', '.') : '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-slate-50 font-bold border-t border-slate-200 text-slate-800">
                                    <td colspan="3" class="p-3 font-sans text-right">TOTAL KESEIMBANGAN:</td>
                                    <td class="p-3 text-right font-mono text-emerald-700">Rp {{ number_format($data['total_debit'], 0, ',', '.') }}</td>
                                    <td class="p-3 text-right font-mono text-emerald-700">Rp {{ number_format($data['total_kredit'], 0, ',', '.') }}</td>
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