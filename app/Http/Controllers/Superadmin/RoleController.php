<?php
// app/Http/Controllers/Superadmin/RoleController.php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    // ── List all roles with user counts ──
    public function index()
    {
        $roles = Role::withCount('users')->orderBy('id')->get();
        return view('superadmin.roles.index', compact('roles'));
    }

    // ── Store new role ──
    public function store(Request $request)
    {
        $data = $request->validate([
            'role_name'   => 'required|string|max:50|unique:roles,role_name',
            'description' => 'nullable|string|max:255',
        ]);

        Role::create($data);

        return redirect()->route('superadmin.roles.index')
            ->with('success', "Role '{$data['role_name']}' berhasil dibuat.");
    }

    // ── Update role ──
    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $data = $request->validate([
            'role_name'   => ['required', 'string', 'max:50', Rule::unique('roles', 'role_name')->ignore($role->id)],
            'description' => 'nullable|string|max:255',
        ]);

        $role->update($data);

        return redirect()->route('superadmin.roles.index')
            ->with('success', "Role '{$role->role_name}' berhasil diperbarui.");
    }

    // ── Delete role (guard against roles in use) ──
    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        // ── Block deletion if users are assigned to this role ──
        if ($role->users()->count() > 0) {
            return back()->with('error', "Role '{$role->role_name}' masih digunakan oleh {$role->users()->count()} akun dan tidak bisa dihapus.");
        }

        $name = $role->role_name;
        $role->delete();

        return redirect()->route('superadmin.roles.index')
            ->with('success', "Role '{$name}' berhasil dihapus.");
    }
}