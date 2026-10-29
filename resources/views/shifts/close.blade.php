@extends('layouts.app', ['title' => 'Tutup Shift (Blind Balancing)', 'header' => 'Tutup Shift Kasir - Blind Balancing'])

@section('content')
<div class="max-w-xl mx-auto py-4" x-data="{
    // Denominasi uang tunai untuk memudahkan kasir menghitung uang fisik
    d100k: 0, d50k: 0, d20k: 0, d10k: 0, d5k: 0, d2k: 0, d1k: 0, koin: 0,
    totalFisikManual: '',
    useDenom: false,
    calculateTotal() {
        if (this.useDenom) {
            return (this.d100k * 100000) +
                   (this.d50k * 50000) +
                   (this.d20k * 20000) +
                   (this.d10k * 10000) +
                   (this.d5k * 5000) +
                   (this.d2k * 2000) +
                   (this.d1k * 1000) +
                   (Number(this.koin) || 0);
        }
        return Number(this.totalFisikManual) || 0;
    }
}">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-md p-6 sm:p-8">
        
        <div class="text-center mb-6">
            <div class="w-14 h-14 rounded-2xl bg-amber-100 text-amber-700 mx-auto flex items-center justify-center mb-3">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
            <h2 class="text-xl font-bold text-slate-900">Penutupan Shift (Blind Balancing)</h2>
            <p class="text-xs text-slate-500 mt-1">Sesuai SOP keamanan kasir, hitung seluruh uang tunai fisik yang ada di laci kasir.</p>
        </div>

        <div class="mb-5 p-3.5 bg-slate-50 rounded-xl border border-slate-200 text-xs text-slate-600 flex justify-between items-center">
            <div>
                <span class="text-slate-400 block">Waktu Mulai Shift:</span>
                <span class="font-bold text-slate-800">{{ $shift->waktu_buka->format('d/m/Y H:i') }}</span>
            </div>
            <div class="text-right">
                <span class="text-slate-400 block">Modal Awal:</span>
                <span class="font-bold text-slate-800">Rp {{ number_format($shift->modal_awal, 0, ',', '.') }}</span>
            </div>
        </div>

        <form action="{{ route('shift.close.store') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Toggle kalkulator pecahan -->
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <label class="text-xs font-semibold text-slate-700 uppercase">Input Total Uang Fisik</label>
                <button type="button" @click="useDenom = !useDenom" class="text-xs text-emerald-600 hover:text-emerald-700 font-semibold underline">
                    <span x-show="!useDenom">Gunakan Kalkulator Pecahan</span>
                    <span x-show="useDenom">Input Langsung Nominal Total</span>
                </button>
            </div>

            <!-- Mode 1: Input Langsung -->
            <div x-show="!useDenom">
                <div class="relative">
                    <span class="absolute left-4 top-2.5 text-sm font-bold text-slate-400">Rp</span>
                    <input 
                        type="number" 
                        name="total_setoran_fisik" 
                        x-model="totalFisikManual"
                        :disabled="useDenom"
                        min="0" 
                        required 
                        placeholder="Contoh: 1250000" 
                        class="w-full pl-12 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xl font-black text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    >
                </div>
            </div>

            <!-- Mode 2: Kalkulator Pecahan (Denominasi) -->
            <div x-show="useDenom" class="space-y-2 p-4 bg-slate-50 rounded-xl border border-slate-200">
                <p class="text-xs font-bold text-slate-600 uppercase mb-2">Hitung Lembaran Pecahan Uang:</p>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div class="flex items-center gap-1">
                        <span class="w-16 font-semibold">100.000 x</span>
                        <input type="number" x-model.number="d100k" min="0" class="w-16 p-1 text-center bg-white border rounded">
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="w-16 font-semibold">50.000 x</span>
                        <input type="number" x-model.number="d50k" min="0" class="w-16 p-1 text-center bg-white border rounded">
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="w-16 font-semibold">20.000 x</span>
                        <input type="number" x-model.number="d20k" min="0" class="w-16 p-1 text-center bg-white border rounded">
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="w-16 font-semibold">10.000 x</span>
                        <input type="number" x-model.number="d10k" min="0" class="w-16 p-1 text-center bg-white border rounded">
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="w-16 font-semibold">5.000 x</span>
                        <input type="number" x-model.number="d5k" min="0" class="w-16 p-1 text-center bg-white border rounded">
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="w-16 font-semibold">2.000 x</span>
                        <input type="number" x-model.number="d2k" min="0" class="w-16 p-1 text-center bg-white border rounded">
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="w-16 font-semibold">1.000 x</span>
                        <input type="number" x-model.number="d1k" min="0" class="w-16 p-1 text-center bg-white border rounded">
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="w-16 font-semibold">Koin (Rp)</span>
                        <input type="number" x-model.number="koin" min="0" class="w-20 p-1 text-center bg-white border rounded">
                    </div>
                </div>

                <!-- Hidden input untuk submit nilai kalkulasi -->
                <input type="hidden" name="total_setoran_fisik" :value="calculateTotal()" :disabled="!useDenom">

                <div class="pt-2 border-t border-slate-200 flex justify-between items-center text-sm font-bold">
                    <span>Total Hasil Hitungan:</span>
                    <span class="text-emerald-700" x-text="'Rp ' + calculateTotal().toLocaleString('id-ID')"></span>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Catatan Penutupan Shift (Opsional)</label>
                <textarea 
                    name="catatan" 
                    rows="2" 
                    placeholder="Catatan kendala / keterangan fisik..." 
                    class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                ></textarea>
            </div>

            <div class="pt-2 flex items-center justify-between gap-3">
                <a href="{{ route('pos.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-50 transition-colors">
                    Kembali ke Layar Kasir
                </a>
                <button 
                    type="submit" 
                    onclick="return confirm('Apakah Anda yakin data setoran fisik sudah benar? Setelah disubmit, shift akan ditutup dan selisih akan dihitung.')"
                    class="px-6 py-2.5 bg-rose-600 hover:bg-rose-500 text-white font-bold rounded-xl text-sm shadow-md transition-colors"
                >
                    Konfirmasi Tutup Shift
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
