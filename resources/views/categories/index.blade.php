@extends('layouts.app', ['title' => 'Kategori Produk', 'header' => 'Kategori Produk'])

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Add Category Form -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs h-fit">
        <h2 class="text-base font-bold text-slate-900 mb-1">Tambah Kategori</h2>
        <p class="text-xs text-slate-500 mb-4">Pengelompokan barang untuk katalog POS kasir.</p>

        <form action="{{ route('categories.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nama Kategori *</label>
                <input 
                    type="text" 
                    name="nama_kategori" 
                    required 
                    placeholder="Contoh: Makanan Ringan" 
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                >
            </div>

            <button type="submit" class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-xl text-sm transition-colors shadow-xs">
                Simpan Kategori
            </button>
        </form>
    </div>

    <!-- Category Table -->
    <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-200">
            <h2 class="text-base font-bold text-slate-900">Daftar Kategori Tersedia</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3.5">#</th>
                        <th class="px-6 py-3.5">Nama Kategori</th>
                        <th class="px-6 py-3.5 text-center">Jumlah Produk</th>
                        <th class="px-6 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($categories as $index => $cat)
                        <tr class="hover:bg-slate-50/70 transition-colors" x-data="{ editing: false, name: '{{ $cat->nama_kategori }}' }">
                            <td class="px-6 py-4 text-xs font-mono text-slate-400">{{ $categories->firstItem() + $index }}</td>
                            <td class="px-6 py-4">
                                <template x-if="!editing">
                                    <span class="font-semibold text-slate-900" x-text="name"></span>
                                </template>
                                <template x-if="editing">
                                    <form action="{{ route('categories.update', $cat->id) }}" method="POST" class="flex items-center gap-2">
                                        @csrf
                                        @method('PUT')
                                        <input type="text" name="nama_kategori" x-model="name" required class="px-3 py-1 text-sm border border-emerald-500 rounded-lg focus:outline-none">
                                        <button type="submit" class="px-2.5 py-1 bg-emerald-600 text-white text-xs rounded-lg font-semibold">Simpan</button>
                                        <button type="button" @click="editing = false" class="text-xs text-slate-500">Batal</button>
                                    </form>
                                </template>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-slate-100 text-slate-800">
                                    {{ $cat->products_count }} item
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="inline-flex items-center gap-2">
                                    <button type="button" @click="editing = !editing" class="p-1.5 text-slate-500 hover:text-emerald-600 rounded-lg hover:bg-slate-100" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    @if($cat->products_count === 0)
                                        <form action="{{ route('categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('Hapus kategori ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-slate-500 hover:text-rose-600 rounded-lg hover:bg-slate-100" title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-slate-400">Belum ada kategori yang dibuat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($categories->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $categories->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
