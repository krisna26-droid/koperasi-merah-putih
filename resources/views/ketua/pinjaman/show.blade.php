<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pinjaman - {{ $pinjaman->id_pinjam }}</title>
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
            <x-page-header title="Pinjaman" />

            <main class="p-8 space-y-6">
                <!-- Notifications -->
                @if(session('success'))
                    <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold rounded-xl flex items-center space-x-2">
                        <i class="fi fi-rr-check-circle text-base"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif
                @if(session('error'))
                    <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold rounded-xl flex items-center space-x-2">
                        <i class="fi fi-rr-cross-circle text-base"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                <!-- Back Link -->
                <a href="{{ route('ketua.pinjaman.index') }}" class="inline-flex items-center space-x-2 text-xs font-semibold text-slate-500 hover:text-slate-800 transition">
                    <i class="fi fi-rr-arrow-small-left text-base"></i>
                    <span>Kembali ke pinjaman</span>
                </a>

                <!-- Header Title & Status -->
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Detail {{ $pinjaman->id_pinjam }}</h1>
                        <p class="text-xs text-slate-500 mt-1">Informasi lengkap pengajuan dan status pinjaman.</p>
                    </div>

                    @if($pinjaman->status === 'Pengajuan')
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                            Menunggu Persetujuan
                        </span>
                    @elseif($pinjaman->status === 'Berjalan')
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Aktif
                        </span>
                    @elseif($pinjaman->status === 'Lunas')
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                            Lunas
                        </span>
                    @else
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                            Ditolak
                        </span>
                    @endif
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    <!-- Sisi Kiri: Anggota & Rincian Pinjaman (7 Cols) -->
                    <div class="lg:col-span-7 space-y-6">
                        <!-- Kartu Profil Peminjam -->
                        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm space-y-4">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-extrabold text-sm">
                                    {{ strtoupper(substr($pinjaman->anggota->nama ?? 'A', 0, 2)) }}
                                </div>
                                <div>
                                    <p class="text-[11px] text-slate-400 font-semibold">Anggota peminjam</p>
                                    <h3 class="text-base font-bold text-slate-800">{{ $pinjaman->anggota->nama }}</h3>
                                    <p class="text-xs text-slate-400">{{ $pinjaman->id_anggota }} · Anggota aktif</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 pt-4 border-t border-slate-100 text-xs">
                                <div>
                                    <p class="text-slate-400">Alamat</p>
                                    <p class="font-bold text-slate-700 mt-0.5">{{ $pinjaman->anggota->alamat ?? 'Br. Buruan, Desa Buruan' }}</p>
                                </div>
                                <div>
                                    <p class="text-slate-400">Nomor telepon</p>
                                    <p class="font-bold text-slate-700 mt-0.5">{{ $pinjaman->anggota->no_telepon ?? '-' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Pinjaman -->
                        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                            <h3 class="text-sm font-bold text-slate-800 mb-4">Informasi pinjaman</h3>
                            <div class="grid grid-cols-3 gap-y-5 gap-x-4 text-xs">
                                <div>
                                    <p class="text-slate-400">Jumlah pinjaman</p>
                                    <p class="text-base font-extrabold text-slate-800 mt-0.5">Rp {{ number_format($pinjaman->jumlah_pinjam, 0, ',', '.') }}</p>
                                </div>
                                <div>
                                    <p class="text-slate-400">Bunga per bulan</p>
                                    <p class="text-base font-extrabold text-slate-800 mt-0.5">1.5%</p>
                                </div>
                                <div>
                                    <p class="text-slate-400">Tenor</p>
                                    <p class="text-base font-extrabold text-slate-800 mt-0.5">{{ $pinjaman->tenor }} <span class="text-xs font-normal text-slate-500">bulan</span></p>
                                </div>
                                <div>
                                    <p class="text-slate-400">Angsuran per bulan</p>
                                    <p class="text-base font-extrabold text-emerald-600 mt-0.5">Rp {{ number_format($pinjaman->pokok_per_bulan + $pinjaman->bunga_per_bulan, 0, ',', '.') }}</p>
                                </div>
                                <div>
                                    <p class="text-slate-400">Tanggal pengajuan</p>
                                    <p class="font-bold text-slate-700 mt-0.5">{{ $pinjaman->created_at->translatedFormat('d F Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-slate-400">Tujuan</p>
                                    <p class="font-bold text-slate-700 mt-0.5">Modal usaha warung</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sisi Kanan: Panel Persetujuan & Riwayat Pinjaman (5 Cols) -->
                    <div class="lg:col-span-5 space-y-6">
                        <!-- Panel Persetujuan -->
                        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                            <h3 class="text-sm font-bold text-slate-800">Panel persetujuan</h3>
                            <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                                Pastikan informasi peminjam dan kemampuan angsuran telah sesuai sebelum mengambil keputusan.
                            </p>

                            @if($pinjaman->status === 'Pengajuan')
                                <div class="flex items-center space-x-3 mt-5">
                                    <form action="{{ route('ketua.pinjaman.approve', $pinjaman->id_pinjam) }}" method="POST" class="flex-1">
                                        @csrf
                                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold py-2.5 rounded-xl shadow-sm transition flex items-center justify-center space-x-1.5">
                                            <span>✓</span>
                                            <span>Setujui</span>
                                        </button>
                                    </form>

                                    <form action="{{ route('ketua.pinjaman.reject', $pinjaman->id_pinjam) }}" method="POST" class="flex-1">
                                        @csrf
                                        <button type="submit" class="w-full bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-bold py-2.5 rounded-xl transition flex items-center justify-center space-x-1.5">
                                            <span>✕</span>
                                            <span>Tolak</span>
                                        </button>
                                    </form>
                                </div>
                            @else
                                <div class="mt-4 p-3 bg-slate-50 rounded-xl text-center text-xs text-slate-500 font-medium">
                                    Keputusan telah diambil (Status: <strong class="text-slate-800">{{ $pinjaman->status }}</strong>)
                                </div>
                            @endif
                        </div>

                        <!-- Riwayat Pinjaman Anggota Terkait -->
                        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                            <h3 class="text-sm font-bold text-slate-800 mb-4">Riwayat pinjaman</h3>
                            <div class="space-y-3 text-xs">
                                @forelse($riwayatPinjaman as $rp)
                                    <div class="p-3 bg-slate-50 rounded-xl flex items-center justify-between">
                                        <div>
                                            <p class="font-bold text-slate-800">{{ $rp->id_pinjam }}</p>
                                            <p class="text-[11px] text-slate-400 mt-0.5">{{ $rp->created_at->translatedFormat('d F Y') }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-extrabold text-slate-800">Rp {{ number_format($rp->jumlah_pinjam / 1000000, 1, ',', '.') }} Jt</p>
                                            <span class="text-[10px] font-bold {{ $rp->status === 'Pengajuan' ? 'text-amber-600' : ($rp->status === 'Berjalan' ? 'text-emerald-600' : 'text-blue-600') }}">
                                                {{ $rp->status === 'Pengajuan' ? 'Menunggu Persetujuan' : ($rp->status === 'Berjalan' ? 'Aktif' : $rp->status) }}
                                            </span>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-slate-400 text-center py-2">Belum ada riwayat pinjaman.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>