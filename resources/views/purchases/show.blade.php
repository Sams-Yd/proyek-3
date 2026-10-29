@extends('layouts.app', ['title' => 'Detail PO ' . $purchase->no_po, 'header' => 'Rincian PO: ' . $purchase->no_po])

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b border-slate-200 gap-4">
            <div>
                <span class="text-xs uppercase font-bold text-emerald-600 tracking-wider">Purchase Order (PO)</span>
                <h2 class="text-2xl font-black font-mono text-slate-900">{{ $purchase->no_po }}</h2>
                <p class="text-xs text-slate-500 mt-1">Diterima oleh {{ $purchase->user->name }} pada {{ $purchase->tgl_masuk->format('d F Y') }}</p>
            </div>
            <div class="text-left sm:text-right">
                <span class="text-xs text-slate-400">Total Nilai Pembelian</span>
                <div class="text-2xl font-black text-slate-900">Rp {{ number_format($purchase->total, 0, ',', '.') }}</div>
            </div>
        </div>

        <div class="py-4 grid grid-cols-2 gap-4 text-sm border-b border-slate-200">
            <div>
                <span class="text-xs text-slate-400 block">Supplier</span>
                <span class="font-bold text-slate-800">{{ $purchase->supplier->nama_supplier }}</span>
                <p class="text-xs text-slate-500">{{ $purchase->supplier->no_telepon ?? '-' }}</p>
            </div>
            <div>
                <span class="text-xs text-slate-400 block">Catatan / Keterangan</span>
                <span class="text-slate-700 italic">{{ $purchase->catatan ?? '-' }}</span>
            </div>
        </div>

        <div class="pt-6">
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-3">Item Barang Masuk</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3">Produk</th>
                            <th class="px-4 py-3">Satuan Masuk</th>
                            <th class="px-4 py-3 text-center">Qty Diterima</th>
                            <th class="px-4 py-3 text-center">Konversi ke PCS</th>
                            <th class="px-4 py-3 text-right">Harga Beli Satuan</th>
                            <th class="px-4 py-3 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($purchase->details as $d)
                            <tr>
                                <td class="px-4 py-3 font-medium text-slate-900">
                                    {{ $d->product->nama_produk }}
                                    <span class="block text-xs font-mono text-slate-400">{{ $d->product->sku_singkat }}</span>
                                </td>
                                <td class="px-4 py-3 font-semibold text-slate-800">{{ $d->unit->nama_satuan }}</td>
                                <td class="px-4 py-3 text-center font-bold text-slate-900">{{ $d->qty }}</td>
                                <td class="px-4 py-3 text-center text-xs text-emerald-600 font-bold">
                                    + {{ $d->qty * $d->unit->jumlah_konversi }} PCS
                                </td>
                                <td class="px-4 py-3 text-right">Rp {{ number_format($d->harga_beli_satuan, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right font-bold text-slate-900">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="pt-6 border-t border-slate-200 flex justify-end">
            <a href="{{ route('purchases.index') }}" class="px-5 py-2 rounded-xl border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-50">
                &larr; Kembali ke Daftar PO
            </a>
        </div>
    </div>

</div>
@endsection
