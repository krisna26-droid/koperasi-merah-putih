<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Data Anggota - Koperasi Merah Putih</title>
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
            <header class="bg-white border-b border-slate-100 py-3.5 px-8 flex items-center justify-between select-none font-sans">
                <div>
                    <div class="flex items-center space-x-2 text-xs text-slate-400">
                        <span>Koperasi Merah Putih</span>
                        <span>&rsaquo;</span>
                        <span>Operasional</span>
                        <span>&rsaquo;</span>
                        <span class="text-slate-600 font-medium">Anggota</span>
                    </div>
                    <h2 class="text-base font-bold text-slate-800 mt-0.5">Master Data Anggota</h2>
                </div>

                <div class="flex items-center space-x-5">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-xs ring-2 ring-emerald-50">
                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                        </div>
                        <div class="text-left">
                            <p class="text-xs font-bold text-slate-800 leading-tight">{{ Auth::user()->name }}</p>
                            <p class="text-[10px] text-slate-400">Admin Pengurus</p>
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
                    <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Kelola Data Anggota</h1>
                    <p class="text-xs text-slate-500 mt-1">Registrasi keanggotaan baru dan manajemen daftar anggota aktif koperasi.</p>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm space-y-5">
                    <div class="flex items-center space-x-3 pb-4 border-b border-slate-50">
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <i class="fi fi-rr-user-add text-base"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-extrabold text-slate-800">Registrasi Anggota Baru</h3>
                            <p class="text-xs text-slate-400">Masukkan identitas lengkap calon anggota koperasi.</p>
                        </div>
                    </div>

                    <form action="{{ route('pengurus.anggota.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">ID Anggota</label>
                            <input type="text" name="id_anggota" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 font-mono font-medium text-slate-800 transition" placeholder="e.g. A-001" value="{{ old('id_anggota') }}" required>
                            @error('id_anggota')
                                <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Lengkap</label>
                            <input type="text" name="nama" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 font-medium text-slate-800 transition" placeholder="Nama sesuai KTP" value="{{ old('nama') }}" required>
                            @error('nama')
                                <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">NIK (Opsional)</label>
                            <input type="text" name="nik" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 font-medium text-slate-800 transition" placeholder="16 digit NIK" value="{{ old('nik') }}">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">No. Telepon / WhatsApp</label>
                            <input type="text" name="no_telepon" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 font-medium text-slate-800 transition" placeholder="e.g. 081234567890" value="{{ old('no_telepon') }}" required>
                            @error('no_telepon')
                                <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Status Anggota</label>
                            <select name="status" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 font-medium text-slate-800 transition">
                                <option value="Aktif" {{ old('status') === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="Cuti" {{ old('status') === 'Cuti' ? 'selected' : '' }}>Cuti</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Alamat Lengkap</label>
                            <input type="text" name="alamat" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 font-medium text-slate-800 transition" placeholder="e.g. Br. Buruan, Gianyar" value="{{ old('alamat') }}" required>
                            @error('alamat')
                                <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-3 flex justify-end pt-2">
                            <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-sm transition flex items-center space-x-2">
                                <i class="fi fi-rr-disk text-xs"></i>
                                <span>Simpan Anggota</span>
                            </button>
                        </div>
                    </form>
                </div>

                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-extrabold text-slate-800">Daftar Anggota Koperasi</h3>
                            <p class="text-xs text-slate-400">Total {{ count($anggota) }} anggota terdaftar pada sistem.</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/70 border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                    <th class="py-3.5 px-5">ID Anggota</th>
                                    <th class="py-3.5 px-5">Nama Anggota</th>
                                    <th class="py-3.5 px-5">No. Telepon</th>
                                    <th class="py-3.5 px-5">Alamat</th>
                                    <th class="py-3.5 px-5">Status</th>
                                    <th class="py-3.5 px-5 text-right">Tanggal Gabung</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 text-xs">
                                @forelse($anggota as $item)
                                    <tr class="hover:bg-slate-50/60 transition">
                                        <td class="py-3.5 px-5 font-mono font-bold text-slate-700">
                                            {{ $item->id_anggota }}
                                        </td>
                                        <td class="py-3.5 px-5">
                                            <div class="flex items-center space-x-3">
                                                <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-[11px]">
                                                    {{ strtoupper(substr($item->nama, 0, 2)) }}
                                                </div>
                                                <div>
                                                    <p class="font-bold text-slate-800">{{ $item->nama }}</p>
                                                    @if($item->nik)
                                                        <p class="text-[10px] text-slate-400 font-mono">NIK: {{ $item->nik }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-5 text-slate-600">
                                            {{ $item->no_telepon }}
                                        </td>
                                        <td class="py-3.5 px-5 text-slate-600 max-w-xs truncate">
                                            {{ $item->alamat }}
                                        </td>
                                        <td class="py-3.5 px-5">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold {{ $item->status === 'Aktif' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                                                {{ $item->status }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-5 text-right text-slate-400">
                                            {{ $item->created_at ? $item->created_at->translatedFormat('d M Y') : '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-8 text-center text-xs text-slate-400">
                                            Belum ada data anggota yang tersimpan.
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