<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TemuanController extends Controller
{
    public function index(Request $request)
    {
        $sort   = $request->get('sort', 'terbaru');
        $search = $request->get('search', '');
        $type   = $request->get('type', 'all');

        $query = DB::table('lost_founds')
            ->join('users', 'lost_founds.user_id', '=', 'users.id')
            ->select('lost_founds.*', 'users.name as user_name')
            ->where('lost_founds.status', 'approved');

        if ($type && $type !== 'all') {
            $query->where('lost_founds.type', $type);
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('lost_founds.item_name', 'like', "%{$search}%")
                  ->orWhere('lost_founds.description', 'like', "%{$search}%");
            });
        }

        $query->orderBy('lost_founds.created_at', $sort === 'terlama' ? 'asc' : 'desc');
        $items = $query->get();

        $ids = $items->pluck('id')->toArray();
        $photoMap = [];
        if (!empty($ids)) {
            $photoMap = DB::table('photos')
                ->where('source_type', 'lost_found')
                ->whereIn('source_id', $ids)
                ->get()->keyBy('source_id')->toArray();
        }

        $recentAnnouncements = DB::table('announcements')
            ->where('is_published', 1)->orderByDesc('created_at')->limit(5)->get()->toArray();

        $recentEvents = DB::table('events')
            ->where('is_published', 1)->orderByDesc('event_date')->limit(5)->get()->toArray();

        return view('temuan.index', compact(
            'items', 'sort', 'search', 'type',
            'recentAnnouncements', 'recentEvents', 'photoMap'
        ));
    }

    public function create()
    {
        return view('temuan.buat');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'type'        => 'required|in:found,lost',
                'item_name'   => 'required|string|max:100',
                'description' => 'nullable|string',
                'found_at'    => 'nullable|string|max:150',
                'photo'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // max 2MB
            ]);

            DB::beginTransaction();

            $lostFoundId = DB::table('lost_founds')->insertGetId([
                'user_id'     => Auth::id(),
                'type'        => $request->type,
                'item_name'   => $request->item_name,
                'description' => $request->description,
                'found_at'    => $request->found_at,
                'status'      => 'pending',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $path = $file->getRealPath();
                $mime = $file->getMimeType();

                // Load gambar
                if (str_contains($mime, 'png')) {
                    $src = imagecreatefrompng($path);
                } else {
                    $src = imagecreatefromjpeg($path);
                }

                // Resize max 800px
                $w = imagesx($src);
                $h = imagesy($src);
                if ($w > 800) {
                    $newW = 800;
                    $newH = (int) round($h * 800 / $w);
                } else {
                    $newW = $w;
                    $newH = $h;
                }

                $dst = imagecreatetruecolor($newW, $newH);
                $white = imagecolorallocate($dst, 255, 255, 255);
                imagefilledrectangle($dst, 0, 0, $newW, $newH, $white);
                imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $w, $h);
                imagedestroy($src);

                // Output JPEG quality 70
                ob_start();
                imagejpeg($dst, null, 70);
                $jpeg = ob_get_clean();
                imagedestroy($dst);

                $base64 = 'data:image/jpeg;base64,' . base64_encode($jpeg);

                DB::table('photos')->insert([
                    'source_type' => 'lost_found',
                    'source_id'   => $lostFoundId,
                    'file_name'   => $file->getClientOriginalName(),
                    'file_path'   => '',
                    'file_data'   => $base64,
                    'file_type'   => 'image/jpeg',
                    'file_size'   => strlen($jpeg),
                    'uploaded_by' => Auth::id(),
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }

            DB::commit();

            return redirect()->route('temuan.index')
                ->with('success', 'Laporan berhasil dikirim dan menunggu persetujuan admin.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('[Temuan.store] FAILED: ' . $e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return back()
                ->with('error', 'Gagal menyimpan laporan: ' . $e->getMessage())
                ->withInput();
        }
    }
}
