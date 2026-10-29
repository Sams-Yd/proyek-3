<?php

namespace App\Http\Controllers;

use App\Models\CashierShift;
use App\Models\Product;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        // Hari ini
        $todayTransactions = Transaction::with(['details.product', 'details.unit'])
            ->whereDate('waktu_transaksi', $today)
            ->get();

        $omzetToday = (float) $todayTransactions->sum('total_akhir');
        $trxCountToday = $todayTransactions->count();

        $grossProfitToday = 0;
        foreach ($todayTransactions as $trx) {
            $grossProfitToday += $trx->calculateGrossProfit();
        }

        // Bulan ini
        $monthTransactions = Transaction::with(['details.product', 'details.unit'])
            ->where('waktu_transaksi', '>=', $thisMonth)
            ->get();

        $omzetMonth = (float) $monthTransactions->sum('total_akhir');
        $grossProfitMonth = 0;
        foreach ($monthTransactions as $trx) {
            $grossProfitMonth += $trx->calculateGrossProfit();
        }

        // Alerts
        $lowStockProducts = Product::with('category', 'units')
            ->lowStock()
            ->orderBy('stok_pcs', 'asc')
            ->get();

        $expiringProducts = Product::with('category', 'units')
            ->expiringSoon(30)
            ->orderBy('tgl_kadaluarsa', 'asc')
            ->get();

        // Active Shifts
        $activeShifts = CashierShift::with('user')
            ->where('status', 'open')
            ->get();

        // Recent 5 Transactions
        $recentTransactions = Transaction::with(['user', 'details'])
            ->latest('waktu_transaksi')
            ->take(5)
            ->get();

        return view('dashboard.index', compact(
            'omzetToday',
            'trxCountToday',
            'grossProfitToday',
            'omzetMonth',
            'grossProfitMonth',
            'lowStockProducts',
            'expiringProducts',
            'activeShifts',
            'recentTransactions'
        ));
    }
}
