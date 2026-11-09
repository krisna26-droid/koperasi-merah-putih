<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jurnal Umum - {{ $cooperativeSettings->name ?? 'Koperasi Merah Putih' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.1.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.1.0/uicons-solid-rounded/css/uicons-solid-rounded.css'>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50/60 antialiased">
    <div class="flex min-h-screen">
        @include('layouts.sidebar-pengurus')

        <div class="flex-1 flex flex-col min-w-0">
            <x-page-header title="Jurnal Umum" section="Laporan" />

            <main class="p-8 space-y-6">
                @if(session('success'))
                    <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold rounded-2xl flex items-center space-x-2">
                        <i class="fi fi-rr-check-circle text-base"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif
                @if(session('error'))
                    <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold rounded-2xl flex items-center space-x-2">
                        <i class="fi fi-rr-cross-circle text-base"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Jurnal Umum</h1>
                        <p class="text-xs text-slate-500 mt-1">Catatan kronologis seluruh transaksi keuangan dan jurnal pembalik (*reversal*).</p>
                    </div>

                    <div class="flex items-center space-x-3">
                        <x-export-buttons report="jurnal" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                    <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-slate-400">Total jurnal bulan ini</p>
                            <h3 class="text-2xl font-extrabold text-slate-800 mt-1">{{ number_format($totalJurnalBulanIni) }}</h3>
                        </div>
                        <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                            <i class="fi fi-rr-book-alt text-xl"></i>
                        </div>
                    </div>

                    <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-slate-400">Total debit</p>
                            <h3 class="text-2xl font-extrabold text-slate-800 mt-1">
                                @if($totalDebit >= 1000000000)
                                    Rp {{ number_format($totalDebit / 1000000000, 2, ',', '.') }} M
                                @elseif($totalDebit >= 1000000)
                                    Rp {{ number_format($totalDebit / 1000000, 1, ',', '.') }} Jt
                                @else
                                    Rp {{ number_format($totalDebit, 0, ',', '.') }}
                                @endif
                            </h3>
                        </div>
                        <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <i class="fi fi-rr-arrow-down-left text-xl"></i>
                        </div>
                    </div>

                    <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-slate-400">Total kredit</p>
                            <h3 class="text-2xl font-extrabold text-slate-800 mt-1">
                                @if($totalKredit >= 1000000000)
                                    Rp {{ number_format($totalKredit / 1000000000, 2, ',', '.') }} M
                                @elseif($totalKredit >= 1000000)
                                    Rp {{ number_format($totalKredit / 1000000, 1, ',', '.') }} Jt
                                @else
                                    Rp {{ number_format($totalKredit, 0, ',', '.') }}
                                @endif
                            </h3>
                        </div>
                        <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                            <i class="fi fi-rr-arrow-up-right text-xl"></i>
                        </div>
                    </div>
                    <div class="bg-white p-5 rounded-2xl border {{ $isLedgerBalanced ? 'border-emerald-200' : 'border-rose-200' }} shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-slate-400">Status seluruh jurnal</p>
                            <h3 class="mt-1 text-sm font-extrabold {{ $isLedgerBalanced ? 'text-emerald-700' : 'text-rose-700' }}">{{ $isLedgerBalanced ? 'Seimbang' : 'Tidak seimbang' }}</h3>
                        </div>
                        <i class="fi {{ $isLedgerBalanced ? 'fi-rr-check-circle text-emerald-600' : 'fi-rr-cross-circle text-rose-600' }} text-2xl"></i>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                        <form method="GET" action="{{ route('pengurus.jurnal.index') }}" class="flex-1 w-full flex items-center gap-2">
                            <div class="relative flex-1">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <i class="fi fi-rr-search text-xs"></i>
                                </div>
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor jurnal atau keterangan..." class="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition placeholder:text-slate-400">
                            </div>

                            <button type="submit" class="px-3.5 py-2 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold rounded-xl transition flex items-center space-x-2 shadow-sm">
                                <i class="fi fi-rr-filter text-xs text-slate-400"></i>
                                <span>Filter</span>
                            </button>
                        </form>

                        <div class="flex items-center space-x-2">
                            <select onchange="window.location.href='{{ route('pengurus.jurnal.index') }}?kode_akun='+this.value" class="px-3.5 py-2 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold rounded-xl transition shadow-sm">
                                <option value="">Semua Akun</option>
                                @foreach($daftarAkun as $ak)
                                    <option value="{{ $ak->kode_akun }}" {{ request('kode_akun') == $ak->kode_akun ? 'selected' : '' }}>
                                        {{ $ak->kode_akun }} - {{ $ak->nama_akun }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100 bg-slate-50/50 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                    <th class="py-3.5 px-6">Tanggal</th>
                                    <th class="py-3.5 px-6">Nomor Jurnal</th>
                                    <th class="py-3.5 px-6">Akun</th>
                                    <th class="py-3.5 px-6">Keterangan</th>
                                    <th class="py-3.5 px-6 text-right">Debit</th>
                                    <th class="py-3.5 px-6 text-right">Kredit</th>
                                    <th class="py-3.5 px-6 text-center">Balance</th>
                                    <th class="py-3.5 px-6 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-xs">
                                @forelse($jurnalDetails as $item)
                                    <tr class="hover:bg-slate-50/60 transition {{ optional($item->header)->is_reversal ? 'bg-rose-50/30' : '' }}">
                                        <td class="py-4 px-6 text-slate-600 font-medium whitespace-nowrap">
                                            {{ optional($item->header)->tanggal ? \Carbon\Carbon::parse($item->header->tanggal)->translatedFormat('d F Y') : '-' }}
                                        </td>
                                        <td class="py-4 px-6 font-semibold text-slate-500 whitespace-nowrap font-mono">
                                            {{ $item->id_jurnal }}
                                        </td>
                                        <td class="py-4 px-6 font-bold text-slate-800 {{ $item->posisi === 'Kredit' ? 'pl-10 text-slate-600' : '' }}">
                                            {{ $item->coa->nama_akun ?? $item->kode_akun }}
                                        </td>
                                        <td class="py-4 px-6 text-slate-600">
                                            {{ optional($item->header)->keterangan ?? '-' }}
                                        </td>
                                        <td class="py-4 px-6 font-bold text-slate-800 whitespace-nowrap font-mono text-right">
                                            {{ $item->posisi === 'Debit' ? 'Rp ' . number_format($item->nominal, 0, ',', '.') : '-' }}
                                        </td>
                                        <td class="py-4 px-6 font-bold text-slate-800 whitespace-nowrap font-mono text-right">
                                            {{ $item->posisi === 'Kredit' ? 'Rp ' . number_format($item->nominal, 0, ',', '.') : '-' }}
                                        </td>
                                        @php
                                            $journalRows = $item->header?->details ?? collect();
                                            $journalDebit = (float) $journalRows->where('posisi', 'Debit')->sum('nominal');
                                            $journalCredit = (float) $journalRows->where('posisi', 'Kredit')->sum('nominal');
                                            $journalBalanced = round($journalDebit, 2) === round($journalCredit, 2);
                                        @endphp
                                        <td class="py-4 px-6 text-center whitespace-nowrap">
                                            <span class="rounded-full px-2 py-1 text-[10px] font-bold {{ $journalBalanced ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">{{ $journalBalanced ? 'Seimbang' : 'Tidak seimbang' }}</span>
                                            <span class="mt-1 block text-[10px] text-slate-400">D {{ number_format($journalDebit, 0, ',', '.') }} · K {{ number_format($journalCredit, 0, ',', '.') }}</span>
                                        </td>
                                        <td class="py-4 px-6 text-center whitespace-nowrap">
                                            <div class="flex items-center justify-center space-x-1.5">
                                                <button type="button" onclick="openJurnalModal('{{ $item->id_jurnal }}')" class="p-1.5 text-slate-400 hover:text-emerald-600 rounded-lg hover:bg-emerald-50 transition inline-flex items-center" title="Lihat Detail Transaksi">
                                                    <i class="fi fi-rr-eye text-sm"></i>
                                                </button>

                                                @if(optional($item->header)->is_reversal)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700">
                                                        Reversed
                                                    </span>
                                                @else
                                                    <form action="{{ route('pengurus.jurnal.reversal', $item->id_jurnal) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin melakukan Reversal pada jurnal {{ $item->id_jurnal }}?')">
                                                        @csrf
                                                        <input type="hidden" name="alasan" value="Koreksi Transaksi Pengurus">
                                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition inline-flex items-center" title="Reversal / Koreksi Transaksi">
                                                            <i class="fi fi-rr-rotate-left text-sm"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="py-8 text-center text-xs text-slate-400">
                                            Belum ada catatan jurnal umum.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($jurnalDetails->hasPages())
                        <div class="p-4 border-t border-slate-100 bg-white">
                            {{ $jurnalDetails->links() }}
                        </div>
                    @endif
                </div>
            </main>
        </div>
    </div>

    <div id="modalDetailJurnal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden">
        <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-800">Detail jurnal umum</h3>
                    <p id="modalKeterangan" class="text-xs text-slate-400 mt-0.5"></p>
                    <p id="modalPencatat" class="text-[10px] text-slate-400 mt-1">Dicatat oleh: -</p>
                </div>
                <button type="button" onclick="closeJurnalModal()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-50 transition">
                    <i class="fi fi-rr-cross-small text-base"></i>
                </button>
            </div>

            <div class="p-6 space-y-5">
                <div class="grid grid-cols-2 gap-3 p-3.5 bg-slate-50 rounded-xl text-xs">
                    <div>
                        <p class="text-slate-400">Tanggal</p>
                        <p id="modalTanggal" class="font-bold text-slate-800 mt-0.5">-</p>
                    </div>
                    <div>
                        <p class="text-slate-400">Nomor jurnal</p>
                        <p id="modalNomorJurnal" class="font-bold text-slate-800 mt-0.5">-</p>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="grid grid-cols-12 text-[10px] font-bold text-slate-400 uppercase tracking-wider pb-1 border-b border-slate-100">
                        <div class="col-span-6">Akun</div>
                        <div class="col-span-3 text-right">Debit</div>
                        <div class="col-span-3 text-right">Kredit</div>
                    </div>

                    <div id="modalEntriesList" class="space-y-2.5 text-xs"></div>

                    <div class="grid grid-cols-12 text-xs font-extrabold text-slate-800 pt-3 border-t border-slate-100">
                        <div class="col-span-6">Total</div>
                        <div id="modalTotalDebit" class="col-span-3 text-right font-mono">Rp 0</div>
                        <div id="modalTotalKredit" class="col-span-3 text-right font-mono">Rp 0</div>
                    </div>
                </div>

                <div id="modalBalancedBadge" class="p-3 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-xl flex items-center justify-center space-x-2 border border-emerald-200/60">
                    <i class="fi fi-rr-check-circle text-sm"></i>
                    <span id="modalBalancedText">Debit dan kredit seimbang</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openJurnalModal(idJurnal) {
            const url = "{{ route('pengurus.jurnal.detail', ':id') }}".replace(':id', idJurnal);

            fetch(url)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('modalNomorJurnal').innerText = data.id_jurnal;
                    document.getElementById('modalTanggal').innerText = data.tanggal;
                    document.getElementById('modalKeterangan').innerText = data.keterangan;
                    document.getElementById('modalPencatat').innerText = 'Dicatat oleh: ' + (data.creator_name || data.created_by || 'Tidak tercatat');
                    document.getElementById('modalTotalDebit').innerText = 'Rp ' + data.total_debit;
                    document.getElementById('modalTotalKredit').innerText = 'Rp ' + data.total_kredit;

                    const list = document.getElementById('modalEntriesList');
                    list.innerHTML = '';

                    data.details.forEach(item => {
                        const isKredit = item.posisi === 'Kredit';
                        const row = document.createElement('div');
                        row.className = 'grid grid-cols-12 items-center';
                        row.innerHTML = `
                            <div class="col-span-6 font-semibold ${isKredit ? 'pl-4 text-slate-600' : 'text-slate-800'}">
                                ${item.nama_akun}
                            </div>
                            <div class="col-span-3 text-right ${item.posisi === 'Debit' ? 'font-bold text-slate-800' : 'text-slate-400'} font-mono">
                                ${item.debit}
                            </div>
                            <div class="col-span-3 text-right ${item.posisi === 'Kredit' ? 'font-bold text-slate-800' : 'text-slate-400'} font-mono">
                                ${item.kredit}
                            </div>
                        `;
                        list.appendChild(row);
                    });

                    const badge = document.getElementById('modalBalancedBadge');
                    const text = document.getElementById('modalBalancedText');

                    if (data.is_balanced) {
                        badge.className = 'p-3 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-xl flex items-center justify-center space-x-2 border border-emerald-200/60';
                        text.innerText = 'Debit dan kredit seimbang';
                    } else {
                        badge.className = 'p-3 bg-rose-50 text-rose-700 text-xs font-bold rounded-xl flex items-center justify-center space-x-2 border border-rose-200/60';
                        text.innerText = 'Debit dan kredit tidak seimbang';
                    }

                    document.getElementById('modalDetailJurnal').classList.remove('hidden');
                })
                .catch(err => {
                    console.error('Gagal memuat detail jurnal:', err);
                });
        }

        function closeJurnalModal() {
            document.getElementById('modalDetailJurnal').classList.add('hidden');
        }
    </script>
</body>
</html>
