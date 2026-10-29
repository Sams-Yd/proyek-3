@extends('layouts.pos', ['title' => 'Mesin Kasir POS Minimarket'])

@section('content')
<div class="flex-1 flex flex-col h-screen overflow-hidden" x-data="posApp()">

    <!-- POS Top Header Bar -->
    <header class="h-14 bg-slate-950 border-b border-slate-800 px-4 flex items-center justify-between shrink-0">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-emerald-600 flex items-center justify-center font-black text-white text-base">
                M
            </div>
            <div>
                <span class="font-bold text-sm tracking-wide text-white">POS KASIR MINIMARKET</span>
                <span class="text-[11px] text-emerald-400 block font-mono">Tanpa Barcode Scanner (Live Search & Grid)</span>
            </div>
        </div>

        <!-- Shift & Kasir Info -->
        <div class="flex items-center gap-4">
            <!-- Active Shift Pill -->
            <div class="flex items-center gap-2 px-3 py-1 bg-slate-900 border border-slate-800 rounded-lg text-xs">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-slate-400">Kasir: <b class="text-white">{{ auth()->user()->name }}</b></span>
                <span class="text-slate-600">|</span>
                <span class="text-slate-400">Modal: <b class="text-emerald-400">Rp {{ number_format($activeShift->modal_awal, 0, ',', '.') }}</b></span>
            </div>

            <!-- Jam Digital Real-Time -->
            <div class="hidden md:block font-mono text-sm font-bold text-slate-300" x-text="currentTime"></div>

            <!-- Tutup Shift Button -->
            <a href="{{ route('shift.close') }}" class="px-3 py-1.5 bg-rose-600/80 hover:bg-rose-600 text-white font-semibold text-xs rounded-lg transition-colors border border-rose-500/50 flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                <span>Tutup Shift</span>
            </a>

            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'supervisor')
                <a href="{{ route('dashboard') }}" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-lg transition-colors">
                    Dashboard
                </a>
            @endif
        </div>
    </header>

    <!-- Main POS Body (Split: Kiri Katalog/Search, Kanan Keranjang) -->
    <div class="flex-1 flex overflow-hidden">

        <!-- SISI KIRI: Live Search + Kategori + Visual Grid -->
        <div class="flex-1 flex flex-col min-w-0 bg-slate-900 border-r border-slate-800 p-4 overflow-hidden">
            
            <!-- REQ-POS-01: Kolom Live Search Autocomplete (<300ms) -->
            <div class="relative mb-3">
                <div class="relative flex items-center">
                    <svg class="w-5 h-5 text-slate-400 absolute left-4 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input 
                        type="text" 
                        x-ref="searchInput"
                        x-model="searchQuery" 
                        @input.debounce.150ms="onSearchInput()"
                        @keydown.escape="searchQuery = ''; searchResults = []"
                        placeholder="Ketik Nama Produk atau SKU Singkat (contoh: IND-GNG, TB-330)..." 
                        class="w-full pl-12 pr-28 py-3 bg-slate-950 border border-slate-700 rounded-xl text-white placeholder-slate-500 font-medium text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 shadow-inner"
                    >
                    <span class="absolute right-3 px-2 py-1 bg-slate-800 text-[10px] font-mono text-slate-400 rounded border border-slate-700">
                        Live &lt;300ms
                    </span>
                </div>

                <!-- Autocomplete Dropdown List -->
                <div 
                    x-show="searchResults.length > 0 && searchQuery.length > 0" 
                    @click.away="searchResults = []"
                    class="absolute left-0 right-0 top-full mt-1.5 bg-slate-950 border border-slate-700 rounded-xl shadow-2xl z-30 max-h-80 overflow-y-auto divide-y divide-slate-800"
                >
                    <template x-for="item in searchResults" :key="item.id">
                        <div class="p-3 hover:bg-slate-800/80 transition-colors flex items-center justify-between cursor-pointer">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded bg-emerald-950 text-emerald-400 border border-emerald-800 text-[11px] font-mono font-bold" x-text="item.sku_singkat"></span>
                                    <span class="font-bold text-white text-sm" x-text="item.nama_produk"></span>
                                </div>
                                <div class="text-xs text-slate-400 mt-1">
                                    Kategori: <span x-text="item.kategori"></span> | Sisa Stok: <b class="text-emerald-400" x-text="item.stok_pcs + ' PCS'"></b>
                                </div>
                            </div>
                            <!-- Tombol pilihan satuan langsung -->
                            <div class="flex items-center gap-1.5 shrink-0">
                                <template x-for="unit in item.units" :key="unit.id">
                                    <button 
                                        type="button" 
                                        @click="addToCart(item, unit); searchResults = []; searchQuery = ''"
                                        class="px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-xs font-bold transition-transform active:scale-95 flex flex-col items-center"
                                    >
                                        <span x-text="unit.nama_satuan"></span>
                                        <span class="text-[10px] font-mono opacity-90" x-text="'Rp ' + unit.harga_jual.toLocaleString('id-ID')"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- REQ-POS-02: Katalog Visual Grid - Kategori Filter Buttons -->
            <div class="flex items-center gap-2 pb-3 overflow-x-auto no-scrollbar shrink-0">
                <button 
                    type="button" 
                    @click="activeCategory = 'all'"
                    :class="activeCategory === 'all' ? 'bg-emerald-600 text-white' : 'bg-slate-800 text-slate-400 hover:text-white hover:bg-slate-700'"
                    class="px-3.5 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-colors"
                >
                    Semua Kategori
                </button>
                @foreach($categories as $cat)
                    <button 
                        type="button" 
                        @click="activeCategory = {{ $cat->id }}"
                        :class="activeCategory == {{ $cat->id }} ? 'bg-emerald-600 text-white' : 'bg-slate-800 text-slate-400 hover:text-white hover:bg-slate-700'"
                        class="px-3.5 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-colors"
                    >
                        {{ $cat->nama_kategori }}
                    </button>
                @endforeach
            </div>

            <!-- Visual Grid Produk Populer & Cepat -->
            <div class="flex-1 overflow-y-auto pr-1">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                    <template x-for="prod in filteredGridProducts" :key="prod.id">
                        <div class="bg-slate-950/70 border border-slate-800 hover:border-emerald-600/50 rounded-xl p-3 flex flex-col justify-between transition-all group hover:bg-slate-850">
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="font-mono text-[10px] font-bold text-emerald-400 px-1.5 py-0.5 rounded bg-emerald-950/80 border border-emerald-900" x-text="prod.sku_singkat"></span>
                                    <span class="text-[11px] font-medium" :class="prod.stok_pcs <= prod.stok_minimum ? 'text-rose-400 font-bold' : 'text-slate-400'" x-text="prod.stok_pcs + ' PCS'"></span>
                                </div>
                                <h4 class="font-bold text-white text-xs leading-snug line-clamp-2" x-text="prod.nama_produk"></h4>
                            </div>

                            <!-- Opsi Satuan Tombol -->
                            <div class="mt-3 pt-2 border-t border-slate-800/80 space-y-1">
                                <template x-for="unit in prod.units" :key="unit.id">
                                    <button 
                                        type="button" 
                                        @click="addToCart(prod, unit)"
                                        class="w-full px-2 py-1.5 rounded-lg bg-slate-800 hover:bg-emerald-600 text-slate-200 hover:text-white text-xs font-semibold flex items-center justify-between transition-colors"
                                    >
                                        <span x-text="unit.nama_satuan"></span>
                                        <span class="font-mono text-[11px]" x-text="'Rp ' + Number(unit.harga_jual).toLocaleString('id-ID')"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Bottom Keyboard Shortcut Guide Bar -->
            <div class="mt-3 pt-3 border-t border-slate-800/80 flex flex-wrap items-center justify-between text-xs text-slate-400 shrink-0">
                <div class="flex items-center gap-3">
                    <span class="flex items-center gap-1"><kbd class="px-1.5 py-0.5 bg-slate-800 border border-slate-700 rounded text-slate-300 font-mono font-bold">F1</kbd> Bayar / Checkout</span>
                    <span class="flex items-center gap-1"><kbd class="px-1.5 py-0.5 bg-slate-800 border border-slate-700 rounded text-slate-300 font-mono font-bold">F2</kbd> Hold Transaksi</span>
                    <span class="flex items-center gap-1"><kbd class="px-1.5 py-0.5 bg-slate-800 border border-slate-700 rounded text-slate-300 font-mono font-bold">F3</kbd> Cek Stok Instan</span>
                    <span class="flex items-center gap-1"><kbd class="px-1.5 py-0.5 bg-slate-800 border border-slate-700 rounded text-slate-300 font-mono font-bold">F4</kbd> Reset Keranjang</span>
                </div>
                <button type="button" @click="openStockModal()" class="text-xs text-emerald-400 hover:text-emerald-300 font-bold underline">
                    Buka Cek Stok (F3)
                </button>
            </div>
        </div>

        <!-- SISI KANAN: Keranjang Belanja (Cart) & Rincian Total -->
        <div class="w-96 md:w-[420px] flex flex-col bg-slate-950 shrink-0">
            
            <!-- Tab Antrean Transaksi Tertahan (REQ-POS-04 Hold & Resume) -->
            <div class="bg-slate-900 border-b border-slate-800 px-3 py-2 flex items-center justify-between text-xs">
                <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar">
                    <button 
                        type="button" 
                        @click="switchCartTab(0)"
                        :class="currentTab === 0 ? 'bg-emerald-600 text-white font-bold' : 'bg-slate-800 text-slate-400 hover:text-white'"
                        class="px-2.5 py-1 rounded-md text-xs transition-colors shrink-0"
                    >
                        Utama (<span x-text="carts[0].length"></span>)
                    </button>

                    <template x-for="(slot, sIndex) in heldSlots" :key="sIndex">
                        <div class="flex items-center gap-1 bg-slate-800 rounded-md px-2 py-0.5 text-xs shrink-0" :class="currentTab === (sIndex + 1) ? 'ring-1 ring-emerald-500' : ''">
                            <button 
                                type="button" 
                                @click="switchCartTab(sIndex + 1)"
                                class="text-slate-300 font-medium"
                                x-text="'Hold ' + (sIndex + 1) + ' (' + slot.length + ')'"
                            ></button>
                            <button 
                                type="button" 
                                @click="deleteHoldSlot(sIndex)"
                                class="text-slate-500 hover:text-rose-400 font-bold ml-1"
                                title="Batal Hold"
                            >&times;</button>
                        </div>
                    </template>
                </div>

                <button 
                    type="button" 
                    @click="holdCurrentOrder()"
                    x-show="cart.length > 0 && heldSlots.length < 5"
                    class="px-2 py-1 bg-amber-600/80 hover:bg-amber-600 text-white font-semibold rounded text-[11px] shrink-0"
                    title="Simpan antrean sementara (F2)"
                >
                    Hold (F2)
                </button>
            </div>

            <!-- Header Keranjang -->
            <div class="p-3 border-b border-slate-800/80 flex items-center justify-between text-xs">
                <span class="font-bold text-slate-200">Keranjang Belanja</span>
                <button 
                    type="button" 
                    @click="requestCartReset()"
                    x-show="cart.length > 0"
                    class="text-rose-400 hover:text-rose-300 font-medium text-xs flex items-center gap-1"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    <span>Reset (F4)</span>
                </button>
            </div>

            <!-- List Item di Keranjang -->
            <div class="flex-1 overflow-y-auto p-3 space-y-2">
                <template x-if="cart.length === 0">
                    <div class="h-full flex flex-col items-center justify-center text-slate-500 text-center p-6">
                        <svg class="w-12 h-12 text-slate-700 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <p class="text-sm font-medium">Keranjang Belanja Kosong</p>
                        <p class="text-xs text-slate-600 mt-1">Cari produk di kolom pencarian atau klik item di katalog visual.</p>
                    </div>
                </template>

                <template x-for="(item, index) in cart" :key="index">
                    <div class="p-2.5 rounded-xl bg-slate-900 border border-slate-800 flex flex-col gap-2">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <span class="font-mono text-[10px] font-bold text-emerald-400" x-text="item.sku"></span>
                                <h5 class="text-xs font-bold text-white leading-tight" x-text="item.name"></h5>
                            </div>
                            
                            <!-- REQ-POS-07: Void Item dengan PIN Supervisor -->
                            <button 
                                type="button" 
                                @click="requestVoidItem(index)"
                                class="text-slate-500 hover:text-rose-400 p-1"
                                title="Hapus Item (Void)"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <!-- Selector Satuan & Qty Controls -->
                        <div class="flex items-center justify-between pt-1 border-t border-slate-800 text-xs">
                            <!-- Satuan Switcher (PCS / PACK / DUS) -->
                            <select 
                                x-model="item.unit_id" 
                                @change="onUnitChange(item)"
                                class="px-2 py-1 bg-slate-950 border border-slate-700 rounded text-xs font-bold text-emerald-400 focus:outline-none"
                            >
                                <template x-for="u in item.available_units" :key="u.id">
                                    <option :value="u.id" x-text="u.nama_satuan + ' (' + u.jumlah_konversi + 'x)'" :selected="u.id == item.unit_id"></option>
                                </template>
                            </select>

                            <!-- Qty - / + -->
                            <div class="flex items-center gap-1">
                                <button 
                                    type="button" 
                                    @click="decreaseQty(index)"
                                    class="w-6 h-6 rounded bg-slate-800 hover:bg-slate-700 text-white font-bold flex items-center justify-center text-xs"
                                >-</button>
                                <input 
                                    type="number" 
                                    x-model.number="item.qty" 
                                    @change="validateItemQty(index)"
                                    min="1" 
                                    class="w-10 py-0.5 text-center bg-slate-950 border border-slate-700 rounded text-xs font-bold text-white focus:outline-none"
                                >
                                <button 
                                    type="button" 
                                    @click="increaseQty(index)"
                                    class="w-6 h-6 rounded bg-slate-800 hover:bg-slate-700 text-white font-bold flex items-center justify-center text-xs"
                                >+</button>
                            </div>

                            <!-- Subtotal Item -->
                            <div class="text-right">
                                <span class="text-xs font-mono font-bold text-white" x-text="'Rp ' + (item.qty * item.harga).toLocaleString('id-ID')"></span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Panel Rincian Pembayaran & Tombol Bayar (F1) -->
            <div class="p-4 bg-slate-900 border-t border-slate-800 space-y-3 shrink-0">
                <div class="space-y-1.5 text-xs">
                    <div class="flex justify-between text-slate-400">
                        <span>Subtotal Barang</span>
                        <span class="font-mono text-white font-medium" x-text="'Rp ' + calculateSubtotal().toLocaleString('id-ID')"></span>
                    </div>
                    <div class="flex justify-between items-center text-slate-400">
                        <span>Diskon Belanja</span>
                        <div class="flex items-center gap-1">
                            <span class="text-rose-400">- Rp</span>
                            <input 
                                type="number" 
                                x-model.number="discount" 
                                min="0" 
                                class="w-20 px-2 py-0.5 bg-slate-950 border border-slate-700 rounded text-right text-xs font-bold text-rose-400 focus:outline-none"
                            >
                        </div>
                    </div>
                    <div class="flex justify-between text-base font-bold text-white pt-2 border-t border-slate-800">
                        <span>Total Akhir</span>
                        <span class="text-emerald-400 font-mono text-xl" x-text="'Rp ' + calculateTotalAkhir().toLocaleString('id-ID')"></span>
                    </div>
                </div>

                <button 
                    type="button" 
                    @click="openCheckoutModal()"
                    :disabled="cart.length === 0"
                    class="w-full py-3.5 px-4 rounded-xl font-black text-sm text-white bg-emerald-600 hover:bg-emerald-500 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg shadow-emerald-600/30 transition-all flex items-center justify-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>BAYAR SEKARANG (F1)</span>
                </button>
            </div>
        </div>

    </div>

    <!-- MODAL 1: CHECKOUT & MULTI-PAYMENT (F1) -->
    <div 
        x-show="isCheckoutModalOpen" 
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-xs p-4"
        style="display: none;"
    >
        <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-lg p-6 shadow-2xl text-slate-100" @click.away="isCheckoutModalOpen = false">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <span>Pembayaran Transaksi</span>
                </h3>
                <button type="button" @click="isCheckoutModalOpen = false" class="text-slate-400 hover:text-white">&times;</button>
            </div>

            <div class="py-4 space-y-4">
                <!-- Total Akhir Tagihan -->
                <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 text-center">
                    <span class="text-xs uppercase font-semibold text-slate-400">Total Yang Harus Dibayar:</span>
                    <div class="text-3xl font-black text-emerald-400 font-mono mt-1" x-text="'Rp ' + calculateTotalAkhir().toLocaleString('id-ID')"></div>
                </div>

                <!-- Metode Pembayaran -->
                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Metode Pembayaran</label>
                    <div class="grid grid-cols-4 gap-2">
                        <button 
                            type="button" 
                            @click="paymentMethod = 'tunai'"
                            :class="paymentMethod === 'tunai' ? 'bg-emerald-600 text-white font-bold border-emerald-500' : 'bg-slate-800 text-slate-300 border-slate-700'"
                            class="py-2.5 px-2 rounded-xl text-xs border text-center transition-all"
                        >Tunai</button>
                        <button 
                            type="button" 
                            @click="paymentMethod = 'qris'; cashPaid = calculateTotalAkhir()"
                            :class="paymentMethod === 'qris' ? 'bg-emerald-600 text-white font-bold border-emerald-500' : 'bg-slate-800 text-slate-300 border-slate-700'"
                            class="py-2.5 px-2 rounded-xl text-xs border text-center transition-all"
                        >QRIS</button>
                        <button 
                            type="button" 
                            @click="paymentMethod = 'debit'; cashPaid = calculateTotalAkhir()"
                            :class="paymentMethod === 'debit' ? 'bg-emerald-600 text-white font-bold border-emerald-500' : 'bg-slate-800 text-slate-300 border-slate-700'"
                            class="py-2.5 px-2 rounded-xl text-xs border text-center transition-all"
                        >Debit</button>
                        <button 
                            type="button" 
                            @click="paymentMethod = 'e-wallet'; cashPaid = calculateTotalAkhir()"
                            :class="paymentMethod === 'e-wallet' ? 'bg-emerald-600 text-white font-bold border-emerald-500' : 'bg-slate-800 text-slate-300 border-slate-700'"
                            class="py-2.5 px-2 rounded-xl text-xs border text-center transition-all"
                        >E-Wallet</button>
                    </div>
                </div>

                <!-- Input Nominal Tunai (Jika Metode Tunai) -->
                <div x-show="paymentMethod === 'tunai'" class="space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Nominal Diterima Kasir (Rp)</label>
                        <input 
                            type="number" 
                            x-ref="cashPaidInput"
                            x-model.number="cashPaid" 
                            min="0" 
                            placeholder="0"
                            class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-xl font-bold font-mono text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                        >
                    </div>

                    <!-- Quick Cash Suggestion Pills -->
                    <div class="flex flex-wrap gap-2">
                        <button type="button" @click="cashPaid = calculateTotalAkhir()" class="px-2.5 py-1 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs rounded-lg border border-slate-700">Uang Pas</button>
                        <button type="button" @click="cashPaid = 10000" class="px-2.5 py-1 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs rounded-lg border border-slate-700">10k</button>
                        <button type="button" @click="cashPaid = 20000" class="px-2.5 py-1 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs rounded-lg border border-slate-700">20k</button>
                        <button type="button" @click="cashPaid = 50000" class="px-2.5 py-1 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs rounded-lg border border-slate-700">50k</button>
                        <button type="button" @click="cashPaid = 100000" class="px-2.5 py-1 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs rounded-lg border border-slate-700">100k</button>
                        <button type="button" @click="cashPaid = 200000" class="px-2.5 py-1 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs rounded-lg border border-slate-700">200k</button>
                    </div>

                    <!-- Kembalian -->
                    <div class="p-3 bg-slate-950 border border-slate-800 rounded-xl flex items-center justify-between">
                        <span class="text-xs text-slate-400">Uang Kembalian:</span>
                        <span class="font-mono text-xl font-black" :class="calculateKembalian() >= 0 ? 'text-teal-400' : 'text-rose-400'" x-text="'Rp ' + calculateKembalian().toLocaleString('id-ID')"></span>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-800 flex items-center justify-end gap-3">
                <button type="button" @click="isCheckoutModalOpen = false" class="px-4 py-2.5 text-slate-400 hover:text-white text-xs font-semibold">
                    Batal
                </button>
                <button 
                    type="button" 
                    @click="processCheckout()"
                    :disabled="checkoutLoading || (paymentMethod === 'tunai' && calculateKembalian() < 0)"
                    class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold text-sm rounded-xl transition-all shadow-lg shadow-emerald-600/30 flex items-center gap-2"
                >
                    <span x-show="checkoutLoading">Memproses...</span>
                    <span x-show="!checkoutLoading">Selesaikan Transaksi (Enter)</span>
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL 2: CEK STOK INSTAN (F3 - REQ-POS-03) -->
    <div 
        x-show="isStockModalOpen" 
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-xs p-4"
        style="display: none;"
    >
        <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-2xl p-6 shadow-2xl text-slate-100 max-h-[85vh] flex flex-col" @click.away="isStockModalOpen = false">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800 shrink-0">
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <span>Cek Stok Instan Real-Time (F3)</span>
                </h3>
                <button type="button" @click="isStockModalOpen = false" class="text-slate-400 hover:text-white">&times;</button>
            </div>

            <div class="py-3 shrink-0">
                <input 
                    type="text" 
                    x-ref="stockSearchInput"
                    x-model="stockSearchQuery" 
                    @input.debounce.150ms="onStockSearchInput()"
                    placeholder="Ketik nama produk atau SKU singkat untuk cek stok..." 
                    class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                >
            </div>

            <div class="flex-1 overflow-y-auto divide-y divide-slate-800 border border-slate-800 rounded-xl bg-slate-950 p-2">
                <template x-for="item in stockResults" :key="item.id">
                    <div class="p-3 flex items-center justify-between hover:bg-slate-900 rounded-lg">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded bg-slate-800 text-emerald-400 font-mono text-xs font-bold" x-text="item.sku"></span>
                                <span class="font-bold text-white text-sm" x-text="item.nama"></span>
                            </div>
                            <div class="text-xs text-slate-400 mt-1">
                                Satuan: <span class="text-slate-300 font-semibold" x-text="item.breakdown"></span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-lg font-black" :class="item.is_low ? 'text-rose-400' : 'text-emerald-400'" x-text="item.stok_pcs + ' PCS'"></span>
                            <span class="block text-[10px] text-slate-500" x-text="'Exp: ' + item.kadaluarsa"></span>
                        </div>
                    </div>
                </template>
                <div x-show="stockResults.length === 0" class="text-center py-8 text-slate-500 text-xs">
                    Ketik kata kunci untuk melihat stok fisik produk.
                </div>
            </div>

            <div class="pt-4 border-t border-slate-800 flex justify-end shrink-0">
                <button type="button" @click="isStockModalOpen = false" class="px-5 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-semibold">
                    Tutup (Esc)
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL 3: OTORISASI PIN SUPERVISOR (VOID ITEM - REQ-POS-07 / TC-04) -->
    <div 
        x-show="isVoidModalOpen" 
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-xs p-4"
        style="display: none;"
    >
        <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-sm p-6 shadow-2xl text-slate-100" @click.away="isVoidModalOpen = false">
            <div class="text-center mb-4">
                <div class="w-12 h-12 rounded-2xl bg-rose-950 text-rose-500 mx-auto flex items-center justify-center mb-2 border border-rose-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v2m0-2h2m-2 0H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-base font-bold text-white">Otorisasi Supervisor Diperlukan</h3>
                <p class="text-xs text-slate-400 mt-1">Pembatalan (Void) item belanja membutuhkan verifikasi 6-digit PIN Supervisor.</p>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase text-center mb-1.5">Masukkan PIN Supervisor (Demo: 123456)</label>
                    <input 
                        type="password" 
                        maxlength="6"
                        x-ref="pinInput"
                        x-model="supervisorPin" 
                        @keydown.enter="submitSupervisorPin()"
                        placeholder="••••••"
                        class="w-full text-center tracking-[0.5em] text-2xl py-3 px-4 bg-slate-950 border border-slate-700 rounded-xl text-white font-mono focus:outline-none focus:ring-2 focus:ring-rose-500"
                    >
                    <template x-if="voidError">
                        <span class="text-xs text-rose-400 font-bold block text-center mt-1.5" x-text="voidError"></span>
                    </template>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" @click="isVoidModalOpen = false; supervisorPin = ''; voidError = ''" class="flex-1 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs rounded-xl">
                        Batal
                    </button>
                    <button type="button" @click="submitSupervisorPin()" class="flex-1 py-2.5 bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-rose-600/30">
                        Verifikasi PIN
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL 4: SUKSES TRANSAKSI & CETAK STRUK (TC-06) -->
    <div 
        x-show="isSuccessModalOpen" 
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-xs p-4"
        style="display: none;"
    >
        <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-md p-6 shadow-2xl text-slate-100 text-center">
            <div class="w-14 h-14 rounded-2xl bg-emerald-950 text-emerald-400 mx-auto flex items-center justify-center mb-3 border border-emerald-800">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h3 class="text-xl font-bold text-white">Transaksi Berhasil!</h3>
            <p class="text-xs text-slate-400 mt-1 font-mono" x-text="'No Faktur: ' + (lastTrx.no_faktur || '')"></p>

            <div class="my-5 p-4 bg-slate-950 rounded-xl border border-slate-800 text-left space-y-2 text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-400">Total Belanja:</span>
                    <span class="font-bold text-white font-mono" x-text="'Rp ' + Number(lastTrx.total || 0).toLocaleString('id-ID')"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Nominal Bayar:</span>
                    <span class="font-bold text-white font-mono" x-text="'Rp ' + Number(lastTrx.bayar || 0).toLocaleString('id-ID')"></span>
                </div>
                <div class="flex justify-between border-t border-slate-800 pt-1 text-sm font-bold">
                    <span class="text-slate-300">Kembalian:</span>
                    <span class="text-teal-400 font-mono" x-text="'Rp ' + Number(lastTrx.kembalian || 0).toLocaleString('id-ID')"></span>
                </div>
            </div>

            <div class="space-y-2">
                <a 
                    :href="lastTrx.receipt_url" 
                    target="_blank"
                    class="w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm rounded-xl flex items-center justify-center gap-2 shadow-lg shadow-emerald-600/30"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Struk Thermal (ESC/POS)</span>
                </a>

                <button 
                    type="button" 
                    @click="isSuccessModalOpen = false; resetCartDirectly()"
                    class="w-full py-2.5 px-4 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs rounded-xl"
                >
                    Transaksi Baru (Enter)
                </button>
            </div>
        </div>
    </div>

