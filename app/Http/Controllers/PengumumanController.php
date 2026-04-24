<?php

namespace App\Http\Controllers;

use App\Http\Helpers\PhotoHelper;
use App\Services\SupabaseStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengumumanController extends Controller
{
    public function index(Request $request)
    {
        $sort   = $request->get('sort', 'terbaru');
        $search = $request->get('search', '');

        $announcements = DB::table('announcements')
            ->join('users', 'announcements.created_by', '=', 'users.id')
            ->where('announcements.is_published', 1)
            ->select(
                'announcements.id',
                'announcements.title',
                'announcements.content',
                'announcements.created_at',
                'users.name as author',
                DB::raw("'announcement' as type")
            );

        $events = DB::table('events')
            ->join('users', 'events.created_by', '=', 'users.id')
            ->join('event_categories', 'events.category_id', '=', 'event_categories.id')
            ->leftJoin('event_locations', 'events.location_id', '=', 'event_locations.id')
            ->where('events.is_published', 1)
            ->select(
                'events.id',
                'events.event_name as title',
                'events.description as content',
                DB::raw('CAST(events.event_date AS TIMESTAMP) as created_at'),
                'users.name as author',
                'event_categories.name as category_name',
                'event_categories.color as category_color',
                'event_locations.name as location_name',
                DB::raw("'event' as type")
            );

        $lostFounds = DB::table('lost_founds')
            ->join('users', 'lost_founds.user_id', '=', 'users.id')
            ->where('lost_founds.status', 'approved')
            ->select(
                'lost_founds.id',
                'lost_founds.item_name as title',
                'lost_founds.description as content',
                'lost_founds.type as lost_type',
                'lost_founds.found_at',
                'lost_founds.created_at',
                'users.name as author',
                DB::raw("'lost_found' as type")
            );

        if ($search) {
            $announcements->where(function ($q) use ($search) {
                $q->where('announcements.title',    'like', "%$search%")
                  ->orWhere('announcements.content', 'like', "%$search%");
            });
            $events->where(function ($q) use ($search) {
                $q->where('events.event_name',   'like', "%$search%")
                  ->orWhere('events.description', 'like', "%$search%");
            });
            $lostFounds->where(function ($q) use ($search) {
                $q->where('lost_founds.item_name',    'like', "%$search%")
                  ->orWhere('lost_founds.description', 'like', "%$search%");
            });
        }

        $items = collect()
            ->merge($announcements->get())
            ->merge($events->get())
            ->merge($lostFounds->get());

        $items = $sort === 'terlama'
            ? $items->sortBy('created_at')->values()
            : $items->sortByDesc('created_at')->values();

        $attachmentMap = [];
        foreach ($items as $item) {
            $sourceType = match($item->type) {
                'event'      => 'event',
                'lost_found' => 'lost_found',
                default      => 'announcement',
            };

            $photos = DB::table('photos')
                ->where('source_type', $sourceType)
                ->where('source_id', $item->id)
                ->get();

            foreach ($photos as $p) {
                $p->resolved_url = PhotoHelper::url($p);
            }

            $attachmentMap[$item->type . '_' . $item->id] = $photos;
        }

        return view('pengumuman.index', compact(
            'items', 'attachmentMap', 'sort', 'search'
        ));
    }
}
