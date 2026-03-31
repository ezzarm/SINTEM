{{-- resources/views/pengumuman/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Pengumuman – SINTEM')

@section('topbar')
    @include('components.topbar')
@endsection

@section('header', 'Pengumuman')
@section('subheader', 'Pengumuman terbaru, laporan aduan, dan unggahan penemuan')

@push('styles')
<style>
    /* ── Layout overrides ── */
    .page-body {
        padding: 0 !important;
        overflow: hidden !important;
        display: flex;
        flex-direction: column;
    }
    .main-content { background: #ffffff !important; }
    .page-header  { padding: 16px 32px 14px !important; background: #ffffff !important; }

    /* ── Outer wrapper ── */
    .pg-wrap {
        flex: 1;
        min-height: 0;
        display: flex;
        flex-direction: column;
        background: #ffffff;
        overflow: hidden;
    }

    /* ── Toolbar: locked ── */
    .pg-toolbar-wrap {
        flex-shrink: 0;
        padding: 14px 32px 12px;
        border-bottom: 1px solid #f0f0f5;
        background: #ffffff;
    }
    .pg-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }
    .pg-toolbar-left { display: flex; align-items: center; gap: 6px; }

    /* ── Custom dropdown ── */
    .pg-dd-custom {
        position: relative;
        display: inline-block;
    }
    .pg-dd-trigger {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 10px;
        border: 1px solid #e5e7eb;
        border-radius: 5px;
        font-size: 12.5px;
        font-family: 'Lato', sans-serif;
        font-weight: 600;
        color: #374151;
        background: #fff;
        cursor: pointer;
        outline: none;
        transition: border-color 0.12s, box-shadow 0.12s;
        white-space: nowrap;
    }
    .pg-dd-trigger:hover { border-color: #c4b5fd; }
    .pg-dd-custom.open .pg-dd-trigger {
        border-color: #7c3aed;
        box-shadow: 0 0 0 2px rgba(124,58,237,0.1);
    }
    .pg-dd-chevron { transition: transform 0.2s ease; }
    .pg-dd-custom.open .pg-dd-chevron { transform: rotate(180deg); }

    .pg-dd-menu {
        display: none;
        position: absolute;
        top: calc(100% + 4px);
        left: 0;
        min-width: 130px;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        z-index: 100;
        overflow: hidden;
        padding: 4px;
    }
    .pg-dd-custom.open .pg-dd-menu {
        display: block;
        animation: ddFadeIn 0.15s ease;
    }
    @keyframes ddFadeIn {
        from { opacity: 0; transform: translateY(-4px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .pg-dd-option {
        display: flex;
        align-items: center;
        gap: 8px;
        width: 100%;
        padding: 7px 10px;
        font-size: 13px;
        font-family: 'Lato', sans-serif;
        font-weight: 500;
        color: #374151;
        background: none;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        text-align: left;
        transition: background 0.1s, color 0.1s;
    }
    .pg-dd-option svg { opacity: 0; flex-shrink: 0; stroke: #7c3aed; }
    .pg-dd-option:hover { background: #f4f0ff; color: #4f28d9; }
    .pg-dd-option.selected { color: #4f28d9; font-weight: 700; }
    .pg-dd-option.selected svg { opacity: 1; }

    /* ── Search ── */
    .pg-search-wrap {
        position: relative;
        display: flex;
        align-items: center;
    }
    .pg-search-wrap .pg-si {
        position: absolute;
        left: 9px;
        color: #b0b0c0;
        pointer-events: none;
        display: flex;
        align-items: center;
    }
    .pg-search {
        padding: 6px 12px 6px 30px;
        border: 1px solid #e5e7eb;
        border-radius: 5px;
        font-size: 12.5px;
        font-family: 'Lato', sans-serif;
        color: #374151;
        background: #fff;
        width: 260px;
        outline: none;
        transition: border-color 0.12s, box-shadow 0.12s;
    }
    .pg-search::placeholder { color: #c4c4cc; }
    .pg-search:focus {
        border-color: #7c3aed;
        box-shadow: 0 0 0 2px rgba(124,58,237,0.1);
    }

    /* ── Columns ── */
    .pg-columns {
        flex: 1;
        min-height: 0;
        display: flex;
        gap: 0;
        overflow: hidden;
    }

    /* LEFT: only this scrolls, no scrollbar */
    .pg-main {
        flex: 1;
        min-width: 0;
        overflow-y: auto;
        padding: 16px 24px 32px 32px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        /* Hide scrollbar */
        scrollbar-width: none;
        -ms-overflow-style: none;
    }
    .pg-main::-webkit-scrollbar { display: none; }

    /* RIGHT: locked */
    .pg-side {
        width: 272px;
        min-width: 272px;
        flex-shrink: 0;
        overflow: hidden;
        padding: 16px 32px 16px 0;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    /* ── Post card ── */
    .pg-post {
        display: flex;
        gap: 12px;
        padding: 16px;
        background: #fff;
        border: 1px solid #ebebf0;
        border-radius: 8px;
        transition: border-color 0.15s;
        flex-shrink: 0;
    }
    .pg-post:hover { border-color: #d4d0f0; }

    .pg-avatar {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: #f4f4f8;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .pg-post-body { flex: 1; min-width: 0; }

    .pg-meta {
        display: flex;
        align-items: center;
        gap: 5px;
        margin-bottom: 5px;
        flex-wrap: wrap;
    }
    .pg-author { font-size: 13px; font-weight: 700; color: #1a1a2e; }
    .pg-dot    { width: 3px; height: 3px; border-radius: 50%; background: #d1d5db; flex-shrink: 0; }
    .pg-time   { font-size: 12px; color: #9ca3af; }

    .pg-badge {
        font-size: 10px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 4px;
        margin-left: 2px;
        letter-spacing: 0.02em;
    }
    .badge-announcement { background: #ede9fe; color: #5b21b6; }
    .badge-event        { background: #dbeafe; color: #1d4ed8; }
    .badge-lost_found   { background: #fef3c7; color: #92400e; }

    .pg-title {
        font-size: 13.5px;
        font-weight: 700;
        color: #1a1a2e;
        margin-bottom: 4px;
        line-height: 1.4;
    }
    .pg-content {
        font-size: 13px;
        color: #4b5563;
        line-height: 1.65;
        white-space: pre-line;
    }
    .pg-image {
        margin-top: 10px;
        border-radius: 6px;
        overflow: hidden;
    }
    .pg-image img {
        width: 100%;
        display: block;
        border-radius: 6px;
        border: 1px solid #f0f0f5;
    }

    /* ── Sidebar widgets ── */
    .pg-widget {
        background: #fff;
        border: 1px solid #ebebf0;
        border-radius: 8px;
        overflow: hidden;
        flex-shrink: 0;
    }
    .pg-widget-head {
        padding: 10px 14px;
        border-bottom: 1px solid #f0f0f5;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .pg-widget-title {
        font-size: 11px;
        font-weight: 700;
        color: #374151;
        letter-spacing: 0.06em;
        text-transform: uppercase;
    }
    .pg-widget-count {
        font-size: 10px;
        font-weight: 700;
        padding: 1px 6px;
        border-radius: 4px;
        margin-left: 2px;
    }
    .pg-widget-list { padding: 4px 0; }

    .pg-side-card {
        display: flex;
        gap: 9px;
        padding: 8px 14px;
        transition: background 0.12s;
    }
    .pg-side-card:hover { background: #fafafa; }

    .pg-side-icon {
        width: 28px;
        height: 28px;
        border-radius: 6px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .icon-event { background: #dbeafe; }
    .icon-lost  { background: #fef3c7; }

    .pg-side-info { flex: 1; min-width: 0; }
    .pg-side-name {
        font-size: 12.5px;
        font-weight: 600;
        color: #1a1a2e;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        line-height: 1.35;
    }
    .pg-side-meta { font-size: 11px; color: #9ca3af; margin-top: 1px; }

    .pg-side-empty {
        padding: 14px;
        font-size: 12px;
        color: #c4c4cc;
        text-align: center;
    }

    /* ── Empty state ── */
    .pg-empty {
        text-align: center;
        padding: 48px 20px;
        color: #9ca3af;
        font-size: 13px;
        background: #fff;
        border: 1px solid #ebebf0;
        border-radius: 8px;
    }
    .pg-empty svg { margin: 0 auto 10px; display: block; }
</style>
@endpush

@section('content')
<div class="pg-wrap">

    {{-- ── TOOLBAR (locked) ── --}}
    <form method="GET" action="{{ route('pengumuman.index') }}" id="filterForm">
        <div class="pg-toolbar-wrap">
            <div class="pg-toolbar">

                <div class="pg-toolbar-left">

                    {{-- Custom sort dropdown --}}
                    <div class="pg-dd-custom" id="sortDropdown">
                        <button type="button" class="pg-dd-trigger" onclick="toggleSortDD()">
                            <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"/>
                            </svg>
                            <span id="sortLabel">{{ $sort === 'terlama' ? 'Terlama' : 'Terbaru' }}</span>
                            <svg class="pg-dd-chevron" width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div class="pg-dd-menu" id="sortMenu">
                            <button type="button"
                                    class="pg-dd-option {{ $sort === 'terbaru' ? 'selected' : '' }}"
                                    onclick="selectSort('terbaru', 'Terbaru')">
                                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Terbaru
                            </button>
                            <button type="button"
                                    class="pg-dd-option {{ $sort === 'terlama' ? 'selected' : '' }}"
                                    onclick="selectSort('terlama', 'Terlama')">
                                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Terlama
                            </button>
                        </div>

                        <input type="hidden" name="sort" id="sortInput" value="{{ $sort }}">
                    </div>

                </div>

                {{-- Search --}}
                <div class="pg-search-wrap">
                    <span class="pg-si">
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8" stroke-width="2"/>
                            <path d="M21 21l-4.35-4.35" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </span>
                    <input type="text" name="search" class="pg-search"
                        placeholder="Telusuri pengumuman..."
                        value="{{ $search }}"
                        oninput="debounceSearch(this.form)">
                </div>

            </div>
        </div>
    </form>

    {{-- ── COLUMNS ── --}}
    <div class="pg-columns">

        {{-- LEFT: only this scrolls ── --}}
        <div class="pg-main">
            @forelse($items->where('type', 'announcement') as $item)
            <div class="pg-post">
                <div class="pg-avatar">
                    <svg width="16" height="16" fill="none" stroke="#c4c4d4" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21v-2a4 4 0 00-4-4H9a4 4 0 00-4 4v2"/>
                        <circle cx="12" cy="7" r="4" stroke-width="1.8"/>
                    </svg>
                </div>
                <div class="pg-post-body">
                    <div class="pg-meta">
                        <span class="pg-author">{{ $item->author }}</span>
                        <span class="pg-dot"></span>
                        <span class="pg-time">{{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}</span>
                        <span class="pg-badge badge-announcement">Pengumuman</span>
                    </div>
                    @if($item->title)
                    <div class="pg-title">{{ $item->title }}</div>
                    @endif
                    <div class="pg-content">{{ $item->content }}</div>

                    @php $files = $attachmentMap['announcement_' . $item->id] ?? collect(); @endphp
                    @foreach($files as $file)
                        @if(str_starts_with($file->file_type, 'image'))
                        <div class="pg-image">
                            <img src="{{ asset('storage/' . $file->file_path) }}"
                                 alt="{{ $file->file_name }}"
                                 onerror="this.parentElement.style.display='none'">
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
            @empty
            <div class="pg-empty">
                <svg width="36" height="36" fill="none" stroke="#d1d5db" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Belum ada pengumuman.
            </div>
            @endforelse
        </div>

        {{-- RIGHT: locked ── --}}
        <aside class="pg-side">

            {{-- Events --}}
            <div class="pg-widget">
                <div class="pg-widget-head">
                    <svg width="13" height="13" fill="none" stroke="#1d4ed8" viewBox="0 0 24 24">
                        <rect x="3" y="4" width="18" height="18" rx="2" stroke-width="1.8"/>
                        <line x1="16" y1="2" x2="16" y2="6" stroke-width="1.8" stroke-linecap="round"/>
                        <line x1="8"  y1="2" x2="8"  y2="6" stroke-width="1.8" stroke-linecap="round"/>
                        <line x1="3"  y1="10" x2="21" y2="10" stroke-width="1.8"/>
                    </svg>
                    <span class="pg-widget-title">Event</span>
                    <span class="pg-widget-count" style="background:#dbeafe; color:#1d4ed8;">
                        {{ $items->where('type', 'event')->count() }}
                    </span>
                </div>
                <div class="pg-widget-list">
                    @forelse($items->where('type', 'event')->take(5) as $item)
                    <a href="{{ route('kalender.index', ['open' => $item->id]) }}" class="pg-side-card" style="text-decoration:none;">
                        <div class="pg-side-icon icon-event">
                            <svg width="13" height="13" fill="none" stroke="#1d4ed8" viewBox="0 0 24 24">
                                <rect x="3" y="4" width="18" height="18" rx="2" stroke-width="1.8"/>
                                <line x1="16" y1="2" x2="16" y2="6" stroke-width="1.8" stroke-linecap="round"/>
                                <line x1="8"  y1="2" x2="8"  y2="6" stroke-width="1.8" stroke-linecap="round"/>
                                <line x1="3"  y1="10" x2="21" y2="10" stroke-width="1.8"/>
                            </svg>
                        </div>
                        <div class="pg-side-info">
                            <div class="pg-side-name">{{ $item->title }}</div>
                            <div class="pg-side-meta">{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}{{ !empty($item->location_name) ? ' · ' . $item->location_name : '' }}</div>
                        </div>
                    </a>
                    @empty
                    <div class="pg-side-empty">Tidak ada event.</div>
                    @endforelse
                </div>
            </div>

            {{-- Lost & Found --}}
            <div class="pg-widget">
                <div class="pg-widget-head">
                    <svg width="13" height="13" fill="none" stroke="#92400e" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" stroke-width="1.8"/>
                        <line x1="12" y1="8"  x2="12" y2="12" stroke-width="2" stroke-linecap="round"/>
                        <line x1="12" y1="16" x2="12.01" y2="16" stroke-width="2.5" stroke-linecap="round"/>
                    </svg>
                    <span class="pg-widget-title">Lost & Found</span>
                    <span class="pg-widget-count" style="background:#fef3c7; color:#92400e;">
                        {{ $items->where('type', 'lost_found')->count() }}
                    </span>
                </div>
                <div class="pg-widget-list">
                    @forelse($items->where('type', 'lost_found')->take(5) as $item)
                    <div class="pg-side-card">
                        <div class="pg-side-icon icon-lost">
                            <svg width="13" height="13" fill="none" stroke="#92400e" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" stroke-width="1.8"/>
                                <line x1="12" y1="8"  x2="12" y2="12" stroke-width="2" stroke-linecap="round"/>
                                <line x1="12" y1="16" x2="12.01" y2="16" stroke-width="2.5" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <div class="pg-side-info">
                            <div class="pg-side-name">{{ $item->title }}</div>
                            <div class="pg-side-meta">{{ !empty($item->lost_type) ? ($item->lost_type === 'found' ? 'Ditemukan' : 'Hilang') . ' · ' : '' }}{{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}{{ !empty($item->found_at) ? ' · ' . $item->found_at : '' }}</div>                        </div>
                    </div>
                    @empty
                    <div class="pg-side-empty">Tidak ada laporan temuan.</div>
                    @endforelse
                </div>
            </div>

        </aside>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // ── Debounced search ──
    let searchTimer;
    function debounceSearch(form) {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => form.submit(), 500);
    }

    // ── Custom sort dropdown ──
    function toggleSortDD() {
        document.getElementById('sortDropdown').classList.toggle('open');
    }

    function selectSort(value, label) {
        document.getElementById('sortInput').value = value;
        document.getElementById('sortLabel').textContent = label;

        // Update selected state visually
        document.querySelectorAll('.pg-dd-option').forEach(opt => {
            opt.classList.toggle('selected', opt.textContent.trim() === label);
        });

        document.getElementById('sortDropdown').classList.remove('open');
        document.getElementById('filterForm').submit();
    }

    // Close when clicking outside
    document.addEventListener('click', e => {
        const dd = document.getElementById('sortDropdown');
        if (dd && !dd.contains(e.target)) dd.classList.remove('open');
    });
</script>
@endpush