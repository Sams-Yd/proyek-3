<?php

namespace App\Http\Controllers;

use App\Models\CashierShift;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Laporan Penjualan Real-time & Detail Transaksi
     */
    public function sales(Request $request): View
    {
        $startDate = $request->input('start_date', Carbon::today()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());
        $paymentMethod = $request->input('metode_pembayaran');

        $query = Transaction::with(['user', 'details.product', 'details.unit'])
            ->whereDate('waktu_transaksi', '>=', $startDate)
            ->whereDate('waktu_transaksi', '<=', $endDate);

        if ($paymentMethod) {
            $query->where('metode_pembayaran', $paymentMethod);
        }

        $transactions = $query->latest('waktu_transaksi')->paginate(20)->withQueryString();

        // Agregasi Ringkasan
        $totalOmzet = (float) $query->sum('total_akhir');
        $totalTransaksi = $query->count();
        $totalDiskon = (float) $query->sum('diskon');

        return view('reports.sales', compact(
            'transactions',
            'startDate',
            'endDate',
            'paymentMethod',
            'totalOmzet',
            'totalTransaksi',
            'totalDiskon'
        ));
    }

    /**
     * Laporan Laba-Rugi Kotor (Omzet - HPP)
     */
    public function profit(Request $request): View
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());

        $transactions = Transaction::with(['details.product', 'details.unit'])
            ->whereDate('waktu_transaksi', '>=', $startDate)
            ->whereDate('waktu_transaksi', '<=', $endDate)
            ->get();

        $totalOmzet = 0;
        $totalHpp = 0;
        $totalDiskon = 0;

        foreach ($transactions as $trx) {
            $totalOmzet += (float) $trx->total_akhir;
            $totalDiskon += (float) $trx->diskon;
            $totalHpp += $trx->calculateHpp();
        }

        $grossProfit = $totalOmzet - $totalHpp;
        $profitMargin = $totalOmzet > 0 ? ($grossProfit / $totalOmzet) * 100 : 0;

        return view('reports.profit', compact(
            'startDate',
            'endDate',
            'transactions',
            'totalOmzet',
            'totalHpp',
            'totalDiskon',
            'grossProfit',
            'profitMargin'
        ));
    }

    /**
     * Laporan Produk Terlaris (Top-Selling SKU)
     */
    public function topProducts(Request $request): View
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());

        $topItems = TransactionDetail::select(
            'product_id',
            DB::raw('SUM(qty) as total_qty_sold'),
            DB::raw('SUM(subtotal_item) as total_revenue')
        )
            ->whereHas('transaction', function ($q) use ($startDate, $endDate) {
                $q->whereDate('waktu_transaksi', '>=', $startDate)
                    ->whereDate('waktu_transaksi', '<=', $endDate);
            })
            ->with(['product.category', 'product.units'])
            ->groupBy('product_id')
            ->orderByDesc('total_qty_sold')
            ->take(20)
            ->get();

        return view('reports.top_products', compact('topItems', 'startDate', 'endDate'));
    }

    /**
     * Laporan Rekapitulasi Shift Kasir & Audit Selisih
     */
    public function shifts(Request $request): View
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());

        $shifts = CashierShift::with(['user', 'transactions'])
            ->whereDate('waktu_buka', '>=', $startDate)
            ->whereDate('waktu_buka', '<=', $endDate)
            ->orderByDesc('waktu_buka')
            ->paginate(15)
            ->withQueryString();

        $totalSelisih = (float) CashierShift::whereDate('waktu_buka', '>=', $startDate)
            ->whereDate('waktu_buka', '<=', $endDate)
            ->where('status', 'closed')
            ->sum('selisih');

        return view('reports.shifts', compact('shifts', 'startDate', 'endDate', 'totalSelisih'));
    }
}
