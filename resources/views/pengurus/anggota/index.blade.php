<x-app-layout>
    <div class="flex min-h-screen bg-slate-100">
        @include('layouts.sidebar-pengurus')
        <main class="flex-1 p-8">
            <h2 class="text-2xl font-bold text-slate-800 mb-6">Kelola Data Anggota</h2>

            @if(session('success'))
                <div class="mb-4 p-4 bg-emerald-100 border border-emerald-400 text-emerald-700 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Form Tambah Anggota -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-slate-200 mb-8">
                <h3 class="text-sm font-semibold text-slate-700 mb-4">Tambah Anggota Baru</h3>
                <form action="{{ route('pengurus.anggota.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">ID Anggota</label>
                        <input type="text" name="id_anggota" class="w-full text-sm border-slate-300 rounded-md" placeholder="e.g. A-001" required>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Nama Lengkap</label>
                        <input type="text" name="nama" class="w-full text-sm border-slate-300 rounded-md" required>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Alamat</label>
                        <input type="text" name="alamat" class="w-full text-sm border-slate-300 rounded-md" required>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">No. Telepon</label>
                        <input type="text" name="no_telepon" class="w-full text-sm border-slate-300 rounded-md" required>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Status</label>
                        <select name="status" class="w-full text-sm border-slate-300 rounded-md">
                            <option value="Aktif">Aktif</option>
                            <option value="Cuti">Cuti</option>
                        </select>
                    </div>
                    <div class="md:col-span-2 text-right">
                        <button type="submit" class="bg-slate-800 text-white text-sm px-6 py-2 rounded font-medium hover:bg-slate-900">
                            Simpan Anggota
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tabel Anggota Sesuai Wireframe Proposal Gambar 3.19 -->
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase">
                        <tr>
                            <th class="p-4">ID</th>
                            <th class="p-4">Nama Anggota</th>
                            <th class="p-4">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-sm text-slate-700">
                        @foreach($anggota as $item)
                        <tr>
                            <td class="p-4 font-mono font-medium">{{ $item->id_anggota }}</td>
                            <td class="p-4">{{ $item->nama }}</td>
                            <td class="p-4">
                                <span class="px-2 py-1 text-xs font-semibold rounded {{ $item->status === 'Aktif' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-800' }}">
                                    {{ $item->status }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</x-app-layout>