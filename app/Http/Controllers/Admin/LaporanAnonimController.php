<?php
// app/Http/Controllers/Admin/LaporanAnonimController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; 

class LaporanAnonimController extends Controller
{
    public function index(Request $request)
    {
        $search  = $request->get('search', '');
        $filter  = $request->get('filter', 'semua');
        $perPage = (int) $request->get('per_page', 10);
        $page    = (int) $request->get('page', 1);

        $user = Auth::user();

        $query = DB::table('anonymous_reports')
            ->join('report_categories', 'anonymous_reports.category_id', '=', 'report_categories.id')
            ->select(
                'anonymous_reports.id',
                'anonymous_reports.ticket_number',
                'anonymous_reports.report_content',
                'anonymous_reports.admin_notes',
                'anonymous_reports.status',
                'anonymous_reports.resolved_at',
                'anonymous_reports.created_at',
                'anonymous_reports.updated_at',
                'report_categories.category_name',
                'report_categories.responsible_role_id'
            )
            ->orderBy('anonymous_reports.created_at', 'desc');

        if ($user->role_id != 1) {
            $query->where('report_categories.responsible_role_id', $user->role_id);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('anonymous_reports.ticket_number', 'like', "%$search%")
                  ->orWhere('anonymous_reports.report_content', 'like', "%$search%")
                  ->orWhere('report_categories.category_name', 'like', "%$search%");
            });
        }

        if ($filter === 'pending') {
            $query->where('anonymous_reports.status', 'pending');
        } elseif ($filter === 'in_progress') {
            $query->where('anonymous_reports.status', 'in_progress');
        } elseif ($filter === 'solved') {
            $query->where('anonymous_reports.status', 'solved');
        }

        $total = $query->count();
        $items = $query->offset(($page - 1) * $perPage)->limit($perPage)->get();

        $categoriesQuery = DB::table('report_categories')->orderBy('id');
        if ($user->role_id != 1) {
            $categoriesQuery->where('responsible_role_id', $user->role_id);
        }
        $categories = $categoriesQuery->get();

        return view('admin.laporan.anonim', compact(
            'items', 'total', 'page', 'perPage', 'search', 'filter', 'categories'
        ));
    }

    public function updateStatus(Request $request, $id)
    {
        $user = Auth::user();
        $report = DB::table('anonymous_reports')
            ->join('report_categories', 'anonymous_reports.category_id', '=', 'report_categories.id')
            ->where('anonymous_reports.id', $id)
            ->select('report_categories.responsible_role_id')
            ->first();

        if ($user->role_id != 1 && $report->responsible_role_id != $user->role_id) {
            return back()->with('error', 'Anda tidak memiliki hak akses untuk memproses laporan ini.');
        }

        $request->validate([
            'status'     => 'required|in:pending,in_progress,solved',
            'admin_notes'=> 'nullable|string|max:1000',
        ]);

        $data = [
            'status'     => $request->status,
            'admin_notes'=> $request->admin_notes,
            'updated_at' => now(),
        ];

        if ($request->status === 'solved') {
            $data['resolved_at'] = now();
        } else {
            $data['resolved_at'] = null;
        }

        DB::table('anonymous_reports')->where('id', $id)->update($data);

        return back()->with('success', 'Status laporan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $report = DB::table('anonymous_reports')
            ->join('report_categories', 'anonymous_reports.category_id', '=', 'report_categories.id')
            ->where('anonymous_reports.id', $id)
            ->select('report_categories.responsible_role_id')
            ->first();

        if ($user->role_id != 1 && $report->responsible_role_id != $user->role_id) {
            return back()->with('error', 'Anda tidak memiliki hak akses untuk menghapus laporan ini.');
        }

        DB::table('anonymous_reports')->where('id', $id)->delete();
        return back()->with('success', 'Laporan berhasil dihapus.');
    }
}