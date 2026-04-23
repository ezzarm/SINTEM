<?php
// app/Http/Middleware/AdminMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $roleId = (int) Auth::user()->role_id;

        // Regular users (role_id = 2) must not access admin panel
        if ($roleId === 2) {
            abort(403, 'Akses ditolak. Halaman ini hanya untuk staf.');
        }

        return $next($request);
    }
}
