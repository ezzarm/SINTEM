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
}