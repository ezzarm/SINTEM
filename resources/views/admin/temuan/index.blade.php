{{-- resources/views/admin/temuan/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Informasi Temuan – Admin SINTEM')

@section('topbar')
<div style="display:flex;align-items:center;justify-content:space-between;padding:14px 32px;border-bottom:1px solid #f0f0f5;background:#fff;">
    <p style="font-size:13.5px;font-weight:700;color:#1a1a2e;">Selamat Datang, {{ Auth::user()->name }}!</p>
    <button type="button" class="btn-publish" onclick="openAddPanel()">
        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 5v14M5 12h14"/>
        </svg>
        Buat Laporan Temuan
    </button>
</div>
@endsection

@section('header', 'Informasi Temuan')
@section('subheader', 'Informasi temuan barang di lingkungan sekolah.')

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
    .adm-body { flex:1;min-height:0;overflow-y:auto;-webkit-overflow-scrolling:touch;scrollbar-width:none;-ms-overflow-style:none; }
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
    .td-title { font-weight:600;color:#1a1a2e;max-width:220px; }
    .td-title span { display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden; }

    /* ── Status badges ── */
    .status-badge { font-size:11px;font-weight:700;padding:2px 10px;border-radius:4px;border:1px solid;display:inline-block;white-space:nowrap; }
    .status-approved { background:#f0fdf4;color:#16a34a;border-color:#bbf7d0; }
    .status-pending  { background:#fffbeb;color:#d97706;border-color:#fde68a; }
    .status-rejected { background:#fef2f2;color:#dc2626;border-color:#fecaca; }

    /* ── Type badge ── */
    .type-badge { font-size:11px;font-weight:700;padding:2px 9px;border-radius:4px;border:1px solid;display:inline-block;white-space:nowrap; }
    .type-found { background:#eff6ff;color:#1d4ed8;border-color:#bfdbfe; }
    .type-lost  { background:#fdf4ff;color:#7c3aed;border-color:#e9d5ff; }

    /* ── Action buttons ── */
    .action-group { display:flex;align-items:center;justify-content:flex-start;gap:5px;flex-wrap:nowrap; }
    .action-btn {
        display:inline-flex;align-items:center;justify-content:center;
        width:28px;height:28px;border-radius:5px;border:1px solid;
        cursor:pointer;transition:background 0.12s,color 0.12s,border-color 0.12s;
        flex-shrink:0;background:none;
    }
    .action-btn-edit    { color:#1d4ed8;border-color:#bfdbfe;background:#eff6ff; }
    .action-btn-edit:hover { background:#dbeafe;color:#1e40af;border-color:#93c5fd; }
    .action-btn-delete  { color:#dc2626;border-color:#fecaca;background:#fef2f2; }
    .action-btn-delete:hover { background:#fee2e2;color:#991b1b;border-color:#fca5a5; }
    .action-btn-view    { color:#6b7280;border-color:#e5e7eb;background:#f9fafb; }
    .action-btn-view:hover { background:#f4f0ff;color:#4f28d9;border-color:#c4b5fd; }

    /* Accept / Reject wide buttons for pending */
    .action-btn-accept {
        display:inline-flex;align-items:center;gap:4px;padding:4px 10px;
        font-size:11.5px;font-weight:700;font-family:'Lato',sans-serif;
        color:#16a34a;border:1px solid #bbf7d0;background:#f0fdf4;
        border-radius:5px;cursor:pointer;transition:background 0.12s;white-space:nowrap;
    }
    .action-btn-accept:hover { background:#dcfce7;border-color:#86efac; }
    .action-btn-reject {
        display:inline-flex;align-items:center;gap:4px;padding:4px 10px;
        font-size:11.5px;font-weight:700;font-family:'Lato',sans-serif;
        color:#dc2626;border:1px solid #fecaca;background:#fef2f2;
        border-radius:5px;cursor:pointer;transition:background 0.12s;white-space:nowrap;
    }
    .action-btn-reject:hover { background:#fee2e2;border-color:#fca5a5; }

    /* ── CARD VIEW (breakpoint ≤900px) ── */
    .cards-wrap { display:none;padding:16px 16px 32px;gap:12px;flex-direction:column; }
    .item-card {
        border:1px solid #f0f0f5;border-radius:10px;padding:0;overflow:hidden;
        background:#fff;box-shadow:0 1px 4px rgba(0,0,0,0.04);
        transition:box-shadow 0.15s,border-color 0.15s;
    }
    .item-card:hover { box-shadow:0 4px 16px rgba(109,40,217,0.08);border-color:#e9d5ff; }
    .card-photo { width:100%;height:140px;object-fit:cover;display:block; }
    .card-photo-placeholder { width:100%;height:100px;background:#f4f4f8;display:flex;align-items:center;justify-content:center; }
    .card-body { padding:12px 14px; }
    .card-badges { display:flex;gap:6px;margin-bottom:8px;flex-wrap:wrap; }
    .card-title { font-size:14px;font-weight:700;color:#1a1a2e;margin-bottom:4px;line-height:1.3; }
    .card-meta  { font-size:12px;color:#9ca3af;margin-bottom:10px; }
    .card-desc  { font-size:12.5px;color:#6b7280;line-height:1.5;margin-bottom:10px;
        display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden; }
    .card-foot  { display:flex;align-items:center;gap:6px;flex-wrap:wrap;padding-top:8px;border-top:1px solid #f5f5f7; }

    @media (max-width:900px) {
        .adm-table-wrap { display:none; }
        .cards-wrap { display:flex; }
        .adm-search { width:180px; }
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

    /* ══ SIDE PANEL ══ */
    .edit-panel {
        position:fixed;right:-540px;top:0;height:100vh;height:100dvh;
        width:480px;max-width:100vw;background:#fff;
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
    .ep-body { flex:1;overflow-y:auto;-webkit-overflow-scrolling:touch;scrollbar-width:none;-ms-overflow-style:none; }
    .ep-body::-webkit-scrollbar { display:none; }
    .ep-inner { padding:20px; }
    .ep-divider { height:1px;background:#f0f0f5;margin:16px 0; }
    .ep-section-label { font-size:11px;font-weight:700;color:#9ca3af;letter-spacing:0.06em;text-transform:uppercase;margin-bottom:10px; }

    /* Photo top banner */
    .ep-photo-wrap { position:relative;overflow:hidden;background:#f4f4f8; }
    .ep-photo { width:100%;height:200px;object-fit:cover;display:block; }
    .ep-photo-placeholder { width:100%;height:120px;background:#f4f4f8;display:flex;align-items:center;justify-content:center; }
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

    /* Form fields */
    .form-field { margin-bottom:16px; }
    .form-label { display:block;font-size:12.5px;font-weight:700;color:#374151;margin-bottom:6px; }
    .form-hint  { font-size:11.5px;color:#9ca3af;margin-top:4px; }
    .form-input,.form-textarea,.form-select {
        width:100%;padding:9px 12px;border:1px solid #e5e7eb;border-radius:6px;
        font-size:13px;font-family:'Lato',sans-serif;color:#111;background:#fff;
        outline:none;transition:border-color 0.15s,box-shadow 0.15s;
    }
    .form-textarea { resize:vertical;min-height:120px;line-height:1.6; }
    .form-select {
        padding-right:32px;
        appearance:none;-webkit-appearance:none;cursor:pointer;
        background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2.5'%3E%3Cpath d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
        background-repeat:no-repeat;background-position:right 10px center;
    }
    .form-select:hover { border-color:#c4b5fd; }
    .form-input:focus,.form-textarea:focus,.form-select:focus { border-color:#7c3aed;box-shadow:0 0 0 2px rgba(124,58,237,0.1); }    .form-row { display:grid;grid-template-columns:1fr 1fr;gap:12px; }

    /* Detail read-only rows */
    .detail-row { display:flex;flex-direction:column;gap:3px;margin-bottom:14px; }
    .detail-label { font-size:11px;font-weight:700;color:#9ca3af;letter-spacing:0.04em;text-transform:uppercase; }
    .detail-value { font-size:13px;color:#1a1a2e;font-weight:500;line-height:1.5; }

    .ep-foot {
        padding:14px 20px;border-top:1px solid #f0f0f5;
        display:flex;gap:8px;justify-content:flex-end;flex-shrink:0;flex-wrap:wrap;
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
    .btn-ep-accept {
        padding:8px 18px;background:linear-gradient(135deg,#22c55e,#16a34a);
        color:#fff;font-size:13px;font-weight:700;font-family:'Lato',sans-serif;
        border:none;border-radius:6px;cursor:pointer;
        box-shadow:0 2px 8px rgba(22,163,74,0.2);transition:opacity 0.15s;
    }
    .btn-ep-accept:hover { opacity:0.88; }
    .btn-ep-reject {
        padding:8px 18px;background:linear-gradient(135deg,#ef4444,#dc2626);
        color:#fff;font-size:13px;font-weight:700;font-family:'Lato',sans-serif;
        border:none;border-radius:6px;cursor:pointer;
        box-shadow:0 2px 8px rgba(220,38,38,0.2);transition:opacity 0.15s;
    }
    .btn-ep-reject:hover { opacity:0.88; }

    /* ══ DELETE CONFIRM ══ */
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

    /* ══ REJECT CONFIRM ══ */
    .rej-overlay { display:none;position:fixed;inset:0;z-index:500;background:rgba(0,0,0,0.35);align-items:center;justify-content:center; }
    .rej-overlay.open { display:flex; }
    .rej-box { background:#fff;border-radius:12px;padding:24px;max-width:340px;width:100%;box-shadow:0 20px 60px rgba(0,0,0,0.18);animation:slideUp 0.2s ease; }
    .rej-title { font-size:15px;font-weight:700;color:#1a1a2e;margin-bottom:6px; }
    .rej-desc  { font-size:13px;color:#6b7280;margin-bottom:14px;line-height:1.5; }
    .rej-textarea { width:100%;padding:9px 12px;border:1px solid #e5e7eb;border-radius:6px;font-size:13px;font-family:'Lato',sans-serif;color:#111;outline:none;resize:vertical;min-height:80px; }
    .rej-textarea:focus { border-color:#7c3aed;box-shadow:0 0 0 2px rgba(124,58,237,0.1); }
    .rej-actions { display:flex;gap:8px;justify-content:flex-end;margin-top:14px; }
    .btn-rej-cancel  { padding:8px 18px;border:1px solid #e5e7eb;border-radius:6px;background:#fff;color:#374151;font-size:13px;font-weight:700;font-family:'Lato',sans-serif;cursor:pointer; }
    .btn-rej-confirm { padding:8px 18px;background:#dc2626;color:#fff;font-size:13px;font-weight:700;font-family:'Lato',sans-serif;border:none;border-radius:6px;cursor:pointer;transition:opacity 0.15s; }
    .btn-rej-confirm:hover { opacity:0.88; }

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
<form method="GET" action="{{ route('admin.temuan.index') }}" id="filterForm">
    <div class="adm-toolbar">
        <div class="adm-toolbar-left">

            {{-- Sort --}}
            <div class="adm-dd" id="sortDd">
                <button type="button" class="adm-dd-trigger" onclick="toggleDd('sortDd')">
                    <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"/></svg>
                    <span id="sortLabel">{{ $sort === 'terlama' ? 'Terlama' : 'Terbaru' }}</span>
                    <svg class="adm-chevron" width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="adm-dd-menu">
                    @foreach(['terbaru'=>'Terbaru','terlama'=>'Terlama'] as $val=>$lbl)
                    <button type="button" class="adm-dd-opt {{ $sort===$val?'selected':'' }}" onclick="selectOpt('sortDd','sortInput','sortLabel','{{ $val }}','{{ $lbl }}')">
                        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/></svg>{{ $lbl }}
                    </button>
                    @endforeach
                </div>
                <input type="hidden" name="sort" id="sortInput" value="{{ $sort }}">
            </div>

            {{-- Status filter --}}
            <div class="adm-dd" id="statusDd">
                <button type="button" class="adm-dd-trigger" onclick="toggleDd('statusDd')">
                    <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                    <span id="statusLabel">{{ ['all'=>'Semua','pending'=>'Pending','approved'=>'Approved','rejected'=>'Rejected'][$status ?? 'all'] }}</span>
                    <svg class="adm-chevron" width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="adm-dd-menu">
                    @foreach(['all'=>'Semua','pending'=>'Pending','approved'=>'Approved','rejected'=>'Rejected'] as $val=>$lbl)
                    <button type="button" class="adm-dd-opt {{ ($status??'all')===$val?'selected':'' }}" onclick="selectOpt('statusDd','statusInput','statusLabel','{{ $val }}','{{ $lbl }}')">
                        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/></svg>{{ $lbl }}
                    </button>
                    @endforeach
                </div>
                <input type="hidden" name="status" id="statusInput" value="{{ $status ?? 'all' }}">
            </div>

            {{-- Type filter --}}
            <div class="adm-dd" id="typeDd">
                <button type="button" class="adm-dd-trigger" onclick="toggleDd('typeDd')">
                    <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6M12 9v6"/></svg>
                    <span id="typeLabel">{{ ['all'=>'Semua Tipe','found'=>'Temuan','lost'=>'Kehilangan'][$type ?? 'all'] }}</span>
                    <svg class="adm-chevron" width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="adm-dd-menu">
                    @foreach(['all'=>'Semua Tipe','found'=>'Temuan','lost'=>'Kehilangan'] as $val=>$lbl)
                    <button type="button" class="adm-dd-opt {{ ($type??'all')===$val?'selected':'' }}" onclick="selectOpt('typeDd','typeInput','typeLabel','{{ $val }}','{{ $lbl }}')">
                        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/></svg>{{ $lbl }}
                    </button>
                    @endforeach
                </div>
                <input type="hidden" name="type" id="typeInput" value="{{ $type ?? 'all' }}">
            </div>

            {{-- Search --}}
            <div class="adm-search-wrap">
                <span class="adm-si"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8" stroke-width="2"/><path d="M21 21l-4.35-4.35" stroke-width="2" stroke-linecap="round"/></svg></span>
                <input type="text" name="search" class="adm-search" placeholder="Telusuri temuan..." value="{{ $search }}" oninput="debounce(this.form)">
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

{{-- Body --}}
<div class="adm-body">
    {{-- TABLE VIEW --}}
    <div class="adm-table-wrap">
        <table>
            <thead>
                <tr>
                    <th class="td-num">#</th>
                    <th>Nama Barang</th>
                    <th>Tipe</th>
                    <th>Pelapor</th>
                    <th>Ditemukan / Hilang</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th style="text-align:left;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $i => $item)
                @php $num = ($page - 1) * $perPage + $i + 1; @endphp
                <tr>
                    <td class="td-num">{{ $num }}</td>
                    <td class="td-title"><span>{{ $item->item_name }}</span></td>
                    <td>
                        <span class="type-badge {{ $item->type === 'found' ? 'type-found' : 'type-lost' }}">
                            {{ $item->type === 'found' ? 'Temuan' : 'Kehilangan' }}
                        </span>
                    </td>
                    <td style="font-size:12.5px;color:#6b7280;">{{ $item->user_name ?? '—' }}</td>
                    <td style="font-size:12.5px;color:#6b7280;max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $item->found_at ?? '—' }}</td>
                    <td style="font-size:12.5px;color:#6b7280;white-space:nowrap;">
                        {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}<br>
                        <span style="color:#9ca3af;">{{ \Carbon\Carbon::parse($item->created_at)->format('H:i') }} WIB</span>
                    </td>
                    <td>
                        <span class="status-badge status-{{ $item->status }}">
                            {{ ucfirst($item->status) }}
                        </span>
                    </td>
                    <td>
                        <div class="action-group">
                            {{-- View / Detail --}}
                            <button type="button" class="action-btn action-btn-view" title="Lihat Detail"
                                onclick="openDetailPanel({{ json_encode([
                                    'id'          => $item->id,
                                    'item_name'   => $item->item_name,
                                    'type'        => $item->type,
                                    'status'      => $item->status,
                                    'user_name'   => $item->user_name,
                                    'found_at'    => $item->found_at,
                                    'description' => $item->description,
                                    'created_at'  => \Carbon\Carbon::parse($item->created_at)->format('d M Y, H:i') . ' WIB',
                                    'photo'       => isset($photoMap[$item->id]) ? $photoMap[$item->id]->file_data : null,
                                ]) }})">
                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3" stroke-width="1.8"/></svg>
                            </button>

                            @if($item->status === 'pending')
                                {{-- Accept --}}
                                <button type="button" class="action-btn-accept" onclick="confirmAccept({{ $item->id }}, '{{ addslashes($item->item_name) }}')">
                                    <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    Terima
                                </button>
                                {{-- Reject --}}
                                <button type="button" class="action-btn-reject" onclick="openRejectModal({{ $item->id }}, '{{ addslashes($item->item_name) }}')">
                                    <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Tolak
                                </button>
                            @else
                                {{-- Edit (approved/rejected) --}}
                                <button type="button" class="action-btn action-btn-edit" title="Edit"
                                    onclick="openEditPanel({{ json_encode([
                                        'id'          => $item->id,
                                        'item_name'   => $item->item_name,
                                        'type'        => $item->type,
                                        'status'      => $item->status,
                                        'found_at'    => $item->found_at,
                                        'description' => $item->description,
                                        'photo'       => isset($photoMap[$item->id]) ? $photoMap[$item->id]->file_data : null,
                                    ]) }})">
                                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 4H6a2 2 0 00-2 2v13a2 2 0 002 2h11a2 2 0 002-2v-5"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                </button>
                            @endif

                            {{-- Delete --}}
                            <button type="button" class="action-btn action-btn-delete" title="Hapus"
                                onclick="confirmDelete({{ $item->id }}, '{{ addslashes($item->item_name) }}')">
                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" points="3 6 5 6 21 6"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:48px;color:#9ca3af;font-size:13px;">
                        <svg width="36" height="36" fill="none" stroke="#d1d5db" viewBox="0 0 24 24" style="margin:0 auto 12px;display:block;">
                            <circle cx="11" cy="11" r="7" stroke-width="1.4"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" d="M21 21l-4-4"/>
                        </svg>
                        Tidak ada data temuan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- CARD VIEW (mobile/narrow) --}}
    <div class="cards-wrap">
        @forelse($items as $i => $item)
        @php $photo = isset($photoMap[$item->id]) ? $photoMap[$item->id]->file_data : null; @endphp
        <div class="item-card">
            @if($photo)
            <img class="card-photo" src="{{ $photo }}" alt="{{ $item->item_name }}" onerror="this.style.display='none'">
            @else
            <div class="card-photo-placeholder">
                <svg width="28" height="28" fill="none" stroke="#c4c4d4" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" stroke-width="1.5"/><circle cx="8.5" cy="8.5" r="1.5" stroke-width="1.5"/><polyline stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" points="21 15 16 10 5 21"/></svg>
            </div>
            @endif
            <div class="card-body">
                <div class="card-badges">
                    <span class="type-badge {{ $item->type === 'found' ? 'type-found' : 'type-lost' }}">{{ $item->type === 'found' ? 'Temuan' : 'Kehilangan' }}</span>
                    <span class="status-badge status-{{ $item->status }}">{{ ucfirst($item->status) }}</span>
                </div>
                <div class="card-title">{{ $item->item_name }}</div>
                <div class="card-meta">
                    {{ $item->user_name ?? '—' }} · {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}
                    @if($item->found_at) · {{ $item->found_at }}@endif
                </div>
                @if($item->description)
                <div class="card-desc">{{ $item->description }}</div>
                @endif
                <div class="card-foot">
                    <button type="button" class="action-btn action-btn-view" title="Detail"
                        onclick="openDetailPanel({{ json_encode([
                            'id'=>$item->id,'item_name'=>$item->item_name,'type'=>$item->type,
                            'status'=>$item->status,'user_name'=>$item->user_name,'found_at'=>$item->found_at,
                            'description'=>$item->description,
                            'created_at'=>\Carbon\Carbon::parse($item->created_at)->format('d M Y, H:i').' WIB',
                            'photo'=>$photo,
                        ]) }})">
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3" stroke-width="1.8"/></svg>
                    </button>

                    @if($item->status === 'pending')
                    <button type="button" class="action-btn-accept" onclick="confirmAccept({{ $item->id }}, '{{ addslashes($item->item_name) }}')">
                        <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Terima
                    </button>
                    <button type="button" class="action-btn-reject" onclick="openRejectModal({{ $item->id }}, '{{ addslashes($item->item_name) }}')">
                        <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        Tolak
                    </button>
                    @else
                    <button type="button" class="action-btn action-btn-edit" title="Edit"
                        onclick="openEditPanel({{ json_encode(['id'=>$item->id,'item_name'=>$item->item_name,'type'=>$item->type,'status'=>$item->status,'found_at'=>$item->found_at,'description'=>$item->description,'photo'=>$photo]) }})">
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 4H6a2 2 0 00-2 2v13a2 2 0 002 2h11a2 2 0 002-2v-5"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </button>
                    @endif

                    <button type="button" class="action-btn action-btn-delete" title="Hapus"
                        onclick="confirmDelete({{ $item->id }}, '{{ addslashes($item->item_name) }}')">
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" points="3 6 5 6 21 6"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div style="text-align:center;padding:48px 16px;color:#9ca3af;font-size:13px;">
            <svg width="36" height="36" fill="none" stroke="#d1d5db" viewBox="0 0 24 24" style="margin:0 auto 12px;display:block;"><circle cx="11" cy="11" r="7" stroke-width="1.4"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" d="M21 21l-4-4"/></svg>
            Tidak ada data temuan.
        </div>
        @endforelse
    </div>
</div>

{{-- Pagination --}}
@php $lastPage = max(1, (int) ceil($total / $perPage)); @endphp
<div class="adm-pagination">
    <span>{{ ($page-1)*$perPage+1 }}–{{ min($page*$perPage,$total) }} dari {{ $total }} temuan</span>
    <div class="adm-page-btns">
        <a href="?{{ http_build_query(array_merge(request()->query(),['page'=>max(1,$page-1)])) }}" class="adm-page-btn {{ $page<=1?'disabled':'' }}">
            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 18l-6-6 6-6"/></svg>
        </a>
        @for($p=1;$p<=$lastPage;$p++)
        <a href="?{{ http_build_query(array_merge(request()->query(),['page'=>$p])) }}" class="adm-page-btn {{ $p==$page?'active':'' }}">{{ $p }}</a>
        @endfor
        <a href="?{{ http_build_query(array_merge(request()->query(),['page'=>min($lastPage,$page+1)])) }}" class="adm-page-btn {{ $page>=$lastPage?'disabled':'' }}">
            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 18l6-6-6-6"/></svg>
        </a>
    </div>
</div>

{{-- Panel Overlay --}}
<div class="panel-overlay" id="panelOverlay" onclick="closePanels()"></div>

{{-- ══ DETAIL SIDE PANEL ══ --}}
<div class="edit-panel" id="detailPanel">
    <div class="ep-head">
        <span class="ep-title">Detail Temuan</span>
        <div class="ep-head-actions">
            <button type="button" class="ep-icon-btn close" onclick="closePanels()">
                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>
    <div class="ep-body">
        <div id="dpPhotoTop"></div>
        <div class="ep-inner">
            <div class="ep-section-label">Detail</div>
            <div class="detail-row">
                <span class="detail-label">Nama Barang</span>
                <span class="detail-value" id="dpItemName">—</span>
            </div>
            <div style="display:flex;gap:8px;margin-bottom:14px;" id="dpBadges"></div>
            <div class="detail-row">
                <span class="detail-label">Pelapor</span>
                <span class="detail-value" id="dpUserName">—</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Lokasi Ditemukan / Hilang</span>
                <span class="detail-value" id="dpFoundAt">—</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Tanggal Laporan</span>
                <span class="detail-value" id="dpCreatedAt">—</span>
            </div>
            <div class="ep-divider"></div>
            <div class="ep-section-label">Deskripsi</div>
            <div class="detail-value" id="dpDesc" style="line-height:1.6;color:#6b7280;font-size:13px;">—</div>
        </div>
    </div>
    <div class="ep-foot" id="dpFoot">
        <button type="button" class="btn-ep-cancel" onclick="closePanels()">Tutup</button>
    </div>
</div>

{{-- ══ EDIT SIDE PANEL ══ --}}
<div class="edit-panel" id="editPanel">
    <div class="ep-head">
        <span class="ep-title" id="epPanelTitle">Edit Temuan</span>
        <div class="ep-head-actions">
            <button type="button" class="ep-icon-btn close" onclick="closePanels()">
                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>
    <div class="ep-body" id="epBody">
        <div id="epPhotoTop"></div>
        <form method="POST" id="editForm" enctype="multipart/form-data">
            @csrf
            <span id="epMethodField"></span>
            <div class="ep-inner">

                <div class="form-field">
                    <div class="ep-section-label">Foto Barang</div>
                    <div id="epPhotoControl"></div>
                </div>

                <div class="ep-divider"></div>

                <div class="form-field">
                    <label class="form-label">Nama Barang <span style="color:#dc2626;">*</span></label>
                    <input type="text" name="item_name" id="epItemName" class="form-input" required>
                </div>

                <div class="form-row">
                    <div class="form-field" style="margin-bottom:0;">
                        <label class="form-label">Tipe <span style="color:#dc2626;">*</span></label>
                        <select name="type" id="epType" class="form-select" required>
                            <option value="found">Temuan (Found)</option>
                            <option value="lost">Kehilangan (Lost)</option>
                        </select>
                    </div>
                    <div class="form-field" style="margin-bottom:0;">
                        <label class="form-label">Status <span style="color:#dc2626;">*</span></label>
                        <select name="status" id="epStatus" class="form-select" required>
                            <option value="approved">Approved</option>
                            <option value="pending">Pending</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                </div>

                <div class="ep-divider"></div>

                <div class="form-field">
                    <label class="form-label">Lokasi Ditemukan / Hilang</label>
                    <input type="text" name="found_at" id="epFoundAt" class="form-input" placeholder="Contoh: Kantin, Ruang Kelas 10A...">
                </div>

                <div class="form-field">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" id="epDesc" class="form-textarea" placeholder="Masukkan deskripsi barang..."></textarea>
                </div>

            </div>
        </form>
    </div>
    <div class="ep-foot">
        <button type="button" class="btn-ep-cancel" onclick="closePanels()">Batal</button>
        <button type="button" class="btn-ep-save" onclick="document.getElementById('editForm').submit()">Simpan Perubahan</button>
    </div>
</div>

{{-- ══ ADD SIDE PANEL ══ --}}
<div class="edit-panel" id="addPanel">
    <div class="ep-head">
        <span class="ep-title">Tambah Temuan</span>
        <div class="ep-head-actions">
            <button type="button" class="ep-icon-btn close" onclick="closePanels()">
                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>
    <div class="ep-body">
        <div id="addPhotoTop"></div>
        <form method="POST" action="{{ route('admin.temuan.store') }}" id="addForm" enctype="multipart/form-data">
            @csrf
            <div class="ep-inner">

                <div class="form-field">
                    <div class="ep-section-label">Foto Barang</div>
                    <div id="addPhotoControl">
                        <label class="ep-photo-upload-empty">
                            <svg width="22" height="22" fill="none" stroke="#9ca3af" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            <span style="font-size:12px;color:#9ca3af;font-family:'Lato',sans-serif;">Tambah foto barang</span>
                            <input type="file" name="photo" accept="image/*" onchange="handleAddPhoto(this)">
                        </label>
                    </div>
                </div>

                <div class="ep-divider"></div>

                <div class="form-field">
                    <label class="form-label">Nama Barang <span style="color:#dc2626;">*</span></label>
                    <input type="text" name="item_name" class="form-input" placeholder="Masukkan nama barang..." required>
                </div>

                <div class="form-row">
                    <div class="form-field" style="margin-bottom:0;">
                        <label class="form-label">Tipe <span style="color:#dc2626;">*</span></label>
                        <select name="type" class="form-select" required>
                            <option value="found">Temuan (Found)</option>
                            <option value="lost">Kehilangan (Lost)</option>
                        </select>
                    </div>
                    <div class="form-field" style="margin-bottom:0;">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="approved">Approved</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>
                </div>

                <div class="ep-divider"></div>

                <div class="form-field">
                    <label class="form-label">Lokasi Ditemukan / Hilang</label>
                    <input type="text" name="found_at" class="form-input" placeholder="Contoh: Kantin, Lapangan...">
                </div>

                <div class="form-field">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-textarea" placeholder="Masukkan deskripsi barang..."></textarea>
                </div>

            </div>
        </form>
    </div>
    <div class="ep-foot">
        <button type="button" class="btn-ep-cancel" onclick="closePanels()">Batal</button>
        <button type="button" class="btn-ep-save" onclick="document.getElementById('addForm').submit()">Simpan</button>
    </div>
</div>

{{-- ══ DELETE CONFIRM ══ --}}
<div class="del-overlay" id="delOverlay">
    <div class="del-box">
        <div class="del-icon">
            <svg width="22" height="22" fill="none" stroke="#dc2626" viewBox="0 0 24 24"><polyline stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" points="3 6 5 6 21 6"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
        </div>
        <div class="del-title">Hapus Temuan?</div>
        <div class="del-desc" id="delDesc">Data ini akan dihapus permanen.</div>
        <div class="del-actions">
            <button type="button" class="btn-del-cancel" onclick="closeDelConfirm()">Batal</button>
            <form id="delForm" method="POST" style="display:inline;">@csrf @method('DELETE')
                <button type="submit" class="btn-del-confirm">Hapus</button>
            </form>
        </div>
    </div>
</div>

{{-- ══ ACCEPT CONFIRM ══ --}}
<div class="del-overlay" id="acceptOverlay">
    <div class="del-box">
        <div class="del-icon" style="background:#f0fdf4;">
            <svg width="22" height="22" fill="none" stroke="#16a34a" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="del-title" style="color:#16a34a;">Terima Laporan?</div>
        <div class="del-desc" id="acceptDesc">Laporan ini akan disetujui dan dipublikasikan.</div>
        <div class="del-actions">
            <button type="button" class="btn-del-cancel" onclick="document.getElementById('acceptOverlay').classList.remove('open')">Batal</button>
            <form id="acceptForm" method="POST" style="display:inline;">@csrf @method('PATCH')
                <button type="submit" class="btn-del-confirm" style="background:#16a34a;">Terima</button>
            </form>
        </div>
    </div>
</div>

{{-- ══ REJECT MODAL ══ --}}
<div class="rej-overlay" id="rejOverlay">
    <div class="rej-box">
        <div class="rej-title">Tolak Laporan?</div>
        <div class="rej-desc" id="rejDesc">Berikan alasan penolakan (opsional):</div>
        <textarea class="rej-textarea" id="rejReason" placeholder="Alasan penolakan..."></textarea>
        <div class="rej-actions">
            <button type="button" class="btn-rej-cancel" onclick="document.getElementById('rejOverlay').classList.remove('open')">Batal</button>
            <form id="rejForm" method="POST" style="display:inline;">@csrf @method('PATCH')
                <input type="hidden" name="reject_reason" id="rejReasonInput">
                <button type="submit" class="btn-rej-confirm" onclick="document.getElementById('rejReasonInput').value=document.getElementById('rejReason').value">Tolak</button>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    /* ── Debounce ── */
    let _st;
    function debounce(form) { clearTimeout(_st); _st=setTimeout(()=>form.submit(),500); }

    /* ── Dropdowns ── */
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

    function toggleDd(id) {
        document.querySelectorAll('.adm-dd').forEach(d=>{ if(d.id!==id) d.classList.remove('open'); });
        document.getElementById(id).classList.toggle('open');
    }
    function selectOpt(ddId,inputId,labelId,val,lbl) {
        document.getElementById(inputId).value=val;
        document.getElementById(labelId).textContent=lbl;
        document.querySelectorAll('#'+ddId+' .adm-dd-opt').forEach(o=>o.classList.toggle('selected',o.textContent.trim()===lbl));
        document.getElementById(ddId).classList.remove('open');
        document.getElementById('filterForm').submit();
    }
    document.addEventListener('click',e=>{
        if(!e.target.closest('.adm-dd')) document.querySelectorAll('.adm-dd').forEach(d=>d.classList.remove('open'));
    });

    /* ── Close all panels ── */
    function closePanels() {
        document.getElementById('panelOverlay').classList.remove('open');
        ['detailPanel','editPanel','addPanel'].forEach(id=>{
            const el=document.getElementById(id);
            if(el) el.classList.remove('open');
        });
    }

    function openPanel(id) {
        closePanels();
        document.getElementById('panelOverlay').classList.add('open');
        document.getElementById(id).classList.add('open');
    }

    /* ── Detail Panel ── */
    function openDetailPanel(data) {
        document.getElementById('dpItemName').textContent = data.item_name ?? '—';
        document.getElementById('dpUserName').textContent = data.user_name ?? '—';
        document.getElementById('dpFoundAt').textContent  = data.found_at || '—';
        document.getElementById('dpCreatedAt').textContent= data.created_at ?? '—';
        document.getElementById('dpDesc').textContent     = data.description || '—';

        // Badges
        const typeLbl  = data.type === 'found' ? 'Temuan' : 'Kehilangan';
        const typeCls  = data.type === 'found' ? 'type-found' : 'type-lost';
        const statCls  = 'status-'+data.status;
        document.getElementById('dpBadges').innerHTML =
            `<span class="type-badge ${typeCls}">${typeLbl}</span><span class="status-badge ${statCls}">${data.status.charAt(0).toUpperCase()+data.status.slice(1)}</span>`;

        // Photo
        const top = document.getElementById('dpPhotoTop');
        if(data.photo) {
            top.innerHTML = `<div class="ep-photo-wrap"><img class="ep-photo" src="${data.photo}" onerror="this.style.display='none'"></div>`;
        } else {
            top.innerHTML = `<div class="ep-photo-placeholder"><svg width="32" height="32" fill="none" stroke="#c4c4d4" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" stroke-width="1.5"/><circle cx="8.5" cy="8.5" r="1.5" stroke-width="1.5"/><polyline stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" points="21 15 16 10 5 21"/></svg></div>`;
        }

        // Footer – accept/reject for pending
        const foot = document.getElementById('dpFoot');
        if(data.status === 'pending') {
            foot.innerHTML = `
                <button type="button" class="btn-ep-cancel" onclick="closePanels()">Tutup</button>
                <button type="button" class="btn-ep-reject" onclick="openRejectModal(${data.id}, '${data.item_name.replace(/'/g,"\\'")}')">Tolak</button>
                <button type="button" class="btn-ep-accept" onclick="confirmAccept(${data.id}, '${data.item_name.replace(/'/g,"\\'")}')">Terima</button>`;
        } else {
            foot.innerHTML = `<button type="button" class="btn-ep-cancel" onclick="closePanels()">Tutup</button>`;
        }

        openPanel('detailPanel');
        document.getElementById('detailPanel').querySelector('.ep-body').scrollTop=0;
    }

    /* ── Edit Panel ── */
function renderEpPhoto(url, containerId, formId, fieldName) {
    const ctrl = document.getElementById(containerId);
    if(url) {
        ctrl.innerHTML=`
            <div class="ep-photo-wrap" style="border-radius:8px;">
                <img src="${url}" style="width:100%;height:130px;object-fit:cover;display:block;border-radius:8px;" onerror="this.style.display='none'">
                <label class="ep-photo-change-btn">
                    <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4H6a2 2 0 00-2 2v13a2 2 0 002 2h11a2 2 0 002-2v-5"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg> 
                    Ganti Foto
                    <input type="file" name="${fieldName}" id="bannerInputEdit" accept="image/*" onchange="handleEpPhoto(this,'${formId}')">
                </label>
            </div>`;
    } else {
        ctrl.innerHTML=`
            <label class="ep-photo-upload-empty">
                <svg width="22" height="22" fill="none" stroke="#9ca3af" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                <span style="font-size:12px;color:#9ca3af;font-family:'Lato',sans-serif;">Tambah foto barang</span>
                <input type="file" name="${fieldName}" id="bannerInputEdit" accept="image/*" onchange="handleEpPhoto(this,'${formId}')">
            </label>`;
    }
}

/* ── Handler Photo (Edit Panel) ── */
function handleEpPhoto(input, formId) {
    if(!input.files||!input.files[0]) return;
    const file=input.files[0];
    const reader=new FileReader();
    reader.onload=e=>{
        const top=document.getElementById('epPhotoTop');
        top.innerHTML=`
            <div class="ep-photo-wrap">
                <img class="ep-photo" src="${e.target.result}">
                <label class="ep-photo-change-btn">
                    Ganti Foto
                    <input type="file" name="photo" accept="image/*" onchange="handleEpPhoto(this,'${formId}')">
                </label>
            </div>`;
        renderEpPhoto(e.target.result,'epPhotoControl',formId,'photo');
        
        const dt=new DataTransfer(); dt.items.add(file);
        document.querySelectorAll(`#${formId} input[type=file][name=photo]`).forEach(el=>{try{el.files=dt.files;}catch(err){}});
    };
    reader.readAsDataURL(file);
}

/* ── Handler Photo (Add Panel) ── */
function handleAddPhoto(input) {
    if(!input.files||!input.files[0]) return;
    const file=input.files[0];
    const reader=new FileReader();
    reader.onload=e=>{
        const top=document.getElementById('addPhotoTop');
        top.innerHTML=`<div class="ep-photo-wrap"><img class="ep-photo" src="${e.target.result}"></div>`;
        
        const ctrl=document.getElementById('addPhotoControl');
        ctrl.innerHTML=`
            <div class="ep-photo-wrap" style="border-radius:8px;">
                <img src="${e.target.result}" style="width:100%;height:130px;object-fit:cover;display:block;border-radius:8px;">
                <label class="ep-photo-change-btn">
                    Ganti Foto
                    <input type="file" name="photo" id="bannerInputAdd" accept="image/*" onchange="handleAddPhoto(this)">
                </label>
            </div>`;
    };
    reader.readAsDataURL(file);
}

    function openEditPanel(data) {
        document.getElementById('epItemName').value = data.item_name ?? '';
        document.getElementById('epType').value     = data.type ?? 'found';
        document.getElementById('epStatus').value   = data.status ?? 'approved';
        document.getElementById('epFoundAt').value  = data.found_at ?? '';
        document.getElementById('epDesc').value     = data.description ?? '';
        document.getElementById('editForm').action  = `/admin/temuan/${data.id}`;
        document.getElementById('epMethodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';

        const top=document.getElementById('epPhotoTop');
        if(data.photo) {
            top.innerHTML=`<div class="ep-photo-wrap"><img class="ep-photo" src="${data.photo}" onerror="this.style.display='none'"><label class="ep-photo-change-btn">Ganti Foto<input type="file" name="photo" accept="image/*" onchange="handleEpPhoto(this,'editForm')"></label></div>`;
        } else { top.innerHTML=''; }
        renderEpPhoto(data.photo||null,'epPhotoControl','editForm','photo');

        openPanel('editPanel');
        document.getElementById('epBody').scrollTop=0;
    }

  function openAddPanel() {
    document.getElementById('addPhotoTop').innerHTML='';
    // Pastikan di sini juga ada accept="image/*"
    document.getElementById('addPhotoControl').innerHTML=`
        <label class="ep-photo-upload-empty">
            <svg width="22" height="22" fill="none" stroke="#9ca3af" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
            <span style="font-size:12px;color:#9ca3af;font-family:'Lato',sans-serif;">Tambah foto barang</span>
            <input type="file" name="photo" id="bannerInput" accept="image/*" onchange="handleAddPhoto(this)">
        </label>`;
    document.getElementById('addForm').reset();
    openPanel('addPanel');
}

    /* ── Accept ── */
    function confirmAccept(id, name) {
        document.getElementById('acceptDesc').textContent = `"${name}" akan disetujui dan dipublikasikan.`;
        document.getElementById('acceptForm').action = `/admin/temuan/${id}/approve`;
        document.getElementById('acceptOverlay').classList.add('open');
    }

    /* ── Reject ── */
    function openRejectModal(id, name) {
        document.getElementById('rejDesc').textContent = `Tolak laporan "${name}"? Berikan alasan (opsional):`;
        document.getElementById('rejReason').value = '';
        document.getElementById('rejForm').action = `/admin/temuan/${id}/reject`;
        closePanels();
        document.getElementById('rejOverlay').classList.add('open');
    }

    /* ── Delete ── */
    function confirmDelete(id, name) {
        document.getElementById('delDesc').textContent = `"${name}" akan dihapus permanen.`;
        document.getElementById('delForm').action = `/admin/temuan/${id}`;
        document.getElementById('delOverlay').classList.add('open');
    }
    function closeDelConfirm() { document.getElementById('delOverlay').classList.remove('open'); }

    /* ── Escape ── */
    document.addEventListener('keydown', e => {
        if(e.key==='Escape') {
            closePanels();
            closeDelConfirm();
            document.getElementById('acceptOverlay').classList.remove('open');
            document.getElementById('rejOverlay').classList.remove('open');
        }
    });
</script>
@endpush