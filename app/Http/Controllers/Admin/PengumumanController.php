<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\PhotoHelper;
use App\Services\SupabaseStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
                $q->where('announcements.title',    'like', "%$search%")
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

        $storage = app(SupabaseStorageService::class);

        foreach ($items as $item) {
            $photos = DB::table('photos')
                ->where('source_type', 'announcement')
                ->where('source_id', $item->id)
                ->get();

            foreach ($photos as $p) {
                if (!empty($p->file_path) && empty($p->file_data)) {
                    $p->file_data = $storage->publicUrl($p->file_path);
                }
            }
            $item->photos = $photos;

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
                'title'           => 'required|string|max:255',
                'content'         => 'required|string',
                'is_published'    => 'required|in:0,1',
                'photo'           => 'nullable|image|max:10240',
                'attachment_file' => 'nullable|file|max:10240',
            ]);

            $announcementId = DB::table('announcements')->insertGetId([
                'title'        => $request->title,
                'content'      => $request->content,
                'is_published' => $request->is_published,
                'created_by'   => auth()->id(),
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);

            if ($request->hasFile('photo')) {
                PhotoHelper::store(
                    $request->file('photo'),
                    'announcement',
                    $announcementId,
                    auth()->id()
                );
            }

            if ($request->hasFile('attachment_file')) {
                $file    = $request->file('attachment_file');
                $storage = app(SupabaseStorageService::class);
                $path    = $storage->upload($file, 'attachments/announcement');
                $url     = $storage->publicUrl($path);

                DB::table('attachments')->insert([
                    'source_type'     => 'announcement',
                    'source_id'       => $announcementId,
                    'attachment_type' => 'file',
                    'file_name'       => $file->getClientOriginalName(),
                    'file_path'       => $path,
                    'link_url'        => $url,
                    'file_type'       => $file->getMimeType(),
                    'file_size'       => $file->getSize(),
                    'label'           => $request->attachment_label ?? null,
                    'uploaded_by'     => auth()->id(),
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }

            return back()->with('success', 'Pengumuman berhasil disimpan.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal menyimpan pengumuman: ' . $e->getMessage())->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'title'           => 'required|string|max:255',
                'content'         => 'required|string',
                'is_published'    => 'required|in:0,1',
                'photo'           => 'nullable|image|max:10240',
                'attachment_file' => 'nullable|file|max:10240',
            ]);

            DB::table('announcements')->where('id', $id)->update([
                'title'        => $request->title,
                'content'      => $request->content,
                'is_published' => $request->is_published,
                'updated_at'   => now(),
            ]);

            if ($request->hasFile('photo')) {
                PhotoHelper::delete('announcement', $id);
                PhotoHelper::store(
                    $request->file('photo'),
                    'announcement',
                    $id,
                    auth()->id()
                );
            }

            if ($request->hasFile('attachment_file')) {
                $storage = app(SupabaseStorageService::class);

                $oldAttachments = DB::table('attachments')
                    ->where('source_type', 'announcement')
                    ->where('source_id', $id)
                    ->get();

                foreach ($oldAttachments as $att) {
                    if (!empty($att->file_path)) {
                        $storage->delete($att->file_path);
                    }
                }

                DB::table('attachments')
                    ->where('source_type', 'announcement')
                    ->where('source_id', $id)
                    ->delete();

                $file = $request->file('attachment_file');
                $path = $storage->upload($file, 'attachments/announcement');
                $url  = $storage->publicUrl($path);

                DB::table('attachments')->insert([
                    'source_type'     => 'announcement',
                    'source_id'       => $id,
                    'attachment_type' => 'file',
                    'file_name'       => $file->getClientOriginalName(),
                    'file_path'       => $path,
                    'link_url'        => $url,
                    'file_type'       => $file->getMimeType(),
                    'file_size'       => $file->getSize(),
                    'label'           => $request->attachment_label ?? null,
                    'uploaded_by'     => auth()->id(),
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }

            return back()->with('success', 'Pengumuman berhasil diperbarui.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal memperbarui pengumuman: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $storage = app(SupabaseStorageService::class);

            PhotoHelper::delete('announcement', $id);

            $attachments = DB::table('attachments')
                ->where('source_type', 'announcement')
                ->where('source_id', $id)
                ->get();

            foreach ($attachments as $att) {
                if (!empty($att->file_path)) {
                    $storage->delete($att->file_path);
                }
            }

            DB::table('attachments')
                ->where('source_type', 'announcement')
                ->where('source_id', $id)
                ->delete();

            DB::table('announcements')->where('id', $id)->delete();

            return back()->with('success', 'Pengumuman berhasil dihapus.');

        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal menghapus pengumuman: ' . $e->getMessage());
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
            return back()->with('error', 'Gagal mengubah status: ' . $e->getMessage());
        }
    }

    public function create()
    {
        return view('admin.pengumuman.buat');
    }
}
