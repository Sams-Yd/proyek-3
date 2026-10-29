@extends('layouts.app', ['title' => 'Rekap Shift Kasir', 'header' => 'Rekapitulasi Shift Kasir & Blind Balancing (REQ-REP-02)'])

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <p class="text-sm text-slate-500">Audit riwayat shift kasir dan pencatatan selisih setoran fisik kas (Blind Balancing).</p>
        <a href="{{ route('shift.open') }}" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-xl text-sm shadow-sm transition-colors">
            + Buka Shift Kasir Baru
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3.5">Kasir</th>
                        <th class="px-6 py-3.5">Waktu Buka / Tutup</th>
                        <th class="px-6 py-3.5 text-right">Modal Awal</th>
                        <th class="px-6 py-3.5 text-right">Pencatatan Sistem</th>
                        <th class="px-6 py-3.5 text-right">Setoran Fisik</th>
                        <th class="px-6 py-3.5 text-center">Selisih (Over/Short)</th>
                        <th class="px-6 py-3.5 text-center">Status</th>
                        <th class="px-6 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($shifts as $sh)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-6 py-4 font-semibold text-slate-900">{{ $sh->user->name }}</td>
                            <td class="px-6 py-4 text-xs text-slate-500">
                                <div><b>Buka:</b> {{ $sh->waktu_buka->format('d/m/Y H:i') }}</div>
                                <div><b>Tutup:</b> {{ $sh->waktu_tutup?->format('d/m/Y H:i') ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-right font-medium">Rp {{ number_format($sh->modal_awal, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right font-medium">
                                {{ $sh->total_sistem !== null ? 'Rp ' . number_format($sh->total_sistem, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-slate-900">
                                {{ $sh->total_setoran_fisik !== null ? 'Rp ' . number_format($sh->total_setoran_fisik, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($sh->status === 'closed')
                                    @php $s = (float) $sh->selisih; @endphp
                                    @if($s === 0.0)
                                        <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-emerald-100 text-emerald-800">
                                            PAS (0)
                                        </span>
                                    @elseif($s > 0)
                                        <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-blue-100 text-blue-800">
                                            + Rp {{ number_format($s, 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-rose-100 text-rose-800">
                                            - Rp {{ number_format(abs($s), 0, ',', '.') }}
                                        </span>
                                    @endif
                                @else
                                    <span class="text-xs text-slate-400 italic">Sedang berjalan</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full uppercase
                                    {{ $sh->status === 'open' ? 'bg-emerald-100 text-emerald-800 animate-pulse' : 'bg-slate-100 text-slate-700' }}">
                                    {{ $sh->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($sh->status === 'closed')
                                    <a href="{{ route('shifts.show', $sh->id) }}" class="inline-flex items-center gap-1 text-xs text-emerald-600 hover:text-emerald-700 font-medium">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <span>Audit</span>
                                    </a>
                                @else
                                    <a href="{{ route('shift.close') }}" class="px-3 py-1 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-lg shadow-xs">
                                        Tutup Shift
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-slate-400">Belum ada riwayat shift kasir.</td>
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
