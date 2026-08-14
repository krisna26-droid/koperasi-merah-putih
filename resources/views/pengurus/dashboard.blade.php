<x-app-layout>
    <div class="flex min-h-screen bg-slate-100">
        @include('layouts.sidebar-pengurus')
        <main class="flex-1 p-8">
            <h2 class="text-2xl font-bold text-slate-800 mb-6">Dashboard Pengurus</h2>

            @if(session('success'))
                <div class="mb-4 p-4 bg-emerald-100 border border-emerald-400 text-emerald-700 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Shortcut Buttons Sesuai Wireframe Gambar 3.17 Proposal -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow-sm border border-slate-200">
                    <h3 class="text-sm font-semibold text-slate-500 uppercase">Input Anggota Baru</h3>
                    <p class="text-xs text-slate-400 mb-4">Registrasi data keanggotaan baru</p>
                    <a href="{{ route('pengurus.anggota.index') }}" class="block w-full text-center bg-slate-800 hover:bg-slate-900 text-white py-2 px-4 rounded font-medium text-sm">
                        TAMBAH
                    </a>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-sm border border-slate-200">
                    <h3 class="text-sm font-semibold text-slate-500 uppercase">Input Simpanan</h3>
                    <p class="text-xs text-slate-400 mb-4">Proses setoran Pokok/Wajib/Sukarela</p>
                    <a href="{{ route('pengurus.simpanan.index') }}" class="block w-full text-center bg-slate-800 hover:bg-slate-900 text-white py-2 px-4 rounded font-medium text-sm">
                        PROSES
                    </a>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-sm border border-slate-200">
                    <h3 class="text-sm font-semibold text-slate-500 uppercase">Input Pinjaman</h3>
                    <p class="text-xs text-slate-400 mb-4">Buat draf permohonan kredit baru</p>
                    <a href="{{ route('pengurus.pinjaman.index') }}" class="block w-full text-center bg-slate-800 hover:bg-slate-900 text-white py-2 px-4 rounded font-medium text-sm">
                        PROSES
                    </a>
                </div>
            </div>
        </main>
    </div>
</x-app-layout>