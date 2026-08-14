<x-app-layout>
    <div class="flex min-h-screen bg-slate-100">
        @include('layouts.sidebar-pengurus')
        <main class="flex-1 p-8">
            <h2 class="text-2xl font-bold text-slate-800 mb-6">Jurnal Umum</h2>

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

            <!-- Tabel Histori Jurnal (Sesuai Wireframe Proposal Gambar 3.25) -->
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase">
                        <tr>
                            <th class="p-4">Tanggal / ID Jurnal</th>
                            <th class="p-4">Keterangan / Akun</th>
                            <th class="p-4">Debit (Rp)</th>
                            <th class="p-4">Kredit (Rp)</th>
                            <th class="p-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-sm text-slate-700">
                        @forelse($jurnal as $header)
                            @foreach($header->details as $index => $detail)
                            <tr class="{{ $header->is_reversal ? 'bg-rose-50/50' : '' }}">
                                @if($index === 0)
                                    <td class="p-4 font-mono text-xs font-semibold align-top" rowspan="{{ $header->details->count() }}">
                                        {{ $header->tanggal->format('d/m/Y') }}<br>
                                        <span class="text-slate-400">{{ $header->id_jurnal }}</span>
                                    </td>
                                @endif
                                <td class="p-4 {{ $detail->posisi === 'Kredit' ? 'pl-8' : '' }}">
                                    <span class="font-medium">{{ $detail->kode_akun }} - {{ $detail->coa->nama_akun }}</span>
                                    @if($index === 0)
                                        <p class="text-xs text-slate-400 italic mt-0.5">{{ $header->keterangan }}</p>
                                    @endif
                                </td>
                                <td class="p-4 font-mono text-xs">
                                    {{ $detail->posisi === 'Debit' ? number_format($detail->nominal, 2, ',', '.') : '-' }}
                                </td>
                                <td class="p-4 font-mono text-xs">
                                    {{ $detail->posisi === 'Kredit' ? number_format($detail->nominal, 2, ',', '.') : '-' }}
                                </td>
                                @if($index === 0)
                                    <td class="p-4 align-top" rowspan="{{ $header->details->count() }}">
                                        @if(!$header->is_reversal)
                                            <form action="{{ route('pengurus.jurnal.reversal', $header->id_jurnal) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin melakukan Reversal Jurnal ini?')">
                                                @csrf
                                                <input type="hidden" name="alasan" value="Koreksi Transaksi Pengurus">
                                                <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white text-xs px-2.5 py-1 rounded">
                                                    Reversal
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs font-semibold text-rose-600 bg-rose-100 px-2 py-0.5 rounded">Reversed</span>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                            @endforeach
                        @empty
                        <tr>
                            <td colspan="5" class="p-4 text-center text-slate-400">Belum ada data jurnal.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</x-app-layout>