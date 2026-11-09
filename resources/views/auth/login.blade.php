<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - {{ $cooperativeSettings->name ?? 'Koperasi Merah Putih' }}</title>
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Flaticon UIcons CDN -->
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.1.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.1.0/uicons-solid-rounded/css/uicons-solid-rounded.css'>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-white antialiased selection:bg-emerald-500 selection:text-white">
    <div class="min-h-screen flex flex-col lg:flex-row">
        
        <!-- SISI KIRI: Dark Branding & Value Proposition Panel -->
        <div class="lg:w-1/2 bg-[#061019] text-white p-8 lg:p-16 flex flex-col justify-between relative overflow-hidden">
            <!-- Background Radial Glow Effect -->
            <div class="absolute -right-32 -top-32 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -left-32 -bottom-32 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Brand Header -->
            <div class="relative z-10">
                <div class="flex items-center space-x-3">
                    @if($cooperativeSettings->logo_path)
                        <img src="{{ route('cooperative.logo') }}?v={{ $cooperativeSettings->updated_at?->timestamp ?? time() }}" alt="Logo {{ $cooperativeSettings->name }}" class="h-10 w-10 rounded-xl bg-white object-contain p-1 shadow-lg">
                    @else
                        <div class="w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center text-white shadow-lg shadow-emerald-500/20"><i class="fi fi-sr-building text-xl flex items-center"></i></div>
                    @endif
                    <div>
                        <h2 class="text-sm font-bold text-white tracking-wide">{{ $cooperativeSettings->name ?? 'Koperasi Merah Putih' }}</h2>
                        <p class="text-xs text-slate-400">{{ $cooperativeSettings->address }}</p>
                    </div>
                </div>
            </div>

            <!-- Hero Value & Headline -->
            <div class="my-auto py-12 relative z-10 max-w-lg">
                <!-- Highlight Icon Box -->
                <div class="w-12 h-12 rounded-2xl bg-emerald-950/60 border border-emerald-800/40 text-emerald-400 flex items-center justify-center mb-8">
                    <i class="fi fi-rr-bank text-xl flex items-center"></i>
                </div>

                <span class="text-xs font-extrabold text-emerald-400 tracking-widest uppercase">
                    SISTEM INFORMASI AKUNTANSI
                </span>

                <h1 class="text-3xl lg:text-4xl font-extrabold text-white tracking-tight mt-3 leading-tight">
                    Mengelola koperasi dengan transparan dan terpercaya.
                </h1>

                <p class="text-sm text-slate-400 mt-4 leading-relaxed">
                    Satu ruang kerja untuk mengelola anggota, simpanan, pinjaman, hingga laporan keuangan {{ $cooperativeSettings->name ?? 'Koperasi Merah Putih' }}.
                </p>

                <!-- Dynamic Quick Stats -->
                <div class="grid grid-cols-2 gap-6 mt-10 pt-8 border-t border-slate-800/80">
                    <div>
                        <h3 class="text-2xl lg:text-3xl font-extrabold text-white">
                            {{ \App\Models\Anggota::where('status', 'Aktif')->count() }}
                        </h3>
                        <p class="text-xs text-slate-400 mt-1 font-medium">Anggota aktif</p>
                    </div>
                    <div>
                        @php
                            $totalAset = \App\Models\Simpanan::sum('nominal') + \App\Models\Pinjaman::where('status', 'Berjalan')->sum('sisa_tagihan');
                        @endphp
                        <h3 class="text-2xl lg:text-3xl font-extrabold text-white">
                            @if($totalAset >= 1000000000)
                                Rp {{ number_format($totalAset / 1000000000, 2, ',', '.') }} M
                            @else
                                Rp {{ number_format($totalAset / 1000000, 1, ',', '.') }} Jt
                            @endif
                        </h3>
                        <p class="text-xs text-slate-400 mt-1 font-medium">Total aset</p>
                    </div>
                </div>
            </div>

            <!-- Footer Badge -->
            <div class="relative z-10 flex items-center space-x-2 text-xs text-slate-400">
                <i class="fi fi-rr-shield-check text-emerald-400 flex items-center text-sm"></i>
                <span>Data koperasi terkelola dengan aman</span>
            </div>
        </div>

        <!-- SISI KANAN: Clean Authentication Form -->
        <div class="lg:w-1/2 bg-white p-8 lg:p-20 flex flex-col justify-center items-center">
            <div class="w-full max-w-md">
                
                <!-- Form Greeting Header -->
                <div class="mb-8">
                    <span class="text-xs font-bold text-emerald-600 tracking-wide">Selamat datang kembali</span>
                    <h2 class="text-2xl lg:text-3xl font-extrabold text-slate-900 tracking-tight mt-1">Masuk ke akun Anda</h2>
                    <p class="text-xs text-slate-500 mt-1.5">Gunakan akun pengurus untuk melanjutkan.</p>
                </div>

                <!-- Session Alert -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <!-- Email / Username Input -->
                    <div>
                        <label for="username" class="block text-xs font-bold text-slate-700 mb-1.5">Email atau username</label>
                        <div class="relative rounded-xl shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="fi fi-rr-user text-sm flex items-center"></i>
                            </div>
                            <input id="username" 
                                   type="text" 
                                   name="username" 
                                   value="{{ old('username') }}" 
                                   required 
                                   autofocus 
                                   autocomplete="username" 
                                   placeholder="nama@koperasi.id"
                                   class="w-full pl-10 pr-4 py-2.5 text-xs text-slate-900 bg-white border border-slate-200 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition duration-150 placeholder:text-slate-400" />
                        </div>
                        <x-input-error :messages="$errors->get('username')" class="mt-1 text-xs text-rose-600" />
                    </div>

                    <!-- Password Input -->
                    <div>
                        <div class="flex justify-between items-center mb-1.5">
                            <label for="password" class="block text-xs font-bold text-slate-700">Kata sandi</label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-[11px] font-bold text-emerald-600 hover:text-emerald-700 transition">
                                    Lupa kata sandi?
                                </a>
                            @endif
                        </div>
                        <div class="relative rounded-xl shadow-sm">
                            <input id="password" 
                                   type="password" 
                                   name="password" 
                                   required 
                                   autocomplete="current-password" 
                                   placeholder="Masukkan kata sandi"
                                   class="w-full pl-4 pr-12 py-2.5 text-xs text-slate-900 bg-white border border-slate-200 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition duration-150 placeholder:text-slate-400" />
                            <button type="button" onclick="togglePasswordVisibility()" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-xs font-semibold text-slate-400 hover:text-slate-600">
                                <span id="toggleText">Lihat</span>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs text-rose-600" />
                    </div>

                    <!-- Remember Me Checkbox -->
                    <div class="flex items-center pt-1">
                        <input id="remember_me" 
                               type="checkbox" 
                               name="remember"
                               class="w-4 h-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 focus:ring-offset-0 transition cursor-pointer">
                        <label for="remember_me" class="ml-2 text-xs font-medium text-slate-600 cursor-pointer select-none">
                            Ingat saya di perangkat ini
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold text-xs py-3 px-4 rounded-xl shadow-sm shadow-emerald-600/30 transition duration-150 flex items-center justify-center space-x-2">
                            <i class="fi fi-rr-sign-in-alt flex items-center text-sm"></i>
                            <span>Masuk ke aplikasi</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <!-- Toggle Password Visibility Script -->
    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const toggleText = document.getElementById('toggleText');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleText.textContent = 'Tutup';
            } else {
                passwordInput.type = 'password';
                toggleText.textContent = 'Lihat';
            }
        }
    </script>
</body>
</html>
