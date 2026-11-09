<aside class="w-64 bg-white border-r border-slate-100 min-h-screen flex flex-col justify-between p-4 select-none font-sans">
    <div>
        <div class="flex items-center space-x-3 px-2 py-3 mb-6">
            @if($cooperativeSettings->logo_path)
                <img src="{{ route('cooperative.logo') }}?v={{ $cooperativeSettings->updated_at?->timestamp ?? time() }}" alt="Logo {{ $cooperativeSettings->name }}" class="h-10 w-10 shrink-0 rounded-xl bg-white object-contain p-1 shadow-sm">
            @else
                <div class="w-9 h-9 shrink-0 rounded-xl bg-emerald-600 flex items-center justify-center text-white font-bold shadow-sm shadow-emerald-200"><i class="fi fi-sr-building flex items-center text-lg"></i></div>
            @endif
            <div class="min-w-0">
                <h1 class="text-sm font-bold text-slate-800 leading-tight">{{ $cooperativeSettings->name }}</h1>
                <p class="truncate text-[11px] text-slate-400" title="{{ $cooperativeSettings->address }}">{{ $cooperativeSettings->address }}</p>
            </div>
        </div>

        <nav class="space-y-5">
            <div>
                <a href="{{ route('ketua.dashboard') }}" class="block px-3 py-2 text-xs font-semibold rounded-xl {{ request()->routeIs('ketua.dashboard') ? 'bg-slate-100 text-slate-800' : 'text-slate-500 hover:bg-slate-50' }}">Dashboard</a>
            </div>
            <div>
                <span class="px-3 text-[10px] font-bold text-slate-400 tracking-wider uppercase">PINJAMAN</span>
                <a href="{{ route('ketua.pinjaman.index') }}" class="mt-2 flex items-center space-x-2.5 px-3 py-2 text-xs font-semibold rounded-xl transition-all {{ request()->routeIs('ketua.pinjaman.*') ? 'bg-emerald-50 text-emerald-600' : 'text-slate-500 hover:bg-slate-50' }}">
                    <i class="fi fi-rr-dollar flex items-center text-sm"></i>
                    <span>Periksa &amp; putuskan</span>
                </a>
                <a href="{{ route('ketua.approval.index') }}" class="mt-1 flex items-center space-x-2.5 px-3 py-2 text-xs font-semibold rounded-xl transition-all {{ request()->routeIs('ketua.approval.*') ? 'bg-emerald-50 text-emerald-600' : 'text-slate-500 hover:bg-slate-50' }}">
                    <i class="fi fi-rr-check-circle flex items-center text-sm"></i>
                    <span>Panel persetujuan</span>
                </a>
            </div>

            <div>
                <span class="px-3 text-[10px] font-bold text-slate-400 tracking-wider uppercase">PEMANTAUAN</span>
                <div class="mt-2 space-y-1">
                    <a href="{{ route('ketua.anggota.index') }}" class="block px-3 py-2 text-xs font-semibold rounded-xl {{ request()->routeIs('ketua.anggota.*') ? 'bg-slate-100 text-slate-800' : 'text-slate-500 hover:bg-slate-50' }}">Anggota</a>
                    <a href="{{ route('ketua.simpanan.index') }}" class="block px-3 py-2 text-xs font-semibold rounded-xl {{ request()->routeIs('ketua.simpanan.*') ? 'bg-slate-100 text-slate-800' : 'text-slate-500 hover:bg-slate-50' }}">Simpanan</a>
                    <a href="{{ route('ketua.angsuran.index') }}" class="block px-3 py-2 text-xs font-semibold rounded-xl {{ request()->routeIs('ketua.angsuran.*') ? 'bg-slate-100 text-slate-800' : 'text-slate-500 hover:bg-slate-50' }}">Angsuran</a>
                    <a href="{{ route('ketua.laporan.jurnal') }}" class="block px-3 py-2 text-xs font-semibold rounded-xl {{ request()->routeIs('ketua.laporan.jurnal*') ? 'bg-slate-100 text-slate-800' : 'text-slate-500 hover:bg-slate-50' }}">Jurnal umum</a>
                    <a href="{{ route('ketua.laporan.buku-besar') }}" class="block px-3 py-2 text-xs font-semibold rounded-xl {{ request()->routeIs('ketua.laporan.buku-besar*') ? 'bg-slate-100 text-slate-800' : 'text-slate-500 hover:bg-slate-50' }}">Buku besar</a>
                    <a href="{{ route('ketua.laporan.index') }}" class="block px-3 py-2 text-xs font-semibold rounded-xl {{ request()->routeIs('ketua.laporan.index') ? 'bg-slate-100 text-slate-800' : 'text-slate-500 hover:bg-slate-50' }}">Laporan</a>
                    <a href="{{ route('ketua.activity-logs.index') }}" class="block px-3 py-2 text-xs font-semibold rounded-xl {{ request()->routeIs('ketua.activity-logs.*') ? 'bg-slate-100 text-slate-800' : 'text-slate-500 hover:bg-slate-50' }}">Log aktivitas</a>
                    <a href="{{ route('ketua.settings.identity.edit') }}" class="block px-3 py-2 text-xs font-semibold rounded-xl {{ request()->routeIs('ketua.settings.identity.*') ? 'bg-slate-100 text-slate-800' : 'text-slate-500 hover:bg-slate-50' }}">Identitas koperasi</a>
                </div>
            </div>
        </nav>
    </div>

    <div class="pt-4 border-t border-slate-100">
        <div class="flex items-center space-x-3 p-2.5 bg-emerald-50/60 rounded-xl border border-emerald-100/60 mb-3">
            <div class="w-8 h-8 rounded-lg bg-emerald-200/70 text-emerald-800 flex items-center justify-center font-bold text-xs">
                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-bold text-slate-800 truncate">{{ Auth::user()->name }}</p>
                <p class="text-[10px] text-emerald-600 font-medium truncate">Ketua Koperasi</p>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center space-x-2.5 px-3 py-2 text-xs font-semibold text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-all">
                <i class="fi fi-rr-sign-out-alt text-sm"></i>
                <span>Keluar</span>
            </button>
        </form>
    </div>
</aside>
