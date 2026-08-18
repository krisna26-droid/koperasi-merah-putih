<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Anggota - Koperasi Merah Putih</title>
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
            <header class="bg-white border-b border-slate-100 py-3.5 px-8 flex items-center justify-between select-none font-sans">
                <div>
                    <div class="flex items-center space-x-2 text-xs text-slate-400">
                        <span>Koperasi Merah Putih</span>
                        <span>&rsaquo;</span>
                        <span class="text-slate-600 font-medium">Anggota</span>
                    </div>
                    <h2 class="text-base font-bold text-slate-800 mt-0.5">Anggota</h2>
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
                <!-- Notifications -->
                @if(session('success'))
                    <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold rounded-xl flex items-center space-x-2">
                        <i class="fi fi-rr-check-circle text-base"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                <!-- Title & Button Trigger Modal -->
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Manajemen Anggota</h1>
                        <p class="text-xs text-slate-500 mt-1">Kelola data anggota dan informasi keanggotaan koperasi.</p>
                    </div>

                    <button type="button" onclick="openModal()" class="bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-sm shadow-emerald-600/20 transition flex items-center space-x-2">
                        <i class="fi fi-rr-plus flex items-center text-xs"></i>
                        <span>Tambah anggota</span>
                    </button>
                </div>

                <!-- 3 Stat Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-slate-400">Total anggota</p>
                            <h3 class="text-2xl font-extrabold text-slate-800 mt-1">{{ number_format($totalAnggota) }}</h3>
                        </div>
                        <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                            <i class="fi fi-rr-users text-xl flex items-center"></i>
                        </div>
                    </div>
                    <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-slate-400">Anggota aktif</p>
                            <h3 class="text-2xl font-extrabold text-slate-800 mt-1">{{ number_format($anggotaAktif) }}</h3>
                        </div>
                        <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <i class="fi fi-rr-user-check text-xl flex items-center"></i>
                        </div>
                    </div>
                    <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-slate-400">Bergabung bulan ini</p>
                            <h3 class="text-2xl font-extrabold text-slate-800 mt-1">{{ number_format($bergabungBulanIni) }}</h3>
                        </div>
                        <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
                            <i class="fi fi-rr-stats text-xl flex items-center"></i>
                        </div>
                    </div>
                </div>

                <!-- Table Card Container -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                        <form method="GET" action="{{ route('ketua.anggota.index') }}" class="flex-1 w-full flex items-center gap-2">
                            <div class="relative flex-1">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <i class="fi fi-rr-search text-xs flex items-center"></i>
                                </div>
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau nomor anggota..." class="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition placeholder:text-slate-400">
                            </div>
                            <button type="submit" class="px-3.5 py-2 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold rounded-xl transition flex items-center space-x-2 shadow-sm">
                                <i class="fi fi-rr-filter text-xs text-slate-400"></i>
                                <span>Filter</span>
                            </button>
                        </form>
                        <button type="button" class="w-full sm:w-auto px-3.5 py-2 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold rounded-xl transition flex items-center justify-center space-x-2 shadow-sm">
                            <i class="fi fi-rr-download text-xs text-slate-400"></i>
                            <span>Export</span>
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100 bg-slate-50/50 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                    <th class="py-3.5 px-6">Anggota</th>
                                    <th class="py-3.5 px-6">Alamat / Wilayah</th>
                                    <th class="py-3.5 px-6">Kontak</th>
                                    <th class="py-3.5 px-6 text-center">Status</th>
                                    <th class="py-3.5 px-6">Bergabung</th>
                                    <th class="py-3.5 px-6 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-xs">
                                @forelse($anggotaList as $anggota)
                                    <tr class="hover:bg-slate-50/60 transition">
                                        <td class="py-4 px-6">
                                            <div class="flex items-center space-x-3.5">
                                                <div class="w-9 h-9 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-xs">
                                                    {{ strtoupper(substr($anggota->nama, 0, 2)) }}
                                                </div>
                                                <div>
                                                    <h4 class="font-bold text-slate-800 leading-tight">{{ $anggota->nama }}</h4>
                                                    <p class="text-[11px] text-slate-400 mt-0.5">{{ $anggota->id_anggota }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-6 font-medium text-slate-600">
                                            {{ $anggota->alamat ?? '-' }}
                                        </td>
                                        <td class="py-4 px-6">
                                            <p class="font-semibold text-slate-700">{{ $anggota->no_telepon }}</p>
                                            <p class="text-[11px] text-slate-400 lowercase">{{ $anggota->email ?? strtolower(str_replace(' ', '', $anggota->nama)) . '@email.com' }}</p>
                                        </td>
                                        <td class="py-4 px-6 text-center">
                                            @if($anggota->status === 'Aktif')
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                                    Aktif
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400 mr-1.5"></span>
                                                    {{ $anggota->status }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-6 text-slate-600 font-medium">
                                            {{ $anggota->created_at->translatedFormat('d F Y') }}
                                        </td>
                                        <td class="py-4 px-6 text-center">
                                            <a href="{{ route('ketua.anggota.show', $anggota->id_anggota) }}" class="p-1.5 text-slate-400 hover:text-emerald-600 rounded-lg hover:bg-emerald-50 transition inline-flex items-center" title="Lihat Detail">
                                                <i class="fi fi-rr-eye text-sm flex items-center"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-8 text-center text-slate-400">
                                            Tidak ada data anggota yang ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($anggotaList->hasPages())
                        <div class="p-4 border-t border-slate-100 bg-white">
                            {{ $anggotaList->links() }}
                        </div>
                    @endif
                </div>
            </main>
        </div>
    </div>

    <!-- MODAL TAMBAH ANGGOTA BARU -->
    <div id="modalTambahAnggota" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden">
        <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl overflow-hidden animate-in fade-in zoom-in duration-150">
            <!-- Modal Header -->
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-800">Tambah anggota baru</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Lengkapi informasi anggota koperasi.</p>
                </div>
                <button type="button" onclick="closeModal()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-50 transition">
                    <i class="fi fi-rr-cross-small text-base"></i>
                </button>
            </div>

            <!-- Modal Form -->
            <form action="{{ route('ketua.anggota.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <!-- Nomor Anggota & Status -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nomor anggota <span class="text-rose-500">*</span></label>
                        <input type="text" name="id_anggota" value="{{ $generatedId }}" required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Status <span class="text-rose-500">*</span></label>
                        <select name="status" required class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition">
                            <option value="Aktif">Aktif</option>
                            <option value="Cuti">Cuti</option>
                            <option value="Nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>

                <!-- Nama Lengkap -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama lengkap <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama" required placeholder="Contoh: I Made Arya" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition placeholder:text-slate-400">
                </div>

                <!-- NIK & Jenis Kelamin -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">NIK / NIM <span class="text-rose-500">*</span></label>
                        <input type="text" name="nik" required placeholder="Masukkan nomor identitas" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition placeholder:text-slate-400">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Jenis kelamin <span class="text-rose-500">*</span></label>
                        <select name="jenis_kelamin" required class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition">
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                </div>

                <!-- Pekerjaan & Nomor Telepon -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Pekerjaan</label>
                        <input type="text" name="pekerjaan" placeholder="Petani / Pedagang" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition placeholder:text-slate-400">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nomor telepon <span class="text-rose-500">*</span></label>
                        <input type="text" name="no_telepon" required placeholder="08xx-xxxx-xxxx" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition placeholder:text-slate-400">
                    </div>
                </div>

                <!-- Alamat -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Alamat <span class="text-rose-500">*</span></label>
                    <input type="text" name="alamat" required placeholder="Alamat lengkap anggota" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition placeholder:text-slate-400">
                </div>

                <!-- Email & Tanggal Bergabung -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Email</label>
                        <input type="email" name="email" placeholder="nama@email.com" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition placeholder:text-slate-400">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal bergabung</label>
                        <input type="date" name="created_at" value="{{ now()->format('Y-m-d') }}" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition">
                    </div>
                </div>

                <!-- Modal Actions -->
                <div class="pt-4 flex items-center justify-end space-x-2 border-t border-slate-100">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-sm transition">
                        Simpan anggota
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Script Modal -->
    <script>
        function openModal() {
            document.getElementById('modalTambahAnggota').classList.remove('hidden');
        }
        function closeModal() {
            document.getElementById('modalTambahAnggota').classList.add('hidden');
        }
    </script>
</body>
</html>