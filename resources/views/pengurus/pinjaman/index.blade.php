<x-app-layout>
    <div class="flex min-h-screen bg-slate-100">
        @include('layouts.sidebar-pengurus')
        <main class="flex-1 p-8">
            <h2 class="text-2xl font-bold text-slate-800 mb-6">Transaksi Pinjaman</h2>

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

            <!-- Form Permohonan Pinjaman Baru (Sesuai Wireframe Gambar 3.23 Proposal) -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-slate-200 mb-8">
                <h3 class="text-sm font-semibold text-slate-700 mb-4">Input Pinjaman Baru</h3>
                <form action="{{ route('pengurus.pinjaman.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Pilih Anggota</label>
                        <select name="id_anggota" class="w-full text-sm border-slate-300 rounded-md" required>
                            <option value="">-- Pilih Anggota --</option>
                            @foreach($anggotaList as $item)
                                <option value="{{ $item->id_anggota }}">{{ $item->id_anggota }} - {{ $item->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Nominal Pinjaman (Rp)</label>
                        <input type="number" name="jumlah_pinjam" class="w-full text-sm border-slate-300 rounded-md" placeholder="0" min="100000" step="100000" required>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Tenor (Bulan)</label>
                        <input type="number" name="tenor" class="w-full text-sm border-slate-300 rounded-md" placeholder="1 - 60" min="1" max="60" required>
                    </div>
                    <div class="md:col-span-3 text-right">
                        <button type="submit" class="bg-slate-800 text-white text-sm px-6 py-2 rounded font-medium hover:bg-slate-900">
                            Ajukan Pinjaman
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tabel Histori Pinjaman -->
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase">
                        <tr>
                            <th class="p-4">ID Pinjam</th>
                            <th class="p-4">Anggota</th>
                            <th class="p-4">Plafond Pokok</th>
                            <th class="p-4">Bunga / Bln (1.5%)</th>
                            <th class="p-4">Sisa Tagihan</th>
                            <th class="p-4">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-sm text-slate-700">
                        @forelse($pinjaman as $item)
                        <tr>
                            <td class="p-4 font-mono font-medium">{{ $item->id_pinjam }}</td>
                            <td class="p-4">{{ $item->anggota->nama }}</td>
                            <td class="p-4">Rp {{ number_format($item->jumlah_pinjam, 2, ',', '.') }}</td>
                            <td class="p-4 text-xs text-slate-500">Rp {{ number_format($item->bunga_per_bulan, 2, ',', '.') }}</td>
                            <td class="p-4 font-semibold">Rp {{ number_format($item->sisa_tagihan, 2, ',', '.') }}</td>
                            <td class="p-4">
                                <span class="px-2 py-1 text-xs font-semibold rounded 
                                    {{ $item->status === 'Berjalan' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                    {{ $item->status === 'Pengajuan' ? 'bg-amber-100 text-amber-800' : '' }}
                                    {{ $item->status === 'Ditolak' ? 'bg-rose-100 text-rose-800' : '' }}
                                    {{ $item->status === 'Lunas' ? 'bg-blue-100 text-blue-800' : '' }}">
                                    {{ $item->status }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="p-4 text-center text-slate-400">Belum ada data pinjaman.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</x-app-layout>