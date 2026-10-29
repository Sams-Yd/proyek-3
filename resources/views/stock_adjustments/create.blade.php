@extends('layouts.app', ['title' => 'Input Stok Opname', 'header' => 'Form Penyesuaian Stok (Stok Opname)'])

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 md:p-8">
        <form action="{{ route('stock-adjustments.store') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Pilih Produk *</label>
                <select name="product_id" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">-- Pilih Produk untuk Disesuaikan --</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}" {{ old('product_id') == $p->id ? 'selected' : '' }}>
                            [{{ $p->sku_singkat }}] {{ $p->nama_produk }} (Stok saat ini: {{ $p->stok_pcs }} PCS)
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Arah Mutasi *</label>
                    <select name="jenis" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="keluar" {{ old('jenis') == 'keluar' ? 'selected' : '' }}>- Pengurangan Stok (Keluar)</option>
                        <option value="masuk" {{ old('jenis') == 'masuk' ? 'selected' : '' }}>+ Penambahan Stok (Masuk)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Jumlah (Dalam Satuan PCS) *</label>
                    <input 
                        type="number" 
                        name="qty_pcs" 
                        value="{{ old('qty_pcs', 1) }}" 
                        min="1" 
                        required 
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    >
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Alasan Penyesuaian *</label>
                <select name="alasan" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="rusak" {{ old('alasan') == 'rusak' ? 'selected' : '' }}>Barang Rusak (Damaged)</option>
                    <option value="kadaluarsa" {{ old('alasan') == 'kadaluarsa' ? 'selected' : '' }}>Kadaluarsa (Expired)</option>
                    <option value="hilang" {{ old('alasan') == 'hilang' ? 'selected' : '' }}>Barang Hilang / Selisih Fisik Minus</option>
                    <option value="selisih_fisik" {{ old('alasan') == 'selisih_fisik' ? 'selected' : '' }}>Penyesuaian Fisik Opname Berkala</option>
                    <option value="lainnya" {{ old('alasan') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Keterangan / Catatan Tambahan</label>
                <textarea 
                    name="catatan" 
                    rows="3" 
                    placeholder="Contoh: Kemasan bocor saat penataan rak atau hasil cek stok fisik akhir pekan" 
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                >{{ old('catatan') }}</textarea>
            </div>

            <div class="pt-4 border-t border-slate-200 flex items-center justify-end gap-3">
                <a href="{{ route('stock-adjustments.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-50 transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-semibold shadow-sm transition-colors">
                    Simpan Penyesuaian
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
