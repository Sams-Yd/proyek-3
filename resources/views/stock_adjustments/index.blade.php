@extends('layouts.app', ['title' => 'Stok Opname & Adjustment', 'header' => 'Stok Opname & Penyesuaian Inventori (REQ-INV-03)'])

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm text-slate-500">Pencatatan penyesuaian selisih fisik vs sistem untuk barang rusak, hilang, kadaluarsa, atau hasil opname.</p>
        </div>
        <a href="{{ route('stock-adjustments.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-xl text-sm shadow-sm transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Input Penyesuaian Stok</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3.5">Waktu</th>
                        <th class="px-6 py-3.5">Petugas</th>
                        <th class="px-6 py-3.5">Produk</th>
                        <th class="px-6 py-3.5 text-center">Jenis Mutasi</th>
                        <th class="px-6 py-3.5 text-center">Qty (PCS)</th>
                        <th class="px-6 py-3.5">Alasan Selisih</th>
                        <th class="px-6 py-3.5">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($adjustments as $adj)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-6 py-4 text-xs text-slate-500 font-mono">{{ $adj->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4 font-medium text-slate-800">{{ $adj->user->name ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-900">{{ $adj->product->nama_produk ?? '-' }}</div>
                                <span class="text-xs font-mono text-slate-400">{{ $adj->product->sku_singkat ?? '' }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full uppercase
                                    {{ $adj->jenis === 'masuk' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                    {{ $adj->jenis === 'masuk' ? '+ Masuk' : '- Keluar' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center font-bold text-slate-900">
                                {{ $adj->qty_pcs }} PCS
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 text-xs font-semibold rounded bg-slate-100 text-slate-700 capitalize">
                                    {{ str_replace('_', ' ', $adj->alasan) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500 italic">
                                {{ $adj->catatan ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-slate-400">Belum ada riwayat penyesuaian stok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($adjustments->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $adjustments->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
