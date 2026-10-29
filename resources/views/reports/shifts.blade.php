@extends('layouts.app', ['title' => 'Audit Selisih Shift', 'header' => 'Laporan Rekapitulasi Shift Kasir & Audit Selisih'])

@section('content')
<div class="space-y-6">

    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <form method="GET" action="{{ route('reports.shifts') }}" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-sm">
            </div>
            <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-xl text-sm">
                Filter Audit
            </button>
        </form>

        <div class="text-left sm:text-right">
            <span class="text-xs font-bold text-slate-400 uppercase">Akumulasi Selisih Periode</span>
            <div class="text-xl font-black {{ $totalSelisih == 0 ? 'text-emerald-600' : ($totalSelisih > 0 ? 'text-blue-600' : 'text-rose-600') }}">
                @if($totalSelisih == 0)
                    Rp 0 (BALANCE)
                @elseif($totalSelisih > 0)
                    + Rp {{ number_format($totalSelisih, 0, ',', '.') }} (OVER)
                @else
                    - Rp {{ number_format(abs($totalSelisih), 0, ',', '.') }} (SHORT)
                @endif
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3.5">Kasir</th>
                        <th class="px-6 py-3.5">Waktu Shift</th>
                        <th class="px-6 py-3.5 text-right">Modal Awal</th>
                        <th class="px-6 py-3.5 text-right">Penjualan Tunai</th>
                        <th class="px-6 py-3.5 text-right">Pencatatan Sistem</th>
                        <th class="px-6 py-3.5 text-right">Setoran Fisik</th>
                        <th class="px-6 py-3.5 text-center">Selisih</th>
                        <th class="px-6 py-3.5 text-center">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($shifts as $sh)
                        @php $s = (float) $sh->selisih; @endphp
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-6 py-4 font-semibold text-slate-900">{{ $sh->user->name }}</td>
                            <td class="px-6 py-4 text-xs text-slate-500">
                                <div><b>Buka:</b> {{ $sh->waktu_buka->format('d/m/Y H:i') }}</div>
                                <div><b>Tutup:</b> {{ $sh->waktu_tutup?->format('H:i') ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-right">Rp {{ number_format($sh->modal_awal, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right text-emerald-600 font-medium">Rp {{ number_format($sh->calculateCashSales(), 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right">Rp {{ number_format($sh->total_sistem ?? 0, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right font-bold text-slate-900">Rp {{ number_format($sh->total_setoran_fisik ?? 0, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-center">
                                @if($sh->status === 'closed')
                                    <span class="px-2.5 py-1 text-xs font-bold rounded-full
                                        {{ $s === 0.0 ? 'bg-emerald-100 text-emerald-800' : ($s > 0 ? 'bg-blue-100 text-blue-800' : 'bg-rose-100 text-rose-800') }}">
                                        {{ $s === 0.0 ? 'PAS (0)' : ($s > 0 ? '+ ' . number_format($s, 0, ',', '.') : '- ' . number_format(abs($s), 0, ',', '.')) }}
                                    </span>
                                @else
                                    <span class="text-xs text-amber-600 font-bold bg-amber-50 px-2 py-0.5 rounded">OPEN</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('shifts.show', $sh->id) }}" class="text-xs text-emerald-600 hover:underline">
                                    Lihat Audit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-slate-400">Belum ada shift pada rentang waktu ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($shifts->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $shifts->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
