@extends('layouts.app', ['title' => 'Laporan Laba-Rugi Kotor', 'header' => 'Laporan Laba-Rugi Kotor Toko (Omzet - HPP)'])

@section('content')
<div class="space-y-6">

    <!-- Filter Bar -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
        <form method="GET" action="{{ route('reports.profit') }}" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-sm">
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-xl text-sm">
                    Filter Periode
                </button>
                <button type="button" onclick="window.print()" class="px-3 py-2 bg-slate-800 text-white rounded-xl text-sm font-semibold hover:bg-slate-700">
                    Cetak Laporan
                </button>
            </div>
        </form>
    </div>

    <!-- Profit & Loss Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <!-- Total Omzet -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200">
            <span class="text-xs font-bold text-slate-400 uppercase">Total Omzet Penjualan</span>
            <div class="text-2xl font-black text-slate-900 mt-1">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</div>
            <span class="text-xs text-slate-400 block mt-1">{{ count($transactions) }} Transaksi</span>
        </div>

        <!-- Total HPP Modal -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200">
            <span class="text-xs font-bold text-slate-400 uppercase">Harga Pokok Penjualan (HPP)</span>
            <div class="text-2xl font-black text-slate-700 mt-1">Rp {{ number_format($totalHpp, 0, ',', '.') }}</div>
            <span class="text-xs text-slate-400 block mt-1">Total modal barang terjual</span>
        </div>

        <!-- Laba Kotor -->
        <div class="p-5 rounded-2xl bg-emerald-50 border border-emerald-200">
            <span class="text-xs font-bold text-emerald-800 uppercase">Laba Kotor Toko</span>
            <div class="text-2xl font-black text-emerald-700 mt-1">Rp {{ number_format($grossProfit, 0, ',', '.') }}</div>
            <span class="text-xs text-emerald-600 font-medium block mt-1">Omzet - HPP Modal</span>
        </div>

        <!-- Margin Laba Kotor -->
        <div class="p-5 rounded-2xl bg-teal-50 border border-teal-200">
            <span class="text-xs font-bold text-teal-800 uppercase">Gross Profit Margin</span>
            <div class="text-2xl font-black text-teal-700 mt-1">{{ number_format($profitMargin, 1) }}%</div>
            <span class="text-xs text-teal-600 font-medium block mt-1">Rasio persentase keuntungan</span>
        </div>
    </div>

    <!-- Breakdown Transaksi & Margin Tiap Faktur -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-200 flex items-center justify-between">
            <h2 class="text-base font-bold text-slate-900">Rincian Laba per Faktur Transaksi</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3.5">No Faktur</th>
                        <th class="px-6 py-3.5">Waktu</th>
                        <th class="px-6 py-3.5 text-right">Nilai Transaksi (Omzet)</th>
                        <th class="px-6 py-3.5 text-right">Beban HPP Modal</th>
                        <th class="px-6 py-3.5 text-right">Laba Kotor</th>
                        <th class="px-6 py-3.5 text-center">Margin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($transactions as $trx)
                        @php
                            $trxHpp = $trx->calculateHpp();
                            $trxProfit = $trx->total_akhir - $trxHpp;
                            $margin = $trx->total_akhir > 0 ? ($trxProfit / $trx->total_akhir) * 100 : 0;
                        @endphp
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-6 py-4 font-mono font-bold text-slate-900">{{ $trx->no_faktur }}</td>
                            <td class="px-6 py-4 text-xs text-slate-500">{{ $trx->waktu_transaksi->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4 text-right font-semibold text-slate-900">Rp {{ number_format($trx->total_akhir, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right text-slate-500">Rp {{ number_format($trxHpp, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right font-bold text-emerald-600">Rp {{ number_format($trxProfit, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-center font-bold text-xs text-teal-700">
                                {{ number_format($margin, 1) }}%
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-slate-400">Tidak ada transaksi pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
