<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Persetujuan Pinjaman · {{ $cooperativeSettings->name }}</title>
    <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/2.1.0/uicons-regular-rounded/css/uicons-regular-rounded.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50/60 antialiased">
    <div class="flex min-h-screen">
        @include('layouts.sidebar-ketua')
        <div class="flex min-w-0 flex-1 flex-col">
            <x-page-header title="Persetujuan Permohonan Kredit" section="Pinjaman" />
            <main class="flex-1 space-y-6 p-6 lg:p-8">

            @if(session('success'))
                <div class="mb-4 p-4 bg-emerald-100 border border-emerald-400 text-emerald-700 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase">
                        <tr>
                            <th class="p-4">ID Pinjam</th>
                            <th class="p-4">Nama Anggota</th>
                            <th class="p-4">Plafond Pokok</th>
                            <th class="p-4">Tenor</th>
                            <th class="p-4">Aksi Approval</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-sm text-slate-700">
                        @forelse($pengajuan as $item)
                        <tr>
                            <td class="p-4 font-mono font-medium">{{ $item->id_pinjam }}</td>
                            <td class="p-4">{{ $item->anggota->nama }}</td>
                            <td class="p-4 font-semibold">Rp {{ number_format($item->jumlah_pinjam, 2, ',', '.') }}</td>
                            <td class="p-4">{{ $item->tenor }} Bulan</td>
                            <td class="p-4 flex space-x-2">
                                <form action="{{ route('ketua.approval.approve', $item->id_pinjam) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs px-3 py-1.5 rounded font-medium">
                                        SETUJUI (APPROVE)
                                    </button>
                                </form>
                                <form action="{{ route('ketua.approval.reject', $item->id_pinjam) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white text-xs px-3 py-1.5 rounded font-medium">
                                        TOLAK
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="p-4 text-center text-slate-400">Tidak ada pengajuan pinjaman baru yang menunggu persetujuan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            </main>
        </div>
    </div>
</body>
</html>
