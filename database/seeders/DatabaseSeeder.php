<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\Supplier;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Users & RBAC
        $admin = User::updateOrCreate(
            ['email' => 'admin@minimarket.com'],
            [
                'name' => 'Owner / Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'pin_supervisor' => '123456',
            ]
        );

        $spv = User::updateOrCreate(
            ['email' => 'spv@minimarket.com'],
            [
                'name' => 'Supervisor Toko',
                'password' => Hash::make('password'),
                'role' => 'supervisor',
                'pin_supervisor' => '123456',
            ]
        );

        $kasir = User::updateOrCreate(
            ['email' => 'kasir@minimarket.com'],
            [
                'name' => 'Mochammad Raihan (Kasir)',
                'password' => Hash::make('password'),
                'role' => 'kasir',
                'pin_supervisor' => null,
            ]
        );

        // 2. Suppliers
        $sup1 = Supplier::create([
            'nama_supplier' => 'PT Indofood Sukses Makmur Tbk',
            'no_telepon' => '021-57958822',
            'alamat' => 'Sudirman Plaza, Indofood Tower, Jakarta Selatan',
        ]);

        $sup2 = Supplier::create([
            'nama_supplier' => 'PT Mayora Indah Tbk',
            'no_telepon' => '021-80637700',
            'alamat' => 'Gedung Mayora, Daan Mogot, Jakarta Barat',
        ]);

        $sup3 = Supplier::create([
            'nama_supplier' => 'PT Unilever Indonesia Tbk',
            'no_telepon' => '021-80827000',
            'alamat' => 'BSD City, Tangerang Selatan',
        ]);

        // 3. Categories
        $catMie = Category::create(['nama_kategori' => 'Makanan Instan']);
        $catMinuman = Category::create(['nama_kategori' => 'Minuman Dingin & Segar']);
        $catSembako = Category::create(['nama_kategori' => 'Sembako']);
        $catSnack = Category::create(['nama_kategori' => 'Snack & Biskuit']);
        $catToiletries = Category::create(['nama_kategori' => 'Perawatan & Kebersihan']);

        // 4. Products & Units
        // Produk 1: Indomie Goreng
        $p1 = Product::create([
            'category_id' => $catMie->id,
            'sku_singkat' => 'IND-GNG',
            'nama_produk' => 'Indomie Mi Goreng Spesial 85g',
            'stok_pcs' => 120,
            'stok_minimum' => 20,
            'harga_beli' => 2800,
            'tgl_kadaluarsa' => Carbon::now()->addMonths(8),
        ]);
        ProductUnit::create(['product_id' => $p1->id, 'nama_satuan' => 'PCS', 'jumlah_konversi' => 1, 'harga_jual' => 3500]);
        ProductUnit::create(['product_id' => $p1->id, 'nama_satuan' => 'PACK', 'jumlah_konversi' => 5, 'harga_jual' => 17000]);
        ProductUnit::create(['product_id' => $p1->id, 'nama_satuan' => 'DUS', 'jumlah_konversi' => 40, 'harga_jual' => 132000]);

        // Produk 2: Indomie Ayam Bawang
        $p2 = Product::create([
            'category_id' => $catMie->id,
            'sku_singkat' => 'IND-AYM',
            'nama_produk' => 'Indomie Kuah Rasa Ayam Bawang 69g',
            'stok_pcs' => 80,
            'stok_minimum' => 20,
            'harga_beli' => 2700,
            'tgl_kadaluarsa' => Carbon::now()->addMonths(7),
        ]);
        ProductUnit::create(['product_id' => $p2->id, 'nama_satuan' => 'PCS', 'jumlah_konversi' => 1, 'harga_jual' => 3300]);
        ProductUnit::create(['product_id' => $p2->id, 'nama_satuan' => 'DUS', 'jumlah_konversi' => 40, 'harga_jual' => 125000]);

        // Produk 3: Le Minerale 600ml
        $p3 = Product::create([
            'category_id' => $catMinuman->id,
            'sku_singkat' => 'LMN-600',
            'nama_produk' => 'Le Minerale Air Mineral 600ml',
            'stok_pcs' => 96,
            'stok_minimum' => 24,
            'harga_beli' => 2500,
            'tgl_kadaluarsa' => Carbon::now()->addMonths(18),
        ]);
        ProductUnit::create(['product_id' => $p3->id, 'nama_satuan' => 'PCS', 'jumlah_konversi' => 1, 'harga_jual' => 3500]);
        ProductUnit::create(['product_id' => $p3->id, 'nama_satuan' => 'DUS', 'jumlah_konversi' => 24, 'harga_jual' => 75000]);

        // Produk 4: Teh Botol Sosro Kotak 330ml
        $p4 = Product::create([
            'category_id' => $catMinuman->id,
            'sku_singkat' => 'TB-330',
            'nama_produk' => 'Teh Botol Sosro Kotak 330ml',
            'stok_pcs' => 48,
            'stok_minimum' => 12,
            'harga_beli' => 3200,
            'tgl_kadaluarsa' => Carbon::now()->addMonths(10),
        ]);
        ProductUnit::create(['product_id' => $p4->id, 'nama_satuan' => 'PCS', 'jumlah_konversi' => 1, 'harga_jual' => 4500]);
        ProductUnit::create(['product_id' => $p4->id, 'nama_satuan' => 'DUS', 'jumlah_konversi' => 24, 'harga_jual' => 98000]);

        // Produk 5: Ultra Milk Cokelat 250ml
        $p5 = Product::create([
            'category_id' => $catMinuman->id,
            'sku_singkat' => 'ULM-250',
            'nama_produk' => 'Ultra Milk UHT Cokelat 250ml',
            'stok_pcs' => 48,
            'stok_minimum' => 12,
            'harga_beli' => 5500,
            'tgl_kadaluarsa' => Carbon::now()->addDays(20), // Near Expiry (H-20!)
        ]);
        ProductUnit::create(['product_id' => $p5->id, 'nama_satuan' => 'PCS', 'jumlah_konversi' => 1, 'harga_jual' => 7000]);
        ProductUnit::create(['product_id' => $p5->id, 'nama_satuan' => 'DUS', 'jumlah_konversi' => 24, 'harga_jual' => 155000]);

        // Produk 6: Bimoli 2L
        $p6 = Product::create([
            'category_id' => $catSembako->id,
            'sku_singkat' => 'BML-2L',
            'nama_produk' => 'Bimoli Minyak Goreng Pouch 2 Liter',
            'stok_pcs' => 30,
            'stok_minimum' => 10,
            'harga_beli' => 33000,
            'tgl_kadaluarsa' => Carbon::now()->addMonths(12),
        ]);
        ProductUnit::create(['product_id' => $p6->id, 'nama_satuan' => 'PCS', 'jumlah_konversi' => 1, 'harga_jual' => 38500]);
        ProductUnit::create(['product_id' => $p6->id, 'nama_satuan' => 'DUS', 'jumlah_konversi' => 6, 'harga_jual' => 225000]);

        // Produk 7: Gulaku Gula Pasir 1kg
        $p7 = Product::create([
            'category_id' => $catSembako->id,
            'sku_singkat' => 'GUL-1KG',
            'nama_produk' => 'Gulaku Gula Pasir Putih Premium 1kg',
            'stok_pcs' => 25,
            'stok_minimum' => 10,
            'harga_beli' => 15000,
            'tgl_kadaluarsa' => Carbon::now()->addMonths(24),
        ]);
        ProductUnit::create(['product_id' => $p7->id, 'nama_satuan' => 'PCS', 'jumlah_konversi' => 1, 'harga_jual' => 18000]);

        // Produk 8: Oreo Vanilla 133g
        $p8 = Product::create([
            'category_id' => $catSnack->id,
            'sku_singkat' => 'OR-133',
            'nama_produk' => 'Oreo Sandwich Cookies Vanilla 133g',
            'stok_pcs' => 36,
            'stok_minimum' => 12,
            'harga_beli' => 7500,
            'tgl_kadaluarsa' => Carbon::now()->addMonths(9),
        ]);
        ProductUnit::create(['product_id' => $p8->id, 'nama_satuan' => 'PCS', 'jumlah_konversi' => 1, 'harga_jual' => 9500]);
        ProductUnit::create(['product_id' => $p8->id, 'nama_satuan' => 'DUS', 'jumlah_konversi' => 24, 'harga_jual' => 215000]);

        // Produk 9: Lifebuoy Sabun Cair 400ml
        $p9 = Product::create([
            'category_id' => $catToiletries->id,
            'sku_singkat' => 'LFB-400',
            'nama_produk' => 'Lifebuoy Sabun Cair Total 10 Refill 400ml',
            'stok_pcs' => 20,
            'stok_minimum' => 10,
            'harga_beli' => 19000,
            'tgl_kadaluarsa' => Carbon::now()->addMonths(24),
        ]);
        ProductUnit::create(['product_id' => $p9->id, 'nama_satuan' => 'PCS', 'jumlah_konversi' => 1, 'harga_jual' => 24000]);

        // Produk 10: Rinso Bubuk 800g (Stok Menipis / Low Stock Test)
        $p10 = Product::create([
            'category_id' => $catToiletries->id,
            'sku_singkat' => 'RNS-800',
            'nama_produk' => 'Rinso Anti Noda Deterjen Bubuk 800g',
            'stok_pcs' => 4, // Dibawah batas stok_minimum (10)
            'stok_minimum' => 10,
            'harga_beli' => 22000,
            'tgl_kadaluarsa' => Carbon::now()->addMonths(15),
        ]);
        ProductUnit::create(['product_id' => $p10->id, 'nama_satuan' => 'PCS', 'jumlah_konversi' => 1, 'harga_jual' => 27500]);
    }
}
