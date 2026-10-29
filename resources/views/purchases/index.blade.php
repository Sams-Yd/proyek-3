@extends('layouts.app', ['title' => 'Barang Masuk (PO)', 'header' => 'Penerimaan Barang Masuk (REQ-INV-04)'])

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <p class="text-sm text-slate-500">Pencatatan Purchase Order (PO) barang masuk dari supplier untuk menambah stok dan memperbarui HPP.</p>
        <a href="{{ route('purchases.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-xl text-sm shadow-sm transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Input Barang Masuk (PO)</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3.5">No. PO</th>
                        <th class="px-6 py-3.5">Tanggal Masuk</th>
                        <th class="px-6 py-3.5">Supplier</th>
                        <th class="px-6 py-3.5">Petugas Penerima</th>
                        <th class="px-6 py-3.5 text-center">Jumlah Item</th>
                        <th class="px-6 py-3.5 text-right">Total Pembelian</th>
                        <th class="px-6 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($purchases as $po)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-6 py-4 font-mono font-bold text-slate-900">{{ $po->no_po }}</td>
                            <td class="px-6 py-4 text-xs text-slate-500">{{ $po->tgl_masuk->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 font-semibold text-slate-800">{{ $po->supplier->nama_supplier ?? '-' }}</td>
                            <td class="px-6 py-4 text-xs text-slate-600">{{ $po->user->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-slate-100 text-slate-800">
                                    {{ $po->details->count() }} macam item
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-slate-900">
                                Rp {{ number_format($po->total, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('purchases.show', $po->id) }}" class="inline-flex items-center gap-1 text-xs text-emerald-600 hover:text-emerald-700 font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <span>Detail</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-slate-400">Belum ada catatan barang masuk (PO).</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($purchases->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $purchases->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
