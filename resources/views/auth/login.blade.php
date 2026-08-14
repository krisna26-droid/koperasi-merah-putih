<x-guest-layout>
    <!-- Header Halaman Login (Sesuai Wireframe Gambar 3.15 Proposal) -->
    <div class="mb-6 text-center">
        <h2 class="text-xl font-bold tracking-wider text-slate-800 uppercase">
            Koperasi Merah Putih
        </h2>
        <p class="text-xs text-slate-500 mt-1">
            Silakan masuk ke sistem informasi akuntansi
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Username -->
        <div>
            <x-input-label for="username" :value="__('USERNAME')" class="text-xs font-semibold uppercase text-slate-700" />
            <x-text-input id="username" 
                          class="block mt-1 w-full text-sm border-slate-300 focus:border-slate-800 focus:ring-slate-800 rounded-md shadow-sm" 
                          type="text" 
                          name="username" 
                          :value="old('username')" 
                          required 
                          autofocus 
                          autocomplete="username" />
            <x-input-error :messages="$errors->get('username')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('PASSWORD')" class="text-xs font-semibold uppercase text-slate-700" />

            <x-text-input id="password" 
                          class="block mt-1 w-full text-sm border-slate-300 focus:border-slate-800 focus:ring-slate-800 rounded-md shadow-sm"
                          type="password"
                          name="password"
                          required 
                          autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-slate-800 shadow-sm focus:ring-slate-800" name="remember">
                <span class="ms-2 text-xs text-slate-600">{{ __('Ingat saya di perangkat ini') }}</span>
            </label>
        </div>

        <!-- Submit Button (Full Width Sesuai Wireframe Gambar 3.15) -->
        <div class="mt-6">
            <button type="submit" class="w-full bg-slate-800 hover:bg-slate-900 active:bg-slate-950 text-white font-semibold text-xs py-3 px-4 rounded-md shadow transition duration-150 uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-slate-800 focus:ring-offset-2">
                {{ __('MASUK') }}
            </button>
        </div>
    </form>
</x-guest-layout>