<!-- Flaticon UIcons CDN -->
<link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.1.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
<link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.1.0/uicons-solid-rounded/css/uicons-solid-rounded.css'>

<aside class="w-64 bg-white border-r border-slate-100 min-h-screen flex flex-col justify-between p-4 select-none font-sans">
    <div>
        <!-- Brand Logo -->
        <div class="flex items-center space-x-3 px-2 py-3 mb-6">
            <div class="w-9 h-9 bg-emerald-600 rounded-xl flex items-center justify-center text-white font-bold shadow-sm shadow-emerald-200">
                <i class="fi fi-sr-building flex items-center text-lg"></i>
            </div>
            <div>
                <h1 class="text-sm font-bold text-slate-800 leading-tight">Koperasi Merah Putih</h1>
                <p class="text-[11px] text-slate-400">Desa Buruan · Gianyar</p>
            </div>
        </div>

        <!-- Navigation Group -->
        <nav class="space-y-6">
            <!-- UTAMA -->
            <div>
                <span class="px-3 text-[10px] font-bold text-slate-400 tracking-wider uppercase">UTAMA</span>
                <div class="mt-2 space-y-1">
                    <a href="{{ route('pengurus.dashboard') }}" class="flex items-center justify-between px-3 py-2 text-xs font-semibold rounded-xl transition-all {{ request()->routeIs('pengurus.dashboard') ? 'bg-emerald-50 text-emerald-600' : 'text-slate-500 hover:bg-slate-50' }}">
                        <div class="flex items-center space-x-2.5">
                            <i class="fi fi-rr-apps flex items-center text-sm"></i>
                            <span>Dashboard</span>
                        </div>
                        @if(request()->routeIs('pengurus.dashboard'))
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        @endif
                    </a>
                </div>
            </div>

            <!-- OPERASIONAL -->
            <div>
                <span class="px-3 text-[10px] font-bold text-slate-400 tracking-wider uppercase">OPERASIONAL</span>
                <div class="mt-2 space-y-1">
                    <a href="{{ route('pengurus.anggota.index') }}" class="flex items-center justify-between px-3 py-2 text-xs font-semibold rounded-xl transition-all {{ request()->routeIs('pengurus.anggota.*') ? 'bg-emerald-50 text-emerald-600' : 'text-slate-500 hover:bg-slate-50' }}">
                        <div class="flex items-center space-x-2.5">
                            <i class="fi fi-rr-users flex items-center text-sm"></i>
                            <span>Anggota</span>
                        </div>
                        @if(request()->routeIs('pengurus.anggota.*'))
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        @endif
                    </a>
                    <a href="{{ route('pengurus.simpanan.index') }}" class="flex items-center justify-between px-3 py-2 text-xs font-semibold rounded-xl transition-all {{ request()->routeIs('pengurus.simpanan.*') ? 'bg-emerald-50 text-emerald-600' : 'text-slate-500 hover:bg-slate-50' }}">
                        <div class="flex items-center space-x-2.5">
                            <i class="fi fi-rr-envelope flex items-center text-sm"></i>
                            <span>Simpanan</span>
                        </div>
                        @if(request()->routeIs('pengurus.simpanan.*'))
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        @endif
                    </a>
                    <a href="{{ route('pengurus.pinjaman.index') }}" class="flex items-center justify-between px-3 py-2 text-xs font-semibold rounded-xl transition-all {{ request()->routeIs('pengurus.pinjaman.*') ? 'bg-emerald-50 text-emerald-600' : 'text-slate-500 hover:bg-slate-50' }}">
                        <div class="flex items-center space-x-2.5">
                            <i class="fi fi-rr-dollar flex items-center text-sm"></i>
                            <span>Pinjaman</span>
                        </div>
                        @if(request()->routeIs('pengurus.pinjaman.*'))
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        @endif
                    </a>
                    <a href="{{ route('pengurus.angsuran.index') }}" class="flex items-center justify-between px-3 py-2 text-xs font-semibold rounded-xl transition-all {{ request()->routeIs('pengurus.angsuran.*') ? 'bg-emerald-50 text-emerald-600' : 'text-slate-500 hover:bg-slate-50' }}">
                        <div class="flex items-center space-x-2.5">
                            <i class="fi fi-rr-receipt flex items-center text-sm"></i>
                            <span>Angsuran</span>
                        </div>
                        @if(request()->routeIs('pengurus.angsuran.*'))
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        @endif
                    </a>
                </div>
            </div>

            <!-- AKUNTANSI -->
            <div>
                <span class="px-3 text-[10px] font-bold text-slate-400 tracking-wider uppercase">AKUNTANSI</span>
                <div class="mt-2 space-y-1">
                    <a href="{{ route('pengurus.jurnal.index') }}" class="flex items-center justify-between px-3 py-2 text-xs font-semibold rounded-xl transition-all {{ request()->routeIs('pengurus.jurnal.*') ? 'bg-emerald-50 text-emerald-600' : 'text-slate-500 hover:bg-slate-50' }}">
                        <div class="flex items-center space-x-2.5">
                            <i class="fi fi-rr-book-alt flex items-center text-sm"></i>
                            <span>Jurnal Umum</span>
                        </div>
                        @if(request()->routeIs('pengurus.jurnal.*'))
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        @endif
                    </a>
                    <a href="{{ route('pengurus.buku-besar.index') }}" class="flex items-center justify-between px-3 py-2 text-xs font-semibold rounded-xl transition-all {{ request()->routeIs('pengurus.buku-besar.*') ? 'bg-emerald-50 text-emerald-600' : 'text-slate-500 hover:bg-slate-50' }}">
                        <div class="flex items-center space-x-2.5">
                            <i class="fi fi-rr-document flex items-center text-sm"></i>
                            <span>Buku Besar</span>
                        </div>
                        @if(request()->routeIs('pengurus.buku-besar.*'))
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        @endif
                    </a>
                </div>
            </div>

            <!-- ANALISIS -->
            <div>
                <span class="px-3 text-[10px] font-bold text-slate-400 tracking-wider uppercase">ANALISIS</span>
                <div class="mt-2 space-y-1">
                    <a href="{{ route('pengurus.laporan.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-xs font-semibold {{ request()->routeIs('pengurus.laporan.*') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
                        <i class="fi fi-rr-document text-base"></i>
                        <span>Laporan</span>
                    </a>
                </div>
            </div>
        </nav>
    </div>

    <!-- Bottom Profile Status -->
    <div class="pt-4 border-t border-slate-100">
        <a href="{{ route('profile.edit') }}" class="flex items-center space-x-2.5 px-3 py-2 mb-3 text-xs font-medium text-slate-500 hover:bg-slate-50 rounded-xl transition-all">
            <i class="fi fi-rr-settings flex items-center text-sm"></i>
            <span>Pengaturan</span>
        </a>

        <div class="flex items-center space-x-3 p-2.5 bg-emerald-50/60 rounded-xl border border-emerald-100/60">
            <div class="w-8 h-8 rounded-lg bg-emerald-200/70 text-emerald-800 flex items-center justify-center font-bold text-xs">
                AP
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-bold text-slate-800 truncate">Admin Pengurus</p>
                <p class="text-[10px] text-emerald-600 font-medium truncate">Akses aktif</p>
            </div>
        </div>
    </div>
</aside>