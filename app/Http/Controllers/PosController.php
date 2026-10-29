<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PosController extends Controller
{
    public function index(): View
    {
        $categories = Category::with(['products' => function ($query) {
            $query->with('units')->where('stok_pcs', '>', 0);
        }])->orderBy('nama_kategori')->get();

        $allProducts = Product::with(['category', 'units'])
            ->where('stok_pcs', '>', 0)
            ->orderBy('nama_produk')
            ->take(30)
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'sku_singkat' => $p->sku_singkat,
                    'nama_produk' => $p->nama_produk,
                    'category_id' => $p->category_id,
                    'stok_pcs' => $p->stok_pcs,
                    'stok_minimum' => $p->stok_minimum,
                    'units' => $p->units->map(function ($u) {
                        return [
                            'id' => $u->id,
                            'nama_satuan' => $u->nama_satuan,
                            'jumlah_konversi' => $u->jumlah_konversi,
                            'harga_jual' => (float) $u->harga_jual,
                        ];
                    })->values(),
                ];
            });

        $allProductsJson = json_encode($allProducts);
        $activeShift = Auth::user()->activeShift();

        return view('pos.index', compact('categories', 'allProducts', 'allProductsJson', 'activeShift'));
    }

    /**
     * REQ-POS-01: Live Search (<300ms response)
     */
    public function search(Request $request): JsonResponse
    {
        $q = trim($request->input('q', ''));
        $categoryId = $request->input('category_id');

        $query = Product::with(['category', 'units']);

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('nama_produk', 'like', "%{$q}%")
                    ->orWhere('sku_singkat', 'like', "%{$q}%");
            });
        }

        if ($categoryId && $categoryId !== 'all') {
            $query->where('category_id', $categoryId);
        }

        $products = $query->take(20)->get()->map(function ($p) {
            return [
                'id' => $p->id,
                'sku_singkat' => $p->sku_singkat,
                'nama_produk' => $p->nama_produk,
                'kategori' => $p->category->nama_kategori ?? '-',
                'stok_pcs' => $p->stok_pcs,
                'units' => $p->units->map(function ($u) {
                    return [
                        'id' => $u->id,
                        'nama_satuan' => $u->nama_satuan,
                        'jumlah_konversi' => $u->jumlah_konversi,
                        'harga_jual' => (float) $u->harga_jual,
                    ];
                }),
            ];
        });

        return response()->json($products);
    }

    /**
     * REQ-POS-03: Cek Stok Instan (F3 Modal)
     */
    public function checkStock(Request $request): JsonResponse
    {
        $q = trim($request->input('q', ''));

        $query = Product::with(['category', 'units']);

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('nama_produk', 'like', "%{$q}%")
                    ->orWhere('sku_singkat', 'like', "%{$q}%");
            });
        }

        $results = $query->take(25)->get()->map(function ($p) {
            // Breakdown stok ke dalam satuan yang tersedia
            $unitsBreakdown = [];
            foreach ($p->units as $unit) {
                $kapasitas = $unit->jumlah_konversi > 0 ? floor($p->stok_pcs / $unit->jumlah_konversi) : 0;
                $unitsBreakdown[] = "{$kapasitas} {$unit->nama_satuan}";
            }

            return [
                'id' => $p->id,
                'sku' => $p->sku_singkat,
                'nama' => $p->nama_produk,
                'kategori' => $p->category->nama_kategori ?? '-',
                'stok_pcs' => $p->stok_pcs,
                'stok_minimum' => $p->stok_minimum,
                'is_low' => $p->stok_pcs <= $p->stok_minimum,
                'breakdown' => implode(' | ', $unitsBreakdown),
                'kadaluarsa' => $p->tgl_kadaluarsa ? $p->tgl_kadaluarsa->format('d/m/Y') : '-',
            ];
        });

        return response()->json($results);
    }

    /**
     * REQ-POS-07: Verifikasi PIN Supervisor untuk Void Item
     */
    public function verifySupervisorPin(Request $request): JsonResponse
    {
        $request->validate([
            'pin' => ['required', 'string'],
        ]);

        $pin = trim($request->input('pin'));

        $supervisor = User::whereIn('role', ['supervisor', 'admin'])
            ->where('pin_supervisor', $pin)
            ->first();

        if ($supervisor) {
            return response()->json([
                'valid' => true,
                'supervisor_name' => $supervisor->name,
                'message' => 'Otorisasi supervisor berhasil disetujui.',
            ]);
        }

        return response()->json([
            'valid' => false,
            'message' => 'PIN Supervisor salah atau tidak terdaftar!',
        ], 422);
    }

    /**
     * REQ-POS-05: Checkout Transaksi dengan Database Transaction & Multi-Satuan
     */
    public function checkout(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.unit_id' => ['required', 'exists:product_units,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'subtotal' => ['required', 'numeric', 'min:0'],
            'diskon' => ['nullable', 'numeric', 'min:0'],
            'total_akhir' => ['required', 'numeric', 'min:0'],
            'bayar' => ['required', 'numeric', 'min:0'],
            'kembalian' => ['required', 'numeric'],
            'metode_pembayaran' => ['required', 'in:tunai,qris,debit,e-wallet'],
        ]);

        $user = Auth::user();
        $shift = $user->activeShift();

        if (! $shift) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada shift kasir yang aktif. Harap buka shift terlebih dahulu.',
            ], 422);
        }

        if ($validated['metode_pembayaran'] === 'tunai' && $validated['bayar'] < $validated['total_akhir']) {
            return response()->json([
                'success' => false,
                'message' => 'Nominal bayar tunai kurang dari total belanja!',
            ], 422);
        }

        try {
            $transaction = DB::transaction(function () use ($validated, $user, $shift) {
                // Buat nomor faktur unik: TRX-YYYYMMDD-HHIISS-RAND
                $noFaktur = 'TRX-'.date('YmdHis').'-'.rand(100, 999);

                $trx = Transaction::create([
                    'no_faktur' => $noFaktur,
                    'user_id' => $user->id,
                    'shift_id' => $shift->id,
                    'subtotal' => $validated['subtotal'],
                    'diskon' => $validated['diskon'] ?? 0,
                    'total_akhir' => $validated['total_akhir'],
                    'bayar' => $validated['bayar'],
                    'kembalian' => $validated['kembalian'],
                    'metode_pembayaran' => $validated['metode_pembayaran'],
                    'waktu_transaksi' => now(),
                ]);

                foreach ($validated['items'] as $item) {
                    $unit = ProductUnit::findOrFail($item['unit_id']);
                    $product = Product::where('id', $item['product_id'])->lockForUpdate()->firstOrFail();

                    // REQ-INV-01: Pemotongan stok otomatis pada unit terkecil (PCS) berdasarkan rasio konversi
                    $totalPcsDipotong = $item['qty'] * $unit->jumlah_konversi;

                    if ($product->stok_pcs < $totalPcsDipotong) {
                        throw new \Exception("Stok {$product->nama_produk} tidak mencukupi! Tersedia {$product->stok_pcs} PCS, dibutuhkan {$totalPcsDipotong} PCS.");
                    }

                    $product->decrement('stok_pcs', $totalPcsDipotong);

                    TransactionDetail::create([
                        'transaction_id' => $trx->id,
                        'product_id' => $product->id,
                        'unit_id' => $unit->id,
                        'qty' => $item['qty'],
                        'harga_satuan' => $unit->harga_jual,
                        'subtotal_item' => $item['qty'] * $unit->harga_jual,
                    ]);
                }

                return $trx;
            });

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil disimpan!',
                'transaction_id' => $transaction->id,
                'no_faktur' => $transaction->no_faktur,
                'receipt_url' => route('pos.receipt', $transaction->id),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * REQ-POS-06: Tampilan Cetak Struk Format Thermal 58mm / 80mm
     */
    public function receipt(Transaction $transaction): View
    {
        $transaction->load(['user', 'shift', 'details.product', 'details.unit']);

        return view('pos.receipt', compact('transaction'));
    }
}