</div>

<script>
function posApp() {
    return {
        currentTime: '',
        searchQuery: '',
        searchResults: [],
        activeCategory: 'all',
        allProductsData: {!! $allProductsJson !!},
        
        // Multi-Cart (Tab 0: Utama, Tab 1..5: Held Orders)
        currentTab: 0,
        carts: [[], [], [], [], [], []], // up to 6 slots
        heldSlots: [],

        get cart() {
            return this.carts[this.currentTab];
        },

        discount: 0,
        cashPaid: 0,
        paymentMethod: 'tunai',
        checkoutLoading: false,

        // Modals
        isCheckoutModalOpen: false,
        isStockModalOpen: false,
        stockSearchQuery: '',
        stockResults: [],
        isVoidModalOpen: false,
        voidActionTarget: null, // { type: 'item', index: 0 } or { type: 'reset' }
        supervisorPin: '',
        voidError: '',
        isSuccessModalOpen: false,
        lastTrx: {},

        init() {
            this.updateClock();
            setInterval(() => this.updateClock(), 1000);

            // Global Keyboard Shortcuts (F1 - F4)
            window.addEventListener('keydown', (e) => {
                if (e.key === 'F1') {
                    e.preventDefault();
                    if (this.cart.length > 0) this.openCheckoutModal();
                } else if (e.key === 'F2') {
                    e.preventDefault();
                    if (this.cart.length > 0) this.holdCurrentOrder();
                } else if (e.key === 'F3') {
                    e.preventDefault();
                    this.openStockModal();
                } else if (e.key === 'F4') {
                    e.preventDefault();
                    if (this.cart.length > 0) this.requestCartReset();
                } else if (e.key === 'Escape') {
                    this.isCheckoutModalOpen = false;
                    this.isStockModalOpen = false;
                    this.isVoidModalOpen = false;
                    this.isSuccessModalOpen = false;
                }
            });
        },

        updateClock() {
            const now = new Date();
            this.currentTime = now.toLocaleTimeString('id-ID', { hour12: false });
        },

        get filteredGridProducts() {
            if (this.activeCategory === 'all') {
                return this.allProductsData;
            }
            return this.allProductsData.filter(p => p.category_id == this.activeCategory);
        },

        // REQ-POS-01: Live Search (<300ms)
        async onSearchInput() {
            if (!this.searchQuery.trim()) {
                this.searchResults = [];
                return;
            }
            try {
                const res = await axios.get('{{ route("pos.search") }}', {
                    params: { q: this.searchQuery, category_id: this.activeCategory }
                });
                this.searchResults = res.data;
            } catch (err) {
                console.error('Search error', err);
            }
        },

        // Add to Cart with selected unit
        addToCart(product, unit) {
            const existing = this.cart.find(item => item.product_id === product.id && item.unit_id === unit.id);
            if (existing) {
                existing.qty++;
            } else {
                this.cart.push({
                    product_id: product.id,
                    sku: product.sku_singkat,
                    name: product.nama_produk,
                    unit_id: unit.id,
                    unit_name: unit.nama_satuan,
                    konversi: unit.jumlah_konversi,
                    harga: Number(unit.harga_jual),
                    qty: 1,
                    available_units: product.units,
                    base_stock: product.stok_pcs
                });
            }
        },

        onUnitChange(item) {
            const selectedUnit = item.available_units.find(u => u.id == item.unit_id);
            if (selectedUnit) {
                item.unit_name = selectedUnit.nama_satuan;
                item.konversi = selectedUnit.jumlah_konversi;
                item.harga = Number(selectedUnit.harga_jual);
            }
        },

        increaseQty(index) {
            this.cart[index].qty++;
        },

        decreaseQty(index) {
            if (this.cart[index].qty > 1) {
                this.cart[index].qty--;
            } else {
                this.requestVoidItem(index);
            }
        },

        validateItemQty(index) {
            if (!this.cart[index].qty || this.cart[index].qty < 1) {
                this.cart[index].qty = 1;
            }
        },

        // REQ-POS-07: Void Item & Reset Cart (PIN Supervisor)
        requestVoidItem(index) {
            this.voidActionTarget = { type: 'item', index: index };
            this.supervisorPin = '';
            this.voidError = '';
            this.isVoidModalOpen = true;
            this.$nextTick(() => this.$refs.pinInput?.focus());
        },

        requestCartReset() {
            this.voidActionTarget = { type: 'reset' };
            this.supervisorPin = '';
            this.voidError = '';
            this.isVoidModalOpen = true;
            this.$nextTick(() => this.$refs.pinInput?.focus());
        },

        async submitSupervisorPin() {
            if (!this.supervisorPin) {
                this.voidError = 'Masukkan 6 digit PIN!';
                return;
            }
            try {
                const res = await axios.post('{{ route("pos.verify-pin") }}', {
                    pin: this.supervisorPin
                });

                if (res.data.valid) {
                    if (this.voidActionTarget.type === 'item') {
                        this.cart.splice(this.voidActionTarget.index, 1);
                    } else if (this.voidActionTarget.type === 'reset') {
                        this.carts[this.currentTab] = [];
                    }
                    this.isVoidModalOpen = false;
                }
            } catch (err) {
                this.voidError = err.response?.data?.message || 'PIN Supervisor salah!';
            }
        },

        // REQ-POS-04: Hold & Resume Order (Maksimal 5)
        holdCurrentOrder() {
            if (this.cart.length === 0) return;
            if (this.heldSlots.length >= 5) {
                alert('Maksimal transaksi tertahan adalah 5 order!');
                return;
            }

            const newSlotIndex = this.heldSlots.length + 1;
            this.heldSlots.push([...this.cart]);
            this.carts[newSlotIndex] = [...this.cart];
            this.carts[0] = [];
            this.currentTab = 0;
            alert('Antrean transaksi berhasil di-HOLD sementara!');
        },

        switchCartTab(tabIndex) {
            this.currentTab = tabIndex;
        },

        deleteHoldSlot(index) {
            if (confirm('Batalkan order tertahan ini?')) {
                this.heldSlots.splice(index, 1);
                this.carts.splice(index + 1, 1);
                this.currentTab = 0;
            }
        },

        // REQ-POS-03: Cek Stok Instan (F3)
        openStockModal() {
            this.isStockModalOpen = true;
            this.stockSearchQuery = '';
            this.onStockSearchInput();
            this.$nextTick(() => this.$refs.stockSearchInput?.focus());
        },

        async onStockSearchInput() {
            try {
                const res = await axios.get('{{ route("pos.check-stock") }}', {
                    params: { q: this.stockSearchQuery }
                });
                this.stockResults = res.data;
            } catch (err) {
                console.error(err);
            }
        },

        // Calculations
        calculateSubtotal() {
            return this.cart.reduce((sum, item) => sum + (item.qty * item.harga), 0);
        },

        calculateTotalAkhir() {
            const sub = this.calculateSubtotal();
            const disc = Number(this.discount) || 0;
            return Math.max(0, sub - disc);
        },

        calculateKembalian() {
            const total = this.calculateTotalAkhir();
            const paid = Number(this.cashPaid) || 0;
            return paid - total;
        },

        // Checkout Modal & Processing
        openCheckoutModal() {
            this.isCheckoutModalOpen = true;
            this.cashPaid = this.calculateTotalAkhir();
            this.$nextTick(() => this.$refs.cashPaidInput?.focus());
        },

        async processCheckout() {
            this.checkoutLoading = true;
            try {
                const payload = {
                    items: this.cart.map(i => ({
                        product_id: i.product_id,
                        unit_id: i.unit_id,
                        qty: i.qty
                    })),
                    subtotal: this.calculateSubtotal(),
                    diskon: Number(this.discount) || 0,
                    total_akhir: this.calculateTotalAkhir(),
                    bayar: Number(this.cashPaid) || 0,
                    kembalian: this.calculateKembalian(),
                    metode_pembayaran: this.paymentMethod
                };

                const res = await axios.post('{{ route("pos.checkout") }}', payload);
                if (res.data.success) {
                    this.lastTrx = {
                        no_faktur: res.data.no_faktur,
                        total: payload.total_akhir,
                        bayar: payload.bayar,
                        kembalian: payload.kembalian,
                        receipt_url: res.data.receipt_url
                    };
                    this.isCheckoutModalOpen = false;
                    this.isSuccessModalOpen = true;

                    // Automatically popup/print receipt
                    window.open(res.data.receipt_url, '_blank', 'width=400,height=600');
                }
            } catch (err) {
                alert(err.response?.data?.message || 'Gagal memproses transaksi!');
            } finally {
                this.checkoutLoading = false;
            }
        },

        resetCartDirectly() {
            this.carts[this.currentTab] = [];
            this.discount = 0;
            this.cashPaid = 0;
            this.paymentMethod = 'tunai';
        }
    };
}
</script>
@endsection
