<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi Pinjaman - {{ $cooperativeSettings->name ?? 'Koperasi Merah Putih' }}</title>
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
        @include('layouts.sidebar-pengurus')

        <div class="flex-1 flex flex-col min-w-0">
            <x-page-header title="Transaksi Pinjaman" />

            <main class="p-8 space-y-6">
                @if(session('success'))
                    <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold rounded-2xl flex items-center space-x-2">
                        <i class="fi fi-rr-check-circle text-base"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif
                @if(session('error'))
                    <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold rounded-2xl flex items-center space-x-2">
                        <i class="fi fi-rr-cross-circle text-base"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                <div>
                    <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Pengajuan & Transaksi Pinjaman</h1>
                    <p class="text-xs text-slate-500 mt-1">Buat draf permohonan kredit anggota baru untuk diteruskan ke Ketua Koperasi.</p>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm space-y-5">
                    <div class="flex items-center space-x-3 pb-4 border-b border-slate-50">
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <i class="fi fi-rr-dollar text-base"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-extrabold text-slate-800">Formulir Permohonan Pinjaman</h3>
                            <p class="text-xs text-slate-400">Sistem memberlakukan aturan <em>Single Active Loan</em> dan bunga tetap 1.5% per bulan.</p>
                        </div>
                    </div>

                    <form action="{{ route('pengurus.pinjaman.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Pilih Anggota</label>
                                <select name="id_anggota" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 font-medium text-slate-800 transition" required>
                                    <option value="">-- Pilih Anggota Aktif --</option>
                                    @foreach($anggotaList as $item)
                                        <option value="{{ $item->id_anggota }}" {{ old('id_anggota') === $item->id_anggota ? 'selected' : '' }}>
                                            {{ $item->id_anggota }} - {{ $item->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_anggota')
                                    <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nominal Pinjaman (Rp)</label>
                                <input type="number" id="input_jumlah_pinjam" name="jumlah_pinjam" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 font-medium text-slate-800 transition" placeholder="Contoh: 5000000" min="100000" step="50000" value="{{ old('jumlah_pinjam') }}" required>
                                @error('jumlah_pinjam')
                                    <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Jangka Waktu / Tenor (Bulan)</label>
                                <input type="number" id="input_tenor" name="tenor" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 font-medium text-slate-800 transition" placeholder="1 - 60 Bulan" min="1" max="60" value="{{ old('tenor') }}" required>
                                @error('tenor')
                                    <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 flex flex-wrap items-center justify-between text-xs gap-4">
                            <div class="flex items-center space-x-6">
                                <div>
                                    <span class="text-slate-400 block text-[11px]">Suku Bunga:</span>
                                    <span class="font-bold text-slate-700">1.50% / Bulan (Tetap)</span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[11px]">Estimasi Pokok / Bln:</span>
                                    <span id="preview_pokok" class="font-bold text-slate-700">Rp 0</span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[11px]">Estimasi Bunga / Bln:</span>
                                    <span id="preview_bunga" class="font-bold text-slate-700">Rp 0</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-slate-400 block text-[11px]">Total Angsuran Bulanan:</span>
                                <span id="preview_total" class="font-extrabold text-emerald-600 text-sm">Rp 0</span>
                            </div>
                        </div>

                        <div class="flex justify-end pt-2">
                            <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-sm transition flex items-center space-x-2">
                                <i class="fi fi-rr-paper-plane text-xs"></i>
                                <span>Ajukan Pinjaman ke Ketua</span>
                            </button>
                        </div>
                    </form>
                </div>

                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-extrabold text-slate-800">Daftar & Riwayat Pinjaman</h3>
                            <p class="text-xs text-slate-400">Daftar seluruh pinjaman yang diajukan beserta status persetujuannya.</p>
                        </div>
                        <x-export-buttons report="pinjaman" />
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/70 border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                    <th class="py-3.5 px-5">ID Pinjam</th>
                                    <th class="py-3.5 px-5">Anggota</th>
                                    <th class="py-3.5 px-5">Plafond Pokok</th>
                                    <th class="py-3.5 px-5">Tenor</th>
                                    <th class="py-3.5 px-5">Bunga / Bln (1.5%)</th>
                                    <th class="py-3.5 px-5">Sisa Kewajiban</th>
                                    <th class="py-3.5 px-5 text-right">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 text-xs">
                                @forelse($pinjaman as $item)
                                    <tr class="hover:bg-slate-50/60 transition">
                                        <td class="py-3.5 px-5 font-mono font-bold text-slate-700">
                                            {{ $item->id_pinjam }}
                                        </td>
                                        <td class="py-3.5 px-5">
                                            <p class="font-bold text-slate-800">{{ $item->anggota->nama ?? '-' }}</p>
                                            <p class="text-[10px] text-slate-400 font-mono">{{ $item->id_anggota }}</p>
                                        </td>
                                        <td class="py-3.5 px-5 font-extrabold text-slate-800">
                                            Rp {{ number_format($item->jumlah_pinjam, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3.5 px-5 text-slate-600">
                                            {{ $item->tenor }} Bulan
                                        </td>
                                        <td class="py-3.5 px-5 text-slate-500 font-medium">
                                            Rp {{ number_format($item->bunga_per_bulan, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3.5 px-5 font-bold text-slate-800">
                                            Rp {{ number_format($item->sisa_tagihan, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3.5 px-5 text-right">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold 
                                                {{ $item->status === 'Berjalan' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : '' }}
                                                {{ $item->status === 'Pengajuan' ? 'bg-amber-50 text-amber-700 border border-amber-200' : '' }}
                                                {{ $item->status === 'Ditolak' ? 'bg-rose-50 text-rose-700 border border-rose-200' : '' }}
                                                {{ $item->status === 'Lunas' ? 'bg-blue-50 text-blue-700 border border-blue-200' : '' }}">
                                                {{ $item->status === 'Pengajuan' ? 'Menunggu Approval' : $item->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-8 text-center text-xs text-slate-400">
                                            Belum ada catatan permohonan pinjaman.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if(method_exists($pinjaman, 'hasPages') && $pinjaman->hasPages())
                        <div class="p-4 border-t border-slate-100">
                            {{ $pinjaman->links() }}
                        </div>
                    @endif
                </div>
            </main>
        </div>
    </div>

    <script>
        const inputJumlah = document.getElementById('input_jumlah_pinjam');
        const inputTenor = document.getElementById('input_tenor');
        const previewPokok = document.getElementById('preview_pokok');
        const previewBunga = document.getElementById('preview_bunga');
        const previewTotal = document.getElementById('preview_total');

        function formatRupiah(number) {
            return 'Rp ' + Math.round(number).toLocaleString('id-ID');
        }

        function calculateInstallment() {
            const jumlah = parseFloat(inputJumlah.value) || 0;
            const tenor = parseInt(inputTenor.value) || 0;

            if (jumlah > 0 && tenor > 0) {
                const pokok = jumlah / tenor;
                const bunga = jumlah * 0.015;
                const total = pokok + bunga;

                previewPokok.innerText = formatRupiah(pokok);
                previewBunga.innerText = formatRupiah(bunga);
                previewTotal.innerText = formatRupiah(total);
            } else {
                previewPokok.innerText = 'Rp 0';
                previewBunga.innerText = 'Rp 0';
                previewTotal.innerText = 'Rp 0';
            }
        }

        inputJumlah.addEventListener('input', calculateInstallment);
        inputTenor.addEventListener('input', calculateInstallment);
    </script>
</body>
</html>
