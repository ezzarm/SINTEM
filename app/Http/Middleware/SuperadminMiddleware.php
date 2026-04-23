<?php
// app/Http/Middleware/SuperadminMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperadminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // ── Reject unauthenticated users ──
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // ── Only role_id = 1 (superadmin) may proceed ──
        if ((int) Auth::user()->role_id !== 1) {
            abort(403, 'Akses ditolak. Halaman ini hanya untuk superadmin.');
        }

        return $next($request);
    }
}