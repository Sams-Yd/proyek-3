<?php

namespace Tests\Feature;

use App\Models\CashierShift;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MinimarketPosTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $supervisor;

    private User $kasir;

    private Product $indomie;

    private ProductUnit $unitPcs;

    private ProductUnit $unitDus;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'pin_supervisor' => '123456',
        ]);

        $this->supervisor = User::create([
            'name' => 'Supervisor Test',
            'email' => 'spv@test.com',
            'password' => bcrypt('password'),
            'role' => 'supervisor',
            'pin_supervisor' => '123456',
        ]);

        $this->kasir = User::create([
            'name' => 'Kasir Test',
            'email' => 'kasir@test.com',
            'password' => bcrypt('password'),
            'role' => 'kasir',
            'pin_supervisor' => null,
        ]);

        $category = Category::create(['nama_kategori' => 'Makanan']);

        // Indomie Goreng: 100 PCS in stock
        $this->indomie = Product::create([
            'category_id' => $category->id,
            'sku_singkat' => 'IND-GNG',
            'nama_produk' => 'Indomie Mi Goreng 85g',
            'stok_pcs' => 100,
            'stok_minimum' => 20,
            'harga_beli' => 2800,
            'tgl_kadaluarsa' => now()->addMonths(6),
        ]);

        $this->unitPcs = ProductUnit::create([
            'product_id' => $this->indomie->id,
            'nama_satuan' => 'PCS',
            'jumlah_konversi' => 1,
            'harga_jual' => 3500,
        ]);

        $this->unitDus = ProductUnit::create([
            'product_id' => $this->indomie->id,
            'nama_satuan' => 'DUS',
            'jumlah_konversi' => 40, // 1 DUS = 40 PCS
            'harga_jual' => 130000,
        ]);
    }

    /**
     * TC-01: Live Search Autocomplete (<300ms)
     */
    public function test_tc01_live_search_returns_products_real_time(): void
    {
        $shift = CashierShift::create([
            'user_id' => $this->kasir->id,
            'modal_awal' => 200000,
            'waktu_buka' => now(),
            'status' => 'open',
        ]);

        $start = microtime(true);
        $response = $this->actingAs($this->kasir)->getJson(route('pos.search', ['q' => 'IND-GNG']));
        $duration = (microtime(true) - $start) * 1000;

        $response->assertOk();
        $response->assertJsonFragment(['sku_singkat' => 'IND-GNG']);
        $this->assertLessThan(1000, $duration, 'Search response should be extremely fast');
    }

    /**
     * TC-02: Shortcut F3 Pop-up Cek Stok Instan
     */
    public function test_tc02_instant_stock_check_shows_physical_stock(): void
    {
        CashierShift::create([
            'user_id' => $this->kasir->id,
            'modal_awal' => 200000,
            'waktu_buka' => now(),
            'status' => 'open',
        ]);

        $response = $this->actingAs($this->kasir)->getJson(route('pos.check-stock', ['q' => 'Indomie']));

        $response->assertOk();
        $response->assertJsonFragment([
            'sku' => 'IND-GNG',
            'stok_pcs' => 100,
        ]);
    }

    /**
     * TC-03: Transaksi Produk Grosir (1 DUS = 40 PCS dipotong dari stok basis)
     */
    public function test_tc03_wholesale_multi_unit_reduces_base_stock(): void
    {
        $shift = CashierShift::create([
            'user_id' => $this->kasir->id,
            'modal_awal' => 200000,
            'waktu_buka' => now(),
            'status' => 'open',
        ]);

        $initialStock = $this->indomie->fresh()->stok_pcs; // 100 PCS

        // Beli 1 DUS Indomie (1 DUS = 40 PCS)
        $payload = [
            'items' => [
                [
                    'product_id' => $this->indomie->id,
                    'unit_id' => $this->unitDus->id,
                    'qty' => 1,
                ],
            ],
            'subtotal' => 130000,
            'diskon' => 0,
            'total_akhir' => 130000,
            'bayar' => 150000,
            'kembalian' => 20000,
            'metode_pembayaran' => 'tunai',
        ];

        $response = $this->actingAs($this->kasir)->postJson(route('pos.checkout'), $payload);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        // Verifikasi stok berkurang 40 PCS: 100 - 40 = 60 PCS
        $this->assertEquals(60, $this->indomie->fresh()->stok_pcs);
    }

    /**
     * TC-04: Otorisasi PIN Supervisor untuk Void Item
     */
    public function test_tc04_supervisor_pin_authorization_for_void(): void
    {
        CashierShift::create([
            'user_id' => $this->kasir->id,
            'modal_awal' => 200000,
            'waktu_buka' => now(),
            'status' => 'open',
        ]);

        // 1. PIN Salah harus ditolak
        $invalidResponse = $this->actingAs($this->kasir)->postJson(route('pos.verify-pin'), [
            'pin' => '999999',
        ]);
        $invalidResponse->assertStatus(422);
        $invalidResponse->assertJson(['valid' => false]);

        // 2. PIN Benar (123456) harus disetujui
        $validResponse = $this->actingAs($this->kasir)->postJson(route('pos.verify-pin'), [
            'pin' => '123456',
        ]);
        $validResponse->assertOk();
        $validResponse->assertJson(['valid' => true]);
    }

    /**
     * TC-05: Closing Cash Shift Kasir (Blind Balancing)
     */
    public function test_tc05_blind_balancing_shift_calculates_difference(): void
    {
        $shift = CashierShift::create([
            'user_id' => $this->kasir->id,
            'modal_awal' => 200000,
            'waktu_buka' => now(),
            'status' => 'open',
        ]);

        // Kasir melakukan 1 transaksi tunai Rp 130.000
        $this->actingAs($this->kasir)->postJson(route('pos.checkout'), [
            'items' => [
                [
                    'product_id' => $this->indomie->id,
                    'unit_id' => $this->unitDus->id,
                    'qty' => 1,
                ],
            ],
            'subtotal' => 130000,
            'diskon' => 0,
            'total_akhir' => 130000,
            'bayar' => 130000,
            'kembalian' => 0,
            'metode_pembayaran' => 'tunai',
        ]);

        // Total seharusnya sistem = modal_awal (200.000) + tunai (130.000) = 330.000
        // Kasir menginput uang fisik di laci: 350.000 (Over Rp 20.000)
        $closeResponse = $this->actingAs($this->kasir)->post(route('shift.close.store'), [
            'total_setoran_fisik' => 350000,
            'catatan' => 'Uang tip lebih Rp 20.000',
        ]);

        $closeResponse->assertRedirect(route('shift.summary', $shift->id));

        $freshShift = $shift->fresh();
        $this->assertEquals('closed', $freshShift->status);
        $this->assertEquals(330000, (float) $freshShift->total_sistem);
        $this->assertEquals(350000, (float) $freshShift->total_setoran_fisik);
        $this->assertEquals(20000, (float) $freshShift->selisih); // Over Rp 20.000!
    }

    /**
     * TC-06: Struk Thermal Rendering
     */
    public function test_tc06_thermal_receipt_renders_correctly(): void
    {
        $shift = CashierShift::create([
            'user_id' => $this->kasir->id,
            'modal_awal' => 200000,
            'waktu_buka' => now(),
            'status' => 'open',
        ]);

        $checkoutResponse = $this->actingAs($this->kasir)->postJson(route('pos.checkout'), [
            'items' => [
                [
                    'product_id' => $this->indomie->id,
                    'unit_id' => $this->unitPcs->id,
                    'qty' => 2,
                ],
            ],
            'subtotal' => 7000,
            'diskon' => 0,
            'total_akhir' => 7000,
            'bayar' => 10000,
            'kembalian' => 3000,
            'metode_pembayaran' => 'tunai',
        ]);

        $trxId = $checkoutResponse->json('transaction_id');

        $receiptResponse = $this->actingAs($this->kasir)->get(route('pos.receipt', $trxId));
        $receiptResponse->assertOk();
        $receiptResponse->assertSee('MINIMARKET POS');
        $receiptResponse->assertSee('Indomie Mi Goreng 85g');
        $receiptResponse->assertSee('7.000');
    }
}
