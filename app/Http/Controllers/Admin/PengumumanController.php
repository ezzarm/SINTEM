<?php
// app/Http/Controllers/Admin/PengumumanController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PengumumanController extends Controller
{
    public function index(Request $request)
    {
        $search  = $request->get('search', '');
        $filter  = $request->get('filter', 'semua');
        $perPage = (int) $request->get('per_page', 10);
        $page    = (int) $request->get('page', 1);

        $query = DB::table('announcements')
            ->join('users', 'announcements.created_by', '=', 'users.id')
            ->select(
                'announcements.id',
                'announcements.title',
                'announcements.content',
                'announcements.is_published',
                'announcements.created_at',
                'users.name as author'
            )
            ->orderBy('announcements.created_at', 'desc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('announcements.title',   'like', "%$search%")
                  ->orWhere('announcements.content', 'like', "%$search%");
            });
        }

        if ($filter === 'published') {
            $query->where('announcements.is_published', 1);
        } elseif ($filter === 'draft') {
            $query->where('announcements.is_published', 0);
        }

        $total = $query->count();
        $items = $query->offset(($page - 1) * $perPage)->limit($perPage)->get();

        foreach ($items as $item) {
            $item->photos = DB::table('photos')
                ->where('source_type', 'announcement')
                ->where('source_id', $item->id)
                ->get();
            $item->attachments = DB::table('attachments')
                ->where('source_type', 'announcement')
                ->where('source_id', $item->id)
                ->get();
        }

        return view('admin.pengumuman.index', compact(
            'items', 'total', 'page', 'perPage', 'search', 'filter'
        ));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'title'        => 'required|string|max:255',
                'content'      => 'required|string',
                'is_published' => 'required|in:0,1',
                'photo'        => 'nullable|image|max:5120',
            ]);

            $announcementId = DB::table('announcements')->insertGetId([
                'title'        => $request->title,
                'content'      => $request->content,
                'is_published' => $request->is_published,
                'created_by'   => auth()->user()->id,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);

            if ($request->hasFile('photo')) {
                $file   = $request->file('photo');
                $base64 = 'data:' . $file->getMimeType() . ';base64,' . base64_encode(file_get_contents($file->getRealPath()));
                DB::table('photos')->insert([
                    'source_type' => 'announcement',
                    'source_id'   => $announcementId,
                    'file_name'   => $file->getClientOriginalName(),
                    'file_path'   => '',
                    'file_data'   => $base64,
                    'file_type'   => $file->getMimeType(),
                    'file_size'   => $file->getSize(),
                    'uploaded_by' => auth()->user()->id,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }

            if ($request->hasFile('attachment_file')) {
                $file = $request->file('attachment_file');
                $path = $file->store('uploads/attachments/announcements', 'public');
                DB::table('attachments')->insert([
                    'source_type'     => 'announcement',
                    'source_id'       => $announcementId,
                    'attachment_type' => 'file',
                    'file_name'       => $file->getClientOriginalName(),
                    'file_path'       => $path,
                    'file_type'       => $file->getMimeType(),
                    'file_size'       => $file->getSize(),
                    'label'           => $request->attachment_label ?? null,
                    'uploaded_by'     => auth()->user()->id,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }

            return back()->with('success', 'Pengumuman berhasil disimpan.');

        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'title'        => 'required|string|max:255',
                'content'      => 'required|string',
                'is_published' => 'required|in:0,1',
                'photo'        => 'nullable|image|max:5120',
            ]);

            DB::table('announcements')->where('id', $id)->update([
                'title'        => $request->title,
                'content'      => $request->content,
                'is_published' => $request->is_published,
                'updated_at'   => now(),
            ]);

            if ($request->hasFile('photo')) {
                $oldPhoto = DB::table('photos')
                    ->where('source_type', 'announcement')
                    ->where('source_id', $id)
                    ->first();

                if ($oldPhoto) {
                    if ($oldPhoto->file_path) {
                        Storage::disk('public')->delete($oldPhoto->file_path);
                    }
                    DB::table('photos')->where('id', $oldPhoto->id)->delete();
                }

                $file   = $request->file('photo');
                $base64 = 'data:' . $file->getMimeType() . ';base64,' . base64_encode(file_get_contents($file->getRealPath()));

                DB::table('photos')->insert([
                    'source_type' => 'announcement',
                    'source_id'   => $id,
                    'file_name'   => $file->getClientOriginalName(),
                    'file_path'   => '',
                    'file_data'   => $base64,
                    'file_type'   => $file->getMimeType(),
                    'file_size'   => $file->getSize(),
                    'uploaded_by' => auth()->user()->id,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }

            return back()->with('success', 'Pengumuman berhasil diperbarui.');

        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $photos = DB::table('photos')->where('source_type', 'announcement')->where('source_id', $id)->get();
            foreach ($photos as $p) {
                Storage::disk('public')->delete($p->file_path);
            }
            DB::table('photos')->where('source_type', 'announcement')->where('source_id', $id)->delete();

            $attachments = DB::table('attachments')->where('source_type', 'announcement')->where('source_id', $id)->get();
            foreach ($attachments as $a) {
                if ($a->file_path) Storage::disk('public')->delete($a->file_path);
            }
            DB::table('attachments')->where('source_type', 'announcement')->where('source_id', $id)->delete();

            DB::table('announcements')->where('id', $id)->delete();

            return back()->with('success', 'Pengumuman berhasil dihapus.');

        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
            ], 500);
        }
    }

    public function toggleDraft($id)
    {
        try {
            $ann = DB::table('announcements')->where('id', $id)->first();
            if (!$ann) return back();

            DB::table('announcements')->where('id', $id)->update([
                'is_published' => $ann->is_published ? 0 : 1,
                'updated_at'   => now(),
            ]);

            return back()->with('success', $ann->is_published ? 'Pengumuman dijadikan draft.' : 'Pengumuman dipublikasi.');

        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
            ], 500);
        }
    }

    public function create()
    {
        return view('admin.pengumuman.buat');
    }
}