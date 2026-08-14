<x-app-layout>
    <div class="flex min-h-screen bg-slate-100">
        @include('layouts.sidebar-pengurus')
        <main class="flex-1 p-8">
            <h2 class="text-2xl font-bold text-slate-800 mb-6">Buku Besar (General Ledger)</h2>

            <!-- Filter Akun (Sesuai Wireframe Gambar 3.27 Proposal) -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-slate-200 mb-8">
                <form action="{{ route('pengurus.buku-besar.index') }}" method="GET" class="flex items-center space-x-4">
                    <label class="text-sm font-semibold text-slate-700">Pilih Akun Rekonsiliasi:</label>
                    <select name="kode_akun" class="text-sm border-slate-300 rounded-md">
                        @foreach($coaList as $coa)
                            <option value="{{ $coa->kode_akun }}" {{ $account->kode_akun === $coa->kode_akun ? 'selected' : '' }}>
                                {{ $coa->kode_akun }} - {{ $coa->nama_akun }} ({{ $coa->saldo_normal }})
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="bg-slate-800 text-white text-sm px-4 py-2 rounded font-medium hover:bg-slate-900">
                        FILTER
                    </button>
                </form>
            </div>

            <!-- Tabel Ledger Mutasi -->
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-4 bg-slate-50 border-b border-slate-200 font-semibold text-slate-700">
                    Pos Akun: {{ $account->kode_akun }} - {{ $account->nama_akun }}
                </div>
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase">
                        <tr>
                            <th class="p-4">Tanggal</th>
                            <th class="p-4">ID Jurnal / Keterangan</th>
                            <th class="p-4">Debit (Rp)</th>
                            <th class="p-4">Kredit (Rp)</th>
                            <th class="p-4">Saldo Berjalan (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-sm text-slate-700 font-mono">
                        @forelse($ledgerData as $row)
                        <tr>
                            <td class="p-4 text-xs">{{ $row['tanggal'] }}</td>
                            <td class="p-4 font-sans">
                                <span class="font-mono text-xs font-medium text-slate-500">{{ $row['id_jurnal'] }}</span> - {{ $row['keterangan'] }}
                            </td>
                            <td class="p-4 text-xs">{{ number_format($row['debit'], 2, ',', '.') }}</td>
                            <td class="p-4 text-xs">{{ number_format($row['kredit'], 2, ',', '.') }}</td>
                            <td class="p-4 text-xs font-semibold text-slate-900">Rp {{ number_format($row['saldo'], 2, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="p-4 text-center text-slate-400 font-sans">Tidak ada mutasi transaksi untuk akun ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</x-app-layout>