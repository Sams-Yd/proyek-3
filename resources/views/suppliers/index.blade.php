@extends('layouts.app', ['title' => 'Daftar Supplier', 'header' => 'Pemasok & Supplier'])

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Add Supplier Form -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs h-fit">
        <h2 class="text-base font-bold text-slate-900 mb-1">Tambah Supplier Baru</h2>
        <p class="text-xs text-slate-500 mb-4">Informasi vendor untuk pencatatan barang masuk (PO).</p>

        <form action="{{ route('suppliers.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nama Supplier / PT *</label>
                <input 
                    type="text" 
                    name="nama_supplier" 
                    required 
                    placeholder="Contoh: PT Indofood Sukses Makmur" 
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                >
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">No. Kontak / Telepon</label>
                <input 
                    type="text" 
                    name="no_telepon" 
                    placeholder="0812-xxxx-xxxx" 
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                >
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Alamat Kantor / Gudang</label>
                <textarea 
                    name="alamat" 
                    rows="3" 
                    placeholder="Alamat lengkap distributor..." 
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                ></textarea>
            </div>

            <button type="submit" class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-xl text-sm transition-colors shadow-xs">
                Simpan Supplier
            </button>
        </form>
    </div>

    <!-- Supplier Table -->
    <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-200">
            <h2 class="text-base font-bold text-slate-900">Daftar Supplier Terdaftar</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3.5">Nama Supplier</th>
                        <th class="px-6 py-3.5">Kontak</th>
                        <th class="px-6 py-3.5">Alamat</th>
                        <th class="px-6 py-3.5 text-center">Riwayat PO</th>
                        <th class="px-6 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($suppliers as $sup)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-6 py-4 font-semibold text-slate-900">{{ $sup->nama_supplier }}</td>
                            <td class="px-6 py-4 text-xs font-mono text-slate-600">{{ $sup->no_telepon ?? '-' }}</td>
                            <td class="px-6 py-4 text-xs text-slate-500 max-w-xs truncate">{{ $sup->alamat ?? '-' }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-slate-100 text-slate-800">
                                    {{ $sup->purchases_count }} PO
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($sup->purchases_count === 0)
                                    <form action="{{ route('suppliers.destroy', $sup->id) }}" method="POST" onsubmit="return confirm('Hapus supplier ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-500 hover:text-rose-600 rounded-lg hover:bg-slate-100" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-400">Belum ada supplier terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($suppliers->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $suppliers->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
