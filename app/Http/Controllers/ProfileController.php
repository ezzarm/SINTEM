<?php
// app/Http/Controllers/ProfileController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\User;

class ProfileController extends Controller
{
    public function show()
    {
        $roleId = (int) Auth::user()->role_id;
        $path   = request()->path();

        if ($roleId === 1) {
            if (! str_starts_with($path, 'superadmin/profile')) {
                return redirect()->route('superadmin.profile.show');
            }
            return view('superadmin.profile.show');
        }

        if ($roleId !== 2) {
            if (! str_starts_with($path, 'admin/profile')) {
                return redirect()->route('admin.profile.show');
            }
            return view('admin.profile.show');
        }

        return view('profile.show');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            // Enforce a strong password: min 8 chars, mixed case, number, symbol
            'new_password'     => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],
        ]);

        $user = Auth::user();

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Password saat ini salah.');
        }

        // Prevent reusing the same password
        if (Hash::check($request->new_password, $user->password)) {
            return back()->with('error', 'Password baru tidak boleh sama dengan password lama.');
        }

        User::where('id', $user->id)
            ->update(['password' => Hash::make($request->new_password)]);

        // Regenerate session after password change to invalidate other sessions
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Password berhasil diperbarui. Silakan login kembali.');
    }
}
