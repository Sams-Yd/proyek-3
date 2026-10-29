<?php

namespace App\Http\Controllers;

use App\Models\CashierShift;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ShiftController extends Controller
{
    public function index(): View
    {
        $shifts = CashierShift::with('user')
            ->latest('waktu_buka')
            ->paginate(15);

        return view('shifts.index', compact('shifts'));
    }

    public function openForm(): View|RedirectResponse
    {
        $activeShift = Auth::user()->activeShift();
        if ($activeShift) {
            return redirect()->route('pos.index')->with('info', 'Anda sudah memiliki shift yang aktif.');
        }

        return view('shifts.open');
    }

    public function storeOpen(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user->activeShift()) {
            return redirect()->route('pos.index')->with('info', 'Anda sudah memiliki shift yang aktif.');
        }

        $validated = $request->validate([
            'modal_awal' => ['required', 'numeric', 'min:0'],
            'catatan' => ['nullable', 'string', 'max:255'],
        ]);

        CashierShift::create([
            'user_id' => $user->id,
            'modal_awal' => $validated['modal_awal'],
            'waktu_buka' => now(),
            'status' => 'open',
            'catatan' => $validated['catatan'] ?? null,
        ]);

        return redirect()->route('pos.index')->with('success', 'Shift kasir berhasil dibuka. Selamat bertugas!');
    }

    public function closeForm(): View|RedirectResponse
    {
        $shift = Auth::user()->activeShift();
        if (! $shift) {
            return redirect()->route('shift.open')->with('warning', 'Tidak ada shift yang aktif untuk ditutup.');
        }

        // Blind balancing: Do not pass system totals to view!
        return view('shifts.close', compact('shift'));
    }

    public function storeClose(Request $request): RedirectResponse
    {
        $shift = Auth::user()->activeShift();
        if (! $shift) {
            return redirect()->route('shift.open')->with('warning', 'Tidak ada shift aktif.');
        }

        $validated = $request->validate([
            'total_setoran_fisik' => ['required', 'numeric', 'min:0'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ]);

        $shift->closeWithPhysicalCash((float) $validated['total_setoran_fisik'], $validated['catatan'] ?? null);

        return redirect()->route('shift.summary', $shift)->with('success', 'Shift berhasil ditutup.');
    }

    public function summary(CashierShift $shift): View
    {
        $shift->load(['user', 'transactions.details.product']);

        return view('shifts.summary', compact('shift'));
    }

    public function show(CashierShift $shift): View
    {
        $shift->load(['user', 'transactions.details.product', 'transactions.details.unit']);

        return view('shifts.show', compact('shift'));
    }
}
