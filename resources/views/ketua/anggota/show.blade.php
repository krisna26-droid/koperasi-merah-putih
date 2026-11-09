<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Anggota - {{ $anggota->nama }}</title>
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
            <x-page-header title="Anggota" />

            <main class="p-8 space-y-6">
                <!-- Back Link -->
                <a href="{{ route('ketua.anggota.index') }}" class="inline-flex items-center space-x-2 text-xs font-semibold text-slate-500 hover:text-slate-800 transition">
                    <i class="fi fi-rr-arrow-small-left text-base"></i>
                    <span>Kembali ke anggota</span>
                </a>

                <!-- Member Identity Card -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                    <div class="flex items-center space-x-4">
                        <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-extrabold text-base">
                            {{ strtoupper(substr($anggota->nama, 0, 2)) }}
                        </div>
                        <div>
                            <div class="flex items-center space-x-2">
                                <h1 class="text-xl font-extrabold text-slate-800">{{ $anggota->nama }}</h1>
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $anggota->status === 'Aktif' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                                    {{ $anggota->status }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-400 mt-1">
                                {{ $anggota->id_anggota }} · Bergabung {{ $anggota->created_at->translatedFormat('d F Y') }}
                            </p>
                            <div class="flex items-center space-x-4 text-xs text-slate-500 mt-2 font-medium">
                                <span>{{ $anggota->no_telepon }}</span>
                                <span>·</span>
                                <span>{{ $anggota->email ?? strtolower(str_replace(' ', '', $anggota->nama)) . '@email.com' }}</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <button type="button" class="px-4 py-2 border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-xl transition flex items-center space-x-2 shadow-sm">
                            <span>•••</span>
                            <span>Aksi anggota</span>
                        </button>
                    </div>
                </div>

                <!-- Tabs Navigation -->
                <div class="border-b border-slate-200/80 flex space-x-8 text-xs font-semibold text-slate-500 select-none">
                    <button type="button" onclick="switchTab('overview')" id="tab-btn-overview" class="pb-3 border-b-2 font-bold transition text-emerald-600 border-emerald-600">Overview</button>
                    <button type="button" onclick="switchTab('simpanan')" id="tab-btn-simpanan" class="pb-3 border-b-2 font-bold transition text-slate-500 border-transparent hover:text-slate-800">Simpanan</button>
                    <button type="button" onclick="switchTab('pinjaman')" id="tab-btn-pinjaman" class="pb-3 border-b-2 font-bold transition text-slate-500 border-transparent hover:text-slate-800">Pinjaman</button>
                    <button type="button" onclick="switchTab('angsuran')" id="tab-btn-angsuran" class="pb-3 border-b-2 font-bold transition text-slate-500 border-transparent hover:text-slate-800">Angsuran</button>
                    <button type="button" onclick="switchTab('riwayat')" id="tab-btn-riwayat" class="pb-3 border-b-2 font-bold transition text-slate-500 border-transparent hover:text-slate-800">Riwayat Transaksi</button>
                </div>

                <!-- TAB 1: OVERVIEW -->
                <div id="tab-content-overview" class="space-y-6">
                    <!-- 4 Stat Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
                            <div>
                                <p class="text-xs font-semibold text-slate-400">Total simpanan</p>
                                <h3 class="text-2xl font-extrabold text-slate-800 mt-1">
                                    @if($totalSimpanan >= 1000000000)
                                        Rp {{ number_format($totalSimpanan / 1000000000, 2, ',', '.') }} M
                                    @elseif($totalSimpanan >= 1000000)
                                        Rp {{ number_format($totalSimpanan / 1000000, 1, ',', '.') }} Jt
                                    @else
                                        Rp {{ number_format($totalSimpanan, 0, ',', '.') }}
                                    @endif
                                </h3>
                            </div>
                            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                <i class="fi fi-rr-envelope text-xl"></i>
                            </div>
                        </div>

                        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
                            <div>
                                <p class="text-xs font-semibold text-slate-400">Sisa pinjaman</p>
                                <h3 class="text-2xl font-extrabold text-slate-800 mt-1">
                                    @if($sisaPinjaman >= 1000000000)
                                        Rp {{ number_format($sisaPinjaman / 1000000000, 2, ',', '.') }} M
                                    @elseif($sisaPinjaman >= 1000000)
                                        Rp {{ number_format($sisaPinjaman / 1000000, 1, ',', '.') }} Jt
                                    @else
                                        Rp {{ number_format($sisaPinjaman, 0, ',', '.') }}
                                    @endif
                                </h3>
                            </div>
                            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                                <i class="fi fi-rr-usd-circle text-xl"></i>
                            </div>
                        </div>

                        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
                            <div>
                                <p class="text-xs font-semibold text-slate-400">Total angsuran</p>
                                <h3 class="text-2xl font-extrabold text-slate-800 mt-1">
                                    @if($totalAngsuran >= 1000000000)
                                        Rp {{ number_format($totalAngsuran / 1000000000, 2, ',', '.') }} M
                                    @elseif($totalAngsuran >= 1000000)
                                        Rp {{ number_format($totalAngsuran / 1000000, 1, ',', '.') }} Jt
                                    @else
                                        Rp {{ number_format($totalAngsuran, 0, ',', '.') }}
                                    @endif
                                </h3>
                            </div>
                            <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                                <i class="fi fi-rr-receipt text-xl"></i>
                            </div>
                        </div>

                        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
                            <div>
                                <p class="text-xs font-semibold text-slate-400">Status pinjaman</p>
                                <h3 class="text-2xl font-extrabold text-slate-800 mt-1">
                                    {{ $pinjamanAktif ? 'Aktif' : 'Tidak Ada' }}
                                </h3>
                            </div>
                            <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
                                <i class="fi fi-rr-shield-check text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Pribadi & Aktivitas -->
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                        <div class="lg:col-span-7 bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                            <h3 class="text-sm font-bold text-slate-800 mb-4">Informasi pribadi</h3>
                            <div class="grid grid-cols-2 gap-y-4 gap-x-6 text-xs">
                                <div>
                                    <p class="text-slate-400">NIK / NIM</p>
                                    <p class="font-bold text-slate-700 mt-0.5">{{ $anggota->nik ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-slate-400">Jenis kelamin</p>
                                    <p class="font-bold text-slate-700 mt-0.5">{{ $anggota->jenis_kelamin ?? 'Laki-laki' }}</p>
                                </div>
                                <div>
                                    <p class="text-slate-400">Pekerjaan</p>
                                    <p class="font-bold text-slate-700 mt-0.5">{{ $anggota->pekerjaan ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-slate-400">Alamat</p>
                                    <p class="font-bold text-slate-700 mt-0.5">{{ $anggota->alamat ?? '-' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="lg:col-span-5 bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-sm font-bold text-slate-800">Aktivitas terbaru</h3>
                                <i class="fi fi-rr-pulse text-slate-400 text-sm"></i>
                            </div>
                            <div class="space-y-4 text-xs">
                                @forelse($simpananTerbaru as $st)
                                    <div class="flex items-start space-x-3">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 mt-1.5 shrink-0"></span>
                                        <div>
                                            <h4 class="font-bold text-slate-800">Simpanan {{ $st->jenis_simpanan }}</h4>
                                            <p class="text-[11px] text-slate-400">{{ $st->created_at->translatedFormat('d F Y') }} · Rp {{ number_format($st->nominal, 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                @empty
                                @endforelse

                                <div class="flex items-start space-x-3">
                                    <span class="w-2 h-2 rounded-full bg-blue-500 mt-1.5 shrink-0"></span>
                                    <div>
                                        <h4 class="font-bold text-slate-800">Menjadi anggota koperasi</h4>
                                        <p class="text-[11px] text-slate-400">{{ $anggota->created_at->translatedFormat('d F Y') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 2: SIMPANAN -->
                <div id="tab-content-simpanan" class="hidden">
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                        @if($simpananList->isEmpty())
                            <div class="p-12 text-center flex flex-col items-center justify-center">
                                <div class="w-12 h-12 rounded-full bg-slate-50 text-slate-400 flex items-center justify-center mb-3">
                                    <i class="fi fi-rr-document text-xl"></i>
                                </div>
                                <h4 class="text-sm font-bold text-slate-800">Belum ada data simpanan</h4>
                                <p class="text-xs text-slate-400 mt-1">Data aktivitas anggota pada kategori ini akan tampil di sini.</p>
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="border-b border-slate-100 bg-slate-50/50 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                            <th class="py-3.5 px-6">Tanggal</th>
                                            <th class="py-3.5 px-6">No. Transaksi</th>
                                            <th class="py-3.5 px-6">Jenis Simpanan</th>
                                            <th class="py-3.5 px-6">Jumlah</th>
                                            <th class="py-3.5 px-6 text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 text-xs">
                                        @foreach($simpananList as $simpanan)
                                            <tr class="hover:bg-slate-50/60 transition">
                                                <td class="py-4 px-6 text-slate-600 font-medium">{{ $simpanan->created_at->translatedFormat('d F Y') }}</td>
                                                <td class="py-4 px-6 font-semibold text-slate-700">{{ $simpanan->id_simpanan }}</td>
                                                <td class="py-4 px-6 font-semibold text-slate-800">{{ $simpanan->jenis_simpanan }}</td>
                                                <td class="py-4 px-6 font-bold text-slate-800">Rp {{ number_format($simpanan->nominal, 0, ',', '.') }}</td>
                                                <td class="py-4 px-6 text-center">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                        Selesai
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- TAB 3: PINJAMAN -->
                <div id="tab-content-pinjaman" class="hidden">
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                        @if($pinjamanList->isEmpty())
                            <div class="p-12 text-center flex flex-col items-center justify-center">
                                <div class="w-12 h-12 rounded-full bg-slate-50 text-slate-400 flex items-center justify-center mb-3">
                                    <i class="fi fi-rr-document text-xl"></i>
                                </div>
                                <h4 class="text-sm font-bold text-slate-800">Belum ada data pinjaman</h4>
                                <p class="text-xs text-slate-400 mt-1">Data aktivitas anggota pada kategori ini akan tampil di sini.</p>
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="border-b border-slate-100 bg-slate-50/50 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                            <th class="py-3.5 px-6">No. Pinjaman</th>
                                            <th class="py-3.5 px-6">Tanggal</th>
                                            <th class="py-3.5 px-6">Jumlah Pinjaman</th>
                                            <th class="py-3.5 px-6">Tenor</th>
                                            <th class="py-3.5 px-6">Sisa Tagihan</th>
                                            <th class="py-3.5 px-6 text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 text-xs">
                                        @foreach($pinjamanList as $pinjam)
                                            <tr class="hover:bg-slate-50/60 transition">
                                                <td class="py-4 px-6 font-semibold text-slate-700">{{ $pinjam->id_pinjam }}</td>
                                                <td class="py-4 px-6 text-slate-600 font-medium">{{ $pinjam->created_at->translatedFormat('d F Y') }}</td>
                                                <td class="py-4 px-6 font-bold text-slate-800">Rp {{ number_format($pinjam->jumlah_pinjam, 0, ',', '.') }}</td>
                                                <td class="py-4 px-6 font-medium text-slate-600">{{ $pinjam->tenor }} Bulan</td>
                                                <td class="py-4 px-6 font-bold text-slate-800">Rp {{ number_format($pinjam->sisa_tagihan, 0, ',', '.') }}</td>
                                                <td class="py-4 px-6 text-center">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $pinjam->status === 'Berjalan' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($pinjam->status === 'Pengajuan' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-blue-50 text-blue-700 border-blue-200') }} border">
                                                        {{ $pinjam->status }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- TAB 4: ANGSURAN -->
                <div id="tab-content-angsuran" class="hidden">
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                        @if($angsuranList->isEmpty())
                            <div class="p-12 text-center flex flex-col items-center justify-center">
                                <div class="w-12 h-12 rounded-full bg-slate-50 text-slate-400 flex items-center justify-center mb-3">
                                    <i class="fi fi-rr-document text-xl"></i>
                                </div>
                                <h4 class="text-sm font-bold text-slate-800">Belum ada data angsuran</h4>
                                <p class="text-xs text-slate-400 mt-1">Data aktivitas anggota pada kategori ini akan tampil di sini.</p>
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="border-b border-slate-100 bg-slate-50/50 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                            <th class="py-3.5 px-6">Tanggal</th>
                                            <th class="py-3.5 px-6">No. Angsuran</th>
                                            <th class="py-3.5 px-6">No. Pinjaman</th>
                                            <th class="py-3.5 px-6">Pokok</th>
                                            <th class="py-3.5 px-6">Bunga</th>
                                            <th class="py-3.5 px-6">Total Bayar</th>
                                            <th class="py-3.5 px-6 text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 text-xs">
                                        @foreach($angsuranList as $angsuran)
                                            <tr class="hover:bg-slate-50/60 transition">
                                                <td class="py-4 px-6 text-slate-600 font-medium">{{ $angsuran->created_at->translatedFormat('d F Y') }}</td>
                                                <td class="py-4 px-6 font-semibold text-slate-700">{{ $angsuran->id_angsuran }}</td>
                                                <td class="py-4 px-6 text-slate-500 font-medium">{{ $angsuran->id_pinjam }}</td>
                                                <td class="py-4 px-6 text-slate-600">Rp {{ number_format($angsuran->bayar_pokok, 0, ',', '.') }}</td>
                                                <td class="py-4 px-6 text-slate-600">Rp {{ number_format($angsuran->bayar_bunga, 0, ',', '.') }}</td>
                                                <td class="py-4 px-6 font-bold text-slate-800">Rp {{ number_format($angsuran->total_bayar, 0, ',', '.') }}</td>
                                                <td class="py-4 px-6 text-center">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                        Selesai
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- TAB 5: RIWAYAT TRANSAKSI -->
                <div id="tab-content-riwayat" class="hidden">
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                        @if($semuaTransaksi->isEmpty())
                            <div class="p-12 text-center flex flex-col items-center justify-center">
                                <div class="w-12 h-12 rounded-full bg-slate-50 text-slate-400 flex items-center justify-center mb-3">
                                    <i class="fi fi-rr-document text-xl"></i>
                                </div>
                                <h4 class="text-sm font-bold text-slate-800">Belum ada data riwayat transaksi</h4>
                                <p class="text-xs text-slate-400 mt-1">Data aktivitas anggota pada kategori ini akan tampil di sini.</p>
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="border-b border-slate-100 bg-slate-50/50 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                            <th class="py-3.5 px-6">Tanggal</th>
                                            <th class="py-3.5 px-6">Kode Transaksi</th>
                                            <th class="py-3.5 px-6">Jenis Transaksi</th>
                                            <th class="py-3.5 px-6">Nominal</th>
                                            <th class="py-3.5 px-6 text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 text-xs">
                                        @foreach($semuaTransaksi as $trx)
                                            <tr class="hover:bg-slate-50/60 transition">
                                                <td class="py-4 px-6 text-slate-600 font-medium">{{ $trx['tanggal']->translatedFormat('d F Y H:i') }}</td>
                                                <td class="py-4 px-6 font-semibold text-slate-700">{{ $trx['kode'] }}</td>
                                                <td class="py-4 px-6 font-bold text-slate-800">{{ $trx['tipe'] }}</td>
                                                <td class="py-4 px-6 font-bold text-slate-800">Rp {{ number_format($trx['nominal'], 0, ',', '.') }}</td>
                                                <td class="py-4 px-6 text-center">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $trx['badge'] }} border">
                                                        {{ $trx['status'] }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- JavaScript Perpindahan Tab -->
    <script>
        function switchTab(activeTab) {
            const tabs = ['overview', 'simpanan', 'pinjaman', 'angsuran', 'riwayat'];

            tabs.forEach(tab => {
                const content = document.getElementById('tab-content-' + tab);
                const btn = document.getElementById('tab-btn-' + tab);

                if (tab === activeTab) {
                    content.classList.remove('hidden');
                    btn.classList.add('text-emerald-600', 'border-emerald-600');
                    btn.classList.remove('text-slate-500', 'border-transparent');
                } else {
                    content.classList.add('hidden');
                    btn.classList.remove('text-emerald-600', 'border-emerald-600');
                    btn.classList.add('text-slate-500', 'border-transparent');
                }
            });
        }
    </script>
</body>
</html>