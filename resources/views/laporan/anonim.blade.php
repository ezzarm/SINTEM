{{-- resources/views/laporan/anonim.blade.php --}}
@extends('layouts.app')

@section('title', 'Laporan Anonim – SINTEM')

@section('topbar')
    @include('components.topbar')
@endsection

@section('header', 'Laporan Anonim')
@section('subheader', 'Pantau status laporan anonim yang sudah kamu kirim.')

@push('styles')
<style>
    .page-body { padding:0!important; overflow:hidden!important; display:flex; flex-direction:column; }
    .main-content { background:#fff!important; }
    .page-header  { padding:16px 32px 14px!important; background:#fff!important; }

    /* ── Toolbar ── */
    .adm-toolbar {
        flex-shrink:0; padding:14px 32px 12px;
        border-bottom:1px solid #f0f0f5; background:#fff;
        display:flex; align-items:center; justify-content:space-between; gap:8px; flex-wrap:wrap;
    }
    .adm-toolbar-left { display:flex; align-items:center; gap:6px; }

    .adm-dd { position:relative; display:inline-block; }
    .adm-dd-trigger {
        display:inline-flex; align-items:center; gap:6px;
        padding:6px 10px; border:1px solid #e5e7eb; border-radius:5px;
        font-size:12.5px; font-family:'Lato',sans-serif; font-weight:600;
        color:#374151; background:#fff; cursor:pointer; outline:none;
        transition:border-color 0.12s; white-space:nowrap;
    }
    .adm-dd-trigger:hover { border-color:#c4b5fd; }
    .adm-dd.open .adm-dd-trigger { border-color:#7c3aed; box-shadow:0 0 0 2px rgba(124,58,237,0.1); }
    .adm-chevron { transition:transform 0.2s; }
    .adm-dd.open .adm-chevron { transform:rotate(180deg); }
    .adm-dd-menu {
        display:none; position:absolute; top:calc(100% + 4px); left:0;
        min-width:160px; background:#fff; border:1px solid #e5e7eb;
        border-radius:6px; box-shadow:0 4px 16px rgba(0,0,0,0.08); z-index:100; padding:4px;
    }
    .adm-dd.open .adm-dd-menu { display:block; animation:ddFade 0.15s ease; }
    @keyframes ddFade { from{opacity:0;transform:translateY(-4px)}to{opacity:1;transform:translateY(0)} }
    .adm-dd-opt {
        display:flex; align-items:center; gap:8px; width:100%;
        padding:7px 10px; font-size:13px; font-family:'Lato',sans-serif;
        font-weight:500; color:#374151; background:none; border:none;
        border-radius:4px; cursor:pointer; text-align:left; transition:background 0.1s,color 0.1s;
    }
    .adm-dd-opt svg { opacity:0; flex-shrink:0; stroke:#7c3aed; }
    .adm-dd-opt:hover { background:#f4f0ff; color:#4f28d9; }
    .adm-dd-opt.selected { color:#4f28d9; font-weight:700; }
    .adm-dd-opt.selected svg { opacity:1; }

    .adm-search-wrap { position:relative; display:flex; align-items:center; }
    .adm-search-wrap .adm-si { position:absolute; left:9px; color:#b0b0c0; pointer-events:none; display:flex; }
    .adm-search {
        padding:6px 12px 6px 30px; border:1px solid #e5e7eb;
        border-radius:5px; font-size:12.5px; font-family:'Lato',sans-serif;
        color:#374151; background:#fff; width:240px; outline:none; transition:border-color 0.12s;
    }
    .adm-search::placeholder { color:#c4c4cc; }
    .adm-search:focus { border-color:#7c3aed; box-shadow:0 0 0 2px rgba(124,58,237,0.1); }

    .adm-body { flex:1; min-height:0; overflow-y:auto;-webkit-overflow-scrolling:touch; scrollbar-width:none; -ms-overflow-style:none; }
    .adm-body::-webkit-scrollbar { display:none; }
    .adm-table-wrap { padding:20px 32px 32px; }

    /* ── Table ── */
    table { width:100%; border-collapse:collapse; }
    thead th {
        padding:10px 14px; text-align:left;
        font-size:12px; font-weight:700; color:#6b7280;
        background:#f9f9fb; border-bottom:1px solid #ebebf0;
    }
    thead th:first-child { border-radius:8px 0 0 0; }
    thead th:last-child  { border-radius:0 8px 0 0; }
    tbody tr { border-bottom:1px solid #f5f5f7; transition:background 0.1s; }
    tbody tr:hover { background:#fafafa; }
    tbody tr:last-child { border-bottom:none; }
    tbody td { padding:12px 14px; font-size:13px; color:#374151; vertical-align:middle; }

    .td-num   { color:#9ca3af; font-size:12px; width:40px; }
    .td-title { font-weight:600; color:#1a1a2e; max-width:200px; }
    .td-title span { display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }

    .td-ticket {
        font-family:'Lato',sans-serif; font-size:12px; font-weight:700;
        color:#4f28d9; background:#ede9fe; padding:2px 8px;
        border-radius:4px; display:inline-block; white-space:nowrap;
    }

    .badge {
        font-size:11px; font-weight:700; padding:2px 10px;
        border-radius:4px; border:1px solid; display:inline-block; white-space:nowrap;
    }
    .badge-pending    { background:#f9fafb; color:#6b7280; border-color:#e5e7eb; }
    .badge-in_progress{ background:#fffbeb; color:#92400e; border-color:#fde68a; }
    .badge-solved     { background:#f0fdf4; color:#16a34a; border-color:#bbf7d0; }

    .cat-chip {
        font-size:11px; font-weight:600; padding:2px 8px;
        border-radius:4px; display:inline-block; white-space:nowrap;
        background:#f4f0ff; color:#4f28d9;
    }

    .action-btn {
        display:inline-flex; align-items:center; justify-content:center;
        width:28px; height:28px; border-radius:5px; border:1px solid #e5e7eb;
        background:#fff; cursor:pointer; color:#6b7280;
        transition:background 0.12s,color 0.12s,border-color 0.12s; margin-right:3px;
    }
    .action-btn:hover { background:#f4f0ff; color:#4f28d9; border-color:#c4b5fd; }
    .action-btn.danger:hover { background:#fef2f2; color:#dc2626; border-color:#fecaca; }

    .adm-empty { text-align:center; padding:56px 20px; color:#9ca3af; font-size:13px; }
    .adm-empty svg { margin:0 auto 12px; display:block; }

    /* ── Pagination ── */
    .adm-pagination {
        display:flex; align-items:center; justify-content:space-between;
        padding:14px 32px; border-top:1px solid #f0f0f5;
        font-size:13px; color:#6b7280; flex-shrink:0; background:#fff;
    }
    .adm-page-btns { display:flex; align-items:center; gap:4px; }
    .adm-page-btn {
        display:inline-flex; align-items:center; justify-content:center;
        width:28px; height:28px; border-radius:5px; border:1px solid #e5e7eb;
        background:#fff; cursor:pointer; font-size:13px; color:#374151;
        text-decoration:none; transition:background 0.1s;
    }
    .adm-page-btn:hover { background:#f4f0ff; color:#4f28d9; border-color:#c4b5fd; }
    .adm-page-btn.active { background:#ede9fe; color:#4f28d9; border-color:#c4b5fd; font-weight:700; pointer-events:none; }
    .adm-page-btn.disabled { opacity:0.4; pointer-events:none; }

    /* ══════════════════════════════
       MOBILE CARDS  (≤ 768px)
    ══════════════════════════════ */
    .mobile-list { display:none; padding:12px 16px 32px; flex-direction:column; gap:10px; }

    .m-card {
        background:#fff; border:1px solid #ebebf0; border-radius:10px;
        padding:14px 16px; transition:border-color 0.15s;
    }
    .m-card:hover { border-color:#d4d0f0; }
    .m-card-top { display:flex; align-items:flex-start; justify-content:space-between; gap:8px; margin-bottom:6px; }
    .m-card-title { font-size:13.5px; font-weight:700; color:#1a1a2e; line-height:1.4; flex:1; min-width:0; }
    .m-card-badges { display:flex; gap:5px; flex-shrink:0; flex-wrap:wrap; justify-content:flex-end; }
    .m-ticket { font-size:11px; font-weight:700; color:#4f28d9; background:#ede9fe; padding:2px 7px; border-radius:4px; margin-bottom:6px; display:inline-block; }
    .m-card-meta { display:flex; flex-wrap:wrap; gap:6px 14px; margin-bottom:8px; }
    .m-card-meta-item { font-size:12px; color:#9ca3af; display:flex; align-items:center; gap:4px; }
    .m-card-desc { font-size:12.5px; color:#4b5563; line-height:1.55; margin-bottom:10px; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
    .m-card-actions { display:flex; gap:6px; }
    .m-action-btn {
        flex:1; display:inline-flex; align-items:center; justify-content:center; gap:5px;
        padding:7px 10px; border-radius:6px; border:1px solid #e5e7eb;
        background:#fff; font-size:12px; font-weight:600; font-family:'Lato',sans-serif;
        color:#374151; cursor:pointer; transition:background 0.12s,color 0.12s,border-color 0.12s;
    }
    .m-action-btn:hover { background:#f4f0ff; color:#4f28d9; border-color:#c4b5fd; }
    .m-action-btn.danger:hover { background:#fef2f2; color:#dc2626; border-color:#fecaca; }

    @media (max-width: 768px) {
        .adm-toolbar { padding:12px 16px; }
        .adm-search  { width:170px; }
        .adm-table-wrap { display:none; }
        .adm-pagination { padding:12px 16px; flex-direction:column; gap:8px; text-align:center; }
        .page-header { padding:14px 16px 12px!important; }
    }
    @media (min-width: 769px) {
        .mobile-list { display:none!important; }
        .adm-table-wrap { display:block; }
    }
    @media (max-width: 768px) {
        .mobile-list { display:flex!important; }
    }

    /* ══════════════════════════════
       DETAIL SLIDE PANEL
    ══════════════════════════════ */
    .panel-overlay { display:none; position:fixed; inset:0; z-index:200; background:rgba(0,0,0,0.3); }
    .panel-overlay.open { display:block; animation:fadeOv 0.18s ease; }
    @keyframes fadeOv { from{opacity:0}to{opacity:1} }

    .detail-panel {
        position:fixed; right:-500px; top:0; bottom:0;
        width:420px; max-width:100vw; background:#fff;
        box-shadow:-4px 0 32px rgba(0,0,0,0.12);
        z-index:300; display:flex; flex-direction:column;
        transition:right 0.3s cubic-bezier(0.22,1,0.36,1);
    }
    .detail-panel.open { right:0; }

    .dp-head { display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid #f0f0f5; flex-shrink:0; }
    .dp-title { font-size:14px; font-weight:700; color:#1a1a2e; }
    .dp-close { width:28px; height:28px; border-radius:6px; border:1px solid #e5e7eb; background:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; color:#9ca3af; transition:background 0.12s,color 0.12s; }
    .dp-close:hover { background:#fef2f2; color:#dc2626; border-color:#fecaca; }
    .dp-body { flex:1; overflow-y:auto; padding:20px; scrollbar-width:none; }
    .dp-body::-webkit-scrollbar { display:none; }
    .dp-foot { padding:14px 20px; border-top:1px solid #f0f0f5; display:flex; gap:8px; justify-content:flex-end; flex-shrink:0; }

    .dp-section { font-size:11px; font-weight:700; color:#9ca3af; letter-spacing:0.06em; text-transform:uppercase; margin-bottom:8px; margin-top:16px; }
    .dp-field { margin-bottom:12px; }
    .dp-field-label { font-size:12px; font-weight:600; color:#6b7280; margin-bottom:3px; }
    .dp-field-value { font-size:13px; color:#1a1a2e; line-height:1.55; }

    /* anon notice inside panel */
    .dp-anon-notice {
        display:flex; align-items:flex-start; gap:8px;
        padding:10px 12px; background:#f0fdf4; border:1px solid #bbf7d0;
        border-radius:6px; margin-bottom:16px;
        font-size:12px; color:#166534; line-height:1.5;
    }

    /* ══════════════════════════════
       DELETE CONFIRM
    ══════════════════════════════ */
    .del-overlay { display:none; position:fixed; inset:0; z-index:500; background:rgba(0,0,0,0.35); align-items:center; justify-content:center; }
    .del-overlay.open { display:flex; }
    .del-box { background:#fff; border-radius:12px; padding:24px; max-width:320px; width:100%; box-shadow:0 20px 60px rgba(0,0,0,0.18); text-align:center; animation:slideUp 0.2s ease; }
    @keyframes slideUp { from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)} }
    .del-icon { margin:0 auto 10px; display:flex; align-items:center; justify-content:center; width:48px; height:48px; border-radius:50%; background:#fef2f2; }
    .del-title { font-size:15px; font-weight:700; color:#1a1a2e; margin-bottom:6px; }
    .del-desc  { font-size:13px; color:#6b7280; margin-bottom:20px; line-height:1.5; }
    .del-actions { display:flex; gap:8px; justify-content:center; }
    .btn-cancel { padding:8px 18px; border:1px solid #e5e7eb; border-radius:6px; background:#fff; color:#6b7280; font-size:13px; font-weight:700; font-family:'Lato',sans-serif; cursor:pointer; transition:background 0.12s; }
    .btn-cancel:hover { background:#f9f9fb; border-color:#c4b5fd; color:#4f28d9; }
    .btn-del-confirm { padding:8px 20px; background:#dc2626; color:#fff; font-size:13px; font-weight:700; font-family:'Lato',sans-serif; border:none; border-radius:6px; cursor:pointer; transition:opacity 0.15s; }
    .btn-del-confirm:hover { opacity:0.88; }

    /* ══════════════════════════════════════════════
       BREAKPOINTS
       ▸ Tablet  768–1023px : reduce padding, horizontal scroll on table
       ▸ Mobile  < 768px   : compact toolbar, full-width search
       ▸ XS      < 480px   : hide less-critical columns
    ══════════════════════════════════════════════ */

    /* ── Tablet ── */
    @media (max-width: 1023px) {
        .adm-toolbar      { padding: 12px 20px 10px; }
        .adm-table-wrap   { padding: 14px 20px 24px; overflow-x: auto; }
        table             { min-width: 600px; }
        .adm-pagination   { padding: 12px 20px; }
    }

    /* ── Mobile ── */
    @media (max-width: 767px) {
        .adm-toolbar      { padding: 10px 16px 8px; flex-wrap: wrap; gap: 8px; }
        .adm-toolbar-left { flex-wrap: wrap; }
        .adm-search       { width: 100%; }
        .adm-search-wrap  { flex: 1; }
        .adm-table-wrap   { padding: 10px 16px 20px; }
        .adm-pagination   { padding: 10px 16px; flex-wrap: wrap; gap: 6px; }
    }

    /* ── Small mobile ── */
    @media (max-width: 479px) {
        .adm-toolbar { flex-direction: column; align-items: stretch; }
        table { min-width: 520px; }
    }
    /* ── Mobile: release adm-body fixed-flex so content not cropped ── */
    @media (max-width: 767px) {
        .adm-body    { overflow-y: auto; min-height: 200px; flex: 1; }
        .page-body   { overflow-y: auto !important; }
    }

    /* ── Mobile: panel full-width, safe area padding for keyboard + notch ── */
    @media (max-width: 767px) {
        .edit-panel, .detail-panel {
            width: 100% !important;
            max-width: 100vw !important;
            right: -100vw !important;
            /* Add bottom padding so ep-foot / save buttons are above keyboard */
            padding-bottom: env(safe-area-inset-bottom, 0px);
        }
        .edit-panel.open, .detail-panel.open {
            right: 0 !important;
        }
        .ep-body, .dp-body, .panel-body {
            -webkit-overflow-scrolling: touch;
            /* Extra bottom padding so last field isn't under keyboard */
            padding-bottom: 80px !important;
        }
        .ep-foot {
            /* Keep save button visible above keyboard on iOS */
            position: sticky;
            bottom: 0;
            background: #fff;
            z-index: 10;
            padding-bottom: calc(12px + env(safe-area-inset-bottom, 0px));
        }
    }

</style>
@endpush

@section('content')

{{-- ── TOOLBAR ── --}}
<div class="adm-toolbar">
    <div class="adm-toolbar-left">
        <div class="adm-dd" id="sortDD">
            <button type="button" class="adm-dd-trigger" onclick="toggleDD('sortDD')">
                <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"/></svg>
                <span id="sortLabel">Terbaru</span>
                <svg class="adm-chevron" width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="adm-dd-menu">
                <button class="adm-dd-opt selected" onclick="pickSort('terbaru','Terbaru',this)">
                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/></svg>Terbaru
                </button>
                <button class="adm-dd-opt" onclick="pickSort('terlama','Terlama',this)">
                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/></svg>Terlama
                </button>
            </div>
        </div>

        <div class="adm-dd" id="statusDD">
            <button type="button" class="adm-dd-trigger" onclick="toggleDD('statusDD')">
                <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="1.8"/><line x1="12" y1="8" x2="12" y2="12" stroke-width="2" stroke-linecap="round"/><line x1="12" y1="16" x2="12.01" y2="16" stroke-width="2.5" stroke-linecap="round"/></svg>
                <span id="statusLabel">Semua Status</span>
                <svg class="adm-chevron" width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="adm-dd-menu">
                <button class="adm-dd-opt selected" onclick="pickStatus('all','Semua Status',this)">
                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/></svg>Semua Status
                </button>
                <button class="adm-dd-opt" onclick="pickStatus('pending','Menunggu',this)">
                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/></svg>Menunggu
                </button>
                <button class="adm-dd-opt" onclick="pickStatus('in_progress','Diproses',this)">
                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/></svg>Diproses
                </button>
                <button class="adm-dd-opt" onclick="pickStatus('solved','Selesai',this)">
                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/></svg>Selesai
                </button>
            </div>
        </div>
    </div>

    <div class="adm-search-wrap">
        <span class="adm-si"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8" stroke-width="2"/><path d="M21 21l-4.35-4.35" stroke-width="2" stroke-linecap="round"/></svg></span>
        <input type="text" class="adm-search" placeholder="Cari laporan..." oninput="filterAll(this.value)" id="searchInput">
    </div>
</div>

<div class="adm-body">
    {{-- ── DESKTOP TABLE ── --}}
    <div class="adm-table-wrap">
        <table id="mainTable">
            <thead>
                <tr>
                    <th class="td-num">#</th>
                    <th>No. Tiket</th>
                    <th>Judul</th>
                    <th>Kategori</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                @forelse($items ?? [] as $i => $item)
                <tr data-status="{{ $item->status }}" data-title="{{ strtolower($item->report_content ?? '') }}">
                    <td class="td-num">{{ $i + 1 }}</td>
                    <td><span class="td-ticket">{{ $item->ticket_number }}</span></td>
                    <td class="td-title"><span>{{ Str::limit($item->report_content ?? '', 60) }}</span></td>
                    <td><span class="cat-chip">{{ $item->category_name ?? 'Umum' }}</span></td>
                    <td>
                        @php $st = $item->status; @endphp
                        <span class="badge badge-{{ $st }}">
                            {{ $st === 'pending' ? 'Menunggu' : ($st === 'in_progress' ? 'Diproses' : 'Selesai') }}
                        </span>
                    </td>
                    <td style="white-space:nowrap; color:#9ca3af; font-size:12px;">
                        {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}
                    </td>
                    <td>
                        <button class="action-btn" title="Lihat detail"
                            onclick="openDetail({{ json_encode($item) }})">
                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3" stroke-width="1.8"/></svg>
                        </button>
                        @if($item->status === 'pending')
                        <button class="action-btn danger" title="Hapus"
                            onclick="openDelete({{ $item->id }}, '{{ addslashes($item->ticket_number) }}')">
                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" points="3 6 5 6 21 6"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10 11v6M14 11v6"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
                        </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7"><div class="adm-empty">
                    <svg width="36" height="36" fill="none" stroke="#d1d5db" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4" stroke-width="1.4"/><line x1="3" y1="3" x2="21" y2="21" stroke-width="1.4" stroke-linecap="round"/></svg>
                    Belum ada laporan anonim yang dikirim.
                </div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ── MOBILE CARDS ── --}}
    <div class="mobile-list" id="mobileList">
        @forelse($items ?? [] as $item)
        <div class="m-card" data-status="{{ $item->status }}" data-title="{{ strtolower($item->report_content ?? '') }}">
            <div class="m-card-top">
                <div class="m-card-title">{{ Str::limit($item->report_content ?? '', 60) }}</div>
                @php $st = $item->status; @endphp
                <div class="m-card-badges">
                    <span class="badge badge-{{ $st }}">
                        {{ $st === 'pending' ? 'Menunggu' : ($st === 'in_progress' ? 'Diproses' : 'Selesai') }}
                    </span>
                </div>
            </div>
            <div><span class="m-ticket">{{ $item->ticket_number }}</span></div>
            <div class="m-card-meta">
                <span class="m-card-meta-item">
                    <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1" stroke-width="1.8"/></svg>
                    {{ $item->category_name ?? 'Umum' }}
                </span>
                <span class="m-card-meta-item">
                    <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" stroke-width="1.8"/><line x1="16" y1="2" x2="16" y2="6" stroke-width="1.8" stroke-linecap="round"/><line x1="8" y1="2" x2="8" y2="6" stroke-width="1.8" stroke-linecap="round"/><line x1="3" y1="10" x2="21" y2="10" stroke-width="1.8"/></svg>
                    {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}
                </span>
            </div>
            <div class="m-card-actions">
                <button class="m-action-btn" onclick="openDetail({{ json_encode($item) }})">
                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3" stroke-width="1.8"/></svg>
                    Detail
                </button>
                @if($item->status === 'pending')
                <button class="m-action-btn danger" onclick="openDelete({{ $item->id }}, '{{ addslashes($item->ticket_number) }}')">
                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" points="3 6 5 6 21 6"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
                    Hapus
                </button>
                @endif
            </div>
        </div>
        @empty
        <div class="adm-empty">
            <svg width="36" height="36" fill="none" stroke="#d1d5db" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4" stroke-width="1.4"/><line x1="3" y1="3" x2="21" y2="21" stroke-width="1.4" stroke-linecap="round"/></svg>
            Belum ada laporan anonim.
        </div>
        @endforelse
    </div>
</div>

{{-- Pagination --}}
@if(isset($items) && method_exists($items, 'links'))
<div class="adm-pagination">
    <span>Menampilkan {{ $items->firstItem() }}–{{ $items->lastItem() }} dari {{ $items->total() }} laporan</span>
    <div class="adm-page-btns">
        <a href="{{ $items->previousPageUrl() ?? '#' }}" class="adm-page-btn {{ $items->onFirstPage() ? 'disabled' : '' }}">
            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 18l-6-6 6-6"/></svg>
        </a>
        @foreach($items->getUrlRange(1, $items->lastPage()) as $page => $url)
        <a href="{{ $url }}" class="adm-page-btn {{ $page === $items->currentPage() ? 'active' : '' }}">{{ $page }}</a>
        @endforeach
        <a href="{{ $items->nextPageUrl() ?? '#' }}" class="adm-page-btn {{ !$items->hasMorePages() ? 'disabled' : '' }}">
            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 18l6-6-6-6"/></svg>
        </a>
    </div>
</div>
@endif

{{-- ── PANEL OVERLAY ── --}}
<div class="panel-overlay" id="panelOverlay" onclick="closeDetail()"></div>

{{-- ── DETAIL PANEL ── --}}
<div class="detail-panel" id="detailPanel">
    <div class="dp-head">
        <span class="dp-title">Detail Laporan</span>
        <button class="dp-close" onclick="closeDetail()">
            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    <div class="dp-body" id="detailBody"></div>
    <div class="dp-foot">
        <button class="btn-cancel" onclick="closeDetail()">Tutup</button>
    </div>
</div>

{{-- ── DELETE CONFIRM ── --}}
<div class="del-overlay" id="delOverlay">
    <div class="del-box">
        <div class="del-icon">
            <svg width="22" height="22" fill="none" stroke="#dc2626" viewBox="0 0 24 24"><polyline stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" points="3 6 5 6 21 6"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
        </div>
        <div class="del-title">Hapus Laporan?</div>
        <div class="del-desc" id="delDesc">Laporan ini akan dihapus permanen.</div>
        <div class="del-actions">
            <button class="btn-cancel" onclick="closeDelete()">Batal</button>
            <form id="delForm" method="POST" style="display:inline;">
                @csrf @method('DELETE')
                <button type="submit" class="btn-del-confirm">Hapus</button>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    /* ── Dropdown ── */
    function toggleDD(id) {
        document.querySelectorAll('.adm-dd').forEach(d => { if(d.id !== id) d.classList.remove('open'); });
        document.getElementById(id).classList.toggle('open');
    }
    document.addEventListener('click', e => {
        if (!e.target.closest('.adm-dd')) document.querySelectorAll('.adm-dd').forEach(d => d.classList.remove('open'));
    });

    let currentSort = 'terbaru', currentStatus = 'all';

    function pickSort(val, label, btn) {
        currentSort = val;
        document.getElementById('sortLabel').textContent = label;
        document.querySelectorAll('#sortDD .adm-dd-opt').forEach(o => o.classList.remove('selected'));
        btn.classList.add('selected');
        document.getElementById('sortDD').classList.remove('open');
        applyFilters();
    }
    function pickStatus(val, label, btn) {
        currentStatus = val;
        document.getElementById('statusLabel').textContent = label;
        document.querySelectorAll('#statusDD .adm-dd-opt').forEach(o => o.classList.remove('selected'));
        btn.classList.add('selected');
        document.getElementById('statusDD').classList.remove('open');
        applyFilters();
    }
    function filterAll(q) { applyFilters(q); }
    function applyFilters(q) {
        q = (q ?? document.getElementById('searchInput').value).toLowerCase();
        const rows  = document.querySelectorAll('#tableBody tr[data-status]');
        const cards = document.querySelectorAll('#mobileList .m-card[data-status]');
        [...rows, ...cards].forEach(el => {
            const matchStatus = currentStatus === 'all' || el.dataset.status === currentStatus;
            const matchQuery  = !q || el.dataset.title.includes(q);
            el.style.display  = (matchStatus && matchQuery) ? '' : 'none';
        });
    }

    /* ── Detail panel ── */
    const statusMap   = {pending:'Menunggu', in_progress:'Diproses', solved:'Selesai'};
    const statusClass = {pending:'badge-pending', in_progress:'badge-in_progress', solved:'badge-solved'};

    function openDetail(item) {
        const st = item.status;
        document.getElementById('detailBody').innerHTML = `
            <div class="dp-anon-notice">
                <svg width="13" height="13" fill="none" stroke="#16a34a" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                Laporan ini bersifat anonim. Identitasmu tidak ditampilkan kepada siapapun.
            </div>
            <div style="margin-bottom:12px; display:flex; gap:6px; align-items:center; flex-wrap:wrap;">
                <span class="td-ticket">${item.ticket_number}</span>
                <span class="badge ${statusClass[st]}">${statusMap[st] ?? st}</span>
            </div>
            <div class="dp-section">Isi Laporan</div>
            <div class="dp-field">
                <div class="dp-field-label">Kategori</div>
                <div class="dp-field-value"><span class="cat-chip">${item.category_name ?? 'Umum'}</span></div>
            </div>
            <div class="dp-field">
                <div class="dp-field-label">Isi Laporan</div>
                <div class="dp-field-value" style="white-space:pre-line;">${item.report_content ?? '—'}</div>
            </div>
            ${item.admin_notes ? `
            <div class="dp-section">Catatan Admin</div>
            <div class="dp-field">
                <div class="dp-field-value" style="background:#f9fafb; padding:10px 12px; border-radius:6px; font-size:12.5px; color:#374151; line-height:1.55;">${item.admin_notes}</div>
            </div>` : ''}
            <div class="dp-section">Waktu</div>
            <div class="dp-field">
                <div class="dp-field-label">Dilaporkan pada</div>
                <div class="dp-field-value">${new Date(item.created_at).toLocaleDateString('id-ID',{day:'numeric',month:'long',year:'numeric'})}</div>
            </div>
            ${item.resolved_at ? `
            <div class="dp-field">
                <div class="dp-field-label">Diselesaikan pada</div>
                <div class="dp-field-value">${new Date(item.resolved_at).toLocaleDateString('id-ID',{day:'numeric',month:'long',year:'numeric'})}</div>
            </div>` : ''}
        `;
        document.getElementById('panelOverlay').classList.add('open');
        document.getElementById('detailPanel').classList.add('open');
    }
    function closeDetail() {
        document.getElementById('panelOverlay').classList.remove('open');
        document.getElementById('detailPanel').classList.remove('open');
    }

    /* ── Delete ── */
    function openDelete(id, ticket) {
        document.getElementById('delDesc').textContent = `Tiket ${ticket} akan dihapus permanen dan tidak bisa dikembalikan.`;
        document.getElementById('delForm').action = `/laporan/anonim/${id}`;
        document.getElementById('delOverlay').classList.add('open');
    }
    function closeDelete() { document.getElementById('delOverlay').classList.remove('open'); }

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') { closeDetail(); closeDelete(); }
    });
</script>
@endpush