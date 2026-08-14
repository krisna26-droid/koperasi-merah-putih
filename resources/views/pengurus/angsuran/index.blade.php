<x-app-layout>
    <div class="flex min-h-screen bg-slate-100">
        @include('layouts.sidebar-pengurus')
        <main class="flex-1 p-8">
            <h2 class="text-2xl font-bold text-slate-800 mb-6">Transaksi Angsuran</h2>

            @if(session('success'))
                <div class="mb-4 p-4 bg-emerald-100 border border-emerald-400 text-emerald-700 rounded-md">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-4 bg-rose-100 border border-rose-400 text-rose-700 rounded-md">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Form Bayar Angsuran -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-slate-200 mb-8">
                <h3 class="text-sm font-semibold text-slate-700 mb-4">Input Pembayaran Angsuran</h3>
                <form action="{{ route('pengurus.angsuran.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Pilih Pinjaman Aktif</label>
                        <select name="id_pinjam" class="w-full text-sm border-slate-300 rounded-md" required>
                            <option value="">-- Pilih Pinjaman (Berjalan) --</option>
                            @foreach($pinjamanBerjalan as $item)
                                <option value="{{ $item->id_pinjam }}">
                                    {{ $item->id_pinjam }} - {{ $item->anggota->nama }} (Sisa: Rp {{ number_format($item->sisa_tagihan, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-slate-800 text-white text-sm px-6 py-2 rounded font-medium hover:bg-slate-900 h-[38px]">
                            Bayar Angsuran (Pokok + Bunga 1.5%)
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tabel Histori Angsuran -->
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase">
                        <tr>
                            <th class="p-4">ID Angsuran</th>
                            <th class="p-4">ID Pinjam</th>
                            <th class="p-4">Angsuran Ke</th>
                            <th class="p-4">Bayar Pokok</th>
                            <th class="p-4">Bayar Bunga</th>
                            <th class="p-4">Total Bayar</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-sm text-slate-700">
                        @forelse($angsuran as $item)
                        <tr>
                            <td class="p-4 font-mono font-medium">{{ $item->id_angsuran }}</td>
                            <td class="p-4 font-mono text-xs">{{ $item->id_pinjam }}</td>
                            <td class="p-4">Ke-{{ $item->angsuran_ke }}</td>
                            <td class="p-4">Rp {{ number_format($item->bayar_pokok, 2, ',', '.') }}</td>
                            <td class="p-4 text-xs text-slate-500">Rp {{ number_format($item->bayar_bunga, 2, ',', '.') }}</td>
                            <td class="p-4 font-semibold text-emerald-700">Rp {{ number_format($item->total_bayar, 2, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="p-4 text-center text-slate-400">Belum ada histori pembayaran angsuran.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</x-app-layout>