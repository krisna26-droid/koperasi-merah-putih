<x-app-layout>
    <div class="flex min-h-screen bg-slate-100">
        @include('layouts.sidebar-ketua')
        <main class="flex-1 p-8">
            <div class="flex justify-between items-center mb-6 print:hidden">
                <h2 class="text-2xl font-bold text-slate-800">Laporan Jurnal Umum</h2>
                <button onclick="window.print()" class="bg-slate-800 text-white text-sm px-4 py-2 rounded font-medium hover:bg-slate-900">
                    Cetak Laporan (Print / PDF)
                </button>
            </div>

            <!-- Area Cetak -->
            <div class="bg-white p-8 rounded-lg shadow-sm border border-slate-200 print:shadow-none print:border-none">
                <div class="text-center mb-6 border-b pb-4">
                    <h1 class="text-xl font-bold">KOPERASI MERAH PUTIH DESA BURUAN</h1>
                    <p class="text-xs text-slate-500">Laporan Audit Jurnal Umum Akuntansi</p>
                </div>

                <table class="w-full text-left border-collapse border border-slate-300">
                    <thead class="bg-slate-100 text-xs font-semibold text-slate-700 uppercase border-b border-slate-300">
                        <tr>
                            <th class="p-2 border border-slate-300">Tanggal / Ref</th>
                            <th class="p-2 border border-slate-300">Akun / Keterangan</th>
                            <th class="p-2 border border-slate-300">Debit (Rp)</th>
                            <th class="p-2 border border-slate-300">Kredit (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs text-slate-800">
                        @foreach($jurnal as $header)
                            @foreach($header->details as $index => $detail)
                            <tr>
                                @if($index === 0)
                                    <td class="p-2 border border-slate-300 align-top" rowspan="{{ $header->details->count() }}">
                                        {{ $header->tanggal->format('d/m/Y') }}<br>
                                        <span class="text-slate-500">{{ $header->id_jurnal }}</span>
                                    </td>
                                @endif
                                <td class="p-2 border border-slate-300 {{ $detail->posisi === 'Kredit' ? 'pl-6' : '' }}">
                                    {{ $detail->kode_akun }} - {{ $detail->coa->nama_akun }}
                                    @if($index === 0)
                                        <p class="text-[10px] text-slate-500 italic">{{ $header->keterangan }}</p>
                                    @endif
                                </td>
                                <td class="p-2 border border-slate-300 font-mono">
                                    {{ $detail->posisi === 'Debit' ? number_format($detail->nominal, 2, ',', '.') : '-' }}
                                </td>
                                <td class="p-2 border border-slate-300 font-mono">
                                    {{ $detail->posisi === 'Kredit' ? number_format($detail->nominal, 2, ',', '.') : '-' }}
                                </td>
                            </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</x-app-layout>