@extends('layouts.app', ['title' => 'Ringkasan Tutup Shift', 'header' => 'Rekapitulasi Shift & Selisih Kasir'])

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
        
        <div class="text-center pb-6 border-b border-slate-200">
            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                {{ $shift->status === 'closed' ? 'bg-slate-200 text-slate-800' : 'bg-emerald-100 text-emerald-800' }}">
                Shift Telah Ditutup
            </span>
            <h2 class="text-2xl font-black text-slate-900 mt-2">Ringkasan Shift: {{ $shift->user->name }}</h2>
            <p class="text-xs text-slate-500 mt-1">
                {{ $shift->waktu_buka->format('d/m/Y H:i') }} &mdash; {{ $shift->waktu_tutup?->format('H:i') ?? '-' }}
            </p>
        </div>

        <!-- Over / Short Result Card -->
        @php
            $diff = (float) $shift->selisih;
        @endphp
        <div class="my-6 p-5 rounded-2xl border text-center
            {{ $diff === 0.0 ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : ($diff > 0 ? 'bg-blue-50 border-blue-200 text-blue-900' : 'bg-rose-50 border-rose-200 text-rose-900') }}">
            <span class="text-xs uppercase font-bold tracking-wider">Status Selisih Setoran Kas (Blind Balance)</span>
            <div class="text-3xl font-black mt-1">
                @if($diff === 0.0)
                    PAS (BALANCE)
                @elseif($diff > 0)
                    + Rp {{ number_format($diff, 0, ',', '.') }} (LEBIH / OVER)
                @else
                    - Rp {{ number_format(abs($diff), 0, ',', '.') }} (KURANG / SHORT)
                @endif
            </div>
            <p class="text-xs mt-1 text-slate-600">
                Setoran Fisik: <b>Rp {{ number_format($shift->total_setoran_fisik, 0, ',', '.') }}</b> | Pencatatan Sistem: <b>Rp {{ number_format($shift->total_sistem, 0, ',', '.') }}</b>
            </p>
        </div>

        <!-- Detail Breakdown Table -->
        <div class="space-y-3 text-sm divide-y divide-slate-100">
            <div class="flex justify-between py-2">
                <span class="text-slate-500">Modal Awal Kasir</span>
                <span class="font-bold text-slate-800">Rp {{ number_format($shift->modal_awal, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between py-2">
                <span class="text-slate-500">Penjualan Tunai (+)</span>
                <span class="font-bold text-emerald-600">Rp {{ number_format($shift->calculateCashSales(), 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between py-2">
                <span class="text-slate-500">Penjualan Non-Tunai (QRIS/Debit)</span>
                <span class="font-bold text-blue-600">Rp {{ number_format($shift->calculateNonCashSales(), 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between py-2 bg-slate-50 px-3 rounded-lg">
                <span class="font-bold text-slate-700">Total Uang Tunai di Laci Menurut Sistem:</span>
                <span class="font-black text-slate-900">Rp {{ number_format($shift->total_sistem, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between py-2 bg-slate-50 px-3 rounded-lg">
                <span class="font-bold text-slate-700">Total Setoran Fisik Kasir:</span>
                <span class="font-black text-slate-900">Rp {{ number_format($shift->total_setoran_fisik, 0, ',', '.') }}</span>
            </div>
        </div>

        @if($shift->catatan)
            <div class="mt-4 p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs">
                <span class="font-bold text-slate-600 block mb-0.5">Catatan Kasir:</span>
                <p class="text-slate-700 italic">{{ $shift->catatan }}</p>
            </div>
        @endif

        <div class="mt-8 pt-6 border-t border-slate-200 flex flex-wrap items-center justify-between gap-3">
            <a href="{{ route('dashboard') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-50">
                Kembali ke Dashboard
            </a>
            <button onclick="window.print()" class="px-5 py-2.5 rounded-xl bg-slate-800 text-white text-sm font-semibold hover:bg-slate-700 inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Ringkasan Shift</span>
            </button>
        </div>

    </div>

</div>
@endsection
