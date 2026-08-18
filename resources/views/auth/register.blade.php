<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Pengurus - Koperasi Merah Putih</title>
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
        
        <!-- SISI KIRI: Dark Branding & Info Panel -->
        <div class="lg:w-1/2 bg-[#061019] text-white p-8 lg:p-16 flex flex-col justify-between relative overflow-hidden">
            <!-- Glow Effects -->
            <div class="absolute -right-32 -top-32 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -left-32 -bottom-32 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Brand Header -->
            <div class="relative z-10">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center text-white shadow-lg shadow-emerald-500/20">
                        <i class="fi fi-sr-building text-xl flex items-center"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-white tracking-wide">Koperasi Merah Putih</h2>
                        <p class="text-xs text-slate-400">Desa Buruan · Gianyar</p>
                    </div>
                </div>
            </div>

            <!-- Hero Headline & Description -->
            <div class="my-auto py-12 relative z-10 max-w-lg">
                <div class="w-12 h-12 rounded-2xl bg-emerald-950/60 border border-emerald-800/40 text-emerald-400 flex items-center justify-center mb-8">
                    <i class="fi fi-rr-user-add text-xl flex items-center"></i>
                </div>

                <span class="text-xs font-extrabold text-emerald-400 tracking-widest uppercase">
                    REGISTRASI PENGURUS
                </span>

                <h1 class="text-3xl lg:text-4xl font-extrabold text-white tracking-tight mt-3 leading-tight">
                    Bergabung mengelola tata kelola keuangan koperasi.
                </h1>

                <p class="text-sm text-slate-400 mt-4 leading-relaxed">
                    Daftarkan akun pengurus untuk mendapatkan hak akses operasional kasir, pembukuan jurnal umum, maupun otorisasi permohonan kredit pinjaman.
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
                <span>Hak akses dibatasi berdasarkan peran pengguna</span>
            </div>
        </div>

        <!-- SISI KANAN: Registration Form -->
        <div class="lg:w-1/2 bg-white p-8 lg:p-16 flex flex-col justify-center items-center overflow-y-auto">
            <div class="w-full max-w-md my-auto py-6">
                
                <!-- Header Greeting -->
                <div class="mb-6">
                    <span class="text-xs font-bold text-emerald-600 tracking-wide">Pendaftaran Akun Baru</span>
                    <h2 class="text-2xl lg:text-3xl font-extrabold text-slate-900 tracking-tight mt-1">Buat akun pengurus</h2>
                    <p class="text-xs text-slate-500 mt-1.5">Lengkapi formulir di bawah untuk mendaftarkan akun.</p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-4">
                    @csrf

                    <!-- Nama Lengkap -->
                    <div>
                        <label for="name" class="block text-xs font-bold text-slate-700 mb-1.5">Nama lengkap</label>
                        <div class="relative rounded-xl shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="fi fi-rr-id-badge text-sm flex items-center"></i>
                            </div>
                            <input id="name" 
                                   type="text" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   required 
                                   autofocus 
                                   autocomplete="name" 
                                   placeholder="I Wayan Sudarma"
                                   class="w-full pl-10 pr-4 py-2.5 text-xs text-slate-900 bg-white border border-slate-200 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition duration-150 placeholder:text-slate-400" />
                        </div>
                        <x-input-error :messages="$errors->get('name')" class="mt-1 text-xs text-rose-600" />
                    </div>

                    <!-- Username & Role Peran (Grid 2 Kolom) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="username" class="block text-xs font-bold text-slate-700 mb-1.5">Username</label>
                            <div class="relative rounded-xl shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <i class="fi fi-rr-user text-sm flex items-center"></i>
                                </div>
                                <input id="username" 
                                       type="text" 
                                       name="username" 
                                       value="{{ old('username') }}" 
                                       required 
                                       autocomplete="username" 
                                       placeholder="username_anda"
                                       class="w-full pl-10 pr-4 py-2.5 text-xs text-slate-900 bg-white border border-slate-200 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition duration-150 placeholder:text-slate-400" />
                            </div>
                            <x-input-error :messages="$errors->get('username')" class="mt-1 text-xs text-rose-600" />
                        </div>

                        <div>
                            <label for="peran" class="block text-xs font-bold text-slate-700 mb-1.5">Peran pengguna</label>
                            <div class="relative rounded-xl shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <i class="fi fi-rr-briefcase text-sm flex items-center"></i>
                                </div>
                                <select id="peran" 
                                        name="peran" 
                                        required 
                                        class="w-full pl-10 pr-8 py-2.5 text-xs text-slate-900 bg-white border border-slate-200 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition duration-150 appearance-none">
                                    <option value="Admin" {{ old('peran') === 'Admin' ? 'selected' : '' }}>Admin (Pengurus)</option>
                                    <option value="Ketua" {{ old('peran') === 'Ketua' ? 'selected' : '' }}>Ketua Koperasi</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                                    <i class="fi fi-rr-angle-small-down text-sm"></i>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('peran')" class="mt-1 text-xs text-rose-600" />
                        </div>
                    </div>

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-700 mb-1.5">Alamat email</label>
                        <div class="relative rounded-xl shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="fi fi-rr-envelope text-sm flex items-center"></i>
                            </div>
                            <input id="email" 
                                   type="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   required 
                                   autocomplete="email" 
                                   placeholder="nama@koperasimerahputih.id"
                                   class="w-full pl-10 pr-4 py-2.5 text-xs text-slate-900 bg-white border border-slate-200 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition duration-150 placeholder:text-slate-400" />
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs text-rose-600" />
                    </div>

                    <!-- Kata Sandi & Konfirmasi (Grid 2 Kolom) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="password" class="block text-xs font-bold text-slate-700 mb-1.5">Kata sandi</label>
                            <div class="relative rounded-xl shadow-sm">
                                <input id="password" 
                                       type="password" 
                                       name="password" 
                                       required 
                                       autocomplete="new-password" 
                                       placeholder="Minimal 8 karakter"
                                       class="w-full pl-3.5 pr-4 py-2.5 text-xs text-slate-900 bg-white border border-slate-200 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition duration-150 placeholder:text-slate-400" />
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs text-rose-600" />
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-xs font-bold text-slate-700 mb-1.5">Konfirmasi sandi</label>
                            <div class="relative rounded-xl shadow-sm">
                                <input id="password_confirmation" 
                                       type="password" 
                                       name="password_confirmation" 
                                       required 
                                       autocomplete="new-password" 
                                       placeholder="Ulangi kata sandi"
                                       class="w-full pl-3.5 pr-4 py-2.5 text-xs text-slate-900 bg-white border border-slate-200 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition duration-150 placeholder:text-slate-400" />
                            </div>
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-xs text-rose-600" />
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold text-xs py-3 px-4 rounded-xl shadow-sm shadow-emerald-600/30 transition duration-150 flex items-center justify-center space-x-2">
                            <i class="fi fi-rr-user-check flex items-center text-sm"></i>
                            <span>Daftar Akun</span>
                        </button>
                    </div>
                </form>

                <!-- Link Kembali ke Halaman Login -->
                <div class="mt-6 pt-6 border-t border-slate-100 text-center">
                    <p class="text-xs text-slate-500">
                        Sudah memiliki akun? 
                        <a href="{{ route('login') }}" class="font-bold text-emerald-600 hover:text-emerald-700 transition">
                            Masuk ke aplikasi
                        </a>
                    </p>
                </div>

            </div>
        </div>

    </div>
</body>
</html>