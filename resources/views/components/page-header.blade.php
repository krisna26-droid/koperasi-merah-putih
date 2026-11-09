@props(['title', 'section' => null, 'printHidden' => false])

<header class="{{ $printHidden ? 'no-print ' : '' }}bg-white border-b border-slate-100 px-6 py-3.5 lg:px-8 flex items-center justify-between gap-4 select-none font-sans">
    <div class="min-w-0">
        <div class="flex items-center gap-2 text-xs text-slate-400 truncate">
            <span class="truncate">{{ $cooperativeSettings->name }}</span>
            <span>&rsaquo;</span>
            <span class="text-slate-600 font-medium truncate">{{ $section ?? $title }}</span>
        </div>
        <h2 class="mt-0.5 text-base font-bold text-slate-800">{{ $title }}</h2>
    </div>

    @auth
        <div class="flex shrink-0 items-center gap-3">
            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-800 ring-2 ring-emerald-50">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <div class="hidden text-left sm:block">
                <p class="text-xs font-bold leading-tight text-slate-800">{{ auth()->user()->name }}</p>
                <p class="text-[10px] text-slate-400">{{ auth()->user()->peran === 'Ketua' ? 'Ketua Koperasi' : 'Pengurus' }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="rounded-lg p-2 text-slate-400 transition hover:bg-rose-50 hover:text-rose-600" title="Keluar">
                    <i class="fi fi-rr-sign-out-alt text-sm"></i>
                </button>
            </form>
        </div>
    @endauth
</header>
