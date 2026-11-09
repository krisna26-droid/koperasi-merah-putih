<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Aktivitas - {{ $cooperativeSettings->name ?? 'Koperasi Merah Putih' }}</title>
    <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/2.1.0/uicons-regular-rounded/css/uicons-regular-rounded.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50/60 antialiased">
    <div class="flex min-h-screen">
        @if(auth()->user()->peran === 'Ketua')
            @include('layouts.sidebar-ketua')
        @else
            @include('layouts.sidebar-pengurus')
        @endif

        <div class="flex min-w-0 flex-1 flex-col">
            <x-page-header title="Log Aktivitas" section="Audit" />
            <main class="min-w-0 flex-1 space-y-6 p-6 md:p-8">
                <p class="text-sm text-slate-500">Riwayat penambahan dan perubahan data, termasuk transaksi keuangan.</p>

            <form method="GET" class="flex flex-wrap items-end gap-3 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
                <label class="text-xs font-semibold text-slate-600">Cari pengguna atau data
                    <input name="search" value="{{ request('search') }}" class="mt-1 block rounded-lg border border-slate-200 px-3 py-2 font-normal" placeholder="Nama, ID transaksi...">
                </label>
                <label class="text-xs font-semibold text-slate-600">Dari
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="mt-1 block rounded-lg border border-slate-200 px-3 py-2 font-normal">
                </label>
                <label class="text-xs font-semibold text-slate-600">Sampai
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="mt-1 block rounded-lg border border-slate-200 px-3 py-2 font-normal">
                </label>
                <button class="rounded-lg bg-slate-900 px-4 py-2 text-xs font-bold text-white">Terapkan</button>
            </form>

            <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-[10px] uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-4 py-3">Waktu</th>
                                <th class="px-4 py-3">Pengguna</th>
                                <th class="px-4 py-3">Aktivitas</th>
                                <th class="px-4 py-3">Data terkait</th>
                                <th class="px-4 py-3">Kolom berubah</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($logs as $log)
                                <tr>
                                    <td class="whitespace-nowrap px-4 py-3 text-slate-500">{{ $log->created_at?->format('d-m-Y H:i:s') }}</td>
                                    <td class="px-4 py-3 font-semibold text-slate-800">{{ $log->actor_name ?: $log->actor_username ?: 'Tidak tercatat' }}
                                        @if($log->actor_username)<span class="block text-[10px] font-normal text-slate-400">{{ $log->actor_username }}</span>@endif
                                    </td>
                                    <td class="px-4 py-3 text-slate-700">{{ ['created' => 'Menambahkan', 'updated' => 'Mengubah', 'deleted' => 'Menghapus', 'printed' => 'Mencetak / mengunduh PDF', 'exported' => 'Mengekspor data'][$log->action] ?? $log->action }}</td>
                                    <td class="px-4 py-3 text-slate-700">{{ $log->subject_type }} <span class="font-mono text-slate-500">{{ $log->subject_id }}</span></td>
                                    <td class="px-4 py-3 text-slate-500">{{ $log->changed_fields ? collect($log->changed_fields)->map(fn ($value, $key) => is_int($key) ? $value : "$key: $value")->implode(', ') : '—' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-4 py-10 text-center text-slate-400">Belum ada aktivitas yang tercatat.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-slate-100 p-4">{{ $logs->links() }}</div>
            </div>
            </main>
        </div>
    </div>
</body>
</html>
