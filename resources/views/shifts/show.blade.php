@extends('layouts.app', ['title' => 'Audit Shift Kasir', 'header' => 'Detail Audit Shift: ' . $shift->user->name])

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 sm:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b border-slate-200 gap-4">
            <div>
                <span class="text-xs uppercase font-bold text-slate-400">Shift ID #{{ $shift->id }}</span>
                <h2 class="text-2xl font-black text-slate-900">{{ $shift->user->name }}</h2>
                <p class="text-xs text-slate-500 mt-1">
                    Buka: {{ $shift->waktu_buka->format('d/m/Y H:i') }} | Tutup: {{ $shift->waktu_tutup?->format('d/m/Y H:i') ?? '-' }}
                </p>
            </div>
            <div class="text-left sm:text-right">
                <span class="text-xs uppercase font-bold text-slate-400 block">Selisih Kas Fisik</span>
                @php $s = (float) $shift->selisih; @endphp
                <div class="text-2xl font-black {{ $s === 0.0 ? 'text-emerald-600' : ($s > 0 ? 'text-blue-600' : 'text-rose-600') }}">
                    @if($s === 0.0)
                        PAS (BALANCE)
                    @elseif($s > 0)
                        + Rp {{ number_format($s, 0, ',', '.') }}
                    @else
                        - Rp {{ number_format(abs($s), 0, ',', '.') }}
                    @endif
                </div>
            </div>
        </div>

        <div class="py-6 grid grid-cols-1 sm:grid-cols-4 gap-4 text-center border-b border-slate-200">
            <div class="p-3 rounded-xl bg-slate-50">
                <span class="text-xs text-slate-400 block mb-1">Modal Awal</span>
                <span class="font-bold text-slate-900">Rp {{ number_format($shift->modal_awal, 0, ',', '.') }}</span>
            </div>
            <div class="p-3 rounded-xl bg-slate-50">
                <span class="text-xs text-slate-400 block mb-1">Penjualan Tunai</span>
                <span class="font-bold text-emerald-600">Rp {{ number_format($shift->calculateCashSales(), 0, ',', '.') }}</span>
            </div>
            <div class="p-3 rounded-xl bg-slate-50">
                <span class="text-xs text-slate-400 block mb-1">Penjualan Non-Tunai</span>
                <span class="font-bold text-blue-600">Rp {{ number_format($shift->calculateNonCashSales(), 0, ',', '.') }}</span>
            </div>
            <div class="p-3 rounded-xl bg-slate-50">
                <span class="text-xs text-slate-400 block mb-1">Setoran Fisik Kasir</span>
                <span class="font-bold text-slate-900">Rp {{ number_format($shift->total_setoran_fisik, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Transactions under this shift -->
        <div class="pt-6">
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-3">Daftar Transaksi Selama Shift Ini</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3">No Faktur</th>
                            <th class="px-4 py-3">Waktu</th>
                            <th class="px-4 py-3">Metode</th>
                            <th class="px-4 py-3 text-right">Subtotal</th>
                            <th class="px-4 py-3 text-right">Diskon</th>
                            <th class="px-4 py-3 text-right">Total Akhir</th>
                            <th class="px-4 py-3 text-center">Struk</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($shift->transactions as $trx)
                            <tr>
                                <td class="px-4 py-3 font-mono font-medium text-slate-900">{{ $trx->no_faktur }}</td>
                                <td class="px-4 py-3 text-xs text-slate-500">{{ $trx->waktu_transaksi->format('H:i:s') }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full uppercase
                                        {{ $trx->metode_pembayaran === 'tunai' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $trx->metode_pembayaran }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">Rp {{ number_format($trx->subtotal, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right text-rose-600">
                                    {{ $trx->diskon > 0 ? '- Rp ' . number_format($trx->diskon, 0, ',', '.') : '-' }}
                                </td>
                                <td class="px-4 py-3 text-right font-bold text-slate-900">Rp {{ number_format($trx->total_akhir, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-center">
                                    <a href="{{ route('pos.receipt', $trx->id) }}" target="_blank" class="text-xs text-emerald-600 hover:underline">
                                        Lihat
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-slate-400">Tidak ada transaksi dalam shift ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="pt-6 border-t border-slate-200 flex justify-end">
            <a href="{{ route('shifts.index') }}" class="px-5 py-2 rounded-xl border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-50">
                &larr; Kembali ke Rekap Shift
            </a>
        </div>
    </div>

</div>
@endsection
