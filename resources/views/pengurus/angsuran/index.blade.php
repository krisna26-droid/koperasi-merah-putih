<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi Angsuran - {{ $cooperativeSettings->name ?? 'Koperasi Merah Putih' }}</title>
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
            <x-page-header title="Transaksi Pembayaran Angsuran" />

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
                    <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Transaksi Angsuran Pinjaman</h1>
                    <p class="text-xs text-slate-500 mt-1">Penerimaan pembayaran cicilan pokok dan pendapatan bunga kredit pinjaman.</p>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm space-y-5">
                    <div class="flex items-center space-x-3 pb-4 border-b border-slate-50">
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <i class="fi fi-rr-receipt text-base"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-extrabold text-slate-800">Form Pembayaran Angsuran</h3>
                            <p class="text-xs text-slate-400">Pilih nomor pinjaman berstatus berjalan untuk memproses angsuran bulanan.</p>
                        </div>
                    </div>

                    <form action="{{ route('pengurus.angsuran.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Pilih Pinjaman Berjalan</label>
                                <select id="select_pinjam" name="id_pinjam" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 font-medium text-slate-800 transition" required>
                                    <option value="" data-pokok="0" data-bunga="0" data-sisa="0" data-total="0">-- Pilih Pinjaman Aktif --</option>
                                    @foreach($pinjamanBerjalan as $item)
                                        <option value="{{ $item->id_pinjam }}" 
                                            data-pokok="{{ $item->pokok_per_bulan }}" 
                                            data-bunga="{{ $item->bunga_per_bulan }}" 
                                            data-sisa="{{ $item->sisa_tagihan }}"
                                            data-total="{{ $item->pokok_per_bulan + $item->bunga_per_bulan }}"
                                            {{ old('id_pinjam') === $item->id_pinjam ? 'selected' : '' }}>
                                            {{ $item->id_pinjam }} - {{ $item->anggota->nama ?? $item->id_anggota }} (Sisa Pokok: Rp {{ number_format($item->sisa_tagihan, 0, ',', '.') }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_pinjam')
                                    <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex items-end">
                                <button type="submit" class="w-full h-[38px] px-5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-sm transition flex items-center justify-center space-x-2">
                                    <i class="fi fi-rr-check text-xs"></i>
                                    <span>Proses & Bayar Angsuran</span>
                                </button>
                            </div>
                        </div>

                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 grid grid-cols-2 md:grid-cols-4 gap-4 text-xs">
                            <div>
                                <span class="text-slate-400 block text-[11px]">Sisa Kewajiban Pokok:</span>
                                <span id="preview_sisa" class="font-bold text-slate-800 text-xs">Rp 0</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[11px]">Porsi Pokok:</span>
                                <span id="preview_pokok" class="font-bold text-slate-800 text-xs">Rp 0</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[11px]">Porsi Bunga (1.5%):</span>
                                <span id="preview_bunga" class="font-bold text-slate-800 text-xs">Rp 0</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[11px]">Total Tagihan Bulanan:</span>
                                <span id="preview_total" class="font-extrabold text-emerald-600 text-sm">Rp 0</span>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-extrabold text-slate-800">Riwayat Pembayaran Angsuran</h3>
                            <p class="text-xs text-slate-400">Daftar transaksi penerimaan angsuran yang telah dijurnal otomatis.</p>
                        </div>
                        <x-export-buttons report="angsuran" />
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/70 border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                    <th class="py-3.5 px-5">ID Angsuran</th>
                                    <th class="py-3.5 px-5">ID Pinjam</th>
                                    <th class="py-3.5 px-5">Angsuran Ke</th>
                                    <th class="py-3.5 px-5">Porsi Pokok</th>
                                    <th class="py-3.5 px-5">Porsi Bunga (1.5%)</th>
                                    <th class="py-3.5 px-5">Total Pembayaran</th>
                                    <th class="py-3.5 px-5 text-right">Status Jurnal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 text-xs">
                                @forelse($angsuran as $item)
                                    <tr class="hover:bg-slate-50/60 transition">
                                        <td class="py-3.5 px-5 font-mono font-bold text-slate-700">
                                            {{ $item->id_angsuran }}
                                        </td>
                                        <td class="py-3.5 px-5 font-mono text-slate-500">
                                            {{ $item->id_pinjam }}
                                        </td>
                                        <td class="py-3.5 px-5">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700">
                                                Ke-{{ $item->angsuran_ke }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-5 text-slate-700 font-medium">
                                            Rp {{ number_format($item->bayar_pokok, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3.5 px-5 text-slate-500">
                                            Rp {{ number_format($item->bayar_bunga, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3.5 px-5 font-extrabold text-emerald-700">
                                            Rp {{ number_format($item->total_bayar, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3.5 px-5 text-right">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold {{ $item->is_jurnaled ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                                {{ $item->is_jurnaled ? 'Terposting' : 'Pending' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-8 text-center text-xs text-slate-400">
                                            Belum ada riwayat pembayaran angsuran.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if(method_exists($angsuran, 'hasPages') && $angsuran->hasPages())
                        <div class="p-4 border-t border-slate-100">
                            {{ $angsuran->links() }}
                        </div>
                    @endif
                </div>
            </main>
        </div>
    </div>

    <script>
        const selectPinjam = document.getElementById('select_pinjam');
        const previewSisa = document.getElementById('preview_sisa');
        const previewPokok = document.getElementById('preview_pokok');
        const previewBunga = document.getElementById('preview_bunga');
        const previewTotal = document.getElementById('preview_total');

        function formatRupiah(num) {
            return 'Rp ' + Math.round(num).toLocaleString('id-ID');
        }

        function updatePreview() {
            const selected = selectPinjam.options[selectPinjam.selectedIndex];
            const pokok = parseFloat(selected.getAttribute('data-pokok')) || 0;
            const bunga = parseFloat(selected.getAttribute('data-bunga')) || 0;
            const sisa = parseFloat(selected.getAttribute('data-sisa')) || 0;
            const total = parseFloat(selected.getAttribute('data-total')) || 0;

            previewSisa.innerText = formatRupiah(sisa);
            previewPokok.innerText = formatRupiah(pokok);
            previewBunga.innerText = formatRupiah(bunga);
            previewTotal.innerText = formatRupiah(total);
        }

        selectPinjam.addEventListener('change', updatePreview);
        if (selectPinjam.value) {
            updatePreview();
        }
    </script>
</body>
</html>
