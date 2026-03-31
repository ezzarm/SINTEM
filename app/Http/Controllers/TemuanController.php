<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TemuanController extends Controller
{
    public function index(Request $request)
    {
        $sort   = $request->get('sort', 'terbaru');
        $search = $request->get('search', '');

        $query = DB::table('lost_founds')
            ->join('users', 'lost_founds.user_id', '=', 'users.id')
            ->select('lost_founds.*', 'users.name as user_name')
            ->where('lost_founds.status', 'approved');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('lost_founds.item_name', 'like', "%{$search}%")
                  ->orWhere('lost_founds.description', 'like', "%{$search}%");
            });
        }

        $query->orderBy('lost_founds.created_at', $sort === 'terlama' ? 'asc' : 'desc');

        $items = $query->get();

        $recentAnnouncements = DB::table('announcements')
            ->where('is_published', 1)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $recentEvents = DB::table('events')
            ->where('is_published', 1)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('temuan.index', compact('items', 'sort', 'search', 'recentAnnouncements', 'recentEvents'));
    }

    public function create()
    {
        return view('temuan.buat');
    }

    public function store(Request $request)
    {
        $request->validate([
            'type'        => 'required|in:found,lost',
            'item_name'   => 'required|string|max:100',
            'description' => 'nullable|string',
            'photo'       => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('upload/photos/lost_founds', 'public');
        }

        DB::table('lost_founds')->insert([
            'user_id'     => Auth::id(),
            'type'        => $request->type,
            'item_name'   => $request->item_name,
            'description' => $request->description,
            'status'      => 'pending',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return redirect()->route('temuan.index')->with('success', 'Laporan berhasil dikirim dan menunggu persetujuan.');
    }
}