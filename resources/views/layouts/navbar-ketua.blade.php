<header class="bg-white border-b border-slate-100 py-3.5 px-8 flex items-center justify-between select-none font-sans">
    <div>
        <div class="flex items-center space-x-2 text-xs text-slate-400">
            <span>Koperasi Merah Putih</span>
            <span>&rsaquo;</span>
            <span class="text-slate-600 font-medium">Dashboard</span>
        </div>
        <h2 class="text-base font-bold text-slate-800 mt-0.5">Ringkasan hari ini</h2>
    </div>

    <div class="flex items-center space-x-5">
        <button class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-50 rounded-full transition-colors">
            <i class="fi fi-rr-search flex items-center text-sm"></i>
        </button>

        <div class="h-6 w-px bg-slate-100"></div>

        <!-- User Info & Logout Form -->
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
                <button type="submit" class="text-slate-400 hover:text-rose-600 p-1.5 rounded-lg transition-colors flex items-center" title="Keluar / Logout">
                    <i class="fi fi-rr-sign-out-alt text-sm"></i>
                </button>
            </form>
        </div>
    </div>
</header>