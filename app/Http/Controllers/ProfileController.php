<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ProfileController extends Controller
{
    public function show()
    {
        $roleId = (int) Auth::user()->role_id;

        // Kalau sudah di URL yang benar, tampilkan view-nya
        $path = request()->path();

        if ($roleId === 1) {
            // Superadmin harus di /superadmin/profile
            if (!str_starts_with($path, 'superadmin/profile')) {
                return redirect()->route('superadmin.profile.show');
            }
            return view('superadmin.profile.show');
        }

        if ($roleId !== 2) {
            // Admin harus di /admin/profile
            if (!str_starts_with($path, 'admin/profile')) {
                return redirect()->route('admin.profile.show');
            }
            return view('admin.profile.show');
        }

        // User biasa — /profile
        return view('profile.show');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password'     => 'required|string|min:6|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Password saat ini salah.');
        }

        User::where('id', $user->id)
            ->update(['password' => Hash::make($request->new_password)]);

        return back()->with('success', 'Password berhasil diperbarui.');
    }
}