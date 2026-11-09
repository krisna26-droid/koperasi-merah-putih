<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Identitas Koperasi · {{ $cooperativeSettings->name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/2.1.0/uicons-regular-rounded/css/uicons-regular-rounded.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50/60 antialiased">
    <div class="flex min-h-screen">
        @include('layouts.sidebar-ketua')
        <div class="flex min-w-0 flex-1 flex-col">
            <x-page-header title="Identitas Koperasi" section="Pengaturan" />
            <main class="w-full max-w-4xl space-y-6 p-6 lg:p-8">
                <div>
                    <h1 class="text-2xl font-extrabold tracking-tight text-slate-800">Identitas Koperasi</h1>
                    <p class="mt-1 text-xs text-slate-500">Perubahan akan digunakan pada navigasi, laporan, dan halaman masuk.</p>
                </div>

                @if(session('success'))
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">{{ session('success') }}</div>
                @endif

                <form method="POST" action="{{ route('ketua.settings.identity.update') }}" enctype="multipart/form-data" class="space-y-6 rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="name" class="mb-1.5 block text-xs font-bold text-slate-700">Nama koperasi</label>
                        <input id="name" name="name" value="{{ old('name', $settings->name) }}" maxlength="150" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20">
                        @error('name')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="address" class="mb-1.5 block text-xs font-bold text-slate-700">Alamat koperasi</label>
                        <textarea id="address" name="address" rows="3" maxlength="1000" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20">{{ old('address', $settings->address) }}</textarea>
                        @error('address')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="space-y-3">
                        <label for="logo" class="block text-xs font-bold text-slate-700">Logo koperasi</label>
                        @if($settings->logo_path)
                            <div class="flex items-center gap-4 rounded-xl border border-slate-100 bg-slate-50 p-4">
                                <img src="{{ route('cooperative.logo') }}?v={{ $settings->updated_at?->timestamp ?? time() }}" alt="Logo {{ $settings->name }}" class="h-16 w-16 rounded-xl bg-white object-contain p-2">
                                <label class="flex items-center gap-2 text-xs font-medium text-slate-600">
                                    <input type="checkbox" name="remove_logo" value="1" class="rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                                    Hapus logo saat menyimpan
                                </label>
                            </div>
                        @endif
                        <input id="logo" name="logo" type="file" accept="image/jpeg,image/png,image/webp" class="block w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-xs text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-emerald-50 file:px-3 file:py-2 file:font-bold file:text-emerald-700">
                        <p class="text-[11px] text-slate-400">Format JPG, PNG, atau WebP. Maksimal 3 MB. Gambar lama akan diganti setelah penyimpanan berhasil.</p>
                        @error('logo')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex justify-end border-t border-slate-100 pt-5">
                        <button type="submit" class="rounded-xl bg-emerald-600 px-5 py-3 text-xs font-bold text-white shadow-sm transition hover:bg-emerald-700">Simpan identitas</button>
                    </div>
                </form>
            </main>
        </div>
    </div>
</body>
</html>
