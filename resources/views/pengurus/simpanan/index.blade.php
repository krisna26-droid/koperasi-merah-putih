<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi Simpanan - {{ $cooperativeSettings->name ?? 'Koperasi Merah Putih' }}</title>
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
            <x-page-header title="Transaksi Simpanan" />

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
                    <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Transaksi Simpanan</h1>
                    <p class="text-xs text-slate-500 mt-1">Pencatatan setoran simpanan pokok, wajib, dan sukarela anggota koperasi.</p>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm space-y-5">
                    <div class="flex items-center space-x-3 pb-4 border-b border-slate-50">
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <i class="fi fi-rr-envelope text-base"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-extrabold text-slate-800">Input Setoran Baru</h3>
                            <p class="text-xs text-slate-400">Setiap setoran otomatis memicu entri jurnal umum berpasangan.</p>
                        </div>
                    </div>

                    <form action="{{ route('pengurus.simpanan.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Pilih Anggota</label>
                            <select name="id_anggota" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 font-medium text-slate-800 transition" required>
                                <option value="">-- Pilih Anggota --</option>
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
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Jenis Simpanan</label>
                            <select name="jenis_simpanan" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 font-medium text-slate-800 transition" required>
                                <option value="Pokok" {{ old('jenis_simpanan') === 'Pokok' ? 'selected' : '' }}>Simpanan Pokok (Akun 301)</option>
                                <option value="Wajib" {{ old('jenis_simpanan') === 'Wajib' ? 'selected' : '' }}>Simpanan Wajib (Akun 302)</option>
                                <option value="Sukarela" {{ old('jenis_simpanan') === 'Sukarela' ? 'selected' : '' }}>Simpanan Sukarela (Akun 303)</option>
                            </select>
                            @error('jenis_simpanan')
                                <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Nominal Setoran (Rp)</label>
                            <input type="number" name="nominal" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 font-medium text-slate-800 transition" placeholder="0" min="1000" step="1000" value="{{ old('nominal') }}" required>
                            @error('nominal')
                                <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-3 flex justify-end pt-2">
                            <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-sm transition flex items-center space-x-2">
                                <i class="fi fi-rr-disk text-xs"></i>
                                <span>Simpan Setoran</span>
                            </button>
                        </div>
                    </form>
                </div>

                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-extrabold text-slate-800">Riwayat Transaksi Simpanan</h3>
                            <p class="text-xs text-slate-400">Catatan mutasi simpanan anggota dan status posting akuntansinya.</p>
                        </div>
                        <x-export-buttons report="simpanan" />
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/70 border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                    <th class="py-3.5 px-5">ID Simpanan</th>
                                    <th class="py-3.5 px-5">Tanggal</th>
                                    <th class="py-3.5 px-5">Anggota</th>
                                    <th class="py-3.5 px-5">Jenis</th>
                                    <th class="py-3.5 px-5">Jumlah Setoran</th>
                                    <th class="py-3.5 px-5 text-right">Status Jurnal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 text-xs">
                                @forelse($simpanan as $item)
                                    <tr class="hover:bg-slate-50/60 transition">
                                        <td class="py-3.5 px-5 font-mono font-bold text-slate-700">
                                            {{ $item->id_simpanan }}
                                        </td>
                                        <td class="py-3.5 px-5 text-slate-500 font-mono">
                                            {{ $item->created_at ? $item->created_at->translatedFormat('d/m/Y H:i') : '-' }}
                                        </td>
                                        <td class="py-3.5 px-5">
                                            <p class="font-bold text-slate-800">{{ $item->anggota->nama ?? '-' }}</p>
                                            <p class="text-[10px] text-slate-400 font-mono">{{ $item->id_anggota }}</p>
                                        </td>
                                        <td class="py-3.5 px-5">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold {{ $item->jenis_simpanan === 'Pokok' ? 'bg-blue-50 text-blue-700 border border-blue-200' : ($item->jenis_simpanan === 'Wajib' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200') }}">
                                                {{ $item->jenis_simpanan }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-5 font-extrabold text-slate-800">
                                            Rp {{ number_format($item->nominal, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3.5 px-5 text-right">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold {{ $item->is_jurnaled ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                                {{ $item->is_jurnaled ? 'Terposting' : 'Pending' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-8 text-center text-xs text-slate-400">
                                            Belum ada data transaksi simpanan.
                                        </td>
                                    </tr>
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
