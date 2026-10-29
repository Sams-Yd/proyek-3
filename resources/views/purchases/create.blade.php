@extends('layouts.app', ['title' => 'Input Barang Masuk', 'header' => 'Penerimaan Barang Masuk (PO)'])

@section('content')
<div class="max-w-5xl mx-auto" x-data="{
    productsData: {!! $productsJson !!},
    items: [
        { product_id: '', unit_id: '', available_units: [], qty: 1, harga_beli_satuan: 0 }
    ],
    onProductChange(index) {
        const selectedProdId = this.items[index].product_id;
        const prod = this.productsData.find(p => p.id == selectedProdId);
        if (prod) {
            this.items[index].available_units = prod.units;
            if (prod.units.length > 0) {
                this.items[index].unit_id = prod.units[0].id;
            }
        } else {
            this.items[index].available_units = [];
            this.items[index].unit_id = '';
        }
    },
    addItem() {
        this.items.push({ product_id: '', unit_id: '', available_units: [], qty: 1, harga_beli_satuan: 0 });
    },
    removeItem(index) {
        if (this.items.length > 1) {
            this.items.splice(index, 1);
        } else {
            alert('Minimal harus ada 1 item barang masuk!');
        }
    },
    calculateTotal() {
        return this.items.reduce((sum, item) => sum + (Number(item.qty || 0) * Number(item.harga_beli_satuan || 0)), 0);
    }
}">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 md:p-8">
        <form action="{{ route('purchases.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="border-b border-slate-200 pb-4">
                <h2 class="text-base font-bold text-slate-900">Data Header Penerimaan Barang</h2>
                <p class="text-xs text-slate-500 mt-0.5">Informasi supplier dan tanggal masuk barang</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Pilih Supplier *</label>
                    <select name="supplier_id" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">-- Pilih Supplier --</option>
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}" {{ old('supplier_id') == $sup->id ? 'selected' : '' }}>{{ $sup->nama_supplier }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Tanggal Masuk Barang *</label>
                    <input 
                        type="date" 
                        name="tgl_masuk" 
                        value="{{ old('tgl_masuk', date('Y-m-d')) }}" 
                        required 
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    >
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Catatan / No. Surat Jalan</label>
                    <input 
                        type="text" 
                        name="catatan" 
                        value="{{ old('catatan') }}" 
                        placeholder="Contoh: SJ-Indofood-8829" 
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    >
                </div>
            </div>

            <!-- Items Repeater Section -->
            <div class="border-t border-slate-200 pt-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Rincian Barang yang Diterima</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Sistem otomatis menghitung konversi satuan ke basis PCS dan memperbarui HPP produk.</p>
                    </div>
                    <button 
                        type="button" 
                        @click="addItem()"
                        class="px-3 py-1.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 text-xs font-semibold rounded-lg transition-colors flex items-center gap-1"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Tambah Baris Barang</span>
                    </button>
                </div>

                <div class="space-y-3">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                            <!-- Produk -->
                            <div class="sm:col-span-4">
                                <label class="block text-[11px] font-semibold text-slate-600 uppercase mb-1">Produk</label>
                                <select 
                                    :name="'items[' + index + '][product_id]'" 
                                    x-model="item.product_id" 
                                    @change="onProductChange(index)"
                                    required 
                                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500"
                                >
                                    <option value="">-- Pilih Produk --</option>
                                    <template x-for="prod in productsData" :key="prod.id">
                                        <option :value="prod.id" x-text="'[' + prod.sku + '] ' + prod.nama"></option>
                                    </template>
                                </select>
                            </div>

                            <!-- Satuan Masuk -->
                            <div class="sm:col-span-2">
                                <label class="block text-[11px] font-semibold text-slate-600 uppercase mb-1">Satuan Beli</label>
                                <select 
                                    :name="'items[' + index + '][unit_id]'" 
                                    x-model="item.unit_id" 
                                    required 
                                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-sm font-semibold uppercase focus:ring-2 focus:ring-emerald-500"
                                >
                                    <template x-for="unit in item.available_units" :key="unit.id">
                                        <option :value="unit.id" x-text="unit.nama_satuan + ' (' + unit.konversi + ' PCS)'"></option>
                                    </template>
                                </select>
                            </div>

                            <!-- Qty -->
                            <div class="sm:col-span-2">
                                <label class="block text-[11px] font-semibold text-slate-600 uppercase mb-1">Jumlah</label>
                                <input 
                                    type="number" 
                                    :name="'items[' + index + '][qty]'" 
                                    x-model="item.qty" 
                                    min="1" 
                                    required 
                                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500"
                                >
                            </div>

                            <!-- Harga Beli per Satuan -->
                            <div class="sm:col-span-3">
                                <label class="block text-[11px] font-semibold text-slate-600 uppercase mb-1">Harga Beli Satuan (Rp)</label>
                                <input 
                                    type="number" 
                                    :name="'items[' + index + '][harga_beli_satuan]'" 
                                    x-model="item.harga_beli_satuan" 
                                    min="0" 
                                    step="0.01" 
                                    required 
                                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-sm font-bold text-slate-900 focus:ring-2 focus:ring-emerald-500"
                                >
                            </div>

                            <!-- Delete Row -->
                            <div class="sm:col-span-1 flex justify-center pb-1">
                                <button 
                                    type="button" 
                                    @click="removeItem(index)"
                                    x-show="items.length > 1"
                                    class="p-2 text-slate-400 hover:text-rose-600 rounded-lg transition-colors"
                                    title="Hapus Baris"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Grand Total Preview -->
            <div class="p-4 rounded-xl bg-slate-900 text-white flex items-center justify-between">
                <span class="text-sm font-medium text-slate-300">Estimasi Total Pembelian:</span>
                <span class="text-xl font-bold text-emerald-400" x-text="'Rp ' + calculateTotal().toLocaleString('id-ID')"></span>
            </div>

            <div class="pt-4 border-t border-slate-200 flex items-center justify-end gap-3">
                <a href="{{ route('purchases.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-50 transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-semibold shadow-sm transition-colors">
                    Simpan & Tambah Stok Masuk
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
