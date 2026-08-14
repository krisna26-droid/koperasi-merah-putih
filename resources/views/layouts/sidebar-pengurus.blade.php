<aside class="w-64 bg-slate-800 text-slate-100 min-h-screen p-4 flex flex-col justify-between">
    <div>
        <div class="mb-8 px-2">
            <h1 class="text-lg font-bold text-white tracking-wider">KOPERASI MERAH PUTIH</h1>
            <p class="text-xs text-slate-400">Desa Buruan - System Operasional</p>
        </div>
        <nav class="space-y-1">
            <a href="{{ route('pengurus.dashboard') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('pengurus.dashboard') ? 'bg-slate-900 text-white' : 'text-slate-300 hover:bg-slate-700' }}">
                Dashboard
            </a>
            <div class="pt-4 pb-1 text-xs font-semibold text-slate-400 uppercase tracking-wider px-3">Modul Operasional</div>
            <a href="{{ route('pengurus.anggota.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('pengurus.anggota.*') ? 'bg-slate-900 text-white' : 'text-slate-300 hover:bg-slate-700' }}">
                Kelola Anggota
            </a>
            <a href="{{ route('pengurus.simpanan.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('pengurus.simpanan.*') ? 'bg-slate-900 text-white' : 'text-slate-300 hover:bg-slate-700' }}">
                Transaksi Simpanan
            </a>
            <a href="{{ route('pengurus.pinjaman.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('pengurus.pinjaman.*') ? 'bg-slate-900 text-white' : 'text-slate-300 hover:bg-slate-700' }}">
                Transaksi Pinjaman
            </a>
            <a href="{{ route('pengurus.angsuran.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('pengurus.angsuran.*') ? 'bg-slate-900 text-white' : 'text-slate-300 hover:bg-slate-700' }}">
                Transaksi Angsuran
            </a>
        </nav>
    </div>
    <div class="border-t border-slate-700 pt-4 px-2">
        <span class="block text-xs font-semibold text-slate-400">Pengurus: {{ Auth::user()->name }}</span>
    </div>
</aside>