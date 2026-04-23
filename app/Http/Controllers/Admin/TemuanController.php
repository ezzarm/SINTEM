<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Helpers\PhotoHelper;

class TemuanController extends Controller
{
    public function index(Request $request)
    {
        $sort    = $request->get('sort', 'terbaru');
        $search  = $request->get('search', '');
        $status  = $request->get('status', 'all');
        $type    = $request->get('type', 'all');
        $perPage = (int) $request->get('per_page', 10);
        $page    = (int) $request->get('page', 1);

        $query = DB::table('lost_founds')
            ->join('users', 'lost_founds.user_id', '=', 'users.id')
            ->select('lost_founds.*', 'users.name as user_name');

        if ($status && $status !== 'all') {
            $query->where('lost_founds.status', $status);
        }
        if ($type && $type !== 'all') {
            $query->where('lost_founds.type', $type);
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('lost_founds.item_name', 'like', "%{$search}%")
                  ->orWhere('lost_founds.description', 'like', "%{$search}%")
                  ->orWhere('lost_founds.found_at', 'like', "%{$search}%");
            });
        }

        $query->orderBy('lost_founds.created_at', $sort === 'terlama' ? 'asc' : 'desc');

        $total = $query->count();
        $items = $query->offset(($page - 1) * $perPage)->limit($perPage)->get();

        $ids = $items->pluck('id')->toArray();
        $photoMap = [];
        if (!empty($ids)) {
    
            $photoMap = DB::table('photos')
                ->where('source_type', 'lost_found')
                ->whereIn('source_id', $ids)
                ->get()
                ->keyBy('source_id');  // ← Hasil: Collection, tiap item adalah object stdClass
        }

        return view('admin.temuan.index', compact(
            'items', 'total', 'perPage', 'page',
            'sort', 'search', 'status', 'type', 'photoMap'
        ));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'type'        => 'required|in:found,lost',
                'item_name'   => 'required|string|max:100',
                'description' => 'nullable|string',
                'found_at'    => 'nullable|string|max:150',
                'status'      => 'nullable|in:approved,pending',
                'photo'       => 'nullable|image|mimes:jpg,jpeg,png|max:10240',
            ]);

            $adminId = auth()->user()->id;

            $id = DB::table('lost_founds')->insertGetId([
                'user_id'     => $adminId,
                'type'        => $request->type,
                'item_name'   => $request->item_name,
                'description' => $request->description,
                'found_at'    => $request->found_at,
                'status'      => $request->status ?? 'approved',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            if ($request->hasFile('photo')) {
                PhotoHelper::store($request->file('photo'), 'lost_found', $id, $adminId);
            }

            return redirect()->route('admin.temuan.index')
                ->with('success', 'Temuan berhasil ditambahkan.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal menyimpan temuan: ' . $e->getMessage())->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'type'        => 'required|in:found,lost',
                'item_name'   => 'required|string|max:100',
                'description' => 'nullable|string',
                'found_at'    => 'nullable|string|max:150',
                'status'      => 'required|in:approved,pending,rejected',
                'photo'       => 'nullable|image|mimes:jpg,jpeg,png|max:10240',
            ]);

            $item = DB::table('lost_founds')->where('id', $id)->first();
            if (!$item) {
                return redirect()->route('admin.temuan.index')
                    ->with('error', 'Temuan tidak ditemukan.');
            }

            DB::table('lost_founds')->where('id', $id)->update([
                'type'        => $request->type,
                'item_name'   => $request->item_name,
                'description' => $request->description,
                'found_at'    => $request->found_at,
                'status'      => $request->status,
                'updated_at'  => now(),
            ]);

            if ($request->hasFile('photo')) {
                PhotoHelper::delete('lost_found', $id);
                PhotoHelper::store($request->file('photo'), 'lost_found', $id, auth()->user()->id);
            }

            return redirect()->route('admin.temuan.index')
                ->with('success', 'Temuan berhasil diperbarui.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal memperbarui temuan: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $item = DB::table('lost_founds')->where('id', $id)->first();
            if (!$item) {
                return redirect()->route('admin.temuan.index')
                    ->with('error', 'Temuan tidak ditemukan.');
            }

            PhotoHelper::delete('lost_found', $id);
            DB::table('lost_founds')->where('id', $id)->delete();

            return redirect()->route('admin.temuan.index')
                ->with('success', 'Temuan berhasil dihapus.');

        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal menghapus temuan: ' . $e->getMessage());
        }
    }

    public function approve($id)
    {
        try {
            $item = DB::table('lost_founds')->where('id', $id)->first();
            if (!$item) {
                return redirect()->route('admin.temuan.index')
                    ->with('error', 'Temuan tidak ditemukan.');
            }

            DB::table('lost_founds')->where('id', $id)->update([
                'status'     => 'approved',
                'updated_at' => now(),
            ]);

            return redirect()->route('admin.temuan.index')
                ->with('success', "Laporan \"{$item->item_name}\" berhasil disetujui dan dipublikasikan.");

        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal menyetujui temuan: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, $id)
    {
        try {
            $item = DB::table('lost_founds')->where('id', $id)->first();
            if (!$item) {
                return redirect()->route('admin.temuan.index')
                    ->with('error', 'Temuan tidak ditemukan.');
            }

            DB::table('lost_founds')->where('id', $id)->update([
                'status'        => 'rejected',
                'reject_reason' => $request->reject_reason,
                'updated_at'    => now(),
            ]);

            return redirect()->route('admin.temuan.index')
                ->with('success', "Laporan \"{$item->item_name}\" telah ditolak.");

        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal menolak temuan: ' . $e->getMessage());
        }
    }
}
