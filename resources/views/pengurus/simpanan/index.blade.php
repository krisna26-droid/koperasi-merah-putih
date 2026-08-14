<x-app-layout>
    <div class="flex min-h-screen bg-slate-100">
        @include('layouts.sidebar-pengurus')
        <main class="flex-1 p-8">
            <h2 class="text-2xl font-bold text-slate-800 mb-6">Transaksi Simpanan</h2>

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

            <!-- Form Input Setoran Simpanan Baru (Sesuai Wireframe Gambar 3.21 Proposal) -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-slate-200 mb-8">
                <h3 class="text-sm font-semibold text-slate-700 mb-4">Input Setoran Baru</h3>
                <form action="{{ route('pengurus.simpanan.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                        <label class="block text-xs font-medium text-slate-600 mb-1">Jenis Simpanan</label>
                        <select name="jenis_simpanan" class="w-full text-sm border-slate-300 rounded-md" required>
                            <option value="Pokok">Simpanan Pokok</option>
                            <option value="Wajib">Simpanan Wajib</option>
                            <option value="Sukarela">Simpanan Sukarela</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Nominal Setoran (Rp)</label>
                        <input type="number" name="nominal" class="w-full text-sm border-slate-300 rounded-md" placeholder="0" min="1000" step="1000" required>
                    </div>
                    <div class="md:col-span-3 text-right">
                        <button type="submit" class="bg-slate-800 text-white text-sm px-6 py-2 rounded font-medium hover:bg-slate-900">
                            Simpan Setoran
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tabel Histori Simpanan -->
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase">
                        <tr>
                            <th class="p-4">Tanggal</th>
                            <th class="p-4">ID Anggota</th>
                            <th class="p-4">Jenis</th>
                            <th class="p-4">Jumlah Setoran</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-sm text-slate-700">
                        @forelse($simpanan as $item)
                        <tr>
                            <td class="p-4 font-mono text-xs">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                            <td class="p-4 font-medium">{{ $item->id_anggota }} - {{ $item->anggota->nama }}</td>
                            <td class="p-4">{{ $item->jenis_simpanan }}</td>
                            <td class="p-4 font-semibold">Rp {{ number_format($item->nominal, 2, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="p-4 text-center text-slate-400">Belum ada histori simpanan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</x-app-layout>