<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PurchaseController extends Controller
{
    public function index(): View
    {
        $purchases = Purchase::with(['supplier', 'user', 'details.product'])
            ->latest()
            ->paginate(15);

        return view('purchases.index', compact('purchases'));
    }

    public function create(): View
    {
        $suppliers = Supplier::orderBy('nama_supplier')->get();
        $products = Product::with('units')->orderBy('nama_produk')->get();
        $productsJson = json_encode($products->map(function ($p) {
            return [
                'id' => $p->id,
                'sku' => $p->sku_singkat,
                'nama' => $p->nama_produk,
                'units' => $p->units->map(function ($u) {
                    return [
                        'id' => $u->id,
                        'nama_satuan' => $u->nama_satuan,
                        'konversi' => $u->jumlah_konversi,
                    ];
                })->values(),
            ];
        }));

        return view('purchases.create', compact('suppliers', 'products', 'productsJson'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'tgl_masuk' => ['required', 'date'],
            'catatan' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.unit_id' => ['required', 'exists:product_units,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'items.*.harga_beli_satuan' => ['required', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($validated) {
            $poNumber = 'PO-'.date('YmdHis').'-'.rand(100, 999);
            $totalPurchase = 0;

            $purchase = Purchase::create([
                'no_po' => $poNumber,
                'supplier_id' => $validated['supplier_id'],
                'user_id' => Auth::id(),
                'total' => 0,
                'tgl_masuk' => $validated['tgl_masuk'],
                'catatan' => $validated['catatan'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                $subtotal = $item['qty'] * $item['harga_beli_satuan'];
                $totalPurchase += $subtotal;

                $unit = ProductUnit::findOrFail($item['unit_id']);
                $product = Product::where('id', $item['product_id'])->lockForUpdate()->firstOrFail();

                // Hitung total PCS yang masuk
                $totalPcsIn = $item['qty'] * $unit->jumlah_konversi;
                $product->increment('stok_pcs', $totalPcsIn);

                // Perbarui HPP (Harga Beli per PCS dasar)
                $newHppPcs = $item['harga_beli_satuan'] / $unit->jumlah_konversi;
                $product->update(['harga_beli' => $newHppPcs]);

                PurchaseDetail::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $product->id,
                    'unit_id' => $unit->id,
                    'qty' => $item['qty'],
                    'harga_beli_satuan' => $item['harga_beli_satuan'],
                    'subtotal' => $subtotal,
                ]);
            }

            $purchase->update(['total' => $totalPurchase]);
        });

        return redirect()->route('purchases.index')->with('success', 'Penerimaan barang dan penambahan stok berhasil dicatat.');
    }

    public function show(Purchase $purchase): View
    {
        $purchase->load(['supplier', 'user', 'details.product', 'details.unit']);

        return view('purchases.show', compact('purchase'));
    }
}
