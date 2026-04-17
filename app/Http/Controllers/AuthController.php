<?php
// app/Http/Controllers/AuthController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    private const MAX_ATTEMPTS = 5;
    private const DECAY_SECONDS = 60;

    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectByRole();
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'nis'      => 'required|string|max:50',
            'password' => 'required|string|max:100',
        ]);

        $throttleKey = $this->throttleKey($request);

        if (RateLimiter::tooManyAttempts($throttleKey, self::MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()
                ->withInput($request->only('nis'))
                ->with('error', "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.");
        }

        if (Auth::attempt([
            'identifier' => $request->nis,
            'password'   => $request->password,
            'status'     => 'active',
        ])) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();
            Auth::user()->update(['last_login' => now()]);
            return $this->redirectByRole();
        }

        RateLimiter::hit($throttleKey, self::DECAY_SECONDS);

        return back()
            ->withInput($request->only('nis'))
            ->with('error', 'NIS atau password salah. Silakan coba lagi.');
    }

    private function redirectByRole(): \Illuminate\Http\RedirectResponse
    {
        $roleId = (int) Auth::user()->role_id;

        if ($roleId === 1) {
            return redirect()->route('superadmin.accounts.index');
        }

        if ($roleId === 2) {
            return redirect()->route('pengumuman.index');
        }

        return redirect()->route('admin.pengumuman.index');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    private function throttleKey(Request $request): string
    {
        return Str::lower($request->input('nis')) . '|' . $request->ip();
    }
}