<?php
// app/Http/Controllers/PengumumanController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengumumanController extends Controller
{
    public function index(Request $request)
    {
        $sort   = $request->get('sort', 'terbaru');
        $search = $request->get('search', '');

        // ── Announcements ──
        $announcements = DB::table('announcements')
            ->join('users', 'announcements.created_by', '=', 'users.id')
            ->select(
                'announcements.id',
                'announcements.title',
                'announcements.content',
                'announcements.created_at',
                'users.name as author',
                DB::raw("'announcement' as type")
            );

        // ── Events ──
        $events = DB::table('events')
            ->join('users', 'events.created_by', '=', 'users.id')
            ->select(
                'events.id',
                'events.event_name as title',
                'events.description as content',
                DB::raw('CAST(events.event_date AS DATETIME) as created_at'),
                'users.name as author',
                DB::raw("'event' as type")
            );

        // ── Lost & Found ──
        $lostFounds = DB::table('lost_founds')
            ->join('users', 'lost_founds.user_id', '=', 'users.id')
            ->select(
                'lost_founds.id',
                'lost_founds.item_name as title',
                'lost_founds.description as content',
                'lost_founds.created_at',
                'users.name as author',
                DB::raw("'lost_found' as type")
            );

        // Apply search
        if ($search) {
            $announcements->where(function ($q) use ($search) {
                $q->where('announcements.title',   'like', "%$search%")
                  ->orWhere('announcements.content', 'like', "%$search%");
            });
            $events->where(function ($q) use ($search) {
                $q->where('events.event_name',   'like', "%$search%")
                  ->orWhere('events.description', 'like', "%$search%");
            });
            $lostFounds->where(function ($q) use ($search) {
                $q->where('lost_founds.item_name',  'like', "%$search%")
                  ->orWhere('lost_founds.description', 'like', "%$search%");
            });
        }

        // Merge all three
        $items = collect()
            ->merge($announcements->get())
            ->merge($events->get())
            ->merge($lostFounds->get());

        // Sort
        $items = $sort === 'terlama'
            ? $items->sortBy('created_at')->values()
            : $items->sortByDesc('created_at')->values();

        // Fetch attachments grouped by type_id key
        $attachmentMap = [];
        foreach ($items as $item) {
            $sourceType = match($item->type) {
                'event'     => 'event',
                'lost_found'=> 'lost_found',
                default     => 'announcement',
            };
            $attachmentMap[$item->type . '_' . $item->id] = DB::table('attachments')
                ->where('source_type', $sourceType)
                ->where('source_id', $item->id)
                ->get();
        }

        return view('pengumuman.index', compact('items', 'attachmentMap', 'sort', 'search'));
    }
}