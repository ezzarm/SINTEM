<?php
// app/Http/Controllers/Admin/KalenderController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class KalenderController extends Controller
{
    public function index(Request $request)
    {
        $search  = $request->get('search', '');
        $sort    = $request->get('sort', 'terbaru');
        $catId   = $request->get('cat', '');
        $perPage = (int) $request->get('per_page', 10);
        $page    = (int) $request->get('page', 1);

        $query = DB::table('events')
            ->leftJoin('event_categories', 'events.category_id', '=', 'event_categories.id')
            ->leftJoin('event_locations',  'events.location_id', '=', 'event_locations.id')
            ->leftJoin('users', 'events.created_by', '=', 'users.id')
            ->select(
                'events.id',
                'events.event_name',
                'events.category_id',
                'events.location_id',
                'events.event_date',
                'events.event_date_end',
                'events.description',
                'events.is_published',
                'events.created_at',
                'event_categories.name  as category_name',
                'event_categories.color as category_color',
                'event_locations.name   as location_name',
                'users.name             as author'
            );

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('events.event_name',  'like', "%$search%")
                  ->orWhere('events.description','like', "%$search%");
            });
        }

        if ($catId) {
            $query->where('events.category_id', $catId);
        }

        $query->orderBy('events.event_date', $sort === 'terlama' ? 'asc' : 'desc');

        $total = $query->count();
        $items = $query->offset(($page - 1) * $perPage)->limit($perPage)->get();

        foreach ($items as $item) {
            $photo = DB::table('photos')
                ->where('source_type', 'event')
                ->where('source_id', $item->id)
                ->first();
            // FIX: Gunakan file_path + Storage::url, bukan base64
            $item->photo_url = $photo && $photo->file_path
                ? Storage::url($photo->file_path)
                : null;
        }

        $categories = DB::table('event_categories')->orderBy('name')->get();
        $locations  = DB::table('event_locations')->orderBy('name')->get();

        return view('admin.kalender.index', compact(
            'items', 'total', 'page', 'perPage',
            'search', 'sort', 'catId',
            'categories', 'locations'
        ));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'event_name'     => 'required|string|max:255',
                'category_id'    => 'required|integer|exists:event_categories,id',
                'location_id'    => 'nullable|integer|exists:event_locations,id',
                'event_date'     => 'required|date',
                'event_date_end' => 'nullable|date|after_or_equal:event_date',
                'description'    => 'nullable|string',
                'is_published'   => 'required|in:0,1',
                'photo'          => 'nullable|image|mimes:jpg,jpeg,png|max:10240',
            ]);

            $eventId = DB::table('events')->insertGetId([
                'event_name'     => $request->event_name,
                'category_id'    => $request->category_id,
                'location_id'    => $request->location_id ?: null,
                'event_date'     => $request->event_date,
                'event_date_end' => $request->event_date_end ?: null,
                'description'    => $request->description,
                'is_published'   => $request->is_published,
                'created_by'     => auth()->user()->id,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                // FIX: Simpan ke storage/public, bukan base64 ke DB
                $path = $file->store('uploads/photos/events', 'public');
                DB::table('photos')->insert([
                    'source_type' => 'event',
                    'source_id'   => $eventId,
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

            return back()->with('success', 'Kegiatan berhasil disimpan.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal menyimpan kegiatan: ' . $e->getMessage())->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'event_name'     => 'required|string|max:255',
                'category_id'    => 'required|integer|exists:event_categories,id',
                'location_id'    => 'nullable|integer|exists:event_locations,id',
                'event_date'     => 'required|date',
                'event_date_end' => 'nullable|date|after_or_equal:event_date',
                'description'    => 'nullable|string',
                'is_published'   => 'required|in:0,1',
                'photo'          => 'nullable|image|mimes:jpg,jpeg,png|max:10240',
            ]);

            DB::table('events')->where('id', $id)->update([
                'event_name'     => $request->event_name,
                'category_id'    => $request->category_id,
                'location_id'    => $request->location_id ?: null,
                'event_date'     => $request->event_date,
                'event_date_end' => $request->event_date_end ?: null,
                'description'    => $request->description,
                'is_published'   => $request->is_published,
                'updated_at'     => now(),
            ]);

            if ($request->hasFile('photo')) {
                $old = DB::table('photos')->where('source_type', 'event')->where('source_id', $id)->first();
                if ($old) {
                    // FIX: Hapus file lama dari storage
                    if ($old->file_path) Storage::disk('public')->delete($old->file_path);
                    DB::table('photos')->where('id', $old->id)->delete();
                }
                $file = $request->file('photo');
                // FIX: Simpan ke storage/public, bukan base64 ke DB
                $path = $file->store('uploads/photos/events', 'public');
                DB::table('photos')->insert([
                    'source_type' => 'event',
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

            return back()->with('success', 'Kegiatan berhasil diperbarui.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal memperbarui kegiatan: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            // FIX: Hapus file dari storage sebelum hapus record
            $photos = DB::table('photos')->where('source_type', 'event')->where('source_id', $id)->get();
            foreach ($photos as $p) {
                if ($p->file_path) Storage::disk('public')->delete($p->file_path);
            }
            DB::table('photos')->where('source_type', 'event')->where('source_id', $id)->delete();
            DB::table('attachments')->where('source_type', 'event')->where('source_id', $id)->delete();
            DB::table('events')->where('id', $id)->delete();

            return back()->with('success', 'Kegiatan berhasil dihapus.');

        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal menghapus kegiatan: ' . $e->getMessage());
        }
    }

    public function toggle($id)
    {
        try {
            $event = DB::table('events')->where('id', $id)->first();
            if (!$event) return back();

            DB::table('events')->where('id', $id)->update([
                'is_published' => $event->is_published ? 0 : 1,
                'updated_at'   => now(),
            ]);

            return back()->with('success', $event->is_published ? 'Kegiatan dijadikan draft.' : 'Kegiatan dipublikasi.');

        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal mengubah status: ' . $e->getMessage());
        }
    }
}
