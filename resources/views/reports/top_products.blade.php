@extends('layouts.app', ['title' => 'Produk Terlaris', 'header' => 'Laporan Produk Terlaris (Top-Selling SKU)'])

@section('content')
<div class="space-y-6">

    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
        <form method="GET" action="{{ route('reports.top-products') }}" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-sm">
            </div>
            <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-xl text-sm">
                Filter Peringkat
            </button>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3.5 text-center">Peringkat</th>
                        <th class="px-6 py-3.5">SKU Singkat</th>
                        <th class="px-6 py-3.5">Nama Produk</th>
                        <th class="px-6 py-3.5">Kategori</th>
                        <th class="px-6 py-3.5 text-center">Total Terjual (Qty Transaksi)</th>
                        <th class="px-6 py-3.5 text-right">Kontribusi Omzet</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($topItems as $index => $item)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-6 py-4 text-center">
                                @if($index === 0)
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-amber-100 text-amber-800 font-black text-xs">🥇 1</span>
                                @elseif($index === 1)
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-slate-200 text-slate-800 font-black text-xs">🥈 2</span>
                                @elseif($index === 2)
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-amber-50 text-amber-900 border border-amber-200 font-black text-xs">🥉 3</span>
                                @else
                                    <span class="text-xs font-bold text-slate-400">#{{ $index + 1 }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-mono font-bold text-slate-900">{{ $item->product->sku_singkat ?? '-' }}</td>
                            <td class="px-6 py-4 font-semibold text-slate-900">{{ $item->product->nama_produk ?? '-' }}</td>
                            <td class="px-6 py-4 text-xs">{{ $item->product->category->nama_kategori ?? '-' }}</td>
                            <td class="px-6 py-4 text-center font-black text-base text-emerald-600">
                                {{ number_format($item->total_qty_sold) }}
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-slate-900">
                                Rp {{ number_format($item->total_revenue, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-slate-400">Belum ada data produk terjual pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
