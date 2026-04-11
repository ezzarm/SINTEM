<?php
// app/Http/Controllers/AuthController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
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
            'nis'      => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt([
            'identifier' => $request->nis,
            'password'   => $request->password,
            'status'     => 'active',
        ])) {
            $request->session()->regenerate();
            return $this->redirectByRole();
        }

        return back()
            ->withInput($request->only('nis'))
            ->with('error', 'NIS atau password salah. Silakan coba lagi.');
    }

    private function redirectByRole()
    {
        $roleId = (int) Auth::user()->role_id;

        // ── Superadmin goes to superadmin panel ──
        if ($roleId === 1) {
            return redirect()->route('superadmin.accounts.index');
        }

        // ── Regular user ──
        if ($roleId === 2) {
            return redirect()->route('pengumuman.index');
        }

        // ── All other staff roles go to admin panel ──
        return redirect()->route('admin.pengumuman.index');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}