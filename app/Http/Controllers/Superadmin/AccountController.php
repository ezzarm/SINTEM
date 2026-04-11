<?php
// app/Http/Controllers/Superadmin/AccountController.php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    // ── List all accounts with optional search/filter ──
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $roleFilter = $request->get('role', '');
        $statusFilter = $request->get('status', '');

        $query = User::with('role')->orderBy('created_at', 'desc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('identifier', 'like', "%$search%");
            });
        }
        if ($roleFilter) {
            $query->where('role_id', $roleFilter);
        }
        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        $users  = $query->paginate(15)->withQueryString();
        $roles  = Role::orderBy('role_name')->get();

        return view('superadmin.accounts.index', compact('users', 'roles', 'search', 'roleFilter', 'statusFilter'));
    }

    // ── Show create form ──
    public function create()
    {
        $roles = Role::orderBy('role_name')->get();
        return view('superadmin.accounts.create', compact('roles'));
    }

    // ── Store new account ──
    public function store(Request $request)
    {
        $data = $request->validate([
            'identifier' => 'required|string|max:50|unique:users,identifier',
            'name'       => 'required|string|max:100',
            'role_id'    => 'required|exists:roles,id',
            'status'     => 'required|in:active,inactive',
            'password'   => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'identifier' => $data['identifier'],
            'name'       => $data['name'],
            'role_id'    => $data['role_id'],
            'status'     => $data['status'],
            'password'   => Hash::make($data['password']),
        ]);

        return redirect()->route('superadmin.accounts.index')
            ->with('success', "Akun {$data['name']} berhasil dibuat.");
    }

    // ── Show edit form ──
    public function edit($id)
    {
        $user  = User::findOrFail($id);
        $roles = Role::orderBy('role_name')->get();
        return view('superadmin.accounts.edit', compact('user', 'roles'));
    }

    // ── Update account ──
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'identifier' => ['required', 'string', 'max:50', Rule::unique('users', 'identifier')->ignore($user->id)],
            'name'       => 'required|string|max:100',
            'role_id'    => 'required|exists:roles,id',
            'status'     => 'required|in:active,inactive',
            'password'   => 'nullable|string|min:6|confirmed',
        ]);

        $user->update([
            'identifier' => $data['identifier'],
            'name'       => $data['name'],
            'role_id'    => $data['role_id'],
            'status'     => $data['status'],
        ]);

        // ── Only update password if a new one was provided ──
        if (!empty($data['password'])) {
            $user->update(['password' => Hash::make($data['password'])]);
        }

        return redirect()->route('superadmin.accounts.index')
            ->with('success', "Akun {$user->name} berhasil diperbarui.");
    }

    // ── Delete account ──
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // ── Prevent deleting own account ──
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Kamu tidak bisa menghapus akun kamu sendiri.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('superadmin.accounts.index')
            ->with('success', "Akun {$name} berhasil dihapus.");
    }
}