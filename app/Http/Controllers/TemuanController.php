<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TemuanController extends Controller
{
    // ── TAMPILKAN SEMUA TEMUAN (HALAMAN PUBLIK) ──
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
                ->get()
                ->keyBy('source_id')
                ->toArray();
        }

        $recentAnnouncements = DB::table('announcements')
            ->where('is_published', 1)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->toArray();

        $recentEvents = DB::table('events')
            ->where('is_published', 1)
            ->orderByDesc('event_date')
            ->limit(5)
            ->get()
            ->toArray();

        return view('temuan.index', compact(
            'items', 'sort', 'search', 'type',
            'recentAnnouncements', 'recentEvents', 'photoMap'
        ));
    }

    // ── FORM BUAT LAPORAN ──
    public function create()
    {
        return view('temuan.buat');
    }

    // ── SIMPAN LAPORAN TEMUAN ──
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'type'        => 'required|in:found,lost',
            'item_name'   => 'required|string|max:100',
            'description' => 'nullable|string',
            'found_at'    => 'nullable|string|max:150',
            'photo'       => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        // Gunakan Transaction agar jika salah satu gagal, semua batal (mencegah data kosong)
        DB::beginTransaction();

        try {
            // 2. Insert ke Tabel lost_founds
            // Pastikan nama kolom 'found_at' benar-benar ada di database kamu
            $lostFoundId = DB::table('lost_founds')->insertGetId([
                'user_id'     => Auth::id(), // Menggunakan Auth::id() lebih singkat
                'type'        => $request->type,
                'item_name'   => $request->item_name,
                'description' => $request->description,
                'found_at'    => $request->found_at, 
                'status'      => 'pending',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            // 3. Simpan Foto jika ada
            if ($request->hasFile('photo')) {
                $file   = $request->file('photo');
                $base64 = 'data:' . $file->getMimeType() . ';base64,' . base64_encode(file_get_contents($file->getRealPath()));
                DB::table('photos')->insert([
                    'source_type' => 'lost_found',
                    'source_id'   => $lostFoundId,
                    'file_name'   => $file->getClientOriginalName(),
                    'file_path'   => '',
                    'file_data'   => $base64,
                    'file_type'   => $file->getMimeType(),
                    'file_size'   => $file->getSize(),
                    'uploaded_by' => Auth::id(),
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }

            DB::commit(); // Simpan permanen ke database

            return redirect()->route('temuan.index')
                ->with('success', 'Laporan berhasil dikirim dan menunggu persetujuan admin.');

        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan semua jika ada error
            
            // Log error ke storage/logs/laravel.log agar bisa kamu cek lewat CMD
            Log::error('Gagal Simpan Temuan: ' . $e->getMessage());

            return back()
                ->with('error', 'Gagal simpan ke database: ' . $e->getMessage())
                ->withInput();
        }
    }
}
