<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Profil - Koperasi Merah Putih</title>
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
            <!-- Header Navbar -->
            <header class="bg-white border-b border-slate-100 py-3.5 px-8 flex items-center justify-between select-none font-sans">
                <div>
                    <div class="flex items-center space-x-2 text-xs text-slate-400">
                        <span>Koperasi Merah Putih</span>
                        <span>&rsaquo;</span>
                        <span class="text-slate-600 font-medium">Pengaturan</span>
                    </div>
                    <h2 class="text-base font-bold text-slate-800 mt-0.5">Pengaturan Profil</h2>
                </div>

                <div class="flex items-center space-x-5">
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
                            <button type="submit" class="text-slate-400 hover:text-rose-600 p-1.5 rounded-lg transition-colors flex items-center" title="Keluar">
                                <i class="fi fi-rr-sign-out-alt text-sm"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="p-8 space-y-6 max-w-4xl">
                <!-- Title Header -->
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Pengaturan Profil</h1>
                    <p class="text-xs text-slate-500 mt-1">Kelola informasi akun, kata sandi, dan keamanan sesi Anda.</p>
                </div>

                <!-- Notifikasi Status -->
                @if (session('status') === 'profile-updated')
                    <div class="p-4 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-2xl border border-emerald-100 flex items-center space-x-2">
                        <i class="fi fi-rr-check-circle text-sm"></i>
                        <span>Informasi profil berhasil diperbarui.</span>
                    </div>
                @elseif (session('status') === 'password-updated')
                    <div class="p-4 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-2xl border border-emerald-100 flex items-center space-x-2">
                        <i class="fi fi-rr-check-circle text-sm"></i>
                        <span>Kata sandi berhasil diperbarui.</span>
                    </div>
                @endif

                <!-- Card 1: Informasi Profil -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm space-y-5">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-800">Informasi Akun</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Perbarui nama pengguna dan alamat surel akun Anda.</p>
                    </div>

                    <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
                        @csrf
                        @method('patch')

                        <div>
                            <label for="name" class="block text-xs font-bold text-slate-700 mb-1.5">Nama Lengkap</label>
                            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition font-medium text-slate-800">
                            @error('name')
                                <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-xs font-bold text-slate-700 mb-1.5">Alamat Surel (Email)</label>
                            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition font-medium text-slate-800">
                            @error('email')
                                <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="pt-2 flex items-center justify-end">
                            <button type="submit" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition flex items-center space-x-2 shadow-sm">
                                <i class="fi fi-rr-disk text-xs"></i>
                                <span>Simpan Perubahan</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Card 2: Perbarui Kata Sandi -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm space-y-5">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-800">Perbarui Kata Sandi</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Pastikan akun Anda menggunakan kata sandi yang kuat dan aman.</p>
                    </div>

                    <form method="post" action="{{ route('password.update') }}" class="space-y-4">
                        @csrf
                        @method('put')

                        <div>
                            <label for="update_password_current_password" class="block text-xs font-bold text-slate-700 mb-1.5">Kata Sandi Saat Ini</label>
                            <input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition font-medium text-slate-800">
                            @error('current_password', 'updatePassword')
                                <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="update_password_password" class="block text-xs font-bold text-slate-700 mb-1.5">Kata Sandi Baru</label>
                            <input id="update_password_password" name="password" type="password" autocomplete="new-password" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition font-medium text-slate-800">
                            @error('password', 'updatePassword')
                                <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="update_password_password_confirmation" class="block text-xs font-bold text-slate-700 mb-1.5">Konfirmasi Kata Sandi Baru</label>
                            <input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition font-medium text-slate-800">
                            @error('password_confirmation', 'updatePassword')
                                <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="pt-2 flex items-center justify-end">
                            <button type="submit" class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition flex items-center space-x-2 shadow-sm">
                                <i class="fi fi-rr-key text-xs"></i>
                                <span>Perbarui Kata Sandi</span>
                            </button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>
</body>
</html>