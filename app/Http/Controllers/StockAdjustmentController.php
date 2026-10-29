<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockAdjustment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StockAdjustmentController extends Controller
{
    public function index(): View
    {
        $adjustments = StockAdjustment::with(['product', 'user'])
            ->latest()
            ->paginate(15);

        return view('stock_adjustments.index', compact('adjustments'));
    }

    public function create(): View
    {
        $products = Product::orderBy('nama_produk')->get();

        return view('stock_adjustments.create', compact('products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'jenis' => ['required', 'in:masuk,keluar'],
            'qty_pcs' => ['required', 'integer', 'min:1'],
            'alasan' => ['required', 'in:rusak,kadaluarsa,hilang,selisih_fisik,lainnya'],
            'catatan' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($validated) {
            $product = Product::where('id', $validated['product_id'])->lockForUpdate()->firstOrFail();

            if ($validated['jenis'] === 'keluar') {
                if ($product->stok_pcs < $validated['qty_pcs']) {
                    throw new \Exception("Stok tidak mencukupi untuk pengurangan ini! Sisa stok saat ini: {$product->stok_pcs} PCS");
                }
                $product->decrement('stok_pcs', $validated['qty_pcs']);
            } else {
                $product->increment('stok_pcs', $validated['qty_pcs']);
            }

            StockAdjustment::create([
                'product_id' => $product->id,
                'user_id' => Auth::id(),
                'jenis' => $validated['jenis'],
                'qty_pcs' => $validated['qty_pcs'],
                'alasan' => $validated['alasan'],
                'catatan' => $validated['catatan'],
            ]);
        });

        return redirect()->route('stock-adjustments.index')->with('success', 'Penyesuaian stok berhasil disimpan.');
    }
}
