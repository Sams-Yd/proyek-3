<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductUnit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with(['category', 'units']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama_produk', 'like', "%{$search}%")
                    ->orWhere('sku_singkat', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->input('filter') === 'low_stock') {
            $query->lowStock();
        } elseif ($request->input('filter') === 'expiring') {
            $query->expiringSoon(30);
        }

        $products = $query->orderBy('nama_produk', 'asc')->paginate(15)->withQueryString();
        $categories = Category::orderBy('nama_kategori')->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('nama_kategori')->get();

        return view('products.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_produk' => ['required', 'string', 'max:150'],
            'sku_singkat' => ['required', 'string', 'max:50', 'unique:products,sku_singkat'],
            'category_id' => ['required', 'exists:categories,id'],
            'stok_pcs' => ['required', 'integer', 'min:0'],
            'stok_minimum' => ['required', 'integer', 'min:1'],
            'harga_beli' => ['required', 'numeric', 'min:0'],
            'tgl_kadaluarsa' => ['nullable', 'date'],
            'units' => ['required', 'array', 'min:1'],
            'units.*.nama_satuan' => ['required', 'string', 'max:20'],
            'units.*.jumlah_konversi' => ['required', 'integer', 'min:1'],
            'units.*.harga_jual' => ['required', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($validated) {
            $product = Product::create([
                'category_id' => $validated['category_id'],
                'sku_singkat' => strtoupper(trim($validated['sku_singkat'])),
                'nama_produk' => $validated['nama_produk'],
                'stok_pcs' => $validated['stok_pcs'],
                'stok_minimum' => $validated['stok_minimum'],
                'harga_beli' => $validated['harga_beli'],
                'tgl_kadaluarsa' => $validated['tgl_kadaluarsa'],
            ]);

            foreach ($validated['units'] as $unitData) {
                ProductUnit::create([
                    'product_id' => $product->id,
                    'nama_satuan' => strtoupper(trim($unitData['nama_satuan'])),
                    'jumlah_konversi' => $unitData['jumlah_konversi'],
                    'harga_jual' => $unitData['harga_jual'],
                ]);
            }
        });

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product): View
    {
        $product->load(['units', 'category']);
        $categories = Category::orderBy('nama_kategori')->get();
        $unitsJson = json_encode($product->units->map(fn ($u) => [
            'nama_satuan' => $u->nama_satuan,
            'jumlah_konversi' => $u->jumlah_konversi,
            'harga_jual' => (float) $u->harga_jual,
        ])->values());

        return view('products.edit', compact('product', 'categories', 'unitsJson'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'nama_produk' => ['required', 'string', 'max:150'],
            'sku_singkat' => ['required', 'string', 'max:50', 'unique:products,sku_singkat,'.$product->id],
            'category_id' => ['required', 'exists:categories,id'],
            'stok_pcs' => ['required', 'integer', 'min:0'],
            'stok_minimum' => ['required', 'integer', 'min:1'],
            'harga_beli' => ['required', 'numeric', 'min:0'],
            'tgl_kadaluarsa' => ['nullable', 'date'],
            'units' => ['required', 'array', 'min:1'],
            'units.*.nama_satuan' => ['required', 'string', 'max:20'],
            'units.*.jumlah_konversi' => ['required', 'integer', 'min:1'],
            'units.*.harga_jual' => ['required', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($validated, $product) {
            $product->update([
                'category_id' => $validated['category_id'],
                'sku_singkat' => strtoupper(trim($validated['sku_singkat'])),
                'nama_produk' => $validated['nama_produk'],
                'stok_pcs' => $validated['stok_pcs'],
                'stok_minimum' => $validated['stok_minimum'],
                'harga_beli' => $validated['harga_beli'],
                'tgl_kadaluarsa' => $validated['tgl_kadaluarsa'],
            ]);

            // Re-create units
            $product->units()->delete();
            foreach ($validated['units'] as $unitData) {
                ProductUnit::create([
                    'product_id' => $product->id,
                    'nama_satuan' => strtoupper(trim($unitData['nama_satuan'])),
                    'jumlah_konversi' => $unitData['jumlah_konversi'],
                    'harga_jual' => $unitData['harga_jual'],
                ]);
            }
        });

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
    }
}
