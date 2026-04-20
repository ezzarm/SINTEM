{{-- resources/views/admin/pengumuman/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Pengumuman – Admin SINTEM')

@section('topbar')
<div style="display:flex;align-items:center;justify-content:space-between;padding:14px 32px;border-bottom:1px solid #f0f0f5;background:#fff;">
    <p style="font-size:13.5px;font-weight:700;color:#1a1a2e;">Selamat Datang, {{ Auth::user()->name }}!</p>
    <a href="{{ route('admin.pengumuman.buat') }}" class="btn-publish">
        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 5v14M5 12h14"/>
        </svg>
        Tambah Pengumuman
    </a>
</div>
@endsection

@section('header', 'Pengumuman')
@section('subheader', 'Kelola semua pengumuman sekolah.')

@push('styles')
<style>
    .page-body { padding:0!important;overflow:hidden!important;display:flex;flex-direction:column; }
    .main-content { background:#fff!important; }
    .page-header  { padding:16px 32px 14px!important;background:#fff!important; }

    .btn-publish {
        display:inline-flex;align-items:center;gap:6px;
        padding:8px 16px;background:linear-gradient(135deg,#9025FB,#4617D3);
        color:#fff;font-size:13px;font-weight:700;font-family:'Lato',sans-serif;
        border-radius:6px;border:none;cursor:pointer;text-decoration:none;
        box-shadow:0 2px 8px rgba(109,40,217,0.2);transition:opacity 0.15s,transform 0.15s;
    }
    .btn-publish:hover { opacity:0.88;transform:translateY(-1px); }

    /* Toolbar */
    .adm-toolbar {
        flex-shrink:0;padding:14px 32px 12px;border-bottom:1px solid #f0f0f5;background:#fff;
        display:flex;align-items:center;justify-content:space-between;gap:8px;flex-wrap:wrap;
    }
    .adm-toolbar-left { display:flex;align-items:center;gap:6px; }
    .adm-dd { position:relative;display:inline-block; }
    .adm-dd-trigger {
        display:inline-flex;align-items:center;gap:6px;padding:6px 10px;
        border:1px solid #e5e7eb;border-radius:5px;font-size:12.5px;
        font-family:'Lato',sans-serif;font-weight:600;color:#374151;background:#fff;
        cursor:pointer;outline:none;transition:border-color 0.12s;white-space:nowrap;
    }
    .adm-dd-trigger:hover { border-color:#c4b5fd; }
    .adm-dd.open .adm-dd-trigger { border-color:#7c3aed;box-shadow:0 0 0 2px rgba(124,58,237,0.1); }
    .adm-chevron { transition:transform 0.2s; }
    .adm-dd.open .adm-chevron { transform:rotate(180deg); }
    .adm-dd-menu {
        display:none;position:absolute;top:calc(100% + 4px);left:0;min-width:140px;
        background:#fff;border:1px solid #e5e7eb;border-radius:6px;
        box-shadow:0 4px 16px rgba(0,0,0,0.08);z-index:100;padding:4px;
    }
    .adm-dd.open .adm-dd-menu { display:block;animation:ddFade 0.15s ease; }
    @keyframes ddFade { from{opacity:0;transform:translateY(-4px)}to{opacity:1;transform:translateY(0)} }
    .adm-dd-opt {
        display:flex;align-items:center;gap:8px;width:100%;padding:7px 10px;
        font-size:13px;font-family:'Lato',sans-serif;font-weight:500;color:#374151;
        background:none;border:none;border-radius:4px;cursor:pointer;text-align:left;
        transition:background 0.1s,color 0.1s;
    }
    .adm-dd-opt svg { opacity:0;flex-shrink:0;stroke:#7c3aed; }
    .adm-dd-opt:hover { background:#f4f0ff;color:#4f28d9; }
    .adm-dd-opt.selected { color:#4f28d9;font-weight:700; }
    .adm-dd-opt.selected svg { opacity:1; }
    .adm-search-wrap { position:relative;display:flex;align-items:center; }
    .adm-search-wrap .adm-si { position:absolute;left:9px;color:#b0b0c0;pointer-events:none;display:flex; }
    .adm-search {
        padding:6px 12px 6px 30px;border:1px solid #e5e7eb;border-radius:5px;
        font-size:12.5px;font-family:'Lato',sans-serif;color:#374151;background:#fff;
        width:240px;outline:none;transition:border-color 0.12s;
    }
    .adm-search::placeholder { color:#c4c4cc; }
    .adm-search:focus { border-color:#7c3aed;box-shadow:0 0 0 2px rgba(124,58,237,0.1); }
    .adm-per-page-select {
        padding:5px 28px 5px 8px;border:1px solid #e5e7eb;border-radius:5px;
        font-size:12.5px;font-family:'Lato',sans-serif;outline:none;
        color:#374151;background:#fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2.5'%3E%3Cpath d='M19 9l-7 7-7-7'/%3E%3C/svg%3E") no-repeat right 8px center;
        appearance:none;-webkit-appearance:none;cursor:pointer;transition:border-color 0.12s;
    }
    .adm-per-page-select:focus { border-color:#7c3aed;box-shadow:0 0 0 2px rgba(124,58,237,0.1); }

    /* Table */
    .adm-body { flex:1;min-height:0;overflow-y:auto;scrollbar-width:none;-ms-overflow-style:none; }
    .adm-body::-webkit-scrollbar { display:none; }
    .adm-table-wrap { padding:20px 32px 32px; }
    table { width:100%;border-collapse:collapse; }
    thead th {
        padding:10px 14px;text-align:left;font-size:12px;font-weight:700;
        color:#6b7280;background:#f9f9fb;border-bottom:1px solid #ebebf0;
    }
    thead th:first-child { border-radius:8px 0 0 0; }
    thead th:last-child  { border-radius:0 8px 0 0; }
    tbody tr { border-bottom:1px solid #f5f5f7;transition:background 0.1s; }
    tbody tr:hover { background:#fafafa; }
    tbody tr:last-child { border-bottom:none; }
    tbody td { padding:12px 14px;font-size:13px;color:#374151;vertical-align:middle; }
    .td-num   { color:#9ca3af;font-size:12px;width:40px; }
    .td-title { font-weight:600;color:#1a1a2e;max-width:280px; }
    .td-title span { display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden; }

    .status-badge { font-size:11px;font-weight:700;padding:2px 10px;border-radius:4px;border:1px solid;display:inline-block; }
    .status-published { background:#f0fdf4;color:#16a34a;border-color:#bbf7d0; }
    .status-draft     { background:#f9fafb;color:#6b7280;border-color:#e5e7eb; }

    /* ── Action buttons with distinct colors ── */
    .action-group { display:flex;align-items:center;justify-content:center;gap:5px; }
    .action-btn {
        display:inline-flex;align-items:center;justify-content:center;
        width:28px;height:28px;border-radius:5px;border:1px solid;
        cursor:pointer;transition:background 0.12s,color 0.12s,border-color 0.12s;
        flex-shrink:0;
    }
    /* Edit / Pen — Blue */
    .action-btn-edit    { color:#1d4ed8;border-color:#bfdbfe;background:#eff6ff; }
    .action-btn-edit:hover { background:#dbeafe;color:#1e40af;border-color:#93c5fd; }
    /* Archive / Folder — Amber */
    .action-btn-archive { color:#d97706;border-color:#fde68a;background:#fffbeb; }
    .action-btn-archive:hover { background:#fef3c7;color:#92400e;border-color:#fcd34d; }
    /* Delete / Trash — Red */
    .action-btn-delete  { color:#dc2626;border-color:#fecaca;background:#fef2f2; }
    .action-btn-delete:hover { background:#fee2e2;color:#991b1b;border-color:#fca5a5; }

    /* Pagination */
    .adm-pagination {
        display:flex;align-items:center;justify-content:space-between;
        padding:14px 32px;border-top:1px solid #f0f0f5;
        font-size:13px;color:#6b7280;flex-shrink:0;background:#fff;
    }
    .adm-page-btns { display:flex;align-items:center;gap:4px; }
    .adm-page-btn {
        display:inline-flex;align-items:center;justify-content:center;
        width:28px;height:28px;border-radius:5px;border:1px solid #e5e7eb;
        background:#fff;cursor:pointer;font-size:13px;color:#374151;
        text-decoration:none;transition:background 0.1s;
    }
    .adm-page-btn:hover { background:#f4f0ff;color:#4f28d9;border-color:#c4b5fd; }
    .adm-page-btn.active { background:#ede9fe;color:#4f28d9;border-color:#c4b5fd;font-weight:700;pointer-events:none; }
    .adm-page-btn.disabled { opacity:0.4;pointer-events:none; }

    /* Alert */
    .adm-alert { margin:16px 32px 0;padding:10px 14px;border-radius:6px;font-size:13px;display:flex;align-items:center;gap:8px;flex-shrink:0;transition:opacity 0.4s; }
    .adm-alert-success { background:#f0fdf4;border:1px solid #bbf7d0;color:#16a34a; }
    .adm-alert-error   { background:#fef2f2;border:1px solid #fecaca;color:#dc2626; }

    /* ── CARD VIEW (breakpoint ≤900px) ── */
    .cards-wrap { display:none;padding:16px 16px 32px;gap:12px;flex-direction:column; }
    .item-card { border:1px solid #f0f0f5;border-radius:10px;overflow:hidden;background:#fff;box-shadow:0 1px 4px rgba(0,0,0,0.04);transition:box-shadow 0.15s,border-color 0.15s; }
    .item-card:hover { box-shadow:0 4px 16px rgba(109,40,217,0.08);border-color:#e9d5ff; }
    .card-photo { width:100%;height:140px;object-fit:cover;display:block; }
    .card-photo-placeholder { width:100%;height:80px;background:#f4f4f8;display:flex;align-items:center;justify-content:center; }
    .card-body { padding:12px 14px; }
    .card-title { font-size:14px;font-weight:700;color:#1a1a2e;margin-bottom:4px;line-height:1.3; }
    .card-meta  { font-size:12px;color:#9ca3af;margin-bottom:8px; }
    .card-foot  { display:flex;align-items:center;gap:6px;flex-wrap:wrap;padding-top:8px;border-top:1px solid #f5f5f7; }

    @media (max-width:900px) {
        .adm-table-wrap { display:none; }
        .cards-wrap { display:flex; }
        .adm-search { width:160px; }
        .adm-toolbar { padding:12px 16px 10px; }
        .adm-pagination { padding:12px 16px; }
        .adm-alert { margin:12px 16px 0; }
    }

    /* ══ PANEL OVERLAY ══ */
    .panel-overlay { display:none;position:fixed;inset:0;z-index:200;background:rgba(0,0,0,0.3); }
    .panel-overlay.open { display:block;animation:fadeOv 0.18s ease; }
    @keyframes fadeOv { from{opacity:0}to{opacity:1} }

    /* ══ EDIT SIDE PANEL — full height, right side, like detail laporan ══ */
    .edit-panel {
        position:fixed;right:-500px;top:0;bottom:0;
        width:420px;max-width:100vw;background:#fff;
        box-shadow:-4px 0 32px rgba(0,0,0,0.12);
        z-index:300;display:flex;flex-direction:column;
        transition:right 0.3s cubic-bezier(0.22,1,0.36,1);
    }
    .edit-panel.open { right:0; }

    .ep-head {
        display:flex;align-items:center;justify-content:space-between;
        padding:16px 20px;border-bottom:1px solid #f0f0f5;flex-shrink:0;
    }
    .ep-title { font-size:14px;font-weight:700;color:#1a1a2e; }
    .ep-head-actions { display:flex;gap:6px; }
    .ep-icon-btn {
        width:28px;height:28px;border-radius:6px;border:1px solid #e5e7eb;
        background:#fff;cursor:pointer;display:flex;align-items:center;
        justify-content:center;color:#9ca3af;
        transition:background 0.12s,color 0.12s,border-color 0.12s;
    }
    .ep-icon-btn:hover       { background:#f4f0ff;color:#4f28d9;border-color:#c4b5fd; }
    .ep-icon-btn.close:hover { background:#fef2f2;color:#dc2626;border-color:#fecaca; }

    .ep-body { flex:1;overflow-y:auto;scrollbar-width:none;-ms-overflow-style:none; }
    .ep-body::-webkit-scrollbar { display:none; }

    /* Photo at top of panel */
    .ep-photo { width:100%;height:200px;object-fit:cover;display:block; }
    .ep-photo-placeholder { width:100%;height:160px;background:#f4f4f8;display:flex;align-items:center;justify-content:center; }

    .ep-inner { padding:18px 20px; }
    .ep-section-label { font-size:11px;font-weight:700;color:#9ca3af;letter-spacing:0.06em;text-transform:uppercase;margin-bottom:8px; }
    .ep-divider { height:1px;background:#f0f0f5;margin:14px 0; }

    /* Form fields */
    .form-field { margin-bottom:16px; }
    .form-label { display:block;font-size:12.5px;font-weight:700;color:#374151;margin-bottom:6px; }
    .form-textarea { resize:vertical;min-height:140px;line-height:1.6; }
    .form-input,.form-textarea,.form-select {
        width:100%;padding:9px 12px;border:1px solid #e5e7eb;border-radius:6px;
        font-size:13px;font-family:'Lato',sans-serif;color:#111;background:#fff;
        outline:none;transition:border-color 0.15s,box-shadow 0.15s;
    }
    .form-select {
        padding-right:32px;
        appearance:none;-webkit-appearance:none;cursor:pointer;
        background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2.5'%3E%3Cpath d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
        background-repeat:no-repeat;background-position:right 10px center;
    }
    .form-select:hover { border-color:#c4b5fd; }
    .form-input:focus,.form-textarea:focus,.form-select:focus {
        border-color:#7c3aed;box-shadow:0 0 0 2px rgba(124,58,237,0.1);
    }

    /* Photo change overlay */
    .ep-photo-wrap { position:relative;border-radius:0;overflow:hidden;background:#f4f4f8;margin-bottom:0; }
    .ep-photo-change-btn {
        position:absolute;bottom:10px;right:10px;
        display:inline-flex;align-items:center;gap:5px;
        padding:5px 10px;background:rgba(0,0,0,0.55);color:#fff;
        font-size:11px;font-weight:700;font-family:'Lato',sans-serif;
        border-radius:5px;cursor:pointer;border:none;
    }
    .ep-photo-change-btn input[type=file] { position:absolute;inset:0;opacity:0;cursor:pointer; }

    .ep-photo-upload-empty {
        width:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;
        gap:6px;background:#f4f4f8;border:1.5px dashed #d1d5db;border-radius:8px;
        padding:20px;cursor:pointer;position:relative;
        transition:border-color 0.15s,background 0.15s;
    }
    .ep-photo-upload-empty:hover { border-color:#7c3aed;background:#faf8ff; }
    .ep-photo-upload-empty input[type=file] { position:absolute;inset:0;opacity:0;cursor:pointer; }

    .ep-foot {
        padding:14px 20px;border-top:1px solid #f0f0f5;
        display:flex;gap:8px;justify-content:flex-end;flex-shrink:0;
    }
    .btn-ep-cancel {
        padding:8px 18px;border:1px solid #e5e7eb;border-radius:6px;
        background:#fff;color:#6b7280;font-size:13px;font-weight:700;
        font-family:'Lato',sans-serif;cursor:pointer;
        transition:background 0.12s,border-color 0.12s;
    }
    .btn-ep-cancel:hover { background:#f9f9fb;border-color:#c4b5fd;color:#4f28d9; }
    .btn-ep-save {
        padding:8px 20px;background:linear-gradient(135deg,#9025FB,#4617D3);
        color:#fff;font-size:13px;font-weight:700;font-family:'Lato',sans-serif;
        border:none;border-radius:6px;cursor:pointer;
        box-shadow:0 2px 8px rgba(109,40,217,0.2);transition:opacity 0.15s;
    }
    .btn-ep-save:hover { opacity:0.88; }

    /* Delete confirm */
    .del-overlay { display:none;position:fixed;inset:0;z-index:500;background:rgba(0,0,0,0.35);align-items:center;justify-content:center; }
    .del-overlay.open { display:flex; }
    .del-box { background:#fff;border-radius:12px;padding:24px;max-width:320px;width:100%;box-shadow:0 20px 60px rgba(0,0,0,0.18);text-align:center;animation:slideUp 0.2s ease; }
    @keyframes slideUp { from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)} }
    .del-icon { margin:0 auto 10px;display:flex;align-items:center;justify-content:center;width:48px;height:48px;border-radius:50%;background:#fef2f2; }
    .del-title { font-size:15px;font-weight:700;color:#1a1a2e;margin-bottom:6px; }
    .del-desc  { font-size:13px;color:#6b7280;margin-bottom:20px;line-height:1.5; }
    .del-actions { display:flex;gap:8px;justify-content:center; }
    .btn-del-cancel  { padding:8px 20px;border:1px solid #e5e7eb;border-radius:6px;background:#fff;color:#374151;font-size:13px;font-weight:700;font-family:'Lato',sans-serif;cursor:pointer; }
    .btn-del-confirm { padding:8px 20px;background:#dc2626;color:#fff;font-size:13px;font-weight:700;font-family:'Lato',sans-serif;border:none;border-radius:6px;cursor:pointer;transition:opacity 0.15s; }
    .btn-del-confirm:hover { opacity:0.88; }

    /* Archive confirm */
    .arc-overlay { display:none;position:fixed;inset:0;z-index:500;background:rgba(0,0,0,0.35);align-items:center;justify-content:center; }
    .arc-overlay.open { display:flex; }
    .arc-box { background:#fff;border-radius:12px;padding:24px;max-width:320px;width:100%;box-shadow:0 20px 60px rgba(0,0,0,0.18);text-align:center;animation:slideUp 0.2s ease; }
    .arc-icon { margin:0 auto 10px;display:flex;align-items:center;justify-content:center;width:48px;height:48px;border-radius:50%;background:#fffbeb; }
    .arc-title { font-size:15px;font-weight:700;color:#1a1a2e;margin-bottom:6px; }
    .arc-desc  { font-size:13px;color:#6b7280;margin-bottom:20px;line-height:1.5; }
    .arc-actions { display:flex;gap:8px;justify-content:center; }
    .btn-arc-cancel  { padding:8px 20px;border:1px solid #e5e7eb;border-radius:6px;background:#fff;color:#374151;font-size:13px;font-weight:700;font-family:'Lato',sans-serif;cursor:pointer; }
    .btn-arc-confirm { padding:8px 20px;background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;font-size:13px;font-weight:700;font-family:'Lato',sans-serif;border:none;border-radius:6px;cursor:pointer;transition:opacity 0.15s; }
    .btn-arc-confirm:hover { opacity:0.88; }

    /* ══════════════════════════════════════════════
       BREAKPOINTS
       ▸ Tablet  768–1023px : reduce padding, table horizontal scroll
       ▸ Mobile  < 768px   : compact toolbar, full-width search
       ▸ XS      < 480px   : stack toolbar
    ══════════════════════════════════════════════ */

    /* ── Tablet ── */
    @media (max-width: 1023px) {
        .adm-toolbar    { padding: 12px 20px 10px; }
        .adm-table-wrap { padding: 14px 20px 24px; overflow-x: auto; }
        table           { min-width: 620px; }
        .adm-pagination { padding: 12px 20px; }
    }

    /* ── Mobile ── */
    @media (max-width: 767px) {
        .adm-toolbar      { padding: 10px 16px 8px; flex-wrap: wrap; gap: 8px; }
        .adm-toolbar-left { flex-wrap: wrap; }
        .adm-search       { width: 100%; }
        .adm-search-wrap  { flex: 1; min-width: 0; }
        .adm-table-wrap   { padding: 10px 16px 20px; }
        .adm-pagination   { padding: 10px 16px; flex-wrap: wrap; gap: 6px; font-size: 12px; }
    }

    /* ── Small mobile ── */
    @media (max-width: 479px) {
        .adm-toolbar { flex-direction: column; align-items: stretch; }
        table        { min-width: 520px; }
    }
</style>
@endpush

@section('content')

{{-- Alerts --}}
@if(session('success'))
<div class="adm-alert adm-alert-success" id="flash-alert">
    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="adm-alert adm-alert-error" id="flash-alert">
    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><path d="M12 8v4m0 4h.01" stroke-width="2.5" stroke-linecap="round"/></svg>
    {{ session('error') }}
</div>
@endif

{{-- Toolbar --}}
<form method="GET" action="{{ route('admin.pengumuman.index') }}" id="filterForm">
    <div class="adm-toolbar">
        <div class="adm-toolbar-left">
            <div class="adm-dd" id="filterDd">
                <button type="button" class="adm-dd-trigger" onclick="toggleDd('filterDd')">
                    <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                    <span id="filterLabel">{{ $filter === 'semua' ? 'Semua' : ($filter === 'published' ? 'Published' : 'Draft') }}</span>
                    <svg class="adm-chevron" width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="adm-dd-menu">
                    @foreach(['semua'=>'Semua','published'=>'Published','draft'=>'Draft'] as $val=>$lbl)
                    <button type="button" class="adm-dd-opt {{ $filter===$val?'selected':'' }}" onclick="selectFilter('{{ $val }}','{{ $lbl }}')">
                        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/></svg>
                        {{ $lbl }}
                    </button>
                    @endforeach
                </div>
                <input type="hidden" name="filter" id="filterInput" value="{{ $filter }}">
            </div>

            <div class="adm-search-wrap">
                <span class="adm-si"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8" stroke-width="2"/><path d="M21 21l-4.35-4.35" stroke-width="2" stroke-linecap="round"/></svg></span>
                <input type="text" name="search" class="adm-search" placeholder="Telusuri pengumuman..." value="{{ $search }}" oninput="debounce(this.form)">
            </div>
        </div>

        <div style="display:flex;align-items:center;gap:6px;font-size:12.5px;color:#6b7280;">
            <span>Per halaman:</span>
            <select name="per_page" onchange="this.form.submit()" class="adm-per-page-select">
                @foreach([10,25,50] as $n)
                <option value="{{ $n }}" {{ $perPage==$n?'selected':'' }}>{{ $n }}</option>
                @endforeach
            </select>
        </div>
    </div>
</form>

{{-- Table --}}
<div class="adm-body">
    <div class="adm-table-wrap">
        <table>
            <thead>
                <tr>
                    <th class="td-num">#</th>
                    <th>Judul Pengumuman</th>
                    <th>Tanggal Publish</th>
                    <th>Dibaca</th>
                    <th>Status</th>
                    <th style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $i => $item)
                @php $num = ($page - 1) * $perPage + $i + 1; @endphp
                <tr>
                    <td class="td-num">{{ $num }}</td>
                    <td class="td-title"><span>{{ $item->title }}</span></td>
                    <td style="font-size:12.5px;color:#6b7280;white-space:nowrap;">
                        @if($item->is_published)
                            {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}<br>
                            <span style="color:#9ca3af;">{{ \Carbon\Carbon::parse($item->created_at)->format('H:i') }} WIB</span>
                        @else
                            <span style="color:#9ca3af;">—</span>
                        @endif
                    </td>
                    <td style="color:#374151;">—</td>
                    <td>
                        <span class="status-badge {{ $item->is_published ? 'status-published' : 'status-draft' }}">
                            {{ $item->is_published ? 'Published' : 'Draft' }}
                        </span>
                    </td>
                    <td>
                        <div class="action-group">
                            {{-- Edit / Pen — Blue --}}
                            <button type="button"
                                    class="action-btn action-btn-edit"
                                    title="Edit Pengumuman"
                                    onclick="openEditPanel({{ json_encode([
                                        'id'           => $item->id,
                                        'title'        => $item->title,
                                        'content'      => $item->content,
                                        'is_published' => $item->is_published,
                                        'photo'        => optional($item->photos->first())->file_data
                                                            ? $item->photos->first()->file_data
                                                            : (optional($item->photos->first())->file_path
                                                                ? asset('storage/' . $item->photos->first()->file_path)
                                                                : null),
                                    ]) }})">
                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 4H6a2 2 0 00-2 2v13a2 2 0 002 2h11a2 2 0 002-2v-5"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                            </button>

                            {{-- Archive / Folder — Amber --}}
                            <button type="button"
                                    class="action-btn action-btn-archive"
                                    title="{{ $item->is_published ? 'Arsipkan (jadikan Draft)' : 'Publish' }}"
                                    onclick="confirmArchive({{ $item->id }}, '{{ addslashes($item->title) }}', {{ $item->is_published ? 'true' : 'false' }})">
                                @if($item->is_published)
                                {{-- Folder open = published → click to archive --}}
                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/>
                                    <line stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" x1="8" y1="14" x2="16" y2="14"/>
                                </svg>
                                @else
                                {{-- Folder with slash = draft → click to publish --}}
                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/>
                                    <line stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" x1="9" y1="12" x2="15" y2="18"/>
                                    <line stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" x1="15" y1="12" x2="9" y2="18"/>
                                </svg>
                                @endif
                            </button>

                            {{-- Delete / Trash — Red --}}
                            <button type="button"
                                    class="action-btn action-btn-delete"
                                    title="Hapus Pengumuman"
                                    onclick="confirmDelete({{ $item->id }}, '{{ addslashes($item->title) }}')">
                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <polyline stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" points="3 6 5 6 21 6"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:48px;color:#9ca3af;font-size:13px;">
                        <svg width="36" height="36" fill="none" stroke="#d1d5db" viewBox="0 0 24 24" style="margin:0 auto 12px;display:block;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                            <rect x="9" y="3" width="6" height="4" rx="1" stroke-width="1.4"/>
                        </svg>
                        Tidak ada pengumuman.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- CARD VIEW (mobile/narrow) --}}
    <div class="cards-wrap">
        @forelse($items as $i => $item)
        @php
            $firstPhoto = $item->photos->first();
            $photo = $firstPhoto
                ? ($firstPhoto->file_data ?: ($firstPhoto->file_path ? asset('storage/'.$firstPhoto->file_path) : null))
                : null;
        @endphp
        <div class="item-card">
            @if($photo)
            <img class="card-photo" src="{{ $photo }}" alt="{{ $item->title }}" onerror="this.style.display='none'">
            @else
            <div class="card-photo-placeholder"><svg width="24" height="24" fill="none" stroke="#c4c4d4" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" stroke-width="1.5"/><circle cx="8.5" cy="8.5" r="1.5" stroke-width="1.5"/><polyline stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" points="21 15 16 10 5 21"/></svg></div>
            @endif
            <div class="card-body">
                <div class="card-title">{{ $item->title }}</div>
                <div class="card-meta">
                    {{ $item->is_published ? \Carbon\Carbon::parse($item->created_at)->format('d M Y, H:i').' WIB' : 'Belum dipublish' }}
                    · <span class="status-badge {{ $item->is_published ? 'status-published' : 'status-draft' }}">{{ $item->is_published ? 'Published' : 'Draft' }}</span>
                </div>
                <div class="card-foot">
                    <button type="button" class="action-btn action-btn-edit" title="Edit"
                        onclick="openEditPanel({{ json_encode(['id'=>$item->id,'title'=>$item->title,'content'=>$item->content,'is_published'=>$item->is_published,'photo'=>$photo]) }})">
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 4H6a2 2 0 00-2 2v13a2 2 0 002 2h11a2 2 0 002-2v-5"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </button>
                    <button type="button" class="action-btn action-btn-archive" title="{{ $item->is_published ? 'Arsipkan' : 'Publish' }}"
                        onclick="confirmArchive({{ $item->id }}, '{{ addslashes($item->title) }}', {{ $item->is_published ? 'true' : 'false' }})">
                        @if($item->is_published)
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/>
                            <line stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" x1="8" y1="14" x2="16" y2="14"/>
                        </svg>
                        @else
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/>
                            <line stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" x1="9" y1="12" x2="15" y2="18"/>
                            <line stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" x1="15" y1="12" x2="9" y2="18"/>
                        </svg>
                        @endif
                    </button>
                    <button type="button" class="action-btn action-btn-delete" title="Hapus"
                        onclick="confirmDelete({{ $item->id }}, '{{ addslashes($item->title) }}')">
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" points="3 6 5 6 21 6"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div style="text-align:center;padding:48px 16px;color:#9ca3af;font-size:13px;">Tidak ada pengumuman.</div>
        @endforelse
    </div>
</div>

{{-- Pagination --}}
@php $lastPage = max(1, (int) ceil($total / $perPage)); @endphp
<div class="adm-pagination">
    <span>{{ ($page-1)*$perPage+1 }}–{{ min($page*$perPage,$total) }} dari {{ $total }} pengumuman</span>
    <div class="adm-page-btns">
        <a href="?{{ http_build_query(array_merge(request()->query(),['page'=>max(1,$page-1)])) }}"
           class="adm-page-btn {{ $page<=1?'disabled':'' }}">
            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 18l-6-6 6-6"/></svg>
        </a>
        @for($p=1;$p<=$lastPage;$p++)
        <a href="?{{ http_build_query(array_merge(request()->query(),['page'=>$p])) }}"
           class="adm-page-btn {{ $p==$page?'active':'' }}">{{ $p }}</a>
        @endfor
        <a href="?{{ http_build_query(array_merge(request()->query(),['page'=>min($lastPage,$page+1)])) }}"
           class="adm-page-btn {{ $page>=$lastPage?'disabled':'' }}">
            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 18l6-6-6-6"/></svg>
        </a>
    </div>
</div>

{{-- ══ PANEL OVERLAY ══ --}}
<div class="panel-overlay" id="panelOverlay" onclick="closeEditPanel()"></div>

{{-- ══ EDIT SIDE PANEL — right, full height, same structure as detail laporan ══ --}}
<div class="edit-panel" id="editPanel">
    <div class="ep-head">
        <span class="ep-title">Edit Pengumuman</span>
        <div class="ep-head-actions">
            <button type="button" class="ep-icon-btn close" onclick="closeEditPanel()" title="Tutup">
                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    <div class="ep-body" id="epBody">
        {{-- Photo banner at top (filled by JS) --}}
        <div id="epPhotoTop"></div>

        <form method="POST" id="editForm" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="ep-inner">

                {{-- Photo replace control --}}
                <div class="form-field">
                    <div class="ep-section-label">Foto / Banner</div>
                    <div id="epPhotoControl"></div>
                </div>

                <div class="ep-divider"></div>

                <div class="form-field">
                    <label class="form-label">Judul Pengumuman <span style="color:#dc2626;">*</span></label>
                    <input type="text" name="title" id="epTitle" class="form-input" required>
                </div>

                <div class="form-field">
                    <label class="form-label">Isi Pengumuman <span style="color:#dc2626;">*</span></label>
                    <textarea name="content" id="epContent" class="form-textarea" required></textarea>
                </div>

                <div class="form-field">
                    <label class="form-label">Status</label>
                    <select name="is_published" id="epStatus" class="form-select">
                        <option value="1">Published</option>
                        <option value="0">Draft</option>
                    </select>
                </div>

            </div>
        </form>
    </div>

    <div class="ep-foot">
        <button type="button" class="btn-ep-cancel" onclick="closeEditPanel()">Batal</button>
        <button type="button" class="btn-ep-save" onclick="document.getElementById('editForm').submit()">Simpan Perubahan</button>
    </div>
</div>

{{-- ══ DELETE CONFIRM ══ --}}
<div class="del-overlay" id="delOverlay">
    <div class="del-box">
        <div class="del-icon">
            <svg width="22" height="22" fill="none" stroke="#dc2626" viewBox="0 0 24 24">
                <polyline stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" points="3 6 5 6 21 6"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/>
            </svg>
        </div>
        <div class="del-title">Hapus Pengumuman?</div>
        <div class="del-desc" id="delDesc">Pengumuman ini akan dihapus permanen.</div>
        <div class="del-actions">
            <button type="button" class="btn-del-cancel" onclick="closeDelConfirm()">Batal</button>
            <form id="delForm" method="POST" style="display:inline;">
                @csrf @method('DELETE')
                <button type="submit" class="btn-del-confirm">Hapus</button>
            </form>
        </div>
    </div>
</div>

{{-- ══ ARCHIVE / TOGGLE CONFIRM ══ --}}
<div class="arc-overlay" id="arcOverlay">
    <div class="arc-box">
        <div class="arc-icon">
            <svg width="22" height="22" fill="none" stroke="#d97706" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/>
            </svg>
        </div>
        <div class="arc-title" id="arcTitle">Arsipkan Pengumuman?</div>
        <div class="arc-desc"  id="arcDesc">Pengumuman akan dijadikan draft.</div>
        <div class="arc-actions">
            <button type="button" class="btn-arc-cancel" onclick="closeArcConfirm()">Batal</button>
            <form id="arcForm" method="POST" style="display:inline;">
                @csrf @method('PATCH')
                <button type="submit" class="btn-arc-confirm" id="arcConfirmBtn">Arsipkan</button>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    /* ── Debounce ── */
    let _st;
    function debounce(form) { clearTimeout(_st); _st = setTimeout(()=>form.submit(),500); }

    /* ── Auto-dismiss flash alert ── */
    (function() {
        const alert = document.getElementById('flash-alert');
        if (alert) {
            setTimeout(() => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 400);
            }, 4000);
        }
    })();

    /* ── Dropdown ── */
    function toggleDd(id) { document.getElementById(id).classList.toggle('open'); }
    function selectFilter(val,lbl) {
        document.getElementById('filterInput').value = val;
        document.getElementById('filterLabel').textContent = lbl;
        document.querySelectorAll('.adm-dd-opt').forEach(o => o.classList.toggle('selected', o.textContent.trim()===lbl));
        document.getElementById('filterDd').classList.remove('open');
        document.getElementById('filterForm').submit();
    }
    document.addEventListener('click', e => {
        ['filterDd'].forEach(id => { const el=document.getElementById(id); if(el&&!el.contains(e.target)) el.classList.remove('open'); });
    });

    /* ── Edit Side Panel ── */
    function openEditPanel(data) {
        document.getElementById('epTitle').value   = data.title;
        document.getElementById('epContent').value = data.content ?? '';
        document.getElementById('epStatus').value  = data.is_published ? '1' : '0';
        document.getElementById('editForm').action = `/admin/pengumuman/${data.id}`;

        // Render top photo banner
        const topWrap = document.getElementById('epPhotoTop');
        if (data.photo) {
            topWrap.innerHTML = `
                <div class="ep-photo-wrap">
                    <img class="ep-photo" src="${data.photo}" alt="" onerror="this.style.display='none'">
                    <label class="ep-photo-change-btn">
                        <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4H6a2 2 0 00-2 2v13a2 2 0 002 2h11a2 2 0 002-2v-5"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        Ganti Foto
                        <input type="file" name="photo" accept="image/*" onchange="handleEpPhoto(this, 'top')">
                    </label>
                </div>`;
        } else {
            topWrap.innerHTML = `<div class="ep-photo-placeholder"><svg width="32" height="32" fill="none" stroke="#c4c4d4" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" stroke-width="1.5"/><circle cx="8.5" cy="8.5" r="1.5" stroke-width="1.5"/><polyline stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" points="21 15 16 10 5 21"/></svg></div>`;
        }

        // Render photo upload control (in form)
        renderEpControl(data.photo);

        document.getElementById('panelOverlay').classList.add('open');
        document.getElementById('editPanel').classList.add('open');
        document.getElementById('epBody').scrollTop = 0;
    }

    function closeEditPanel() {
        document.getElementById('panelOverlay').classList.remove('open');
        document.getElementById('editPanel').classList.remove('open');
    }

    function renderEpControl(url) {
        const ctrl = document.getElementById('epPhotoControl');
        if (url) {
            ctrl.innerHTML = `
                <div class="ep-photo-wrap" style="border-radius:8px;">
                    <img class="ep-edit-photo-preview" src="${url}" alt="" style="width:100%;height:140px;object-fit:cover;display:block;border-radius:8px;">
                    <label class="ep-photo-change-btn">
                        <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4H6a2 2 0 00-2 2v13a2 2 0 002 2h11a2 2 0 002-2v-5"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        Ganti Foto
                        <input type="file" name="photo" accept="image/*" onchange="handleEpPhoto(this, 'ctrl')">
                    </label>
                </div>`;
        } else {
            ctrl.innerHTML = `
                <label class="ep-photo-upload-empty">
                    <svg width="22" height="22" fill="none" stroke="#9ca3af" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    <span style="font-size:12px;color:#9ca3af;font-family:'Lato',sans-serif;">Tambah foto</span>
                    <input type="file" name="photo" accept="image/*" onchange="handleEpPhoto(this, 'ctrl')">
                </label>`;
        }
    }

    function handleEpPhoto(input, source) {
        if (!input.files || !input.files[0]) return;
        const file = input.files[0];
        const reader = new FileReader();
        reader.onload = e => {
            const url = e.target.result;
            // Update top banner
            const topWrap = document.getElementById('epPhotoTop');
            topWrap.innerHTML = `
                <div class="ep-photo-wrap">
                    <img class="ep-photo" src="${url}" alt="">
                    <label class="ep-photo-change-btn">
                        <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4H6a2 2 0 00-2 2v13a2 2 0 002 2h11a2 2 0 002-2v-5"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        Ganti Foto
                        <input type="file" name="photo" accept="image/*" onchange="handleEpPhoto(this, 'top')">
                    </label>
                </div>`;
            // Update control
            renderEpControl(url);
            // Sync file to form
            const dt = new DataTransfer(); dt.items.add(file);
            document.querySelectorAll('#editForm input[type=file][name=photo]').forEach(el => { try { el.files = dt.files; } catch(e){} });
        };
        reader.readAsDataURL(file);
    }

    /* ── Delete ── */
    function confirmDelete(id, title) {
        document.getElementById('delDesc').textContent = `"${title}" akan dihapus permanen.`;
        document.getElementById('delForm').action = `/admin/pengumuman/${id}`;
        document.getElementById('delOverlay').classList.add('open');
    }
    function closeDelConfirm() { document.getElementById('delOverlay').classList.remove('open'); }

    /* ── Archive / Toggle ── */
    function confirmArchive(id, title, isPublished) {
        document.getElementById('arcTitle').textContent   = isPublished ? 'Arsipkan Pengumuman?' : 'Publish Pengumuman?';
        document.getElementById('arcDesc').textContent    = isPublished ? `"${title}" akan dijadikan draft.` : `"${title}" akan dipublish.`;
        document.getElementById('arcConfirmBtn').textContent = isPublished ? 'Arsipkan' : 'Publish';
        document.getElementById('arcForm').action = `/admin/pengumuman/${id}/toggle-draft`;
        document.getElementById('arcOverlay').classList.add('open');
    }
    function closeArcConfirm() { document.getElementById('arcOverlay').classList.remove('open'); }

    /* ── Escape key ── */
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') { closeEditPanel(); closeDelConfirm(); closeArcConfirm(); }
    });
</script>
@endpush
