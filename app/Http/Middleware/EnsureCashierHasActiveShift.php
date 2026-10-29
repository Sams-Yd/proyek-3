<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCashierHasActiveShift
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Admin might want to view POS, but let's require active shift for actual POS operations
        $activeShift = $user->activeShift();

        if (! $activeShift) {
            return redirect()->route('shift.open')->with('warning', 'Wajib mengisi Modal Awal (Opening Cash) sebelum membuka transaksi kasir.');
        }

        return $next($request);
    }
}
