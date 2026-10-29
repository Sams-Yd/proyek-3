@extends('layouts.app', ['title' => 'Dashboard Utama', 'header' => 'Ringkasan Toko & Analitik Bisnis'])

@section('content')
<div class="space-y-6">

    <!-- KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Omzet Hari Ini -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Omzet Hari Ini</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">Rp {{ number_format($omzetToday, 0, ',', '.') }}</h3>
                <p class="text-xs text-slate-500 mt-1">{{ $trxCountToday }} transaksi selesai</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <!-- Laba Kotor Hari Ini -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Laba Kotor Hari Ini</p>
                <h3 class="text-2xl font-bold text-teal-600 mt-1">Rp {{ number_format($grossProfitToday, 0, ',', '.') }}</h3>
                <p class="text-xs text-slate-500 mt-1">Omzet dikurangi HPP modal</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
        </div>

        <!-- Omzet Bulan Ini -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Omzet Bulan Ini</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">Rp {{ number_format($omzetMonth, 0, ',', '.') }}</h3>
                <p class="text-xs text-slate-500 mt-1">{{ now()->translatedFormat('F Y') }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
        </div>

        <!-- Laba Kotor Bulan Ini -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Laba Kotor Bulan Ini</p>
                <h3 class="text-2xl font-bold text-indigo-600 mt-1">Rp {{ number_format($grossProfitMonth, 0, ',', '.') }}</h3>
                <p class="text-xs text-slate-500 mt-1">Akumulasi laba bersih kotor</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
        </div>
    </div>

    <!-- Active Shifts & Quick Shortcuts -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Active Shifts Section -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                    <span>Shift Kasir Aktif</span>
                </h2>
                <a href="{{ route('shifts.index') }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium">Lihat Semua &rarr;</a>
            </div>

            @if($activeShifts->isEmpty())
                <div class="text-center py-6 text-slate-400 text-sm">
                    <p>Tidak ada shift kasir yang sedang dibuka.</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($activeShifts as $shift)
                        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-bold text-slate-900">{{ $shift->user->name }}</p>
                                <p class="text-xs text-slate-500">Mulai: {{ $shift->waktu_buka->format('H:i') }} | Modal: Rp {{ number_format($shift->modal_awal, 0, ',', '.') }}</p>
                            </div>
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800">
                                OPEN
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Alert Inventori: Stok Menipis & Kadaluarsa -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
            <h2 class="text-base font-bold text-slate-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>Peringatan Stok & Kadaluarsa (REQ-INV-02)</span>
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Stok Menipis -->
                <div class="p-4 rounded-xl bg-rose-50/70 border border-rose-200">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-rose-800 uppercase tracking-wide">Stok Menipis (≤ Batas Min)</span>
                        <span class="px-2 py-0.5 rounded-full bg-rose-200 text-rose-800 text-xs font-bold">{{ $lowStockProducts->count() }}</span>
                    </div>
                    @if($lowStockProducts->isEmpty())
                        <p class="text-xs text-rose-600 italic">Semua stok produk dalam batas aman.</p>
                    @else
                        <ul class="space-y-1.5 max-h-40 overflow-y-auto pr-1">
                            @foreach($lowStockProducts->take(5) as $lp)
                                <li class="text-xs flex justify-between items-center bg-white p-2 rounded-lg border border-rose-100">
                                    <span class="font-medium text-slate-800 truncate mr-2">{{ $lp->nama_produk }}</span>
                                    <span class="font-bold text-rose-600 shrink-0">{{ $lp->stok_pcs }} PCS (Min: {{ $lp->stok_minimum }})</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <!-- Kadaluarsa Mendekati -->
                <div class="p-4 rounded-xl bg-amber-50/70 border border-amber-200">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-amber-800 uppercase tracking-wide">Mendekati Expired (H-30)</span>
                        <span class="px-2 py-0.5 rounded-full bg-amber-200 text-amber-800 text-xs font-bold">{{ $expiringProducts->count() }}</span>
                    </div>
                    @if($expiringProducts->isEmpty())
                        <p class="text-xs text-amber-600 italic">Tidak ada produk mendekati kadaluarsa.</p>
                    @else
                        <ul class="space-y-1.5 max-h-40 overflow-y-auto pr-1">
                            @foreach($expiringProducts->take(5) as $ep)
                                <li class="text-xs flex justify-between items-center bg-white p-2 rounded-lg border border-amber-100">
                                    <span class="font-medium text-slate-800 truncate mr-2">{{ $ep->nama_produk }}</span>
                                    <span class="font-bold text-amber-700 shrink-0">{{ $ep->tgl_kadaluarsa->format('d M Y') }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-200 flex items-center justify-between">
            <h2 class="text-base font-bold text-slate-900">Transaksi Penjualan Terakhir</h2>
            <a href="{{ route('reports.sales') }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-semibold">Lihat Riwayat Lengkap &rarr;</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3.5">No Faktur</th>
                        <th class="px-6 py-3.5">Waktu</th>
                        <th class="px-6 py-3.5">Kasir</th>
                        <th class="px-6 py-3.5">Metode</th>
                        <th class="px-6 py-3.5 text-right">Total Akhir</th>
                        <th class="px-6 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($recentTransactions as $trx)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4 font-mono font-medium text-slate-900">{{ $trx->no_faktur }}</td>
                            <td class="px-6 py-4 text-xs text-slate-500">{{ $trx->waktu_transaksi->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4 font-medium text-slate-800">{{ $trx->user->name }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full uppercase
                                    {{ $trx->metode_pembayaran === 'tunai' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ $trx->metode_pembayaran }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-slate-900">Rp {{ number_format($trx->total_akhir, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('pos.receipt', $trx->id) }}" target="_blank" class="inline-flex items-center gap-1 text-xs text-emerald-600 hover:text-emerald-700 font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    <span>Struk</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-400">Belum ada data transaksi yang tercatat hari ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
