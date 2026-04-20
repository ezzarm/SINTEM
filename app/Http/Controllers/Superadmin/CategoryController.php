<?php
// app/Http/Controllers/Superadmin/CategoryController.php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    // ── List all report categories with their assigned role ──
    public function index()
    {
        $categories = DB::table('report_categories')
            ->join('roles', 'report_categories.responsible_role_id', '=', 'roles.id')
            ->select('report_categories.*', 'roles.role_name')
            ->orderBy('report_categories.id')
            ->get();

        $roles = Role::orderBy('role_name')->get();

        return view('superadmin.categories.index', compact('categories', 'roles'));
    }

    // ── Store new category ──
    public function store(Request $request)
    {
        $data = $request->validate([
            'category_name'       => 'required|string|max:100|unique:report_categories,category_name',
            'description'         => 'nullable|string|max:255',
            'responsible_role_id' => 'required|exists:roles,id',
        ]);

        DB::table('report_categories')->insert([
            'category_name'       => $data['category_name'],
            'description'         => $data['description'] ?? null,
            'responsible_role_id' => $data['responsible_role_id'],
            'created_at'          => now(),
            'updated_at'          => now(),
        ]);

        return redirect()->route('superadmin.categories.index')
            ->with('success', "Kategori '{$data['category_name']}' berhasil dibuat.");
    }

    // ── Update category ──
    public function update(Request $request, $id)
    {
        $category = DB::table('report_categories')->findOrFail($id) ?? abort(404);
        $category = DB::table('report_categories')->where('id', $id)->first();
        abort_if(!$category, 404);

        $data = $request->validate([
            'category_name'       => ['required', 'string', 'max:100', Rule::unique('report_categories', 'category_name')->ignore($id)],
            'description'         => 'nullable|string|max:255',
            'responsible_role_id' => 'required|exists:roles,id',
        ]);

        DB::table('report_categories')->where('id', $id)->update([
            'category_name'       => $data['category_name'],
            'description'         => $data['description'] ?? null,
            'responsible_role_id' => $data['responsible_role_id'],
            'updated_at'          => now(),
        ]);

        return redirect()->route('superadmin.categories.index')
            ->with('success', "Kategori berhasil diperbarui.");
    }

    // ── Delete category (guard if it has reports) ──
    public function destroy($id)
    {
        $category = DB::table('report_categories')->where('id', $id)->first();
        abort_if(!$category, 404);

        // ── Block deletion if anonymous reports reference this category ──
        $reportCount = DB::table('anonymous_reports')->where('category_id', $id)->count();
        if ($reportCount > 0) {
            return back()->with('error', "Kategori ini masih memiliki {$reportCount} laporan dan tidak bisa dihapus.");
        }

        DB::table('report_categories')->where('id', $id)->delete();

        return redirect()->route('superadmin.categories.index')
            ->with('success', "Kategori '{$category->category_name}' berhasil dihapus.");
    }
}