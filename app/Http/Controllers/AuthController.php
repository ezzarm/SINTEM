<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('pengumuman.index');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'nis'      => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = [
            'identifier' => $request->nis,
            'password'   => $request->password,
            'status'     => 'active',
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended(route('pengumuman.index'));
        }

        return back()
            ->withInput($request->only('nis'))
            ->with('error', 'NIS atau password salah. Silakan coba lagi.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}