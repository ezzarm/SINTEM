{{-- resources/views/admin/laporan/anonim.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Laporan Anonim – Admin SINTEM')

@section('topbar')
<div style="display:flex;align-items:center;justify-content:space-between;padding:14px 32px;border-bottom:1px solid #f0f0f5;background:#fff;">
    <p style="font-size:13.5px;font-weight:700;color:#1a1a2e;">Selamat Datang, {{ Auth::user()->name }}!</p>
</div>
@endsection

@section('header', 'Laporan Anonim')
@section('subheader', 'Kelola semua laporan anonim dari siswa dan staf.')

@push('styles')
<style>
    .page-body { padding:0!important;overflow:hidden!important;display:flex;flex-direction:column; }
    .main-content { background:#fff!important; }
    .page-header  { padding:16px 32px 14px!important;background:#fff!important; }

    /* ── Toolbar ── */
    .adm-toolbar {
        flex-shrink:0;padding:14px 32px 12px;border-bottom:1px solid #f0f0f5;background:#fff;
        display:flex;align-items:center;justify-content:space-between;gap:8px;flex-wrap:wrap;
    }
    .adm-toolbar-left { display:flex;align-items:center;gap:6px;flex-wrap:wrap; }
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
        display:none;position:absolute;top:calc(100% + 4px);left:0;min-width:160px;
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

    /* ── TABLE VIEW ── */
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
    .td-ticket { font-weight:700;color:#7c3aed;font-size:12px;white-space:nowrap; }
    .td-content { max-width:260px; }
    .td-content span { display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden; }

    /* ── Status badges ── */
    .status-badge { font-size:11px;font-weight:700;padding:2px 10px;border-radius:4px;border:1px solid;display:inline-block;white-space:nowrap; }
    .status-pending    { background:#fffbeb;color:#d97706;border-color:#fde68a; }
    .status-in_progress{ background:#eff6ff;color:#1d4ed8;border-color:#bfdbfe; }
    .status-solved     { background:#f0fdf4;color:#16a34a;border-color:#bbf7d0; }

    /* ── Category badge ── */
    .cat-badge { font-size:11px;font-weight:600;padding:2px 8px;border-radius:4px;border:1px solid;display:inline-block;white-space:nowrap;background:#fdf4ff;color:#7c3aed;border-color:#e9d5ff; }

    /* ── Action buttons ── */
    .action-group { display:flex;align-items:center;justify-content:flex-start;gap:5px;flex-wrap:nowrap; }
    .action-btn {
        display:inline-flex;align-items:center;justify-content:center;
        width:28px;height:28px;border-radius:5px;border:1px solid;
        cursor:pointer;transition:background 0.12s,color 0.12s,border-color 0.12s;
        flex-shrink:0;background:none;
    }
    .action-btn-view   { color:#6b7280;border-color:#e5e7eb;background:#f9fafb; }
    .action-btn-view:hover { background:#f4f0ff;color:#4f28d9;border-color:#c4b5fd; }
    .action-btn-delete { color:#dc2626;border-color:#fecaca;background:#fef2f2; }
    .action-btn-delete:hover { background:#fee2e2;color:#991b1b;border-color:#fca5a5; }

    /* ── CARD VIEW (breakpoint ≤900px) ── */
    .cards-wrap { display:none;padding:16px 16px 32px;gap:12px;flex-direction:column; }
    .item-card {
        border:1px solid #f0f0f5;border-radius:10px;padding:0;overflow:hidden;
        background:#fff;box-shadow:0 1px 4px rgba(0,0,0,0.04);
        transition:box-shadow 0.15s,border-color 0.15s;
    }
    .item-card:hover { box-shadow:0 4px 16px rgba(109,40,217,0.08);border-color:#e9d5ff; }
    .card-body { padding:12px 14px; }
    .card-badges { display:flex;gap:6px;margin-bottom:8px;flex-wrap:wrap;align-items:center; }
    .card-ticket { font-size:12px;font-weight:700;color:#7c3aed; }
    .card-title { font-size:14px;font-weight:700;color:#1a1a2e;margin-bottom:4px;line-height:1.3; }
    .card-meta  { font-size:12px;color:#9ca3af;margin-bottom:10px; }
    .card-desc  { font-size:12.5px;color:#6b7280;line-height:1.5;margin-bottom:10px;
        display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden; }
    .card-foot  { display:flex;align-items:center;gap:6px;flex-wrap:wrap;padding-top:8px;border-top:1px solid #f5f5f7; }

    @media (max-width:900px) {
        .adm-table-wrap { display:none; }
        .cards-wrap { display:flex; }
        .adm-search { width:160px; }
        .adm-toolbar { padding:12px 16px 10px; }
        .adm-pagination { padding:12px 16px; }
    }

    /* ── Pagination ── */
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

    /* ── Alert ── */
    .adm-alert { margin:16px 32px 0;padding:10px 14px;border-radius:6px;font-size:13px;display:flex;align-items:center;gap:8px;flex-shrink:0;transition:opacity 0.4s; }
    .adm-alert-success { background:#f0fdf4;border:1px solid #bbf7d0;color:#16a34a; }
    .adm-alert-error   { background:#fef2f2;border:1px solid #fecaca;color:#dc2626; }

    /* ══ PANEL OVERLAY ══ */
    .panel-overlay { display:none;position:fixed;inset:0;z-index:200;background:rgba(0,0,0,0.3); }
    .panel-overlay.open { display:block;animation:fadeOv 0.18s ease; }
    @keyframes fadeOv { from{opacity:0}to{opacity:1} }

    /* ══ VIEW MODAL (centered) ══ */
    .modal-wrap {
        position:fixed;inset:0;z-index:300;display:flex;align-items:center;justify-content:center;
        padding:20px;
    }
    .modal-box {
        background:#fff;border-radius:12px;box-shadow:0 8px 40px rgba(0,0,0,0.15);
        width:100%;max-width:560px;max-height:90vh;overflow-y:auto;
        display:flex;flex-direction:column;
    }
    .modal-header {
        padding:18px 20px 14px;border-bottom:1px solid #f0f0f5;
        display:flex;align-items:center;justify-content:space-between;flex-shrink:0;
    }
    .modal-header h3 { font-size:15px;font-weight:700;color:#1a1a2e;margin:0; }
    .modal-close {
        display:inline-flex;align-items:center;justify-content:center;
        width:28px;height:28px;border-radius:6px;border:1px solid #e5e7eb;
        background:#f9fafb;color:#6b7280;cursor:pointer;transition:background 0.12s;
    }
    .modal-close:hover { background:#fee2e2;color:#dc2626;border-color:#fecaca; }
    .modal-body { padding:18px 20px;flex:1; }
    .modal-field { margin-bottom:14px; }
    .modal-label { font-size:11.5px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px; }
    .modal-value { font-size:13.5px;color:#374151;line-height:1.6; }
    .modal-content-box {
        background:#f9f9fb;border:1px solid #ebebf0;border-radius:6px;
        padding:12px 14px;font-size:13px;color:#374151;line-height:1.7;
    }
    .modal-notes-box {
        background:#fffbeb;border:1px solid #fde68a;border-radius:6px;
        padding:10px 14px;font-size:13px;color:#92400e;line-height:1.6;
    }
    .modal-footer { padding:14px 20px;border-top:1px solid #f0f0f5;flex-shrink:0; }

    /* Status update form inside modal */
    .modal-status-form { display:flex;flex-direction:column;gap:10px; }
    .form-label-sm { font-size:12px;font-weight:700;color:#374151;margin-bottom:4px;display:block; }
    .form-select-styled {
        width:100%;padding:8px 32px 8px 10px;border:1px solid #e5e7eb;border-radius:6px;
        font-size:13px;font-family:'Lato',sans-serif;color:#374151;background:#fff;
        outline:none;transition:border-color 0.15s;
        appearance:none;-webkit-appearance:none;
        background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2.5'%3E%3Cpath d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
        background-repeat:no-repeat;background-position:right 10px center;cursor:pointer;
    }
    .form-select-styled:focus { border-color:#7c3aed;box-shadow:0 0 0 2px rgba(124,58,237,0.1); }
    .form-textarea-styled {
        width:100%;padding:8px 10px;border:1px solid #e5e7eb;border-radius:6px;
        font-size:13px;font-family:'Lato',sans-serif;color:#374151;background:#fff;
        outline:none;transition:border-color 0.15s;resize:vertical;min-height:80px;line-height:1.6;
        box-sizing:border-box;
    }
    .form-textarea-styled:focus { border-color:#7c3aed;box-shadow:0 0 0 2px rgba(124,58,237,0.1); }
    .btn-save {
        display:inline-flex;align-items:center;gap:6px;
        padding:8px 18px;background:linear-gradient(135deg,#9025FB,#4617D3);
        color:#fff;font-size:13px;font-weight:700;font-family:'Lato',sans-serif;
        border-radius:6px;border:none;cursor:pointer;transition:opacity 0.15s,transform 0.15s;
    }
    .btn-save:hover { opacity:0.88;transform:translateY(-1px); }
    .btn-cancel-sm {
        display:inline-flex;align-items:center;padding:8px 14px;
        color:#6b7280;font-size:13px;font-weight:600;font-family:'Lato',sans-serif;
        border:1px solid #e5e7eb;border-radius:6px;background:#fff;cursor:pointer;
        transition:background 0.12s;
    }
    .btn-cancel-sm:hover { background:#f3f4f6; }

    /* ── Delete confirm modal ── */
    .del-modal-wrap {
        position:fixed;inset:0;z-index:400;display:flex;align-items:center;justify-content:center;padding:20px;
    }
    .del-modal-box {
        background:#fff;border-radius:12px;box-shadow:0 8px 40px rgba(0,0,0,0.15);
        width:100%;max-width:380px;padding:24px;text-align:center;
    }
    .del-icon { width:48px;height:48px;margin:0 auto 12px;display:flex;align-items:center;justify-content:center;border-radius:50%;background:#fef2f2; }
    .del-title { font-size:15px;font-weight:700;color:#1a1a2e;margin-bottom:6px; }
    .del-desc  { font-size:13px;color:#6b7280;margin-bottom:18px;line-height:1.5; }
    .del-btns  { display:flex;gap:8px;justify-content:center; }
    .btn-del-confirm {
        padding:8px 20px;background:#dc2626;color:#fff;font-size:13px;
        font-weight:700;font-family:'Lato',sans-serif;border:none;border-radius:6px;cursor:pointer;
        transition:background 0.12s;
    }
    .btn-del-confirm:hover { background:#b91c1c; }

    @media (max-width:900px) {
        .adm-alert { margin:12px 16px 0; }
        .modal-box { max-width:100%; }
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
<form method="GET" action="{{ route('admin.laporan.anonim') }}" id="filterForm">
    <div class="adm-toolbar">
        <div class="adm-toolbar-left">

            {{-- Status Filter Dropdown --}}
            <div class="adm-dd" id="filterDd">
                <button type="button" class="adm-dd-trigger" onclick="toggleDd('filterDd')">
                    <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                    <span id="filterLabel">
                        @if($filter === 'pending') Pending
                        @elseif($filter === 'in_progress') In Progress
                        @elseif($filter === 'solved') Solved
                        @else Semua Status
                        @endif
                    </span>
                    <svg class="adm-chevron" width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="adm-dd-menu">
                    @foreach(['semua'=>'Semua Status','pending'=>'Pending','in_progress'=>'In Progress','solved'=>'Solved'] as $val=>$lbl)
                    <button type="button" class="adm-dd-opt {{ $filter===$val?'selected':'' }}" onclick="selectFilter('{{ $val }}','{{ $lbl }}')">
                        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/></svg>
                        {{ $lbl }}
                    </button>
                    @endforeach
                </div>
                <input type="hidden" name="filter" id="filterInput" value="{{ $filter }}">
            </div>

            {{-- Search --}}
            <div class="adm-search-wrap">
                <span class="adm-si"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8" stroke-width="2"/><path d="M21 21l-4.35-4.35" stroke-width="2" stroke-linecap="round"/></svg></span>
                <input type="text" name="search" class="adm-search" placeholder="Cari tiket, isi laporan..." value="{{ $search }}" oninput="debounce(this.form)">
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

{{-- ══ TABLE VIEW ══ --}}
<div class="adm-body">
    <div class="adm-table-wrap">
        <table>
            <thead>
                <tr>
                    <th class="td-num">#</th>
                    <th>Tiket</th>
                    <th>Kategori</th>
                    <th>Isi Laporan</th>
                    <th>Status</th>
                    <th>Tgl. Masuk</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $i => $item)
                <tr>
                    <td class="td-num">{{ ($page - 1) * $perPage + $i + 1 }}</td>
                    <td class="td-ticket">{{ $item->ticket_number }}</td>
                    <td><span class="cat-badge">{{ $item->category_name }}</span></td>
                    <td class="td-content">
                        <span>{{ $item->report_content }}</span>
                    </td>
                    <td>
                        <span class="status-badge status-{{ $item->status }}">
                            @if($item->status === 'pending') Pending
                            @elseif($item->status === 'in_progress') In Progress
                            @else Solved
                            @endif
                        </span>
                    </td>
                    <td style="white-space:nowrap;font-size:12px;color:#6b7280;">
                        {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}
                    </td>
                    <td>
                        <div class="action-group">
                            {{-- View --}}
                            <button type="button" class="action-btn action-btn-view" title="Lihat Detail"
                                onclick="openViewModal({{ json_encode([
                                    'id'           => $item->id,
                                    'ticket'       => $item->ticket_number,
                                    'category'     => $item->category_name,
                                    'content'      => $item->report_content,
                                    'admin_notes'  => $item->admin_notes,
                                    'status'       => $item->status,
                                    'created_at'   => \Carbon\Carbon::parse($item->created_at)->format('d M Y, H:i').' WIB',
                                    'resolved_at'  => $item->resolved_at ? \Carbon\Carbon::parse($item->resolved_at)->format('d M Y, H:i').' WIB' : null,
                                ]) }})">
                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3" stroke-width="1.8"/>
                                </svg>
                            </button>
                            {{-- Delete --}}
                            <button type="button" class="action-btn action-btn-delete" title="Hapus Laporan"
                                onclick="confirmDelete({{ $item->id }}, '{{ $item->ticket_number }}')">
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
                    <td colspan="7" style="text-align:center;padding:48px;color:#9ca3af;font-size:13px;">
                        <svg width="36" height="36" fill="none" stroke="#d1d5db" viewBox="0 0 24 24" style="margin:0 auto 12px;display:block;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Tidak ada laporan ditemukan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ══ CARD VIEW (mobile) ══ --}}
    <div class="cards-wrap">
        @forelse($items as $item)
        <div class="item-card">
            <div class="card-body">
                <div class="card-badges">
                    <span class="card-ticket">{{ $item->ticket_number }}</span>
                    <span class="cat-badge">{{ $item->category_name }}</span>
                    <span class="status-badge status-{{ $item->status }}">
                        @if($item->status === 'pending') Pending
                        @elseif($item->status === 'in_progress') In Progress
                        @else Solved
                        @endif
                    </span>
                </div>
                <div class="card-meta">{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y, H:i') }} WIB</div>
                <div class="card-desc">{{ $item->report_content }}</div>
                <div class="card-foot">
                    <button type="button" class="action-btn action-btn-view" title="Lihat Detail"
                        onclick="openViewModal({{ json_encode([
                            'id'          => $item->id,
                            'ticket'      => $item->ticket_number,
                            'category'    => $item->category_name,
                            'content'     => $item->report_content,
                            'admin_notes' => $item->admin_notes,
                            'status'      => $item->status,
                            'created_at'  => \Carbon\Carbon::parse($item->created_at)->format('d M Y, H:i').' WIB',
                            'resolved_at' => $item->resolved_at ? \Carbon\Carbon::parse($item->resolved_at)->format('d M Y, H:i').' WIB' : null,
                        ]) }})">
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3" stroke-width="1.8"/>
                        </svg>
                    </button>
                    <button type="button" class="action-btn action-btn-delete" title="Hapus"
                        onclick="confirmDelete({{ $item->id }}, '{{ $item->ticket_number }}')">
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <polyline stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" points="3 6 5 6 21 6"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div style="text-align:center;padding:48px 16px;color:#9ca3af;font-size:13px;">Tidak ada laporan ditemukan.</div>
        @endforelse
    </div>
</div>

{{-- Pagination --}}
@php $lastPage = max(1, (int) ceil($total / $perPage)); @endphp
<div class="adm-pagination">
    <span>Menampilkan {{ $total > 0 ? (($page-1)*$perPage+1) : 0 }}–{{ min($page*$perPage,$total) }} dari {{ $total }} laporan</span>
    <div class="adm-page-btns">
        <a class="adm-page-btn {{ $page<=1?'disabled':'' }}"
           href="{{ request()->fullUrlWithQuery(['page'=>$page-1]) }}">
            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 18l-6-6 6-6"/></svg>
        </a>
        @for($p = max(1,$page-2); $p <= min($lastPage,$page+2); $p++)
        <a class="adm-page-btn {{ $p===$page?'active':'' }}"
           href="{{ request()->fullUrlWithQuery(['page'=>$p]) }}">{{ $p }}</a>
        @endfor
        <a class="adm-page-btn {{ $page>=$lastPage?'disabled':'' }}"
           href="{{ request()->fullUrlWithQuery(['page'=>$page+1]) }}">
            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 18l6-6-6-6"/></svg>
        </a>
    </div>
</div>

{{-- ══ VIEW DETAIL MODAL ══ --}}
<div class="panel-overlay" id="viewOverlay" onclick="closeViewModal()"></div>
<div class="modal-wrap" id="viewModalWrap" style="display:none;">
    <div class="modal-box" id="viewModal">
        <div class="modal-header">
            <h3 id="modalTicket">Detail Laporan</h3>
            <button type="button" class="modal-close" onclick="closeViewModal()">
                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="modal-body">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px;">
                <div class="modal-field">
                    <div class="modal-label">Kategori</div>
                    <div class="modal-value" id="modalCategory">—</div>
                </div>
                <div class="modal-field">
                    <div class="modal-label">Tanggal Masuk</div>
                    <div class="modal-value" id="modalCreatedAt">—</div>
                </div>
            </div>
            <div class="modal-field" id="modalResolvedWrap" style="margin-bottom:14px;display:none;">
                <div class="modal-label">Diselesaikan Pada</div>
                <div class="modal-value" id="modalResolvedAt">—</div>
            </div>
            <div class="modal-field" style="margin-bottom:14px;">
                <div class="modal-label">Isi Laporan</div>
                <div class="modal-content-box" id="modalContent">—</div>
            </div>
            <div class="modal-field" id="modalNotesWrap" style="margin-bottom:14px;display:none;">
                <div class="modal-label">Catatan Admin</div>
                <div class="modal-notes-box" id="modalNotes">—</div>
            </div>

            {{-- Status update form --}}
            <div style="border-top:1px solid #f0f0f5;padding-top:14px;margin-top:4px;">
                <div class="modal-label" style="margin-bottom:10px;">Ubah Status & Catatan</div>
                <form method="POST" id="statusForm" class="modal-status-form">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label class="form-label-sm">Status</label>
                        <select name="status" id="modalStatusSelect" class="form-select-styled">
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="solved">Solved</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label-sm">Catatan Admin <span style="font-weight:400;color:#9ca3af;">(opsional)</span></label>
                        <textarea name="admin_notes" id="modalAdminNotes" class="form-textarea-styled" placeholder="Tambahkan catatan penanganan..."></textarea>
                    </div>
                    <div style="display:flex;gap:8px;justify-content:flex-end;">
                        <button type="button" class="btn-cancel-sm" onclick="closeViewModal()">Batal</button>
                        <button type="submit" class="btn-save">
                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ══ DELETE CONFIRM MODAL ══ --}}
<div class="panel-overlay" id="delOverlay" onclick="closeDelConfirm()"></div>
<div class="del-modal-wrap" id="delModalWrap" style="display:none;">
    <div class="del-modal-box">
        <div class="del-icon">
            <svg width="22" height="22" fill="none" stroke="#dc2626" viewBox="0 0 24 24">
                <polyline stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" points="3 6 5 6 21 6"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/>
            </svg>
        </div>
        <div class="del-title">Hapus Laporan?</div>
        <div class="del-desc" id="delDesc">Laporan ini akan dihapus secara permanen dan tidak dapat dikembalikan.</div>
        <div class="del-btns">
            <button type="button" class="btn-cancel-sm" onclick="closeDelConfirm()">Batal</button>
            <form method="POST" id="delForm">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-del-confirm">Hapus</button>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // ── Auto-dismiss flash alert ──
    (function() {
        const alert = document.getElementById('flash-alert');
        if (alert) {
            setTimeout(() => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 400);
            }, 4000);
        }
    })();

    // ── Dropdown toggle ──
    function toggleDd(id) {
        document.querySelectorAll('.adm-dd.open').forEach(d => { if (d.id !== id) d.classList.remove('open'); });
        document.getElementById(id).classList.toggle('open');
    }
    document.addEventListener('click', e => {
        if (!e.target.closest('.adm-dd')) {
            document.querySelectorAll('.adm-dd.open').forEach(d => d.classList.remove('open'));
        }
    });

    // ── Filter select ──
    function selectFilter(val, lbl) {
        document.getElementById('filterInput').value = val;
        document.getElementById('filterLabel').textContent = lbl;
        document.querySelectorAll('#filterDd .adm-dd-opt').forEach(o =>
            o.classList.toggle('selected', o.textContent.trim() === lbl)
        );
        document.getElementById('filterDd').classList.remove('open');
        document.getElementById('filterForm').submit();
    }

    // ── Search debounce ──
    let _dt;
    function debounce(form) { clearTimeout(_dt); _dt = setTimeout(() => form.submit(), 450); }

    // ── View Modal ──
    function openViewModal(data) {
        document.getElementById('modalTicket').textContent  = 'Detail Laporan – ' + data.ticket;
        document.getElementById('modalCategory').textContent = data.category;
        document.getElementById('modalCreatedAt').textContent = data.created_at;
        document.getElementById('modalContent').textContent  = data.content;

        // Admin notes
        const notesWrap = document.getElementById('modalNotesWrap');
        if (data.admin_notes) {
            document.getElementById('modalNotes').textContent = data.admin_notes;
            notesWrap.style.display = 'block';
        } else {
            notesWrap.style.display = 'none';
        }

        // Resolved at
        const resWrap = document.getElementById('modalResolvedWrap');
        if (data.resolved_at) {
            document.getElementById('modalResolvedAt').textContent = data.resolved_at;
            resWrap.style.display = 'block';
        } else {
            resWrap.style.display = 'none';
        }

        // Status form
        document.getElementById('modalStatusSelect').value = data.status;
        document.getElementById('modalAdminNotes').value   = data.admin_notes || '';
        document.getElementById('statusForm').action       = `/admin/laporan/anonim/${data.id}/status`;

        document.getElementById('viewOverlay').classList.add('open');
        document.getElementById('viewModalWrap').style.display = 'flex';
    }
    function closeViewModal() {
        document.getElementById('viewOverlay').classList.remove('open');
        document.getElementById('viewModalWrap').style.display = 'none';
    }

    // ── Delete Confirm ──
    function confirmDelete(id, ticket) {
        document.getElementById('delDesc').textContent = `Laporan ${ticket} akan dihapus secara permanen.`;
        document.getElementById('delForm').action = `/admin/laporan/anonim/${id}`;
        document.getElementById('delOverlay').classList.add('open');
        document.getElementById('delModalWrap').style.display = 'flex';
    }
    function closeDelConfirm() {
        document.getElementById('delOverlay').classList.remove('open');
        document.getElementById('delModalWrap').style.display = 'none';
    }

    // ── Escape key ──
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') { closeViewModal(); closeDelConfirm(); }
    });
</script>
@endpush