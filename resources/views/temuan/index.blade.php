{{-- resources/views/temuan/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Informasi Temuan – SINTEM')

@section('topbar')
    @include('components.topbar')
@endsection

@section('header', 'Informasi Temuan')
@section('subheader', 'Informasi temuan barang di lingkungan sekolah.')

@push('styles')
<style>
    .page-body { padding:0!important; overflow:hidden!important; display:flex; flex-direction:column; }
    .main-content { background:#ffffff!important; }
    .page-header  { padding:16px 32px 14px!important; background:#ffffff!important; }

    .pg-wrap { flex:1; min-height:0; display:flex; flex-direction:column; background:#ffffff; overflow:hidden; }

    /* Toolbar */
    .pg-toolbar-wrap { flex-shrink:0; padding:14px 32px 12px; border-bottom:1px solid #f0f0f5; background:#ffffff; }
    .pg-toolbar { display:flex; align-items:center; justify-content:space-between; gap:8px; }
    .pg-toolbar-left { display:flex; align-items:center; gap:6px; }

    /* Dropdowns */
    .pg-dd-custom { position:relative; display:inline-block; }
    .pg-dd-trigger {
        display:inline-flex; align-items:center; gap:6px;
        padding:6px 10px; border:1px solid #e5e7eb; border-radius:5px;
        font-size:12.5px; font-family:'Lato',sans-serif; font-weight:600;
        color:#374151; background:#fff; cursor:pointer; outline:none;
        transition:border-color 0.12s,box-shadow 0.12s; white-space:nowrap;
    }
    .pg-dd-trigger:hover { border-color:#c4b5fd; }
    .pg-dd-custom.open .pg-dd-trigger { border-color:#7c3aed; box-shadow:0 0 0 2px rgba(124,58,237,0.1); }
    .pg-dd-chevron { transition:transform 0.2s ease; }
    .pg-dd-custom.open .pg-dd-chevron { transform:rotate(180deg); }
    .pg-dd-menu {
        display:none; position:absolute; top:calc(100% + 4px); left:0;
        min-width:130px; background:#fff; border:1px solid #e5e7eb;
        border-radius:6px; box-shadow:0 4px 16px rgba(0,0,0,0.08); z-index:100; padding:4px;
    }
    .pg-dd-custom.open .pg-dd-menu { display:block; animation:ddFadeIn 0.15s ease; }
    @keyframes ddFadeIn { from{opacity:0;transform:translateY(-4px)} to{opacity:1;transform:translateY(0)} }
    .pg-dd-option {
        display:flex; align-items:center; gap:8px; width:100%;
        padding:7px 10px; font-size:13px; font-family:'Lato',sans-serif;
        font-weight:500; color:#374151; background:none; border:none;
        border-radius:4px; cursor:pointer; text-align:left; transition:background 0.1s,color 0.1s;
    }
    .pg-dd-option svg { opacity:0; flex-shrink:0; stroke:#7c3aed; }
    .pg-dd-option:hover { background:#f4f0ff; color:#4f28d9; }
    .pg-dd-option.selected { color:#4f28d9; font-weight:700; }
    .pg-dd-option.selected svg { opacity:1; }

    /* Search */
    .pg-search-wrap { position:relative; display:flex; align-items:center; }
    .pg-search-wrap .pg-si { position:absolute; left:9px; color:#b0b0c0; pointer-events:none; display:flex; align-items:center; }
    .pg-search {
        padding:6px 12px 6px 30px; border:1px solid #e5e7eb; border-radius:5px;
        font-size:12.5px; font-family:'Lato',sans-serif; color:#374151; background:#fff;
        width:260px; outline:none; transition:border-color 0.12s,box-shadow 0.12s;
    }
    .pg-search::placeholder { color:#c4c4cc; }
    .pg-search:focus { border-color:#7c3aed; box-shadow:0 0 0 2px rgba(124,58,237,0.1); }

    /* Columns */
    .pg-columns { flex:1; min-height:0; display:flex; gap:0; overflow:hidden; }
    .pg-main {
        flex:1; min-width:0; overflow-y:auto;
        padding:16px 24px 32px 32px; display:flex; flex-direction:column; gap:8px;
        scrollbar-width:none; -ms-overflow-style:none;
    }
    .pg-main::-webkit-scrollbar { display:none; }
    .pg-side {
        width:272px; min-width:272px; flex-shrink:0;
        overflow:hidden; padding:16px 32px 16px 0;
        display:flex; flex-direction:column; gap:12px;
    }

    /* Feed post */
    .pg-post {
        display:flex; gap:12px; padding:16px;
        background:#fff; border:1px solid #ebebf0; border-radius:8px;
        transition:border-color 0.15s; flex-shrink:0;
    }
    .pg-post:hover { border-color:#d4d0f0; }
    .pg-avatar {
        width:34px; height:34px; border-radius:50%; background:#f4f4f8;
        flex-shrink:0; display:flex; align-items:center; justify-content:center;
    }
    .pg-post-body { flex:1; min-width:0; }
    .pg-meta { display:flex; align-items:center; gap:5px; margin-bottom:5px; flex-wrap:wrap; }
    .pg-author { font-size:13px; font-weight:700; color:#1a1a2e; }
    .pg-dot    { width:3px; height:3px; border-radius:50%; background:#d1d5db; flex-shrink:0; }
    .pg-time   { font-size:12px; color:#9ca3af; }
    .pg-badge  { font-size:10px; font-weight:700; padding:2px 8px; border-radius:4px; margin-left:2px; letter-spacing:0.02em; }
    .badge-temuan     { background:#fef3c7; color:#92400e; }
    .badge-kehilangan { background:#fce7f3; color:#9d174d; }
    .pg-title { font-size:13.5px; font-weight:700; color:#1a1a2e; margin-bottom:4px; line-height:1.4; }
    .pg-content { font-size:13px; color:#4b5563; line-height:1.65; white-space:pre-line; }
    .pg-location {
        display:flex; align-items:center; gap:4px; margin-top:6px;
        font-size:12px; color:#9ca3af; flex-wrap:wrap;
    }
    .pg-image { margin-top:10px; border-radius:6px; overflow:hidden; }
    .pg-image img { width:100%; display:block; border-radius:6px; border:1px solid #f0f0f5; }

    /* Sidebar */
    .pg-widget { background:#fff; border:1px solid #ebebf0; border-radius:8px; overflow:hidden; flex-shrink:0; }
    .pg-widget-head { padding:10px 14px; border-bottom:1px solid #f0f0f5; display:flex; align-items:center; gap:6px; }
    .pg-widget-title { font-size:11px; font-weight:700; color:#374151; letter-spacing:0.06em; text-transform:uppercase; }
    .pg-widget-count { font-size:10px; font-weight:700; padding:1px 6px; border-radius:4px; margin-left:2px; }
    .pg-widget-list { padding:4px 0; }
    .pg-side-card { display:flex; gap:9px; padding:8px 14px; transition:background 0.12s; text-decoration:none; }
    .pg-side-card:hover { background:#fafafa; }
    .pg-side-icon { width:28px; height:28px; border-radius:6px; flex-shrink:0; display:flex; align-items:center; justify-content:center; }
    .icon-announcement { background:#ede9fe; }
    .icon-event        { background:#dbeafe; }
    .pg-side-info { flex:1; min-width:0; }
    .pg-side-name { font-size:12.5px; font-weight:600; color:#1a1a2e; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; line-height:1.35; }
    .pg-side-meta { font-size:11px; color:#9ca3af; margin-top:1px; }
    .pg-side-empty { padding:14px; font-size:12px; color:#c4c4cc; text-align:center; }
    .pg-see-more {
        display:flex; align-items:center; justify-content:center;
        padding:8px 14px; font-size:12px; font-weight:700; color:#7c3aed;
        text-decoration:none; border-top:1px solid #f0f0f5; transition:background 0.1s;
    }
    .pg-see-more:hover { background:#f4f0ff; }

    /* Empty */
    .pg-empty { text-align:center; padding:48px 20px; color:#9ca3af; font-size:13px; background:#fff; border:1px solid #ebebf0; border-radius:8px; }
    .pg-empty svg { margin:0 auto 10px; display:block; }
</style>
@endpush

@section('content')
<div class="pg-wrap">

    {{-- Toolbar --}}
    <form method="GET" action="{{ route('temuan.index') }}" id="filterForm">
        <div class="pg-toolbar-wrap">
            <div class="pg-toolbar">
                <div class="pg-toolbar-left">
                    <div class="pg-dd-custom" id="sortDropdown">
                        <button type="button" class="pg-dd-trigger" onclick="toggleDD('sortDropdown')">
                            <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"/></svg>
                            <span id="sortLabel">{{ ($sort ?? 'terbaru') === 'terlama' ? 'Terlama' : 'Terbaru' }}</span>
                            <svg class="pg-dd-chevron" width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div class="pg-dd-menu">
                            <button type="button" class="pg-dd-option {{ ($sort ?? 'terbaru') === 'terbaru' ? 'selected' : '' }}" onclick="selectOpt('sortDropdown','sortInput','sortLabel','terbaru','Terbaru')">
                                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/></svg>Terbaru
                            </button>
                            <button type="button" class="pg-dd-option {{ ($sort ?? '') === 'terlama' ? 'selected' : '' }}" onclick="selectOpt('sortDropdown','sortInput','sortLabel','terlama','Terlama')">
                                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/></svg>Terlama
                            </button>
                        </div>
                        <input type="hidden" name="sort" id="sortInput" value="{{ $sort ?? 'terbaru' }}">
                    </div>

                    <div class="pg-dd-custom" id="typeDropdown">
                        <button type="button" class="pg-dd-trigger" onclick="toggleDD('typeDropdown')">
                            <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h18M3 8h18M3 12h18M3 16h18"/></svg>
                            <span id="typeLabel">{{ ($type ?? 'all') === 'found' ? 'Temuan' : (($type ?? '') === 'lost' ? 'Kehilangan' : 'Semua') }}</span>
                            <svg class="pg-dd-chevron" width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div class="pg-dd-menu">
                            <button type="button" class="pg-dd-option {{ ($type ?? 'all') === 'all' ? 'selected' : '' }}" onclick="selectOpt('typeDropdown','typeInput','typeLabel','all','Semua')">
                                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/></svg>Semua
                            </button>
                            <button type="button" class="pg-dd-option {{ ($type ?? '') === 'found' ? 'selected' : '' }}" onclick="selectOpt('typeDropdown','typeInput','typeLabel','found','Temuan')">
                                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/></svg>Temuan
                            </button>
                            <button type="button" class="pg-dd-option {{ ($type ?? '') === 'lost' ? 'selected' : '' }}" onclick="selectOpt('typeDropdown','typeInput','typeLabel','lost','Kehilangan')">
                                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/></svg>Kehilangan
                            </button>
                        </div>
                        <input type="hidden" name="type" id="typeInput" value="{{ $type ?? 'all' }}">
                    </div>
                </div>

                <div class="pg-search-wrap">
                    <span class="pg-si">
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8" stroke-width="2"/><path d="M21 21l-4.35-4.35" stroke-width="2" stroke-linecap="round"/></svg>
                    </span>
                    <input type="text" name="search" class="pg-search"
                        placeholder="Telurusi laporan temuan atau kehilangan..."
                        value="{{ $search ?? '' }}"
                        oninput="debounceSearch(this.form)">
                </div>
            </div>
        </div>
    </form>

    <div class="pg-columns">
        {{-- LEFT --}}
        <div class="pg-main">
            @forelse($items ?? [] as $item)
            <div class="pg-post">
                <div class="pg-avatar">
                    <svg width="16" height="16" fill="none" stroke="#c4c4d4" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21v-2a4 4 0 00-4-4H9a4 4 0 00-4 4v2"/>
                        <circle cx="12" cy="7" r="4" stroke-width="1.8"/>
                    </svg>
                </div>
                <div class="pg-post-body">
                    <div class="pg-meta">
                        <span class="pg-author">{{ $item->user_name ?? 'Anonim' }}</span>
                        <span class="pg-dot"></span>
                        <span class="pg-time">{{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}</span>
                        <span class="pg-badge {{ $item->type === 'found' ? 'badge-temuan' : 'badge-kehilangan' }}">
                            {{ $item->type === 'found' ? 'Temuan' : 'Kehilangan' }}
                        </span>
                    </div>
                    <div class="pg-title">{{ $item->item_name }}</div>
                    <div class="pg-content">{{ $item->description }}</div>
                    @if($item->found_at)
                    <div class="pg-location">
                        <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><circle cx="12" cy="11" r="3" stroke-width="2"/></svg>
                        Lokasi : {{ $item->found_at }}
                        &nbsp;·&nbsp;
                        <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="1.8"/><polyline stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" points="12 6 12 12 16 14"/></svg>
                        &nbsp;Waktu ditemukan : {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y, H.i') }} WIB
                    </div>
                    @endif
                    @php $photo = isset($photoMap) ? ($photoMap[$item->id] ?? null) : null; @endphp
                    @if($photo)
                    <div class="pg-image">
                        <img src="{{ asset('storage/' . $photo->file_path) }}" alt="{{ $item->item_name }}" onerror="this.parentElement.style.display='none'">
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="pg-empty">
                <svg width="36" height="36" fill="none" stroke="#d1d5db" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8" stroke-width="1.4"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" d="M21 21l-4.35-4.35"/></svg>
                Belum ada laporan temuan atau kehilangan.
            </div>
            @endforelse
        </div>

        {{-- RIGHT --}}
        <aside class="pg-side">
            {{-- Pengumuman --}}
            <div class="pg-widget">
                <div class="pg-widget-head">
                    <svg width="13" height="13" fill="none" stroke="#5b21b6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                    <span class="pg-widget-title">Announcement</span>
                    <span class="pg-widget-count" style="background:#ede9fe;color:#5b21b6;">{{ count($recentAnnouncements ?? []) }}</span>
                </div>
                <div class="pg-widget-list">
                    @forelse(array_slice($recentAnnouncements ?? [], 0, 3) as $ann)
                    <a href="{{ route('pengumuman.index') }}" class="pg-side-card">
                        <div class="pg-side-icon icon-announcement">
                            <svg width="13" height="13" fill="none" stroke="#5b21b6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                        </div>
                        <div class="pg-side-info">
                            <div class="pg-side-name">{{ $ann->title }}</div>
                            <div class="pg-side-meta">{{ \Carbon\Carbon::parse($ann->created_at)->format('d M Y') }}</div>
                        </div>
                    </a>
                    @empty
                    <div class="pg-side-empty">Tidak ada pengumuman.</div>
                    @endforelse
                </div>
                @if(count($recentAnnouncements ?? []) > 3)
                <a href="{{ route('pengumuman.index') }}" class="pg-see-more">Lihat semua</a>
                @endif
            </div>

            {{-- Events --}}
            <div class="pg-widget">
                <div class="pg-widget-head">
                    <svg width="13" height="13" fill="none" stroke="#1d4ed8" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" stroke-width="1.8"/><line x1="16" y1="2" x2="16" y2="6" stroke-width="1.8" stroke-linecap="round"/><line x1="8" y1="2" x2="8" y2="6" stroke-width="1.8" stroke-linecap="round"/><line x1="3" y1="10" x2="21" y2="10" stroke-width="1.8"/></svg>
                    <span class="pg-widget-title">Event</span>
                    <span class="pg-widget-count" style="background:#dbeafe;color:#1d4ed8;">{{ count($recentEvents ?? []) }}</span>
                </div>
                <div class="pg-widget-list">
                    @forelse(array_slice($recentEvents ?? [], 0, 3) as $event)
                    <a href="{{ route('kalender.index') }}" class="pg-side-card">
                        <div class="pg-side-icon icon-event">
                            <svg width="13" height="13" fill="none" stroke="#1d4ed8" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" stroke-width="1.8"/><line x1="16" y1="2" x2="16" y2="6" stroke-width="1.8" stroke-linecap="round"/><line x1="8" y1="2" x2="8" y2="6" stroke-width="1.8" stroke-linecap="round"/><line x1="3" y1="10" x2="21" y2="10" stroke-width="1.8"/></svg>
                        </div>
                        <div class="pg-side-info">
                            <div class="pg-side-name">{{ $event->event_name }}</div>
                            <div class="pg-side-meta">{{ \Carbon\Carbon::parse($event->event_date)->format('d M Y') }}{{ !empty($event->location_name) ? ' · ' . $event->location_name : '' }}</div>
                        </div>
                    </a>
                    @empty
                    <div class="pg-side-empty">Tidak ada event.</div>
                    @endforelse
                </div>
                @if(count($recentEvents ?? []) > 3)
                <a href="{{ route('kalender.index') }}" class="pg-see-more">Lihat semua</a>
                @endif
            </div>
        </aside>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let searchTimer;
    function debounceSearch(form) {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => form.submit(), 500);
    }
    function toggleDD(id) {
        document.querySelectorAll('.pg-dd-custom').forEach(d => { if(d.id!==id) d.classList.remove('open'); });
        document.getElementById(id).classList.toggle('open');
    }
    function selectOpt(ddId, inputId, labelId, value, label) {
        document.getElementById(inputId).value = value;
        document.getElementById(labelId).textContent = label;
        document.querySelectorAll('#'+ddId+' .pg-dd-option').forEach(o => o.classList.toggle('selected', o.textContent.trim() === label));
        document.getElementById(ddId).classList.remove('open');
        document.getElementById('filterForm').submit();
    }
    document.addEventListener('click', e => {
        if (!e.target.closest('.pg-dd-custom')) document.querySelectorAll('.pg-dd-custom').forEach(d => d.classList.remove('open'));
    });
</script>
@endpush