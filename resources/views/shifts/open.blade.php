@extends('layouts.app', ['title' => 'Buka Shift Kasir', 'header' => 'Buka Shift Baru (Opening Cash)'])

@section('content')
<div class="max-w-lg mx-auto py-6">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-md p-6 sm:p-8">
        <div class="text-center mb-6">
            <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-700 mx-auto flex items-center justify-center mb-3">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <h2 class="text-xl font-bold text-slate-900">Mulai Shift Kasir (REQ-REP-01)</h2>
            <p class="text-xs text-slate-500 mt-1">Masukkan modal awal uang tunai di laci sebelum melayani transaksi penjualan.</p>
        </div>

        <form action="{{ route('shift.open.store') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Kasir Bertugas</label>
                <input 
                    type="text" 
                    value="{{ auth()->user()->name }}" 
                    disabled 
                    class="w-full px-4 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-sm font-semibold text-slate-700 cursor-not-allowed"
                >
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Modal Awal Kas (Opening Cash Rp) *</label>
                <div class="relative">
                    <span class="absolute left-4 top-2.5 text-sm font-bold text-slate-400">Rp</span>
                    <input 
                        type="number" 
                        name="modal_awal" 
                        value="{{ old('modal_awal', 200000) }}" 
                        min="0" 
                        required 
                        placeholder="Contoh: 200000" 
                        class="w-full pl-12 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-lg font-bold text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    >
                </div>
                <span class="text-xs text-slate-500 mt-1 block">Uang pecahan kecil untuk kembalian pelanggan.</span>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Catatan Tambahan (Opsional)</label>
                <input 
                    type="text" 
                    name="catatan" 
                    value="{{ old('catatan') }}" 
                    placeholder="Contoh: Shift pagi / laci kasir 1" 
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                >
            </div>

            <button 
                type="submit" 
                class="w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-sm shadow-lg shadow-emerald-600/30 transition-all flex items-center justify-center gap-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Buka Shift & Masuk Layar Kasir</span>
            </button>
        </form>
    </div>
</div>
@endsection
