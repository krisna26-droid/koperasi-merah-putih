<x-app-layout>
    <div class="flex min-h-screen bg-slate-100">
        @include('layouts.sidebar-ketua')
        <main class="flex-1 p-8">
            <div class="flex justify-between items-center mb-6 print:hidden">
                <h2 class="text-2xl font-bold text-slate-800">Laporan Buku Besar</h2>
                <button onclick="window.print()" class="bg-slate-800 text-white text-sm px-4 py-2 rounded font-medium hover:bg-slate-900">
                    Cetak Laporan (Print / PDF)
                </button>
            </div>

            <!-- Filter Akun (Hidden saat dicetak) -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-slate-200 mb-8 print:hidden">
                <form action="{{ route('ketua.laporan.buku-besar') }}" method="GET" class="flex items-center space-x-4">
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

            <!-- Area Cetak Laporan -->
            <div class="bg-white p-8 rounded-lg shadow-sm border border-slate-200 print:shadow-none print:border-none">
                <div class="text-center mb-6 border-b pb-4">
                    <h1 class="text-xl font-bold">KOPERASI MERAH PUTIH DESA BURUAN</h1>
                    <p class="text-xs text-slate-500">Laporan Audit Mutasi Buku Besar (General Ledger)</p>
                    <p class="text-xs font-semibold text-slate-700 mt-1">Pos Akun: {{ $account->kode_akun }} - {{ $account->nama_akun }} ({{ $account->saldo_normal }})</p>
                </div>

                <table class="w-full text-left border-collapse border border-slate-300">
                    <thead class="bg-slate-100 text-xs font-semibold text-slate-700 uppercase border-b border-slate-300">
                        <tr>
                            <th class="p-2 border border-slate-300">Tanggal</th>
                            <th class="p-2 border border-slate-300">ID Jurnal / Keterangan</th>
                            <th class="p-2 border border-slate-300">Debit (Rp)</th>
                            <th class="p-2 border border-slate-300">Kredit (Rp)</th>
                            <th class="p-2 border border-slate-300">Saldo Berjalan (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs text-slate-800 font-mono">
                        @forelse($ledgerData as $row)
                        <tr>
                            <td class="p-2 border border-slate-300">{{ $row['tanggal'] }}</td>
                            <td class="p-2 border border-slate-300 font-sans">
                                <span class="font-mono text-xs font-medium text-slate-500">{{ $row['id_jurnal'] }}</span> - {{ $row['keterangan'] }}
                            </td>
                            <td class="p-2 border border-slate-300">{{ number_format($row['debit'], 2, ',', '.') }}</td>
                            <td class="p-2 border border-slate-300">{{ number_format($row['kredit'], 2, ',', '.') }}</td>
                            <td class="p-2 border border-slate-300 font-semibold text-slate-900">Rp {{ number_format($row['saldo'], 2, ',', '.') }}</td>
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