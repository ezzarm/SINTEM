<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class TemuanController extends Controller
{
    // ── GET /admin/temuan ──
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
            $photos = DB::table('photos')
                ->where('source_type', 'lost_found')
                ->whereIn('source_id', $ids)
                ->get();

            foreach ($photos as $photo) {
                // FIX: Simpan photo_url dari file_path, bukan file_data
                $photo->photo_url = $photo->file_path ? Storage::url($photo->file_path) : null;
                $photoMap[$photo->source_id] = $photo;
            }
        }

        return view('admin.temuan.index', compact(
            'items', 'total', 'perPage', 'page',
            'sort', 'search', 'status', 'type', 'photoMap'
        ));
    }

    // ── POST /admin/temuan ──
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
                $file = $request->file('photo');
                // FIX: Simpan ke storage/public, bukan base64 ke DB
                $path = $file->store('uploads/photos/lost-found', 'public');
                DB::table('photos')->insert([
                    'source_type' => 'lost_found',
                    'source_id'   => $id,
                    'file_name'   => $file->getClientOriginalName(),
                    'file_path'   => $path,
                    'file_data'   => null,
                    'file_type'   => $file->getMimeType(),
                    'file_size'   => $file->getSize(),
                    'uploaded_by' => $adminId,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }

            return redirect()->route('admin.temuan.index')
                ->with('success', 'Temuan berhasil ditambahkan.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal menyimpan temuan: ' . $e->getMessage())->withInput();
        }
    }

    // ── PUT /admin/temuan/{id} ──
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
                $old = DB::table('photos')
                    ->where('source_type', 'lost_found')
                    ->where('source_id', $id)
                    ->first();

                if ($old) {
                    // FIX: Hapus file lama dari storage
                    if ($old->file_path) Storage::disk('public')->delete($old->file_path);
                    DB::table('photos')->where('id', $old->id)->delete();
                }

                $file = $request->file('photo');
                // FIX: Simpan ke storage/public, bukan base64 ke DB
                $path = $file->store('uploads/photos/lost-found', 'public');
                DB::table('photos')->insert([
                    'source_type' => 'lost_found',
                    'source_id'   => $id,
                    'file_name'   => $file->getClientOriginalName(),
                    'file_path'   => $path,
                    'file_data'   => null,
                    'file_type'   => $file->getMimeType(),
                    'file_size'   => $file->getSize(),
                    'uploaded_by' => auth()->user()->id,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }

            return redirect()->route('admin.temuan.index')
                ->with('success', 'Temuan berhasil diperbarui.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal memperbarui temuan: ' . $e->getMessage())->withInput();
        }
    }

    // ── DELETE /admin/temuan/{id} ──
    public function destroy($id)
    {
        try {
            $item = DB::table('lost_founds')->where('id', $id)->first();
            if (!$item) {
                return redirect()->route('admin.temuan.index')
                    ->with('error', 'Temuan tidak ditemukan.');
            }

            // FIX: Hapus file dari storage sebelum hapus record
            $photos = DB::table('photos')->where('source_type', 'lost_found')->where('source_id', $id)->get();
            foreach ($photos as $p) {
                if ($p->file_path) Storage::disk('public')->delete($p->file_path);
            }
            DB::table('photos')->where('source_type', 'lost_found')->where('source_id', $id)->delete();
            DB::table('lost_founds')->where('id', $id)->delete();

            return redirect()->route('admin.temuan.index')
                ->with('success', 'Temuan berhasil dihapus.');

        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal menghapus temuan: ' . $e->getMessage());
        }
    }

    // ── PATCH /admin/temuan/{id}/approve ──
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

    // ── PATCH /admin/temuan/{id}/reject ──
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
