@extends('layouts.app', ['title' => 'Tambah Produk Baru', 'header' => 'Tambah Produk & Multi-Satuan'])

@section('content')
<div class="max-w-4xl mx-auto" x-data="{
    units: [
        { nama_satuan: 'PCS', jumlah_konversi: 1, harga_jual: '' }
    ],
    addUnit() {
        this.units.push({ nama_satuan: '', jumlah_konversi: '', harga_jual: '' });
    },
    removeUnit(index) {
        if (this.units.length > 1) {
            this.units.splice(index, 1);
        } else {
            alert('Minimal harus memiliki 1 satuan dasar (PCS)!');
        }
    }
}">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 md:p-8">
        <form action="{{ route('products.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="border-b border-slate-200 pb-4">
                <h2 class="text-base font-bold text-slate-900">Informasi Dasar Produk</h2>
                <p class="text-xs text-slate-500 mt-0.5">Identitas produk dan stok awal basis eceran (PCS)</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">SKU Singkat / Kode Alias *</label>
                    <input 
                        type="text" 
                        name="sku_singkat" 
                        value="{{ old('sku_singkat') }}" 
                        required 
                        placeholder="Contoh: IND-GNG, TB-330" 
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-mono uppercase focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    >
                    <span class="text-[11px] text-slate-400 mt-1 block">Digunakan kasir untuk pencarian instan tanpa barcode</span>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Kategori Produk *</label>
                    <select 
                        name="category_id" 
                        required 
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    >
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nama Produk Lengkap *</label>
                    <input 
                        type="text" 
                        name="nama_produk" 
                        value="{{ old('nama_produk') }}" 
                        required 
                        placeholder="Contoh: Indomie Mi Goreng Spesial 85g" 
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    >
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Stok Awal (Dalam PCS) *</label>
                    <input 
                        type="number" 
                        name="stok_pcs" 
                        value="{{ old('stok_pcs', 0) }}" 
                        min="0" 
                        required 
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    >
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Batas Minimum Stok (Alert) *</label>
                    <input 
                        type="number" 
                        name="stok_minimum" 
                        value="{{ old('stok_minimum', 10) }}" 
                        min="1" 
                        required 
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    >
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Harga Beli Dasar / HPP per PCS (Rp) *</label>
                    <input 
                        type="number" 
                        name="harga_beli" 
                        value="{{ old('harga_beli', 0) }}" 
                        min="0" 
                        step="0.01" 
                        required 
                        placeholder="0" 
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    >
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Tanggal Kadaluarsa (Expiry Date)</label>
                    <input 
                        type="date" 
                        name="tgl_kadaluarsa" 
                        value="{{ old('tgl_kadaluarsa') }}" 
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    >
                </div>
            </div>

            <!-- Multi-Satuan Repeater Section (REQ-INV-01) -->
            <div class="border-t border-slate-200 pt-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Konversi Satuan & Harga Jual (REQ-INV-01)</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Tentukan opsi satuan jual (misal PCS, PACK, DUS) dan rasio konversinya ke PCS.</p>
                    </div>
                    <button 
                        type="button" 
                        @click="addUnit()"
                        class="px-3 py-1.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 text-xs font-semibold rounded-lg transition-colors flex items-center gap-1"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Tambah Satuan Grosir</span>
                    </button>
                </div>

                <div class="space-y-3">
                    <template x-for="(unit, index) in units" :key="index">
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                            <div class="sm:col-span-4">
                                <label class="block text-[11px] font-semibold text-slate-600 uppercase mb-1">Nama Satuan</label>
                                <input 
                                    type="text" 
                                    :name="'units[' + index + '][nama_satuan]'" 
                                    x-model="unit.nama_satuan" 
                                    required 
                                    placeholder="PCS / PACK / DUS" 
                                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-sm font-semibold uppercase focus:ring-2 focus:ring-emerald-500"
                                >
                            </div>

                            <div class="sm:col-span-3">
                                <label class="block text-[11px] font-semibold text-slate-600 uppercase mb-1">Konversi ke PCS</label>
                                <div class="relative">
                                    <input 
                                        type="number" 
                                        :name="'units[' + index + '][jumlah_konversi]'" 
                                        x-model="unit.jumlah_konversi" 
                                        min="1" 
                                        required 
                                        class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500"
                                    >
                                    <span class="absolute right-3 top-2 text-xs text-slate-400">PCS</span>
                                </div>
                            </div>

                            <div class="sm:col-span-4">
                                <label class="block text-[11px] font-semibold text-slate-600 uppercase mb-1">Harga Jual (Rp)</label>
                                <input 
                                    type="number" 
                                    :name="'units[' + index + '][harga_jual]'" 
                                    x-model="unit.harga_jual" 
                                    min="0" 
                                    step="0.01" 
                                    required 
                                    placeholder="Harga jual kasir" 
                                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-sm font-bold text-slate-900 focus:ring-2 focus:ring-emerald-500"
                                >
                            </div>

                            <div class="sm:col-span-1 flex justify-center pb-1">
                                <button 
                                    type="button" 
                                    @click="removeUnit(index)"
                                    x-show="units.length > 1"
                                    class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors"
                                    title="Hapus Satuan"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Submit Button Bar -->
            <div class="pt-6 border-t border-slate-200 flex items-center justify-end gap-3">
                <a href="{{ route('products.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-50 transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-semibold shadow-sm transition-colors">
                    Simpan Produk & Satuan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
