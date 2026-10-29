@extends('layouts.app', ['title' => 'Master Produk', 'header' => 'Master Produk & Konversi Satuan'])

@section('content')
<div class="space-y-6">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <form method="GET" action="{{ route('products.index') }}" class="flex flex-wrap items-center gap-2 flex-1 max-w-2xl">
            <div class="relative flex-1 min-w-[200px]">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Cari nama produk atau SKU singkat..." 
                    class="w-full pl-9 pr-4 py-2 bg-white border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                >
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <select name="category_id" class="px-3 py-2 bg-white border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nama_kategori }}</option>
                @endforeach
            </select>

            <select name="filter" class="px-3 py-2 bg-white border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <option value="">Semua Kondisi</option>
                <option value="low_stock" {{ request('filter') === 'low_stock' ? 'selected' : '' }}>Stok Menipis</option>
                <option value="expiring" {{ request('filter') === 'expiring' ? 'selected' : '' }}>Mendekati Expired</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-slate-800 text-white rounded-xl text-sm font-semibold hover:bg-slate-700">Filter</button>
            @if(request()->hasAny(['search', 'category_id', 'filter']))
                <a href="{{ route('products.index') }}" class="px-3 py-2 text-slate-500 hover:text-slate-800 text-sm">Reset</a>
            @endif
        </form>

        <a href="{{ route('products.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-xl text-sm shadow-sm transition-colors shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Tambah Produk Baru</span>
        </a>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3.5">SKU Singkat</th>
                        <th class="px-6 py-3.5">Nama Produk</th>
                        <th class="px-6 py-3.5">Kategori</th>
                        <th class="px-6 py-3.5">Satuan & Harga Jual</th>
                        <th class="px-6 py-3.5 text-center">Stok Basis (PCS)</th>
                        <th class="px-6 py-3.5 text-right">Harga Beli (HPP)</th>
                        <th class="px-6 py-3.5 text-center">Kadaluarsa</th>
                        <th class="px-6 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($products as $prod)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-6 py-4 font-mono font-bold text-slate-900">{{ $prod->sku_singkat }}</td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-900">{{ $prod->nama_produk }}</div>
                                @if($prod->stok_pcs <= $prod->stok_minimum)
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded-md mt-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Stok Menipis (Min: {{ $prod->stok_minimum }})
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-xs font-medium rounded-lg bg-slate-100 text-slate-700">
                                    {{ $prod->category->nama_kategori ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($prod->units as $u)
                                        <span class="px-2 py-1 text-xs rounded-md bg-emerald-50 text-emerald-800 border border-emerald-200 font-medium">
                                            <b>{{ $u->nama_satuan }}</b> (1:{{ $u->jumlah_konversi }}) = Rp {{ number_format($u->harga_jual, 0, ',', '.') }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="font-bold text-base {{ $prod->stok_pcs <= $prod->stok_minimum ? 'text-rose-600' : 'text-slate-900' }}">
                                    {{ $prod->stok_pcs }}
                                </span>
                                <span class="text-xs text-slate-400 block">PCS</span>
                            </td>
                            <td class="px-6 py-4 text-right font-medium text-slate-800">
                                Rp {{ number_format($prod->harga_beli, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center text-xs">
                                @if($prod->tgl_kadaluarsa)
                                    <span class="{{ $prod->isExpiringSoon() ? 'text-amber-600 font-bold bg-amber-50 px-2 py-0.5 rounded' : 'text-slate-600' }}">
                                        {{ $prod->tgl_kadaluarsa->format('d/m/Y') }}
                                    </span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('products.edit', $prod->id) }}" class="p-1.5 text-slate-500 hover:text-emerald-600 hover:bg-slate-100 rounded-lg transition-colors" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form action="{{ route('products.destroy', $prod->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-500 hover:text-rose-600 hover:bg-slate-100 rounded-lg transition-colors" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                Tidak ada produk yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($products->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $products->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
