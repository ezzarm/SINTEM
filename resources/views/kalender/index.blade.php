{{-- resources/views/kalender/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Kalender Kegiatan – SINTEM')

@section('topbar')
<div style="display:flex; align-items:center; justify-content:space-between; padding: 14px 32px; border-bottom: 1px solid #f0f0f5; background:#ffffff;">
    <p style="font-size:13.5px; font-weight:700; color:#1a1a2e;">
        Selamat Datang, {{ Auth::user()->name ?? 'Pengguna' }}! 
    </p>
    <a href="{{ route('laporan.buat') }}" class="btn-laporkan">
        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M11 4H6a2 2 0 00-2 2v13a2 2 0 002 2h11a2 2 0 002-2v-5"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
        </svg>
        Laporkan
    </a>
</div>
@endsection

@section('header', 'Kalender Kegiatan')
@section('subheader', 'Informasi kegiatan di lingkungan sekolah.')

@push('styles')
<style>
    /* ── Layout overrides ── */
    .page-body {
        padding: 0 !important;
        overflow: hidden !important;
        display: flex; flex-direction: column;
    }
    .main-content { background: #ffffff !important; }
    .page-header  { padding: 16px 32px 14px !important; background: #ffffff !important; }

    .btn-laporkan {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 8px 16px;
        background: linear-gradient(135deg, #9025FB, #4617D3);
        color: #fff; font-size: 13px; font-weight: 700;
        font-family: 'Lato', sans-serif; border-radius: 6px;
        text-decoration: none; box-shadow: 0 2px 8px rgba(109,40,217,0.2);
        transition: opacity 0.15s, transform 0.15s;
    }
    .btn-laporkan:hover { opacity: 0.88; transform: translateY(-1px); }

    .kl-wrap {
        flex: 1; min-height: 0;
        display: flex; flex-direction: column;
        background: #ffffff; overflow: hidden;
    }

    /* ── Toolbar ── */
    .kl-toolbar {
        flex-shrink: 0;
        padding: 14px 32px 12px;
        border-bottom: 1px solid #f0f0f5;
        background: #ffffff;
        display: flex; align-items: center;
        justify-content: space-between; gap: 8px; flex-wrap: wrap;
    }
    .kl-toolbar-left  { display: flex; align-items: center; gap: 8px; }
    .kl-toolbar-right { display: flex; align-items: center; gap: 8px; }

    /* ── Custom filter dropdown (same style as pengumuman) ── */
    .kl-dd-custom {
        position: relative;
        display: inline-block;
    }
    .kl-dd-trigger {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 6px 10px;
        border: 1px solid #e5e7eb; border-radius: 5px;
        font-size: 12.5px; font-family: 'Lato', sans-serif;
        font-weight: 600; color: #374151; background: #fff;
        cursor: pointer; outline: none;
        transition: border-color 0.12s, box-shadow 0.12s;
        white-space: nowrap;
    }
    .kl-dd-trigger:hover { border-color: #c4b5fd; }
    .kl-dd-custom.open .kl-dd-trigger {
        border-color: #7c3aed;
        box-shadow: 0 0 0 2px rgba(124,58,237,0.1);
    }
    .kl-dd-chevron { transition: transform 0.2s ease; }
    .kl-dd-custom.open .kl-dd-chevron { transform: rotate(180deg); }

    .kl-dd-menu {
        display: none;
        position: absolute;
        top: calc(100% + 4px); left: 0;
        min-width: 150px;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        z-index: 100; overflow: hidden; padding: 4px;
    }
    .kl-dd-custom.open .kl-dd-menu {
        display: block;
        animation: ddFadeIn 0.15s ease;
    }
    @keyframes ddFadeIn {
        from { opacity: 0; transform: translateY(-4px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .kl-dd-option {
        display: flex; align-items: center; gap: 8px;
        width: 100%; padding: 7px 10px;
        font-size: 13px; font-family: 'Lato', sans-serif;
        font-weight: 500; color: #374151;
        background: none; border: none; border-radius: 4px;
        cursor: pointer; text-align: left;
        transition: background 0.1s, color 0.1s;
    }
    .kl-dd-option svg { opacity: 0; flex-shrink: 0; stroke: #7c3aed; }
    .kl-dd-option:hover { background: #f4f0ff; color: #4f28d9; }
    .kl-dd-option.selected { color: #4f28d9; font-weight: 700; }
    .kl-dd-option.selected svg { opacity: 1; }

    /* Search */
    .kl-search-wrap { position: relative; display: flex; align-items: center; }
    .kl-search-wrap .kl-si { position: absolute; left: 9px; color: #b0b0c0; pointer-events: none; display: flex; align-items: center; }
    .kl-search {
        padding: 6px 12px 6px 30px; border: 1px solid #e5e7eb;
        border-radius: 5px; font-size: 12.5px; font-family: 'Lato', sans-serif;
        color: #374151; background: #fff; width: 220px; outline: none;
        transition: border-color 0.12s, box-shadow 0.12s;
    }
    .kl-search::placeholder { color: #c4c4cc; }
    .kl-search:focus { border-color: #7c3aed; box-shadow: 0 0 0 2px rgba(124,58,237,0.1); }

    /* View toggle */
    .kl-view-toggle {
        display: flex; align-items: center;
        border: 1px solid #e5e7eb; border-radius: 6px; overflow: hidden;
    }
    .kl-view-btn {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 6px 12px;
        font-size: 12.5px; font-family: 'Lato', sans-serif;
        font-weight: 600; color: #6b7280;
        background: #fff; border: none; cursor: pointer;
        transition: background 0.12s, color 0.12s;
        text-decoration: none;
    }
    .kl-view-btn:not(:last-child) { border-right: 1px solid #e5e7eb; }
    .kl-view-btn:hover:not(.active) { background: #f4f0ff; color: #4f28d9; }
    .kl-view-btn.active {
        background: #ede9fe; color: #4f28d9;
        pointer-events: none; cursor: default;
    }

    /* ── Scrollable body ── */
    .kl-body {
        flex: 1; min-height: 0;
        overflow-y: auto; padding: 24px 32px 40px;
        scrollbar-width: none; -ms-overflow-style: none;
    }
    .kl-body::-webkit-scrollbar { display: none; }

    /* ══ CARD VIEW ══ */
    .kl-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }
    .kl-card {
        border: 1px solid #ebebf0; border-radius: 10px;
        overflow: hidden; background: #fff;
        cursor: pointer;
        transition: border-color 0.15s, box-shadow 0.15s;
        display: flex; flex-direction: row;
    }
    .kl-card:hover { border-color: #c4b5fd; box-shadow: 0 2px 12px rgba(109,40,217,0.08); }

    .kl-card-img {
        width: 180px; min-width: 180px; min-height: 160px;
        background: #f4f4f8; overflow: hidden; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
    }
    .kl-card-img img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .kl-card-img-placeholder { font-size: 36px; }

    .kl-card-body { padding: 16px; flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 6px; }
    .kl-card-title { font-size: 14.5px; font-weight: 700; color: #1a1a2e; line-height: 1.35; }
    .kl-card-date  { font-size: 12px; color: #6b7280; display: flex; align-items: center; gap: 5px; }
    .kl-card-desc  {
        font-size: 12.5px; color: #6b7280; line-height: 1.55; flex: 1;
        display: -webkit-box; -webkit-line-clamp: 3;
        -webkit-box-orient: vertical; overflow: hidden;
    }
    .kl-card-footer { display: flex; align-items: center; justify-content: space-between; margin-top: 4px; }
    .kl-badge {
        font-size: 11px; font-weight: 700;
        padding: 2px 10px; border-radius: 999px; border: 1.5px solid;
    }
    .kl-detail-link { font-size: 12px; font-weight: 700; color: #7c3aed; text-decoration: none; transition: color 0.12s; }
    .kl-detail-link:hover { color: #4f28d9; }

    .kl-empty {
        grid-column: 1 / -1; text-align: center;
        padding: 60px 20px; color: #9ca3af; font-size: 13px;
    }
    .kl-empty svg { margin: 0 auto 12px; display: block; }

    /* ══ CALENDAR VIEW ══ */
    .kl-cal-nav {
        display: flex; align-items: center;
        justify-content: space-between; margin-bottom: 16px;
    }
    .kl-cal-title { font-size: 15px; font-weight: 700; color: #1a1a2e; }
    .kl-cal-nav-btn {
        display: inline-flex; align-items: center; justify-content: center;
        width: 30px; height: 30px; border-radius: 6px;
        border: 1px solid #e5e7eb; background: #fff;
        cursor: pointer; color: #374151;
        text-decoration: none; font-size: 16px; font-weight: 500;
        transition: background 0.12s, border-color 0.12s;
        line-height: 1;
    }
    .kl-cal-nav-btn:hover { background: #f4f0ff; border-color: #c4b5fd; color: #4f28d9; }

    .kl-cal-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        border: 1px solid #ebebf0;
        border-radius: 8px;
        overflow: hidden;
        /* Force equal column widths */
        width: 100%;
        table-layout: fixed;
    }

    .kl-cal-head {
        background: #f9f9fb; padding: 10px 4px;
        text-align: center; font-size: 12px;
        font-weight: 700; color: #6b7280;
        border-bottom: 1px solid #ebebf0;
    }

    .kl-cal-cell {
        /* Fixed equal height for all cells */
        height: 110px;
        padding: 8px;
        border-right: 1px solid #f0f0f5;
        border-bottom: 1px solid #f0f0f5;
        background: #fff;
        vertical-align: top;
        overflow: hidden;
        box-sizing: border-box;
    }
    .kl-cal-cell:nth-child(7n) { border-right: none; }
    /* Remove bottom border from last row */
    .kl-cal-cell.last-row { border-bottom: none; }
    .kl-cal-cell.has-event { cursor: pointer; }
    .kl-cal-cell.has-event:hover { background: #faf8ff; }
    .kl-cal-cell.today { background: #faf8ff; }
    .kl-cal-cell.other-month { background: #fafafa; }

    .kl-cal-day {
        font-size: 12.5px; font-weight: 600; color: #374151;
        margin-bottom: 5px; text-align: right;
        display: flex; justify-content: flex-end;
    }
    .kl-cal-day-num {
        width: 24px; height: 24px;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 50%; line-height: 1;
    }
    .kl-cal-cell.today .kl-cal-day-num {
        background: linear-gradient(135deg, #9025FB, #4617D3);
        color: #fff;
    }
    .kl-cal-cell.other-month .kl-cal-day-num { color: #c4c4cc; }

    .kl-cal-event-dot {
        display: flex; align-items: center; gap: 4px;
        margin-bottom: 3px; cursor: pointer;
        padding: 1px 2px; border-radius: 3px;
        transition: background 0.1s;
    }
    .kl-cal-event-dot:hover { background: #ede9fe; }
    .kl-cal-event-dot .dot {
        width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0;
    }
    .kl-cal-event-dot .label {
        font-size: 10.5px; font-weight: 600; color: #374151;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        max-width: 100%; flex: 1; min-width: 0;
    }
    .kl-cal-more { font-size: 10px; color: #9ca3af; font-weight: 600; margin-top: 2px; padding-left: 2px; }

    /* ══ POPUP MODAL ══ */
    .kl-overlay {
        display: none;
        position: fixed; inset: 0; z-index: 200;
        background: rgba(0,0,0,0.35);
        align-items: center; justify-content: center;
        padding: 24px;
    }
    .kl-overlay.open { display: flex; animation: fadeOverlay 0.18s ease; }
    @keyframes fadeOverlay { from { opacity: 0; } to { opacity: 1; } }

    .kl-modal {
        background: #fff; border-radius: 12px;
        width: 100%; max-width: 480px;
        max-height: 90vh; overflow-y: auto;
        box-shadow: 0 20px 60px rgba(0,0,0,0.18);
        animation: slideModal 0.2s cubic-bezier(0.22,1,0.36,1);
        scrollbar-width: none; -ms-overflow-style: none;
        position: relative;
    }
    .kl-modal::-webkit-scrollbar { display: none; }
    @keyframes slideModal {
        from { opacity: 0; transform: translateY(16px) scale(0.98); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }

    /* Close button — top right of modal */
    .kl-modal-close {
        position: absolute;
        top: 12px; right: 12px; z-index: 10;
        width: 28px; height: 28px; border-radius: 6px;
        border: 1px solid rgba(255,255,255,0.3);
        background: rgba(0,0,0,0.25);
        cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        color: #fff; transition: background 0.12s;
    }
    .kl-modal-close:hover { background: rgba(0,0,0,0.45); }

    .kl-modal-img {
        width: 100%; height: 200px; overflow: hidden;
        border-radius: 12px 12px 0 0;
        background: #f4f4f8;
        display: flex; align-items: center; justify-content: center;
        font-size: 48px; flex-shrink: 0;
    }
    .kl-modal-img img { width: 100%; height: 100%; object-fit: cover; display: block; }

    .kl-modal-body { padding: 20px; }

    .kl-modal-top {
        display: flex; align-items: flex-start;
        justify-content: space-between; gap: 12px;
        margin-bottom: 10px;
    }
    .kl-modal-badge-wrap { flex: 1; }
    .kl-modal-title {
        font-size: 16px; font-weight: 700; color: #1a1a2e;
        line-height: 1.35; margin-top: 10px;
    }

    .kl-modal-meta {
        display: flex; flex-direction: column; gap: 8px;
        margin-bottom: 16px;
    }
    .kl-modal-meta-row {
        display: flex; align-items: center; gap: 10px;
        font-size: 13px; color: #374151;
    }
    .kl-modal-meta-icon {
        width: 28px; height: 28px; border-radius: 6px;
        background: #f4f4f8; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
    }
    .kl-modal-meta-icon svg { stroke: #6b7280; }
    .kl-modal-meta-text { flex: 1; line-height: 1.3; }
    .kl-modal-meta-label { font-size: 10.5px; color: #9ca3af; font-weight: 600; letter-spacing: 0.04em; text-transform: uppercase; }
    .kl-modal-meta-value { font-size: 13px; color: #1a1a2e; font-weight: 600; margin-top: 1px; }

    .kl-modal-divider { height: 1px; background: #f0f0f5; margin-bottom: 14px; }

    .kl-modal-desc {
        font-size: 13px; color: #4b5563; line-height: 1.7;
        white-space: pre-line;
    }
</style>
@endpush

@section('content')
<div class="kl-wrap">

    {{-- ── TOOLBAR ── --}}
    <form method="GET" action="{{ route('kalender.index') }}" id="kalenderForm">
        <input type="hidden" name="view"  id="viewInput" value="{{ $view }}">
        <input type="hidden" name="month" value="{{ $month }}">
        <input type="hidden" name="year"  value="{{ $year }}">
        <input type="hidden" name="filter" id="filterInput" value="{{ $filter }}">

        <div class="kl-toolbar">
            <div class="kl-toolbar-left">

                {{-- Category custom dropdown ── --}}
                <div class="kl-dd-custom" id="filterDropdown">
                    <button type="button" class="kl-dd-trigger" onclick="toggleFilterDD()">
                        <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                        </svg>
                        <span id="filterLabel">{{ $filter === 'semua' ? 'Semua' : $filter }}</span>
                        <svg class="kl-dd-chevron" width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="kl-dd-menu" id="filterMenu">
                        <button type="button" class="kl-dd-option {{ $filter === 'semua' ? 'selected' : '' }}"
                                onclick="selectFilter('semua', 'Semua')">
                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Semua
                        </button>
                        @foreach($categories as $cat)
                        <button type="button" class="kl-dd-option {{ $filter === $cat->name ? 'selected' : '' }}"
                                onclick="selectFilter('{{ $cat->name }}', '{{ $cat->name }}')">
                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ $cat->name }}
                        </button>
                        @endforeach
                    </div>
                </div>

                {{-- Search ── --}}
                <div class="kl-search-wrap">
                    <span class="kl-si">
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8" stroke-width="2"/>
                            <path d="M21 21l-4.35-4.35" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </span>
                    <input type="text" name="search" class="kl-search"
                        placeholder="Cari kegiatan..."
                        value="{{ $search }}"
                        oninput="debounceSearch(this.form)">
                </div>

            </div>

            {{-- View toggle ── --}}
            <div class="kl-toolbar-right">
                <div class="kl-view-toggle">
                    <a href="{{ route('kalender.index', array_merge(request()->except('view'), ['view' => 'card'])) }}"
                       class="kl-view-btn {{ $view === 'card' ? 'active' : '' }}">
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <rect x="3" y="3" width="7" height="7" rx="1" stroke-width="1.8"/>
                            <rect x="14" y="3" width="7" height="7" rx="1" stroke-width="1.8"/>
                            <rect x="3" y="14" width="7" height="7" rx="1" stroke-width="1.8"/>
                            <rect x="14" y="14" width="7" height="7" rx="1" stroke-width="1.8"/>
                        </svg>
                        Card
                    </a>
                    <a href="{{ route('kalender.index', array_merge(request()->except('view'), ['view' => 'calendar'])) }}"
                       class="kl-view-btn {{ $view === 'calendar' ? 'active' : '' }}">
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2" stroke-width="1.8"/>
                            <line x1="16" y1="2" x2="16" y2="6" stroke-width="1.8" stroke-linecap="round"/>
                            <line x1="8"  y1="2" x2="8"  y2="6" stroke-width="1.8" stroke-linecap="round"/>
                            <line x1="3"  y1="10" x2="21" y2="10" stroke-width="1.8"/>
                        </svg>
                        Kalender
                    </a>
                </div>
            </div>
        </div>
    </form>

    {{-- ── BODY ── --}}
    <div class="kl-body">

        {{-- ══ CARD VIEW ══ --}}
        @if($view === 'card')
        <div class="kl-grid">
            @forelse($events as $event)
            @php
                $photo   = $event->photos->first();
                $color   = $event->category_color ?? '#9025FB';
                $dateStr = \Carbon\Carbon::parse($event->event_date)->locale('id')->isoFormat('dddd, D MMMM YYYY');
                if ($event->event_date_end && $event->event_date_end !== $event->event_date) {
                    $dateStr = \Carbon\Carbon::parse($event->event_date)->locale('id')->isoFormat('ddd, D MMM')
                             . ' – '
                             . \Carbon\Carbon::parse($event->event_date_end)->locale('id')->isoFormat('ddd, D MMM YYYY');
                }
                $eventData = [
                    'id'             => $event->id,
                    'event_name'     => $event->event_name,
                    'description'    => $event->description,
                    'event_date'     => $event->event_date,
                    'event_date_end' => $event->event_date_end,
                    'category_name'  => $event->category_name,
                    'category_color' => $event->category_color,
                    'location_name'  => $event->location_name,
                    'author'         => $event->author,
                    'photo'          => $photo ? asset('storage/' . $photo->file_path) : null,
                    'date_str'       => $dateStr,
                ];
            @endphp
            <div class="kl-card" onclick='openModal(@json($eventData))'>
                <div class="kl-card-img">
                    @if($photo)
                    <img src="{{ asset('storage/' . $photo->file_path) }}" alt="{{ $event->event_name }}"
                         onerror="this.parentElement.innerHTML='<div class=kl-card-img-placeholder>📅</div>'">
                    @else
                    <div class="kl-card-img-placeholder">📅</div>
                    @endif
                </div>
                <div class="kl-card-body">
                    <div class="kl-card-title">{{ $event->event_name }}</div>
                    <div class="kl-card-date">
                        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2" stroke-width="1.8"/>
                            <line x1="16" y1="2" x2="16" y2="6" stroke-width="1.8" stroke-linecap="round"/>
                            <line x1="8"  y1="2" x2="8"  y2="6" stroke-width="1.8" stroke-linecap="round"/>
                            <line x1="3"  y1="10" x2="21" y2="10" stroke-width="1.8"/>
                        </svg>
                        {{ $dateStr }}
                    </div>
                    @if($event->description)
                    <div class="kl-card-desc">{{ $event->description }}</div>
                    @endif
                    <div class="kl-card-footer">
                        <span class="kl-badge" style="color:{{ $color }}; border-color:{{ $color }}; background:{{ $color }}18;">
                            {{ $event->category_name }}
                        </span>
                        <span class="kl-detail-link">Lihat detail →</span>
                    </div>
                </div>
            </div>
            @empty
            <div class="kl-empty">
                <svg width="36" height="36" fill="none" stroke="#d1d5db" viewBox="0 0 24 24">
                    <rect x="3" y="4" width="18" height="18" rx="2" stroke-width="1.4"/>
                    <line x1="3" y1="10" x2="21" y2="10" stroke-width="1.4"/>
                </svg>
                Tidak ada kegiatan ditemukan.
            </div>
            @endforelse
        </div>
        @endif

        {{-- ══ CALENDAR VIEW ══ --}}
        @if($view === 'calendar')
        @php
            $prevMonth  = $month - 1 < 1  ? 12 : $month - 1;
            $prevYear   = $month - 1 < 1  ? $year - 1 : $year;
            $nextMonth  = $month + 1 > 12 ? 1  : $month + 1;
            $nextYear   = $month + 1 > 12 ? $year + 1 : $year;
            $monthNames = ['','Januari','Februari','Maret','April','Mei','Juni',
                           'Juli','Agustus','September','Oktober','November','Desember'];
            $dayNames   = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];
            $firstDay   = (int) date('w', mktime(0,0,0,$month,1,$year));
            $daysInMonth= (int) date('t', mktime(0,0,0,$month,1,$year));
            $today      = date('Y-m-d');
            // total cells = empty prefix + days, rounded up to multiple of 7
            $totalCells = $firstDay + $daysInMonth;
            $trailingCells = (7 - ($totalCells % 7)) % 7;
            $lastRowStart = $firstDay + $daysInMonth - (($firstDay + $daysInMonth - 1) % 7) - 6;
        @endphp

        <div class="kl-cal-nav">
            <a href="{{ route('kalender.index', array_merge(request()->except(['month','year']), ['month' => $prevMonth, 'year' => $prevYear])) }}"
               class="kl-cal-nav-btn">‹</a>
            <div class="kl-cal-title">{{ $monthNames[$month] }} {{ $year }}</div>
            <a href="{{ route('kalender.index', array_merge(request()->except(['month','year']), ['month' => $nextMonth, 'year' => $nextYear])) }}"
               class="kl-cal-nav-btn">›</a>
        </div>

        <div class="kl-cal-grid">
            {{-- Day headers --}}
            @foreach($dayNames as $d)
            <div class="kl-cal-head">{{ $d }}</div>
            @endforeach

            {{-- Leading empty cells --}}
            @for($i = 0; $i < $firstDay; $i++)
            <div class="kl-cal-cell other-month"></div>
            @endfor

            {{-- Day cells --}}
            @for($day = 1; $day <= $daysInMonth; $day++)
            @php
                $dateKey   = sprintf('%04d-%02d-%02d', $year, $month, $day);
                $dayEvents = $eventsByDate[$dateKey] ?? [];
                $isToday   = $dateKey === $today;
                $hasEvent  = count($dayEvents) > 0;
                $isLastRow = ($firstDay + $day - 1) >= ($totalCells - 7);
            @endphp
            <div class="kl-cal-cell {{ $isToday ? 'today' : '' }} {{ $hasEvent ? 'has-event' : '' }} {{ $isLastRow ? 'last-row' : '' }}">
                <div class="kl-cal-day">
                    <span class="kl-cal-day-num">{{ $day }}</span>
                </div>

                @foreach(array_slice($dayEvents, 0, 3) as $ev)
                @php
                    $evDateStr = \Carbon\Carbon::parse($ev->event_date)->locale('id')->isoFormat('dddd, D MMMM YYYY');
                    if ($ev->event_date_end && $ev->event_date_end !== $ev->event_date) {
                        $evDateStr = \Carbon\Carbon::parse($ev->event_date)->locale('id')->isoFormat('ddd, D MMM')
                                   . ' – '
                                   . \Carbon\Carbon::parse($ev->event_date_end)->locale('id')->isoFormat('ddd, D MMM YYYY');
                    }
                    $firstPhoto = $ev->photos->first();
                    $evData = [
                        'id'             => $ev->id,
                        'event_name'     => $ev->event_name,
                        'description'    => $ev->description,
                        'event_date'     => $ev->event_date,
                        'event_date_end' => $ev->event_date_end,
                        'category_name'  => $ev->category_name,
                        'category_color' => $ev->category_color,
                        'location_name'  => $ev->location_name ?? null,
                        'author'         => $ev->author,
                        'photo'          => $firstPhoto ? asset('storage/' . $firstPhoto->file_path) : null,
                        'date_str'       => $evDateStr,
                    ];
                @endphp
                <div class="kl-cal-event-dot" onclick='openModal(@json($evData)); event.stopPropagation();'>
                    <span class="dot" style="background:{{ $ev->category_color ?? '#9025FB' }};"></span>
                    <span class="label">{{ $ev->event_name }}</span>
                </div>
                @endforeach

                @if(count($dayEvents) > 3)
                <div class="kl-cal-more">+{{ count($dayEvents) - 3 }} lagi</div>
                @endif
            </div>
            @endfor

            {{-- Trailing empty cells ── --}}
            @for($i = 0; $i < $trailingCells; $i++)
            <div class="kl-cal-cell other-month last-row"></div>
            @endfor

        </div>
        @endif

    </div>
</div>

{{-- ══ POPUP MODAL ══ --}}
<div class="kl-overlay" id="klOverlay" onclick="closeModal(event)">
    <div class="kl-modal" id="klModal">

        {{-- Close button top-right of modal (over image) ── --}}
        <button type="button" class="kl-modal-close" onclick="closeModalBtn()">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        <div class="kl-modal-img" id="modalImg">
            <span>📅</span>
        </div>

        <div class="kl-modal-body">

            {{-- Badge ── --}}
            <div id="modalBadge" style="margin-bottom:8px;"></div>

            {{-- Title ── --}}
            <div class="kl-modal-title" id="modalTitle">—</div>

            <div style="height:14px;"></div>

            {{-- Meta rows ── --}}
            <div class="kl-modal-meta">
                {{-- Date ── --}}
                <div class="kl-modal-meta-row">
                    <div class="kl-modal-meta-icon">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2" stroke-width="1.8"/>
                            <line x1="16" y1="2" x2="16" y2="6" stroke-width="1.8" stroke-linecap="round"/>
                            <line x1="8"  y1="2" x2="8"  y2="6" stroke-width="1.8" stroke-linecap="round"/>
                            <line x1="3"  y1="10" x2="21" y2="10" stroke-width="1.8"/>
                        </svg>
                    </div>
                    <div class="kl-modal-meta-text">
                        <div class="kl-modal-meta-label">Tanggal</div>
                        <div class="kl-modal-meta-value" id="modalDate">—</div>
                    </div>
                </div>

                {{-- Location ── --}}
                <div class="kl-modal-meta-row" id="modalLocationRow">
                    <div class="kl-modal-meta-icon">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 2C8.134 2 5 5.134 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.866-3.134-7-7-7z"/>
                            <circle cx="12" cy="9" r="2.5" stroke-width="1.8"/>
                        </svg>
                    </div>
                    <div class="kl-modal-meta-text">
                        <div class="kl-modal-meta-label">Lokasi</div>
                        <div class="kl-modal-meta-value" id="modalLocation">—</div>
                    </div>
                </div>
            </div>

            <div class="kl-modal-divider"></div>

            {{-- Description ── --}}
            <div class="kl-modal-desc" id="modalDesc">—</div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // ── Debounce search ──
    let searchTimer;
    function debounceSearch(form) {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => form.submit(), 500);
    }

    // ── Call Custom filter dropdown ──
    function toggleFilterDD() {
        document.getElementById('filterDropdown').classList.toggle('open');
    }
    function selectFilter(value, label) {
        document.getElementById('filterInput').value = value;
        document.getElementById('kalenderForm').submit();
    }

    window.addEventListener('click', function(e) {
        const dd = document.getElementById('filterDropdown');
        if (dd && !dd.contains(e.target)) {
            dd.classList.remove('open');
        }
    });

    // ── MODAL LOGIC ──
    const overlay = document.getElementById('klOverlay');
    const modal   = document.getElementById('klModal');

    function openModal(data) {
        if (!data) return;

        // Fill data
        document.getElementById('modalTitle').innerText = data.event_name;
        document.getElementById('modalDate').innerText  = data.date_str;
        document.getElementById('modalDesc').innerText  = data.description || 'Tidak ada deskripsi.';
        
        // Location logic
        const locRow = document.getElementById('modalLocationRow');
        if (data.location_name) {
            locRow.style.display = 'flex';
            document.getElementById('modalLocation').innerText = data.location_name;
        } else {
            locRow.style.display = 'none';
        }

        // Image logic
        const imgDiv = document.getElementById('modalImg');
        if (data.photo) {
            imgDiv.innerHTML = `<img src="${data.photo}" alt="${data.event_name}">`;
        } else {
            imgDiv.innerHTML = `<span>📅</span>`;
        }

        // Badge logic
        const badgeDiv = document.getElementById('modalBadge');
        const color = data.category_color || '#9025FB';
        badgeDiv.innerHTML = `
            <span class="kl-badge" style="color:${color}; border-color:${color}; background:${color}18;">
                ${data.category_name}
            </span>
        `;

        // Show overlay
        overlay.classList.add('open');
        document.body.style.overflow = 'hidden';

        const url = new URL(window.location);
        if (url.searchParams.has('open')) {
            url.searchParams.delete('open');
            window.history.replaceState({}, document.title, url.pathname + url.search);
        }
    }

    function closeModalBtn() {
        overlay.classList.remove('open');
        document.body.style.overflow = '';
    }

    function closeModal(e) {
        if (e.target === overlay) closeModalBtn();
    }

    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && overlay.classList.contains('open')) closeModalBtn();
    });

    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const openId = urlParams.get('open');

        if (openId) {
            const targetCard = document.querySelector(`[onclick*='"id":${openId}']`) || 
                               document.querySelector(`[onclick*='"id": "${openId}"']`);
            
            if (targetCard) {
                targetCard.click();
            }
        }
    });
</script>
@endpush