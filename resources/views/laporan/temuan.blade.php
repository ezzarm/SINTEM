{{-- resources/views/laporan/temuan.blade.php --}}
@extends('layouts.app')

@section('title', 'Laporan Temuan – SINTEM')

@section('topbar')
    @include('components.topbar')
@endsection

@section('header', 'Laporan Temuan')
@section('subheader', 'Kelola laporan temuan dan kehilangan barangmu.')

@push('styles')
<style>
    .page-body { padding:0!important; overflow:hidden!important; display:flex; flex-direction:column; }
    .main-content { background:#fff!important; }
    .page-header  { padding:16px 32px 14px!important; background:#fff!important; }

    /* ── Toolbar ── */
    .adm-toolbar { flex-shrink:0; padding:14px 32px 12px; border-bottom:1px solid #f0f0f5; background:#fff; display:flex; align-items:center; justify-content:space-between; gap:8px; flex-wrap:wrap; }
    .adm-toolbar-left { display:flex; align-items:center; gap:6px; }
    .adm-dd { position:relative; display:inline-block; }
    .adm-dd-trigger { display:inline-flex; align-items:center; gap:6px; padding:6px 10px; border:1px solid #e5e7eb; border-radius:5px; font-size:12.5px; font-family:'Lato',sans-serif; font-weight:600; color:#374151; background:#fff; cursor:pointer; outline:none; transition:border-color 0.12s; white-space:nowrap; }
    .adm-dd-trigger:hover { border-color:#c4b5fd; }
    .adm-dd.open .adm-dd-trigger { border-color:#7c3aed; box-shadow:0 0 0 2px rgba(124,58,237,0.1); }
    .adm-chevron { transition:transform 0.2s; }
    .adm-dd.open .adm-chevron { transform:rotate(180deg); }
    .adm-dd-menu { display:none; position:absolute; top:calc(100% + 4px); left:0; min-width:140px; background:#fff; border:1px solid #e5e7eb; border-radius:6px; box-shadow:0 4px 16px rgba(0,0,0,0.08); z-index:100; padding:4px; }
    .adm-dd.open .adm-dd-menu { display:block; animation:ddFade 0.15s ease; }
    @keyframes ddFade { from{opacity:0;transform:translateY(-4px)}to{opacity:1;transform:translateY(0)} }
    .adm-dd-opt { display:flex; align-items:center; gap:8px; width:100%; padding:7px 10px; font-size:13px; font-family:'Lato',sans-serif; font-weight:500; color:#374151; background:none; border:none; border-radius:4px; cursor:pointer; text-align:left; transition:background 0.1s,color 0.1s; }
    .adm-dd-opt svg { opacity:0; flex-shrink:0; stroke:#7c3aed; }
    .adm-dd-opt:hover { background:#f4f0ff; color:#4f28d9; }
    .adm-dd-opt.selected { color:#4f28d9; font-weight:700; }
    .adm-dd-opt.selected svg { opacity:1; }
    .adm-search-wrap { position:relative; display:flex; align-items:center; }
    .adm-search-wrap .adm-si { position:absolute; left:9px; color:#b0b0c0; pointer-events:none; display:flex; }
    .adm-search { padding:6px 12px 6px 30px; border:1px solid #e5e7eb; border-radius:5px; font-size:12.5px; font-family:'Lato',sans-serif; color:#374151; background:#fff; width:240px; outline:none; transition:border-color 0.12s; }
    .adm-search::placeholder { color:#c4c4cc; }
    .adm-search:focus { border-color:#7c3aed; box-shadow:0 0 0 2px rgba(124,58,237,0.1); }

    /* ── Body + table ── */
    .adm-body { flex:1; min-height:0; overflow-y:auto; scrollbar-width:none; -ms-overflow-style:none; }
    .adm-body::-webkit-scrollbar { display:none; }
    .adm-table-wrap { padding:20px 32px 32px; }
    table { width:100%; border-collapse:collapse; }
    thead th { padding:10px 14px; text-align:left; font-size:12px; font-weight:700; color:#6b7280; background:#f9f9fb; border-bottom:1px solid #ebebf0; }
    thead th:first-child { border-radius:8px 0 0 0; }
    thead th:last-child  { border-radius:0 8px 0 0; }
    tbody tr { border-bottom:1px solid #f5f5f7; transition:background 0.1s; }
    tbody tr:hover { background:#fafafa; }
    tbody tr:last-child { border-bottom:none; }
    tbody td { padding:12px 14px; font-size:13px; color:#374151; vertical-align:middle; }
    .td-num   { color:#9ca3af; font-size:12px; width:40px; }
    .td-title { font-weight:600; color:#1a1a2e; max-width:240px; }
    .td-title span { display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }

    /* ── Badges ── */
    .badge { font-size:11px; font-weight:700; padding:2px 10px; border-radius:4px; border:1px solid; display:inline-block; white-space:nowrap; }
    .badge-found    { background:#fffbeb; color:#92400e; border-color:#fde68a; }
    .badge-lost     { background:#fdf2f8; color:#9d174d; border-color:#f9a8d4; }
    .badge-pending  { background:#f9fafb; color:#6b7280; border-color:#e5e7eb; }
    .badge-approved { background:#f0fdf4; color:#16a34a; border-color:#bbf7d0; }
    .badge-solved   { background:#eff6ff; color:#1d4ed8; border-color:#bfdbfe; }

    /* ── Action btns ── */
    .action-btn { display:inline-flex; align-items:center; justify-content:center; width:28px; height:28px; border-radius:5px; border:1px solid #e5e7eb; background:#fff; cursor:pointer; color:#6b7280; transition:background 0.12s,color 0.12s,border-color 0.12s; margin-right:3px; }
    .action-btn:hover { background:#eff6ff; color:#1d4ed8; border-color:#bfdbfe; }
    .action-btn.edit { color:#d97706; border-color:#fde68a; background:#fffbeb; }
    .action-btn.edit:hover { background:#fef3c7; color:#92400e; border-color:#fcd34d; }
    .action-btn.danger { color:#dc2626; border-color:#fecaca; background:#fef2f2; }
    .action-btn.danger:hover { background:#fee2e2; color:#991b1b; border-color:#fca5a5; }

    /* ── Empty ── */
    .adm-empty { text-align:center; padding:56px 20px; color:#9ca3af; font-size:13px; }
    .adm-empty svg { margin:0 auto 12px; display:block; }

    /* ── Pagination ── */
    .adm-pagination { display:flex; align-items:center; justify-content:space-between; padding:14px 32px; border-top:1px solid #f0f0f5; font-size:13px; color:#6b7280; flex-shrink:0; background:#fff; }
    .adm-page-btns { display:flex; align-items:center; gap:4px; }
    .adm-page-btn { display:inline-flex; align-items:center; justify-content:center; width:28px; height:28px; border-radius:5px; border:1px solid #e5e7eb; background:#fff; cursor:pointer; font-size:13px; color:#374151; text-decoration:none; transition:background 0.1s; }
    .adm-page-btn:hover { background:#f4f0ff; color:#4f28d9; border-color:#c4b5fd; }
    .adm-page-btn.active { background:#ede9fe; color:#4f28d9; border-color:#c4b5fd; font-weight:700; pointer-events:none; }
    .adm-page-btn.disabled { opacity:0.4; pointer-events:none; }

    /* Mobile cards */
    .mobile-list { display:none; padding:12px 16px 32px; flex-direction:column; gap:10px; }
    .m-card { background:#fff; border:1px solid #ebebf0; border-radius:10px; padding:14px 16px; transition:border-color 0.15s; }
    .m-card:hover { border-color:#d4d0f0; }
    .m-card-top { display:flex; align-items:flex-start; justify-content:space-between; gap:8px; margin-bottom:8px; }
    .m-card-title { font-size:13.5px; font-weight:700; color:#1a1a2e; line-height:1.4; flex:1; min-width:0; }
    .m-card-badges { display:flex; gap:5px; flex-shrink:0; flex-wrap:wrap; justify-content:flex-end; }
    .m-card-meta { display:flex; flex-wrap:wrap; gap:6px 14px; margin-bottom:10px; }
    .m-card-meta-item { font-size:12px; color:#9ca3af; display:flex; align-items:center; gap:4px; }
    .m-card-desc { font-size:12.5px; color:#4b5563; line-height:1.55; margin-bottom:10px; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
    .m-card-actions { display:flex; gap:6px; }
    .m-action-btn { flex:1; display:inline-flex; align-items:center; justify-content:center; gap:5px; padding:7px 10px; border-radius:6px; border:1px solid; font-size:12px; font-weight:600; font-family:'Lato',sans-serif; cursor:pointer; transition:background 0.12s,color 0.12s,border-color 0.12s; color:#1d4ed8; border-color:#bfdbfe; background:#eff6ff; }
    .m-action-btn:hover { background:#dbeafe; color:#1e40af; border-color:#93c5fd; }
    .m-action-btn.edit { color:#d97706; border-color:#fde68a; background:#fffbeb; }
    .m-action-btn.edit:hover { background:#fef3c7; color:#92400e; border-color:#fcd34d; }
    .m-action-btn.danger { color:#dc2626; border-color:#fecaca; background:#fef2f2; }
    .m-action-btn.danger:hover { background:#fee2e2; color:#991b1b; border-color:#fca5a5; }

    @media (max-width:768px) {
        .adm-toolbar { padding:12px 16px; }
        .adm-search  { width:180px; }
        .adm-table-wrap { display:none; }
        .adm-pagination { padding:12px 16px; flex-direction:column; gap:8px; text-align:center; }
        .page-header { padding:14px 16px 12px!important; }
    }
    @media (min-width:769px) { .mobile-list { display:none!important; } .adm-table-wrap { display:block; } }
    @media (max-width:768px) { .mobile-list { display:flex!important; } }

    /* Detail panel */
    .panel-overlay { display:none; position:fixed; inset:0; z-index:200; background:rgba(0,0,0,0.3); }
    .panel-overlay.open { display:block; animation:fadeOv 0.18s ease; }
    @keyframes fadeOv { from{opacity:0}to{opacity:1} }
    .detail-panel { position:fixed; right:-500px; top:0; bottom:0; width:360px; max-width:100vw; background:#fff; box-shadow:-4px 0 32px rgba(0,0,0,0.12); z-index:300; display:flex; flex-direction:column; transition:right 0.3s cubic-bezier(0.22,1,0.36,1); }
    .detail-panel.open { right:0; }
    .dp-head { display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid #f0f0f5; flex-shrink:0; }
    .dp-title { font-size:14px; font-weight:700; color:#1a1a2e; }
    .dp-more  { width:28px; height:28px; border-radius:6px; border:1px solid #e5e7eb; background:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; color:#9ca3af; transition:background 0.12s,color 0.12s; }
    .dp-more:hover { background:#f4f0ff; color:#4f28d9; border-color:#c4b5fd; }
    .dp-close { width:28px; height:28px; border-radius:6px; border:1px solid #e5e7eb; background:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; color:#9ca3af; transition:background 0.12s,color 0.12s; }
    .dp-close:hover { background:#fef2f2; color:#dc2626; border-color:#fecaca; }
    .dp-body { flex:1; overflow-y:auto; scrollbar-width:none; }
    .dp-body::-webkit-scrollbar { display:none; }
    .dp-foot { padding:14px 20px; border-top:1px solid #f0f0f5; display:flex; gap:8px; justify-content:flex-end; flex-shrink:0; }

    /* Detail panel content */
    .dp-photo { width:100%; height:200px; object-fit:cover; display:block; }
    .dp-photo-placeholder { width:100%; height:160px; background:#f4f4f8; display:flex; align-items:center; justify-content:center; }
    .dp-inner { padding:18px 20px; }
    .dp-item-title { font-size:16px; font-weight:700; color:#1a1a2e; margin-bottom:8px; line-height:1.35; }
    .dp-badges { display:flex; gap:6px; flex-wrap:wrap; margin-bottom:14px; }
    .dp-meta-row { display:flex; align-items:center; gap:7px; font-size:13px; color:#6b7280; margin-bottom:8px; }
    .dp-meta-row svg { flex-shrink:0; }
    .dp-divider { height:1px; background:#f0f0f5; margin:14px 0; }
    .dp-section-label { font-size:11px; font-weight:700; color:#9ca3af; letter-spacing:0.06em; text-transform:uppercase; margin-bottom:8px; }
    .dp-desc { font-size:13px; color:#374151; line-height:1.65; white-space:pre-line; }

    /* Edit modal */
    .overlay { display:none; position:fixed; inset:0; z-index:400; background:rgba(0,0,0,0.4); align-items:center; justify-content:center; padding:24px; }
    .overlay.open { display:flex; animation:fadeOv2 0.18s ease; }
    @keyframes fadeOv2 { from{opacity:0}to{opacity:1} }
    .edit-modal { background:#fff; border-radius:14px; width:100%; max-width:520px; max-height:92vh; overflow-y:auto; box-shadow:0 24px 64px rgba(0,0,0,0.18); animation:slideUp 0.22s cubic-bezier(0.22,1,0.36,1); scrollbar-width:none; }
    .edit-modal::-webkit-scrollbar { display:none; }
    @keyframes slideUp { from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)} }
    .em-head { display:flex; align-items:center; justify-content:space-between; padding:18px 20px 16px; border-bottom:1px solid #f0f0f5; }
    .em-title { font-size:15px; font-weight:700; color:#1a1a2e; }
    .em-close { width:28px; height:28px; border-radius:6px; border:1px solid #e5e7eb; background:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; color:#9ca3af; transition:background 0.12s,color 0.12s; }
    .em-close:hover { background:#fef2f2; color:#dc2626; border-color:#fecaca; }
    .em-body { padding:20px; }
    .em-foot { display:flex; align-items:center; justify-content:flex-end; gap:8px; padding:14px 20px; border-top:1px solid #f0f0f5; }
    .form-field { margin-bottom:16px; }
    .form-label { display:block; font-size:12.5px; font-weight:700; color:#374151; margin-bottom:6px; }
    .form-input, .form-textarea { width:100%; padding:9px 12px; border:1px solid #e5e7eb; border-radius:6px; font-size:13px; font-family:'Lato',sans-serif; color:#111; background:#fff; outline:none; transition:border-color 0.15s,box-shadow 0.15s; }
    .form-textarea { resize:vertical; min-height:90px; line-height:1.6; }
    .form-input:focus, .form-textarea:focus { border-color:#7c3aed; box-shadow:0 0 0 2px rgba(124,58,237,0.1); }
    .type-toggle { display:flex; gap:6px; }
    .type-btn { flex:1; padding:8px; border:1.5px solid #e5e7eb; border-radius:6px; font-size:12.5px; font-weight:600; font-family:'Lato',sans-serif; color:#6b7280; background:#fff; cursor:pointer; text-align:center; transition:border-color 0.15s,color 0.15s,background 0.15s; }
    .type-btn:hover { border-color:#c4b5fd; color:#4f28d9; }
    .type-btn.active-found { border-color:#d97706; color:#92400e; background:#fffbeb; }
    .type-btn.active-lost  { border-color:#db2777; color:#9d174d; background:#fdf2f8; }

    /* Photo upload in edit modal */
    .edit-photo-wrap { position:relative; border-radius:8px; overflow:hidden; background:#f4f4f8; margin-bottom:4px; }
    .edit-photo-preview { width:100%; height:140px; object-fit:cover; display:block; }
    .edit-photo-placeholder { width:100%; height:100px; display:flex; align-items:center; justify-content:center; background:#f4f4f8; border-radius:8px; border:1.5px dashed #d1d5db; cursor:pointer; position:relative; transition:border-color 0.15s,background 0.15s; }
    .edit-photo-placeholder:hover { border-color:#7c3aed; background:#faf8ff; }
    .edit-photo-placeholder input[type=file] { position:absolute; inset:0; opacity:0; cursor:pointer; }
    .edit-photo-change { position:absolute; bottom:8px; right:8px; display:inline-flex; align-items:center; gap:5px; padding:5px 10px; background:rgba(0,0,0,0.55); color:#fff; font-size:11px; font-weight:700; font-family:'Lato',sans-serif; border-radius:5px; cursor:pointer; border:none; position:relative; }
    .edit-photo-change input[type=file] { position:absolute; inset:0; opacity:0; cursor:pointer; }

    .btn-cancel { padding:8px 18px; border:1px solid #e5e7eb; border-radius:6px; background:#fff; color:#6b7280; font-size:13px; font-weight:700; font-family:'Lato',sans-serif; cursor:pointer; transition:background 0.12s,border-color 0.12s; }
    .btn-cancel:hover { background:#f9f9fb; border-color:#c4b5fd; color:#4f28d9; }
    .btn-save { padding:8px 20px; background:linear-gradient(135deg,#9025FB,#4617D3); color:#fff; font-size:13px; font-weight:700; font-family:'Lato',sans-serif; border:none; border-radius:6px; cursor:pointer; box-shadow:0 2px 8px rgba(109,40,217,0.2); transition:opacity 0.15s; }
    .btn-save:hover { opacity:0.88; }

    /* Delete */
    .del-overlay { display:none; position:fixed; inset:0; z-index:500; background:rgba(0,0,0,0.35); align-items:center; justify-content:center; }
    .del-overlay.open { display:flex; }
    .del-box { background:#fff; border-radius:12px; padding:24px; max-width:320px; width:100%; box-shadow:0 20px 60px rgba(0,0,0,0.18); text-align:center; animation:slideUp 0.2s ease; }
    .del-icon { margin:0 auto 10px; display:flex; align-items:center; justify-content:center; width:48px; height:48px; border-radius:50%; background:#fef2f2; }
    .del-title { font-size:15px; font-weight:700; color:#1a1a2e; margin-bottom:6px; }
    .del-desc  { font-size:13px; color:#6b7280; margin-bottom:20px; line-height:1.5; }
    .del-actions { display:flex; gap:8px; justify-content:center; }
    .btn-del-cancel  { padding:8px 20px; border:1px solid #e5e7eb; border-radius:6px; background:#fff; color:#374151; font-size:13px; font-weight:700; font-family:'Lato',sans-serif; cursor:pointer; }
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
</style>
@endpush

@section('content')

<div class="adm-toolbar">
    <div class="adm-toolbar-left">
        <div class="adm-dd" id="sortDD">
            <button type="button" class="adm-dd-trigger" onclick="toggleDD('sortDD')">
                <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"/></svg>
                <span id="sortLabel">Terbaru</span>
                <svg class="adm-chevron" width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="adm-dd-menu">
                <button class="adm-dd-opt selected" onclick="pickSort('terbaru','Terbaru',this)"><svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/></svg>Terbaru</button>
                <button class="adm-dd-opt" onclick="pickSort('terlama','Terlama',this)"><svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/></svg>Terlama</button>
            </div>
        </div>
        <div class="adm-dd" id="typeDD">
            <button type="button" class="adm-dd-trigger" onclick="toggleDD('typeDD')">
                <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h18M3 8h18M3 12h18M3 16h18"/></svg>
                <span id="typeLabel">Semua</span>
                <svg class="adm-chevron" width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="adm-dd-menu">
                <button class="adm-dd-opt selected" onclick="pickType('all','Semua',this)"><svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/></svg>Semua</button>
                <button class="adm-dd-opt" onclick="pickType('found','Temuan',this)"><svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/></svg>Temuan</button>
                <button class="adm-dd-opt" onclick="pickType('lost','Kehilangan',this)"><svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/></svg>Kehilangan</button>
            </div>
        </div>
    </div>
    <div class="adm-search-wrap">
        <span class="adm-si"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8" stroke-width="2"/><path d="M21 21l-4.35-4.35" stroke-width="2" stroke-linecap="round"/></svg></span>
        <input type="text" class="adm-search" placeholder="Cari laporan..." oninput="filterTable(this.value)" id="searchInput">
    </div>
</div>

<div class="adm-body">
    {{-- Desktop table --}}
    <div class="adm-table-wrap">
        <table id="mainTable">
            <thead>
                <tr>
                    <th class="td-num">#</th>
                    <th>Nama Barang</th>
                    <th>Jenis</th>
                    <th>Lokasi</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                @forelse($items ?? [] as $i => $item)
                <tr data-type="{{ $item->type }}" data-title="{{ strtolower($item->item_name) }}">
                    <td class="td-num">{{ $i + 1 }}</td>
                    <td class="td-title"><span>{{ $item->item_name }}</span></td>
                    <td><span class="badge {{ $item->type === 'found' ? 'badge-found' : 'badge-lost' }}">{{ $item->type === 'found' ? 'Temuan' : 'Kehilangan' }}</span></td>
                    <td>{{ $item->found_at ?? '—' }}</td>
                    <td>
                        @php $st = $item->status; @endphp
                        <span class="badge badge-{{ $st }}">{{ $st === 'pending' ? 'Menunggu' : ($st === 'approved' ? 'Disetujui' : 'Selesai') }}</span>
                    </td>
                    <td style="white-space:nowrap;color:#9ca3af;font-size:12px;">{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}</td>
                    <td>
                        <button class="action-btn" title="Lihat detail" onclick="openDetail({{ json_encode($item) }}, {{ json_encode(isset($photoMap[$item->id]) ? ($photoMap[$item->id]->file_data ?: asset('storage/'.$photoMap[$item->id]->file_path)) : null) }})">
                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3" stroke-width="1.8"/></svg>
                        </button>
                        @if($item->status === 'pending')
                        <button class="action-btn edit" title="Edit" onclick="openEdit({{ json_encode($item) }}, {{ json_encode(isset($photoMap[$item->id]) ? ($photoMap[$item->id]->file_data ?: asset('storage/'.$photoMap[$item->id]->file_path)) : null) }})">
                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 4H6a2 2 0 00-2 2v13a2 2 0 002 2h11a2 2 0 002-2v-5"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        </button>
                        <button class="action-btn danger" title="Hapus" onclick="openDelete({{ $item->id }}, '{{ addslashes($item->item_name) }}')">
                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" points="3 6 5 6 21 6"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
                        </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7"><div class="adm-empty">
                    <svg width="36" height="36" fill="none" stroke="#d1d5db" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8" stroke-width="1.4"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" d="M21 21l-4.35-4.35"/></svg>
                    Belum ada laporan temuan.
                </div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile cards --}}
    <div class="mobile-list" id="mobileList">
        @forelse($items ?? [] as $item)
        <div class="m-card" data-type="{{ $item->type }}" data-title="{{ strtolower($item->item_name) }}">
            <div class="m-card-top">
                <div class="m-card-title">{{ $item->item_name }}</div>
                <div class="m-card-badges">
                    <span class="badge {{ $item->type === 'found' ? 'badge-found' : 'badge-lost' }}">{{ $item->type === 'found' ? 'Temuan' : 'Kehilangan' }}</span>
                    @php $st = $item->status; @endphp
                    <span class="badge badge-{{ $st }}">{{ $st === 'pending' ? 'Menunggu' : ($st === 'approved' ? 'Disetujui' : 'Selesai') }}</span>
                </div>
            </div>
            <div class="m-card-meta">
                @if($item->found_at)<span class="m-card-meta-item"><svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><circle cx="12" cy="11" r="3" stroke-width="2"/></svg>{{ $item->found_at }}</span>@endif
                <span class="m-card-meta-item"><svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" stroke-width="1.8"/><line x1="16" y1="2" x2="16" y2="6" stroke-width="1.8" stroke-linecap="round"/><line x1="8" y1="2" x2="8" y2="6" stroke-width="1.8" stroke-linecap="round"/><line x1="3" y1="10" x2="21" y2="10" stroke-width="1.8"/></svg>{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}</span>
            </div>
            @if($item->description)<div class="m-card-desc">{{ $item->description }}</div>@endif
            <div class="m-card-actions">
                <button class="m-action-btn" onclick="openDetail({{ json_encode($item) }}, {{ json_encode(isset($photoMap[$item->id]) ? ($photoMap[$item->id]->file_data ?: asset('storage/'.$photoMap[$item->id]->file_path)) : null) }})">
                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3" stroke-width="1.8"/></svg>Detail
                </button>
                @if($item->status === 'pending')
                <button class="m-action-btn" onclick="openEdit({{ json_encode($item) }}, {{ json_encode(isset($photoMap[$item->id]) ? ($photoMap[$item->id]->file_data ?: asset('storage/'.$photoMap[$item->id]->file_path)) : null) }})">
                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 4H6a2 2 0 00-2 2v13a2 2 0 002 2h11a2 2 0 002-2v-5"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>Edit
                </button>
                <button class="m-action-btn danger" onclick="openDelete({{ $item->id }}, '{{ addslashes($item->item_name) }}')">
                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" points="3 6 5 6 21 6"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>Hapus
                </button>
                @endif
            </div>
        </div>
        @empty
        <div class="adm-empty"><svg width="36" height="36" fill="none" stroke="#d1d5db" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8" stroke-width="1.4"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" d="M21 21l-4.35-4.35"/></svg>Belum ada laporan temuan.</div>
        @endforelse
    </div>
</div>

@if(isset($items) && method_exists($items, 'links'))
<div class="adm-pagination">
    <span>Menampilkan {{ $items->firstItem() }}–{{ $items->lastItem() }} dari {{ $items->total() }} laporan</span>
    <div class="adm-page-btns">
        <a href="{{ $items->previousPageUrl() ?? '#' }}" class="adm-page-btn {{ $items->onFirstPage() ? 'disabled' : '' }}"><svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 18l-6-6 6-6"/></svg></a>
        @foreach($items->getUrlRange(1, $items->lastPage()) as $page => $url)
        <a href="{{ $url }}" class="adm-page-btn {{ $page === $items->currentPage() ? 'active' : '' }}">{{ $page }}</a>
        @endforeach
        <a href="{{ $items->nextPageUrl() ?? '#' }}" class="adm-page-btn {{ !$items->hasMorePages() ? 'disabled' : '' }}"><svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 18l6-6-6-6"/></svg></a>
    </div>
</div>
@endif

{{-- Panel overlay --}}
<div class="panel-overlay" id="panelOverlay" onclick="closeDetail()"></div>

{{-- Detail panel --}}
<div class="detail-panel" id="detailPanel">
    <div class="dp-head">
        <span class="dp-title">Detail Laporan</span>
        <div style="display:flex;gap:6px;">
            <button class="dp-more" id="dpEditBtn" onclick="editFromDetail()" title="Edit" style="display:none;">
                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 4H6a2 2 0 00-2 2v13a2 2 0 002 2h11a2 2 0 002-2v-5"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            </button>
            <button class="dp-close" onclick="closeDetail()">
                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>
    <div class="dp-body" id="detailBody"></div>
</div>

{{-- Edit modal --}}
<div class="overlay" id="editOverlay">
    <div class="edit-modal">
        <div class="em-head">
            <span class="em-title">Edit Laporan</span>
            <button class="em-close" onclick="closeEdit()"><svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <form id="editForm" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="em-body">
                {{-- Photo --}}
                <div class="form-field">
                    <label class="form-label">Foto</label>
                    <div id="editPhotoWrap">
                        {{-- filled by JS --}}
                    </div>
                    <input type="file" name="photo" id="editPhotoInput" accept="image/png,image/jpeg" style="display:none;" onchange="handleEditPhoto(this)">
                </div>
                {{-- Type --}}
                <div class="form-field">
                    <label class="form-label">Jenis Laporan</label>
                    <div class="type-toggle">
                        <button type="button" class="type-btn active-found" id="editBtnFound" onclick="setEditType('found')">Temuan</button>
                        <button type="button" class="type-btn" id="editBtnLost" onclick="setEditType('lost')">Kehilangan</button>
                    </div>
                    <input type="hidden" name="type" id="editType" value="found">
                </div>
                <div class="form-field">
                    <label class="form-label" for="editItemName">Nama Barang</label>
                    <input type="text" class="form-input" id="editItemName" name="item_name" required>
                </div>
                <div class="form-field">
                    <label class="form-label" for="editDesc">Deskripsi</label>
                    <textarea class="form-textarea" id="editDesc" name="description"></textarea>
                </div>
                <div class="form-field">
                    <label class="form-label" for="editLocation">Lokasi</label>
                    <input type="text" class="form-input" id="editLocation" name="found_at" placeholder="Contoh: Kantin Belakang">
                </div>
            </div>
            <div class="em-foot">
                <button type="button" class="btn-cancel" onclick="closeEdit()">Batal</button>
                <button type="submit" class="btn-save">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

{{-- Delete confirm --}}
<div class="del-overlay" id="delOverlay">
    <div class="del-box">
        <div class="del-icon"><svg width="22" height="22" fill="none" stroke="#dc2626" viewBox="0 0 24 24"><polyline stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" points="3 6 5 6 21 6"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg></div>
        <div class="del-title">Hapus Laporan?</div>
        <div class="del-desc" id="delDesc">Laporan ini akan dihapus permanen.</div>
        <div class="del-actions">
            <button class="btn-del-cancel" onclick="closeDelete()">Batal</button>
            <form id="delForm" method="POST" style="display:inline;">@csrf @method('DELETE')<button type="submit" class="btn-del-confirm">Hapus</button></form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Dropdowns
    function toggleDD(id) {
        document.querySelectorAll('.adm-dd').forEach(d => { if(d.id!==id) d.classList.remove('open'); });
        document.getElementById(id).classList.toggle('open');
    }
    document.addEventListener('click', e => { if(!e.target.closest('.adm-dd')) document.querySelectorAll('.adm-dd').forEach(d=>d.classList.remove('open')); });

    let currentSort='terbaru', currentType='all';
    function pickSort(val,label,btn) { currentSort=val; document.getElementById('sortLabel').textContent=label; document.querySelectorAll('#sortDD .adm-dd-opt').forEach(o=>o.classList.remove('selected')); btn.classList.add('selected'); document.getElementById('sortDD').classList.remove('open'); applyFilters(); }
    function pickType(val,label,btn) { currentType=val; document.getElementById('typeLabel').textContent=label; document.querySelectorAll('#typeDD .adm-dd-opt').forEach(o=>o.classList.remove('selected')); btn.classList.add('selected'); document.getElementById('typeDD').classList.remove('open'); applyFilters(); }
    function filterTable(q) { applyFilters(q); }
    function applyFilters(q) {
        q=(q??document.getElementById('searchInput').value).toLowerCase();
        [...document.querySelectorAll('#tableBody tr[data-type]'),...document.querySelectorAll('#mobileList .m-card[data-type]')].forEach(el=>{
            el.style.display=(currentType==='all'||el.dataset.type===currentType)&&(!q||el.dataset.title.includes(q))?'':'none';
        });
    }

    // Detail panel
    let _currentItem=null, _currentPhoto=null;
    const statusMap={pending:'Menunggu',approved:'Disetujui',solved:'Selesai'};
    const statusClass={pending:'badge-pending',approved:'badge-approved',solved:'badge-solved'};

    function openDetail(item, photoUrl) {
        _currentItem=item; _currentPhoto=photoUrl;
        const st=item.status;
        const date=new Date(item.created_at).toLocaleDateString('id-ID',{weekday:'long',day:'numeric',month:'long',year:'numeric'});
        const time=new Date(item.created_at).toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit'});

        let photoHtml = photoUrl
            ? `<img class="dp-photo" src="${photoUrl}" alt="${item.item_name}" onerror="this.style.display='none'">`
            : `<div class="dp-photo-placeholder"><svg width="32" height="32" fill="none" stroke="#c4c4d4" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" stroke-width="1.5"/><circle cx="8.5" cy="8.5" r="1.5" stroke-width="1.5"/><polyline stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" points="21 15 16 10 5 21"/></svg></div>`;

        document.getElementById('detailBody').innerHTML = `
            ${photoHtml}
            <div class="dp-inner">
                <div class="dp-item-title">${item.item_name}</div>
                <div class="dp-badges">
                    <span class="badge ${item.type==='found'?'badge-found':'badge-lost'}">${item.type==='found'?'Temuan':'Kehilangan'}</span>
                    <span class="badge ${statusClass[st]}">${statusMap[st]??st}</span>
                </div>
                ${item.found_at ? `
                <div class="dp-meta-row">
                    <svg width="14" height="14" fill="none" stroke="#9ca3af" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><circle cx="12" cy="11" r="3" stroke-width="1.8"/></svg>
                    ${item.found_at}
                </div>` : ''}
                <div class="dp-meta-row">
                    <svg width="14" height="14" fill="none" stroke="#9ca3af" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" stroke-width="1.8"/><line x1="16" y1="2" x2="16" y2="6" stroke-width="1.8" stroke-linecap="round"/><line x1="8" y1="2" x2="8" y2="6" stroke-width="1.8" stroke-linecap="round"/><line x1="3" y1="10" x2="21" y2="10" stroke-width="1.8"/></svg>
                    ${date}
                </div>
                <div class="dp-meta-row">
                    <svg width="14" height="14" fill="none" stroke="#9ca3af" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="1.8"/><polyline stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" points="12 6 12 12 16 14"/></svg>
                    ${time} WIB
                </div>
                ${item.description ? `
                <div class="dp-divider"></div>
                <div class="dp-section-label">Deskripsi</div>
                <div class="dp-desc">${item.description}</div>` : ''}
                ${st!=='pending' ? `<div style="margin-top:16px;padding:10px 12px;background:#f9fafb;border-radius:6px;font-size:12.5px;color:#6b7280;">Laporan sudah diproses dan tidak dapat diedit.</div>` : ''}
            </div>`;

        // show edit button only if pending
        document.getElementById('dpEditBtn').style.display = st==='pending' ? 'flex' : 'none';
        document.getElementById('panelOverlay').classList.add('open');
        document.getElementById('detailPanel').classList.add('open');
    }
    function closeDetail() {
        document.getElementById('panelOverlay').classList.remove('open');
        document.getElementById('detailPanel').classList.remove('open');
    }
    function editFromDetail() {
        closeDetail();
        setTimeout(() => openEdit(_currentItem, _currentPhoto), 320);
    }

    // Edit modal
    function openEdit(item, photoUrl) {
        _currentItem=item; _currentPhoto=photoUrl;
        document.getElementById('editForm').action=`/temuan/${item.id}`;
        document.getElementById('editItemName').value=item.item_name;
        document.getElementById('editDesc').value=item.description??'';
        document.getElementById('editLocation').value=item.found_at??'';
        setEditType(item.type);
        renderEditPhoto(photoUrl);
        document.getElementById('editOverlay').classList.add('open');
    }
    function closeEdit() { document.getElementById('editOverlay').classList.remove('open'); }
    function setEditType(t) {
        document.getElementById('editType').value=t;
        document.getElementById('editBtnFound').className='type-btn'+(t==='found'?' active-found':'');
        document.getElementById('editBtnLost').className='type-btn'+(t==='lost'?' active-lost':'');
    }
    function renderEditPhoto(url) {
        const wrap=document.getElementById('editPhotoWrap');
        if(url) {
            wrap.innerHTML=`
                <div class="edit-photo-wrap">
                    <img class="edit-photo-preview" id="editPhotoImg" src="${url}" onerror="this.src=''">
                    <label class="edit-photo-change" style="position:absolute;bottom:8px;right:8px;display:inline-flex;align-items:center;gap:5px;padding:5px 10px;background:rgba(0,0,0,0.55);color:#fff;font-size:11px;font-weight:700;font-family:'Lato',sans-serif;border-radius:5px;cursor:pointer;border:none;">
                        <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4H6a2 2 0 00-2 2v13a2 2 0 002 2h11a2 2 0 002-2v-5"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        Ganti Foto
                        <input type="file" accept="image/png,image/jpeg" onchange="previewEditPhoto(this)" style="position:absolute;inset:0;opacity:0;cursor:pointer;">
                    </label>
                </div>`;
        } else {
            wrap.innerHTML=`
                <label class="edit-photo-placeholder" style="width:100%;height:100px;display:flex;flex-direction:column;align-items:center;justify-content:center;background:#f4f4f8;border-radius:8px;border:1.5px dashed #d1d5db;cursor:pointer;position:relative;transition:border-color 0.15s,background 0.15s;gap:6px;" onmouseover="this.style.borderColor='#7c3aed';this.style.background='#faf8ff'" onmouseout="this.style.borderColor='#d1d5db';this.style.background='#f4f4f8'">
                    <svg width="22" height="22" fill="none" stroke="#9ca3af" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    <span style="font-size:12px;color:#9ca3af;font-family:'Lato',sans-serif;">Tambah foto</span>
                    <input type="file" name="photo" accept="image/png,image/jpeg" onchange="previewEditPhoto(this)" style="position:absolute;inset:0;opacity:0;cursor:pointer;">
                </label>`;
        }
    }
    function previewEditPhoto(input) {
        if(!input.files[0]) return;
        // Copy file to the main input
        const dt=new DataTransfer(); dt.items.add(input.files[0]);
        document.getElementById('editForm').querySelector('input[name=photo]') && (document.getElementById('editForm').querySelector('input[name=photo]').files=dt.files);
        const reader=new FileReader();
        reader.onload=e=>renderEditPhoto(e.target.result);
        reader.readAsDataURL(input.files[0]);
        // Ensure the form has the file
        const formPhotoInput=document.getElementById('editForm').querySelector('input[type=file][name=photo]');
        if(!formPhotoInput){ const ni=document.createElement('input'); ni.type='file'; ni.name='photo'; ni.style.display='none'; ni.files=dt.files; document.getElementById('editForm').appendChild(ni); } else { formPhotoInput.files=dt.files; }
    }

    // Delete
    function openDelete(id,name) {
        document.getElementById('delDesc').textContent=`"${name}" akan dihapus permanen.`;
        document.getElementById('delForm').action=`/temuan/${id}`;
        document.getElementById('delOverlay').classList.add('open');
    }
    function closeDelete() { document.getElementById('delOverlay').classList.remove('open'); }

    document.addEventListener('keydown', e => { if(e.key==='Escape'){closeDetail();closeEdit();closeDelete();} });
</script>
@endpush
