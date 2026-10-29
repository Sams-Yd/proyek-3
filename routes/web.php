<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Redirect Root
Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        if ($user->role === 'kasir') {
            return redirect()->route('pos.index');
        }

        return redirect()->route('dashboard');
    }

    return redirect()->route('login');
});

// Authentication
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Authenticated Routes
Route::middleware('auth')->group(function () {

    // Shift Kasir (Opening Cash & Blind Balancing)
    Route::prefix('shift')->name('shift.')->group(function () {
        Route::get('/open', [ShiftController::class, 'openForm'])->name('open');
        Route::post('/open', [ShiftController::class, 'storeOpen'])->name('open.store');
        Route::get('/close', [ShiftController::class, 'closeForm'])->name('close');
        Route::post('/close', [ShiftController::class, 'storeClose'])->name('close.store');
        Route::get('/summary/{shift}', [ShiftController::class, 'summary'])->name('summary');
    });

    // POS Front-End (Kasir & Admin) with active shift guard
    Route::middleware(['role:kasir,admin', 'cashier.shift'])->prefix('pos')->name('pos.')->group(function () {
        Route::get('/', [PosController::class, 'index'])->name('index');
        Route::get('/search', [PosController::class, 'search'])->name('search');
        Route::get('/check-stock', [PosController::class, 'checkStock'])->name('check-stock');
        Route::post('/verify-pin', [PosController::class, 'verifySupervisorPin'])->name('verify-pin');
        Route::post('/checkout', [PosController::class, 'checkout'])->name('checkout');
        Route::get('/receipt/{transaction}', [PosController::class, 'receipt'])->name('receipt');
    });

    // Back-office Management (Admin & Supervisor)
    Route::middleware('role:admin,supervisor')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Master Data Produk & Satuan
        Route::resource('products', ProductController::class);
        Route::resource('categories', CategoryController::class)->except(['create', 'show', 'edit']);
        Route::resource('suppliers', SupplierController::class)->except(['create', 'show', 'edit']);

        // Inventori: Stok Opname & Barang Masuk
        Route::resource('stock-adjustments', StockAdjustmentController::class)->only(['index', 'create', 'store']);
        Route::resource('purchases', PurchaseController::class)->only(['index', 'create', 'store', 'show']);

        // Rekapitulasi Shift Kasir
        Route::get('/shifts', [ShiftController::class, 'index'])->name('shifts.index');
        Route::get('/shifts/{shift}', [ShiftController::class, 'show'])->name('shifts.show');
    });

    // Laporan Keuangan & Bisnis (Admin / Owner)
    Route::middleware('role:admin')->prefix('reports')->name('reports.')->group(function () {
        Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
        Route::get('/profit', [ReportController::class, 'profit'])->name('profit');
        Route::get('/top-products', [ReportController::class, 'topProducts'])->name('top-products');
        Route::get('/shifts', [ReportController::class, 'shifts'])->name('shifts');
    });

});
