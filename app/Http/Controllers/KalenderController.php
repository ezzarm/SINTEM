<?php
// app/Http/Controllers/KalenderController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KalenderController extends Controller
{
    public function index(Request $request)
    {
        $view   = $request->get('view', 'card');
        $search = $request->get('search', '');
        $filter = $request->get('filter', 'semua');
        $openId = $request->get('open', null); // auto-open popup for this event id

        $query = DB::table('events')
            ->join('users', 'events.created_by', '=', 'users.id')
            ->join('event_categories', 'events.category_id', '=', 'event_categories.id')
            ->leftJoin('event_locations', 'events.location_id', '=', 'event_locations.id')
            ->where('events.is_published', 1)
            ->select(
                'events.id',
                'events.event_name',
                'events.description',
                'events.event_date',
                'events.event_date_end',
                'users.name as author',
                'event_categories.id as category_id',
                'event_categories.name as category_name',
                'event_categories.color as category_color',
                'event_locations.name as location_name',
                'events.created_at'
            );

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('events.event_name', 'like', "%$search%")
                  ->orWhere('events.description', 'like', "%$search%");
            });
        }

        if ($filter !== 'semua') {
            $query->where('event_categories.name', $filter);
        }

        $events = $query->orderBy('events.event_date', 'asc')->get();

        foreach ($events as $event) {
            $event->photos = DB::table('photos')
                ->where('source_type', 'event')
                ->where('source_id', $event->id)
                ->get();
        }

        $categories = DB::table('event_categories')->orderBy('name')->get();

        // Calendar: map events by date
        $eventsByDate = [];
        foreach ($events as $event) {
            $date = $event->event_date;
            $eventsByDate[$date][] = $event;

            if ($event->event_date_end && $event->event_date_end !== $event->event_date) {
                $start    = new \DateTime($event->event_date);
                $end      = new \DateTime($event->event_date_end);
                $interval = new \DateInterval('P1D');
                $range    = new \DatePeriod($start, $interval, $end);
                foreach ($range as $d) {
                    $key = $d->format('Y-m-d');
                    if ($key !== $date) {
                        $eventsByDate[$key][] = $event;
                    }
                }
                // include end date too
                $eventsByDate[$event->event_date_end][] = $event;
            }
        }

        $month = (int) $request->get('month', date('n'));
        $year  = (int) $request->get('year',  date('Y'));

        // If openId given, find that event data to auto-open popup
        $openEvent = null;
        if ($openId) {
            $openEvent = $events->firstWhere('id', (int) $openId);
        }

        return view('kalender.index', compact(
            'events', 'eventsByDate', 'categories',
            'view', 'search', 'filter',
            'month', 'year', 'openEvent'
        ));
    }
}