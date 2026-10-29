@extends('layouts.app', ['title' => 'Laporan Penjualan', 'header' => 'Laporan Penjualan Real-Time'])

@section('content')
<div class="space-y-6">

    <!-- Filter Bar -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
        <form method="GET" action="{{ route('reports.sales') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Metode Bayar</label>
                <select name="metode_pembayaran" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                    <option value="">Semua Metode</option>
                    <option value="tunai" {{ $paymentMethod === 'tunai' ? 'selected' : '' }}>Tunai</option>
                    <option value="qris" {{ $paymentMethod === 'qris' ? 'selected' : '' }}>QRIS</option>
                    <option value="debit" {{ $paymentMethod === 'debit' ? 'selected' : '' }}>Debit</option>
                    <option value="e-wallet" {{ $paymentMethod === 'e-wallet' ? 'selected' : '' }}>E-Wallet</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-xl text-sm">
                    Terapkan Filter
                </button>
                <button type="button" onclick="window.print()" class="px-3 py-2 bg-slate-800 text-white rounded-xl text-sm font-semibold hover:bg-slate-700">
                    Cetak
                </button>
            </div>
        </form>
    </div>

    <!-- KPI Summary Row -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-5 rounded-2xl bg-white border border-slate-200">
            <span class="text-xs font-bold text-slate-400 uppercase">Total Omzet Penjualan</span>
            <div class="text-2xl font-black text-slate-900 mt-1">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</div>
        </div>
        <div class="p-5 rounded-2xl bg-white border border-slate-200">
            <span class="text-xs font-bold text-slate-400 uppercase">Jumlah Transaksi Selesai</span>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ number_format($totalTransaksi) }}</div>
        </div>
        <div class="p-5 rounded-2xl bg-white border border-slate-200">
            <span class="text-xs font-bold text-slate-400 uppercase">Total Diskon Diberikan</span>
            <div class="text-2xl font-black text-rose-600 mt-1">Rp {{ number_format($totalDiskon, 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Transactions List -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3.5">No Faktur</th>
                        <th class="px-6 py-3.5">Waktu Transaksi</th>
                        <th class="px-6 py-3.5">Kasir</th>
                        <th class="px-6 py-3.5">Metode Bayar</th>
                        <th class="px-6 py-3.5 text-right">Subtotal</th>
                        <th class="px-6 py-3.5 text-right">Diskon</th>
                        <th class="px-6 py-3.5 text-right">Total Akhir</th>
                        <th class="px-6 py-3.5 text-center">Struk</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($transactions as $trx)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-6 py-4 font-mono font-bold text-slate-900">{{ $trx->no_faktur }}</td>
                            <td class="px-6 py-4 text-xs text-slate-500">{{ $trx->waktu_transaksi->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4 font-medium text-slate-800">{{ $trx->user->name }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full uppercase
                                    {{ $trx->metode_pembayaran === 'tunai' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ $trx->metode_pembayaran }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">Rp {{ number_format($trx->subtotal, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right text-rose-600">
                                {{ $trx->diskon > 0 ? '- Rp ' . number_format($trx->diskon, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-slate-900">Rp {{ number_format($trx->total_akhir, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('pos.receipt', $trx->id) }}" target="_blank" class="text-xs text-emerald-600 hover:underline">
                                    Cetak
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-slate-400">Tidak ada transaksi pada periode yang dipilih.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($transactions->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
