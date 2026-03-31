{{-- resources/views/admin/pengumuman/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Pengumuman – Admin SINTEM')

@section('topbar')
<div style="display:flex;align-items:center;justify-content:space-between;padding:14px 32px;border-bottom:1px solid #f0f0f5;background:#fff;">
    <p style="font-size:13.5px;font-weight:700;color:#1a1a2e;">Selamat Datang, {{ Auth::user()->name }}! </p>
    <button type="button" class="btn-publish" onclick="openAddModal()">
        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 5v14M5 12h14"/>
        </svg>
        Publish Pengumuman
    </button>
</div>
@endsection

@section('header', 'Pengumuman')
@section('subheader', 'Kelola semua pengumuman sekolah.')

@push('styles')
<style>
    .page-body { padding:0!important; overflow:hidden!important; display:flex; flex-direction:column; }
    .main-content { background:#fff!important; }
    .page-header  { padding:16px 32px 14px!important; background:#fff!important; }

    .btn-publish {
        display:inline-flex;align-items:center;gap:6px;
        padding:8px 16px;background:linear-gradient(135deg,#9025FB,#4617D3);
        color:#fff;font-size:13px;font-weight:700;font-family:'Lato',sans-serif;
        border-radius:6px;border:none;cursor:pointer;
        box-shadow:0 2px 8px rgba(109,40,217,0.2);
        transition:opacity 0.15s,transform 0.15s;
    }
    .btn-publish:hover { opacity:0.88;transform:translateY(-1px); }

    /* Toolbar */
    .adm-toolbar {
        flex-shrink:0;padding:14px 32px 12px;
        border-bottom:1px solid #f0f0f5;background:#fff;
        display:flex;align-items:center;justify-content:space-between;gap:8px;flex-wrap:wrap;
    }
    .adm-toolbar-left { display:flex;align-items:center;gap:6px; }

    /* Custom dropdown */
    .adm-dd { position:relative;display:inline-block; }
    .adm-dd-trigger {
        display:inline-flex;align-items:center;gap:6px;
        padding:6px 10px;border:1px solid #e5e7eb;border-radius:5px;
        font-size:12.5px;font-family:'Lato',sans-serif;font-weight:600;
        color:#374151;background:#fff;cursor:pointer;outline:none;
        transition:border-color 0.12s;white-space:nowrap;
    }
    .adm-dd-trigger:hover { border-color:#c4b5fd; }
    .adm-dd.open .adm-dd-trigger { border-color:#7c3aed;box-shadow:0 0 0 2px rgba(124,58,237,0.1); }
    .adm-chevron { transition:transform 0.2s; }
    .adm-dd.open .adm-chevron { transform:rotate(180deg); }
    .adm-dd-menu {
        display:none;position:absolute;top:calc(100% + 4px);left:0;
        min-width:140px;background:#fff;border:1px solid #e5e7eb;
        border-radius:6px;box-shadow:0 4px 16px rgba(0,0,0,0.08);
        z-index:100;padding:4px;
    }
    .adm-dd.open .adm-dd-menu { display:block;animation:ddFade 0.15s ease; }
    @keyframes ddFade { from{opacity:0;transform:translateY(-4px)}to{opacity:1;transform:translateY(0)} }
    .adm-dd-opt {
        display:flex;align-items:center;gap:8px;width:100%;
        padding:7px 10px;font-size:13px;font-family:'Lato',sans-serif;
        font-weight:500;color:#374151;background:none;border:none;
        border-radius:4px;cursor:pointer;text-align:left;
        transition:background 0.1s,color 0.1s;
    }
    .adm-dd-opt svg { opacity:0;flex-shrink:0;stroke:#7c3aed; }
    .adm-dd-opt:hover { background:#f4f0ff;color:#4f28d9; }
    .adm-dd-opt.selected { color:#4f28d9;font-weight:700; }
    .adm-dd-opt.selected svg { opacity:1; }

    .adm-search-wrap { position:relative;display:flex;align-items:center; }
    .adm-search-wrap .adm-si { position:absolute;left:9px;color:#b0b0c0;pointer-events:none; }
    .adm-search {
        padding:6px 12px 6px 30px;border:1px solid #e5e7eb;
        border-radius:5px;font-size:12.5px;font-family:'Lato',sans-serif;
        color:#374151;background:#fff;width:240px;outline:none;
        transition:border-color 0.12s;
    }
    .adm-search::placeholder { color:#c4c4cc; }
    .adm-search:focus { border-color:#7c3aed;box-shadow:0 0 0 2px rgba(124,58,237,0.1); }

    /* Table area */
    .adm-body {
        flex:1;min-height:0;overflow-y:auto;
        scrollbar-width:none;-ms-overflow-style:none;
    }
    .adm-body::-webkit-scrollbar { display:none; }

    .adm-table-wrap { padding:20px 32px 32px; }

    table { width:100%;border-collapse:collapse; }
    thead th {
        padding:10px 14px;text-align:left;
        font-size:12px;font-weight:700;color:#6b7280;
        background:#f9f9fb;border-bottom:1px solid #ebebf0;
    }
    thead th:first-child { border-radius:8px 0 0 0; }
    thead th:last-child  { border-radius:0 8px 0 0; }
    tbody tr { border-bottom:1px solid #f5f5f7;transition:background 0.1s; }
    tbody tr:hover { background:#fafafa; }
    tbody tr:last-child { border-bottom:none; }
    tbody td { padding:12px 14px;font-size:13px;color:#374151;vertical-align:middle; }

    .td-num    { color:#9ca3af;font-size:12px;width:40px; }
    .td-title  { font-weight:600;color:#1a1a2e;max-width:280px; }
    .td-title span { display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden; }

    .status-badge {
        font-size:11px;font-weight:700;padding:2px 10px;
        border-radius:4px;border:1px solid;display:inline-block;
    }
    .status-published { background:#f0fdf4;color:#16a34a;border-color:#bbf7d0; }
    .status-draft     { background:#f9fafb;color:#6b7280;border-color:#e5e7eb; }

    .action-btn {
        display:inline-flex;align-items:center;justify-content:center;
        width:28px;height:28px;border-radius:5px;border:1px solid #e5e7eb;
        background:#fff;cursor:pointer;color:#6b7280;
        transition:background 0.12s,color 0.12s,border-color 0.12s;
        margin-right:3px;
    }
    .action-btn:hover { background:#f4f0ff;color:#4f28d9;border-color:#c4b5fd; }
    .action-btn.danger:hover { background:#fef2f2;color:#dc2626;border-color:#fecaca; }

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

    /* ══ ADD MODAL ══ */
    .overlay {
        display:none;position:fixed;inset:0;z-index:300;
        background:rgba(0,0,0,0.4);align-items:center;justify-content:center;padding:24px;
    }
    .overlay.open { display:flex;animation:fadeOv 0.18s ease; }
    @keyframes fadeOv { from{opacity:0}to{opacity:1} }

    .add-modal {
        background:#fff;border-radius:14px;
        width:100%;max-width:560px;max-height:92vh;
        overflow-y:auto;box-shadow:0 24px 64px rgba(0,0,0,0.18);
        animation:slideUp 0.22s cubic-bezier(0.22,1,0.36,1);
        scrollbar-width:none;-ms-overflow-style:none;
    }
    .add-modal::-webkit-scrollbar { display:none; }
    @keyframes slideUp { from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)} }

    .add-modal-head {
        display:flex;align-items:center;justify-content:space-between;
        padding:18px 20px 16px;border-bottom:1px solid #f0f0f5;
    }
    .add-modal-title { font-size:15px;font-weight:700;color:#1a1a2e; }
    .modal-close-btn {
        width:28px;height:28px;border-radius:6px;border:1px solid #e5e7eb;
        background:#fff;cursor:pointer;display:flex;align-items:center;
        justify-content:center;color:#9ca3af;
        transition:background 0.12s,color 0.12s;
    }
    .modal-close-btn:hover { background:#fef2f2;color:#dc2626;border-color:#fecaca; }

    .add-modal-body { padding:20px; }

    .form-field { margin-bottom:16px; }
    .form-label {
        display:block;font-size:12.5px;font-weight:700;
        color:#374151;margin-bottom:6px;
    }
    .form-input, .form-textarea, .form-select {
        width:100%;padding:9px 12px;
        border:1px solid #e5e7eb;border-radius:6px;
        font-size:13px;font-family:'Lato',sans-serif;
        color:#111;background:#fff;outline:none;
        transition:border-color 0.15s,box-shadow 0.15s;
    }
    .form-textarea { resize:vertical;min-height:100px;line-height:1.6; }
    .form-input:focus,.form-textarea:focus,.form-select:focus {
        border-color:#7c3aed;box-shadow:0 0 0 2px rgba(124,58,237,0.1);
    }
    .form-hint { font-size:11.5px;color:#9ca3af;margin-top:4px; }

    /* Photo upload area */
    .upload-zone {
        border:2px dashed #e5e7eb;border-radius:8px;
        padding:20px;text-align:center;cursor:pointer;
        transition:border-color 0.15s,background 0.15s;
        position:relative;
    }
    .upload-zone:hover { border-color:#c4b5fd;background:#faf8ff; }
    .upload-zone input[type=file] { position:absolute;inset:0;opacity:0;cursor:pointer; }
    .upload-zone-icon { font-size:28px;margin-bottom:6px; }
    .upload-zone-text { font-size:13px;color:#6b7280; }
    .upload-zone-sub  { font-size:11.5px;color:#9ca3af;margin-top:2px; }
    #photoPreview { margin-top:10px;display:none; }
    #photoPreview img { width:100%;border-radius:6px;max-height:160px;object-fit:cover; }

    /* Attachment tabs */
    .attach-tabs { display:flex;gap:4px;margin-bottom:12px; }
    .attach-tab {
        padding:5px 12px;border-radius:5px;font-size:12.5px;font-weight:600;
        border:1px solid #e5e7eb;background:#fff;color:#6b7280;cursor:pointer;
        transition:background 0.12s,color 0.12s;
    }
    .attach-tab.active { background:#ede9fe;color:#4f28d9;border-color:#c4b5fd; }
    .attach-pane { display:none; }
    .attach-pane.active { display:block; }

    .add-modal-foot {
        display:flex;align-items:center;justify-content:flex-end;gap:8px;
        padding:14px 20px;border-top:1px solid #f0f0f5;
    }
    .btn-draft {
        padding:8px 18px;border:1px solid #e5e7eb;border-radius:6px;
        background:#fff;color:#6b7280;font-size:13px;font-weight:700;
        font-family:'Lato',sans-serif;cursor:pointer;
        transition:background 0.12s,border-color 0.12s;
    }
    .btn-draft:hover { background:#f9f9fb;border-color:#c4b5fd;color:#4f28d9; }
    .btn-save {
        padding:8px 20px;background:linear-gradient(135deg,#9025FB,#4617D3);
        color:#fff;font-size:13px;font-weight:700;font-family:'Lato',sans-serif;
        border:none;border-radius:6px;cursor:pointer;
        box-shadow:0 2px 8px rgba(109,40,217,0.2);
        transition:opacity 0.15s;
    }
    .btn-save:hover { opacity:0.88; }

    /* ══ EDIT SIDE PANEL ══ */
    .edit-panel {
        position:fixed;right:-480px;top:0;bottom:0;
        width:440px;background:#fff;
        box-shadow:-4px 0 32px rgba(0,0,0,0.12);
        z-index:200;display:flex;flex-direction:column;
        transition:right 0.3s cubic-bezier(0.22,1,0.36,1);
        overflow:hidden;
    }
    .edit-panel.open { right:0; }

    .edit-panel-head {
        display:flex;align-items:center;justify-content:space-between;
        padding:16px 20px;border-bottom:1px solid #f0f0f5;flex-shrink:0;
    }
    .edit-panel-title { font-size:14px;font-weight:700;color:#1a1a2e; }

    .edit-panel-body {
        flex:1;overflow-y:auto;padding:20px;
        scrollbar-width:none;-ms-overflow-style:none;
    }
    .edit-panel-body::-webkit-scrollbar { display:none; }

    .edit-panel-foot {
        padding:14px 20px;border-top:1px solid #f0f0f5;
        display:flex;gap:8px;justify-content:flex-end;flex-shrink:0;
    }

    /* Edit panel preview image */
    .ep-img { width:100%;height:160px;border-radius:8px;object-fit:cover;margin-bottom:14px;display:block; }
    .ep-img-placeholder {
        width:100%;height:160px;border-radius:8px;
        background:#f4f4f8;display:flex;align-items:center;
        justify-content:center;font-size:36px;margin-bottom:14px;
    }

    .ep-section-label {
        font-size:11px;font-weight:700;color:#9ca3af;
        letter-spacing:0.06em;text-transform:uppercase;margin-bottom:8px;
    }

    /* Delete confirm mini modal */
    .del-confirm {
        display:none;position:fixed;inset:0;z-index:400;
        background:rgba(0,0,0,0.35);align-items:center;justify-content:center;
    }
    .del-confirm.open { display:flex; }
    .del-box {
        background:#fff;border-radius:12px;padding:24px;
        max-width:340px;width:100%;
        box-shadow:0 20px 60px rgba(0,0,0,0.18);
        text-align:center;
    }
    .del-box-icon { font-size:32px;margin-bottom:10px; }
    .del-box-title { font-size:15px;font-weight:700;color:#1a1a2e;margin-bottom:6px; }
    .del-box-sub   { font-size:13px;color:#6b7280;margin-bottom:20px; }
    .del-box-btns  { display:flex;gap:8px;justify-content:center; }
    .btn-cancel    { padding:8px 20px;border:1px solid #e5e7eb;border-radius:6px;background:#fff;color:#6b7280;font-size:13px;font-weight:700;font-family:'Lato',sans-serif;cursor:pointer; }
    .btn-delete-confirm { padding:8px 20px;background:#dc2626;color:#fff;font-size:13px;font-weight:700;font-family:'Lato',sans-serif;border:none;border-radius:6px;cursor:pointer; }

    /* Alert */
    .adm-alert {
        margin:16px 32px 0;padding:10px 14px;border-radius:6px;
        font-size:13px;display:flex;align-items:center;gap:8px;flex-shrink:0;
    }
    .adm-alert-success { background:#f0fdf4;border:1px solid #bbf7d0;color:#16a34a; }
    .adm-alert-error   { background:#fef2f2;border:1px solid #fecaca;color:#dc2626; }
</style>
@endpush

@section('content')

{{-- Alerts --}}
@if(session('success'))
<div class="adm-alert adm-alert-success">
    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="adm-alert adm-alert-error">
    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="10" stroke-width="2"/>
        <path d="M12 8v4m0 4h.01" stroke-width="2.5" stroke-linecap="round"/>
    </svg>
    {{ session('error') }}
</div>
@endif

{{-- Toolbar --}}
<form method="GET" action="{{ route('admin.pengumuman.index') }}" id="filterForm">
    <div class="adm-toolbar">
        <div class="adm-toolbar-left">

            {{-- Sort/Filter dropdown --}}
            <div class="adm-dd" id="filterDd">
                <button type="button" class="adm-dd-trigger" onclick="toggleDd('filterDd')">
                    <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                    </svg>
                    <span id="filterLabel">{{ $filter === 'semua' ? 'Semua' : ($filter === 'published' ? 'Published' : 'Draft') }}</span>
                    <svg class="adm-chevron" width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="adm-dd-menu">
                    @foreach(['semua'=>'Semua','published'=>'Published','draft'=>'Draft'] as $val=>$lbl)
                    <button type="button" class="adm-dd-opt {{ $filter===$val?'selected':'' }}"
                            onclick="selectFilter('{{ $val }}','{{ $lbl }}')">
                        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ $lbl }}
                    </button>
                    @endforeach
                </div>
                <input type="hidden" name="filter" id="filterInput" value="{{ $filter }}">
            </div>

            {{-- Search --}}
            <div class="adm-search-wrap">
                <span class="adm-si">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8" stroke-width="2"/>
                        <path d="M21 21l-4.35-4.35" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </span>
                <input type="text" name="search" class="adm-search"
                    placeholder="Telusuri pengumuman..."
                    value="{{ $search }}"
                    oninput="debounce(this.form)">
            </div>

        </div>

        {{-- Per page --}}
        <div style="display:flex;align-items:center;gap:6px;font-size:12.5px;color:#6b7280;">
            <span>Per halaman:</span>
            <select name="per_page" onchange="this.form.submit()"
                style="padding:5px 8px;border:1px solid #e5e7eb;border-radius:5px;font-size:12.5px;font-family:'Lato',sans-serif;outline:none;">
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
                    <th>Action</th>
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
                        {{-- Edit --}}
                        <button type="button" class="action-btn" title="Edit"
                                onclick="openEditPanel({{ json_encode([
                                    'id' => $item->id,
                                    'title' => $item->title,
                                    'content' => $item->content,
                                    'is_published' => $item->is_published,
                                    'photo' => optional($item->photos->first())->file_path
                                        ? asset('storage/' . $item->photos->first()->file_path)
                                        : null,
                                ]) }})"
                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                      d="M11 4H6a2 2 0 00-2 2v13a2 2 0 002 2h11a2 2 0 002-2v-5"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                      d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                            </svg>
                        </button>
                        {{-- Toggle draft --}}
                        <form method="POST" action="{{ route('admin.pengumuman.toggleDraft', $item->id) }}" style="display:inline;">
                            @csrf @method('PATCH')
                            <button type="submit" class="action-btn" title="{{ $item->is_published ? 'Jadikan Draft' : 'Publish' }}">
                                @if($item->is_published)
                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a10 10 0 015.304-5.98M9.9 4.24A9.12 9.12 0 0112 4c4.478 0 8.268 2.943 9.543 7a10 10 0 01-4.132 5.411M3 3l18 18"/>
                                </svg>
                                @else
                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                @endif
                            </button>
                        </form>
                        {{-- Delete --}}
                        <button type="button" class="action-btn danger" title="Hapus"
                                onclick="confirmDelete({{ $item->id }}, '{{ addslashes($item->title) }}')">
                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:40px;color:#9ca3af;font-size:13px;">
                        Tidak ada pengumuman.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Pagination --}}
@php
    $lastPage = max(1, (int) ceil($total / $perPage));
@endphp
<div class="adm-pagination">
    <span>{{ ($page-1)*$perPage+1 }}–{{ min($page*$perPage,$total) }} of {{ $total }}</span>
    <div class="adm-page-btns">
        <a href="?{{ http_build_query(array_merge(request()->query(),['page'=>max(1,$page-1)])) }}"
           class="adm-page-btn {{ $page<=1?'disabled':'' }}">‹</a>
        @for($p=1;$p<=$lastPage;$p++)
        <a href="?{{ http_build_query(array_merge(request()->query(),['page'=>$p])) }}"
           class="adm-page-btn {{ $p==$page?'active':'' }}">{{ $p }}</a>
        @endfor
        <a href="?{{ http_build_query(array_merge(request()->query(),['page'=>min($lastPage,$page+1)])) }}"
           class="adm-page-btn {{ $page>=$lastPage?'disabled':'' }}">›</a>
    </div>
</div>

{{-- ══ ADD MODAL ══ --}}
<div class="overlay" id="addOverlay" onclick="closeAddIfOutside(event)">
<div class="add-modal" id="addModal">
    <div class="add-modal-head">
        <div class="add-modal-title">Publish Pengumuman Baru</div>
        <button type="button" class="modal-close-btn" onclick="closeAddModal()">
            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
    <form method="POST" action="{{ route('admin.pengumuman.store') }}"
          enctype="multipart/form-data" id="addForm">
        @csrf
        <div class="add-modal-body">

            {{-- Photo --}}
            <div class="form-field">
                <label class="form-label">Foto / Banner</label>
                <div class="upload-zone" id="uploadZone">
                    <input type="file" name="photo" accept="image/*" onchange="previewPhoto(this)">
                    <div class="upload-zone-icon">🖼️</div>
                    <div class="upload-zone-text">Klik atau seret foto ke sini</div>
                    <div class="upload-zone-sub">PNG, JPG, WEBP — maks. 5MB</div>
                </div>
                <div id="photoPreview">
                    <img id="photoPreviewImg" src="" alt="preview">
                </div>
            </div>

            {{-- Title --}}
            <div class="form-field">
                <label class="form-label">Judul Pengumuman <span style="color:#dc2626;">*</span></label>
                <input type="text" name="title" class="form-input" placeholder="Masukkan judul pengumuman" required>
            </div>

            {{-- Description --}}
            <div class="form-field">
                <label class="form-label">Isi Pengumuman <span style="color:#dc2626;">*</span></label>
                <textarea name="content" class="form-textarea" placeholder="Tulis isi pengumuman di sini..." required></textarea>
            </div>

            {{-- Attachment --}}
            <div class="form-field">
                <label class="form-label">Lampiran (opsional)</label>
                <div class="attach-tabs">
                    <button type="button" class="attach-tab active" onclick="switchTab('file')">📎 File</button>
                    <button type="button" class="attach-tab"        onclick="switchTab('link')">🔗 Link</button>
                </div>
                <div class="attach-pane active" id="pane-file">
                    <input type="file" name="attachment_file" class="form-input"
                           style="padding:7px;" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx">
                    <p class="form-hint">Maks. 5MB. Format: PDF, Word, Excel, PowerPoint.</p>
                    <input type="text" name="attachment_label" class="form-input" style="margin-top:8px;"
                           placeholder="Label lampiran (opsional)">
                </div>
                <div class="attach-pane" id="pane-link">
                    <input type="url" name="link_url" class="form-input" placeholder="https://drive.google.com/...">
                    <input type="text" name="link_label" class="form-input" style="margin-top:8px;"
                           placeholder="Label link (contoh: Lihat di Google Drive)">
                </div>
            </div>

        </div>
        <div class="add-modal-foot">
            <button type="submit" name="is_published" value="0" class="btn-draft">Simpan Draft</button>
            <button type="submit" name="is_published" value="1" class="btn-save">Publish</button>
        </div>
    </form>
</div>
</div>

{{-- ══ EDIT SIDE PANEL ══ --}}
<div class="edit-panel" id="editPanel">
    <div class="edit-panel-head">
        <div class="edit-panel-title">Edit Pengumuman</div>
        <button type="button" class="modal-close-btn" onclick="closeEditPanel()">
            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
    <form method="POST" id="editForm" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="edit-panel-body">

            {{-- Current photo --}}
            <div class="ep-section-label">Foto Saat Ini</div>
            <div id="epImgWrap" class="ep-img-placeholder">🖼️</div>

            {{-- Replace photo --}}
            <div class="form-field" style="margin-top:12px;">
                <label class="form-label">Ganti Foto</label>
                <div class="upload-zone">
                    <input type="file" name="photo" accept="image/*" onchange="previewEditPhoto(this)">
                    <div class="upload-zone-icon" style="font-size:20px;">📁</div>
                    <div class="upload-zone-text" style="font-size:12.5px;">Klik untuk ganti foto</div>
                </div>
            </div>

            <div class="form-field">
                <label class="form-label">Judul</label>
                <input type="text" name="title" id="editTitle" class="form-input" required>
            </div>

            <div class="form-field">
                <label class="form-label">Isi Pengumuman</label>
                <textarea name="content" id="editContent" class="form-textarea" required></textarea>
            </div>

            <div class="form-field">
                <label class="form-label">Status</label>
                <select name="is_published" id="editStatus" class="form-select">
                    <option value="1">Published</option>
                    <option value="0">Draft</option>
                </select>
            </div>

        </div>
        <div class="edit-panel-foot">
            <button type="button" class="btn-draft" onclick="closeEditPanel()">Batal</button>
            <button type="submit" class="btn-save">Simpan Perubahan</button>
        </div>
    </form>
</div>

{{-- Panel backdrop --}}
<div id="editBackdrop" style="display:none;position:fixed;inset:0;z-index:199;background:rgba(0,0,0,0.2);"
     onclick="closeEditPanel()"></div>

{{-- ══ DELETE CONFIRM ══ --}}
<div class="del-confirm" id="delConfirm">
    <div class="del-box">
        <div class="del-box-icon">🗑️</div>
        <div class="del-box-title">Hapus Pengumuman?</div>
        <div class="del-box-sub" id="delBoxSub">Tindakan ini tidak dapat dibatalkan.</div>
        <div class="del-box-btns">
            <button type="button" class="btn-cancel" onclick="closeDelConfirm()">Batal</button>
            <form id="delForm" method="POST" style="display:inline;">
                @csrf @method('DELETE')
                <button type="submit" class="btn-delete-confirm">Hapus</button>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Debounce
    let st;
    function debounce(form) { clearTimeout(st); st = setTimeout(()=>form.submit(),500); }

    // Dropdown
    function toggleDd(id) { document.getElementById(id).classList.toggle('open'); }
    function selectFilter(val,lbl) {
        document.getElementById('filterInput').value=val;
        document.getElementById('filterLabel').textContent=lbl;
        document.querySelectorAll('.adm-dd-opt').forEach(o=>o.classList.toggle('selected',o.textContent.trim()===lbl));
        document.getElementById('filterDd').classList.remove('open');
        document.getElementById('filterForm').submit();
    }
    document.addEventListener('click',e=>{
        ['filterDd'].forEach(id=>{
            const el=document.getElementById(id);
            if(el&&!el.contains(e.target)) el.classList.remove('open');
        });
    });

    // Photo preview (add)
    function previewPhoto(input) {
        const wrap = document.getElementById('photoPreview');
        const img  = document.getElementById('photoPreviewImg');
        if (input.files && input.files[0]) {
            img.src = URL.createObjectURL(input.files[0]);
            wrap.style.display = 'block';
        }
    }

    // Photo preview (edit)
    function previewEditPhoto(input) {
        if (input.files && input.files[0]) {
            const wrap = document.getElementById('epImgWrap');
            wrap.outerHTML = `<img id="epImgWrap" class="ep-img" src="${URL.createObjectURL(input.files[0])}" alt="">`;
        }
    }

    // Attachment tabs
    function switchTab(tab) {
        document.querySelectorAll('.attach-tab').forEach((t,i)=>t.classList.toggle('active',['file','link'][i]===tab));
        document.querySelectorAll('.attach-pane').forEach(p=>p.classList.remove('active'));
        document.getElementById('pane-'+tab).classList.add('active');
    }

    // Add modal
    function openAddModal()  { document.getElementById('addOverlay').classList.add('open'); }
    function closeAddModal() { document.getElementById('addOverlay').classList.remove('open'); }
    function closeAddIfOutside(e) { if(e.target===document.getElementById('addOverlay')) closeAddModal(); }

    // Edit panel
    function openEditPanel(data) {
        document.getElementById('editTitle').value   = data.title;
        document.getElementById('editContent').value = data.content;
        document.getElementById('editStatus').value  = data.is_published;
        document.getElementById('editForm').action   = `/admin/pengumuman/${data.id}`;

        const wrap = document.getElementById('epImgWrap');
        if (data.photo) {
            wrap.outerHTML = `<img id="epImgWrap" class="ep-img" src="${data.photo}" alt="" onerror="this.outerHTML='<div id=epImgWrap class=ep-img-placeholder>🖼️</div>'">`;
        } else {
            if (wrap.tagName === 'IMG') wrap.outerHTML = `<div id="epImgWrap" class="ep-img-placeholder">🖼️</div>`;
        }

        document.getElementById('editPanel').classList.add('open');
        document.getElementById('editBackdrop').style.display = 'block';
    }
    function closeEditPanel() {
        document.getElementById('editPanel').classList.remove('open');
        document.getElementById('editBackdrop').style.display = 'none';
    }

    // Delete
    function confirmDelete(id, title) {
        document.getElementById('delBoxSub').textContent = `"${title}" akan dihapus permanen.`;
        document.getElementById('delForm').action = `/admin/pengumuman/${id}`;
        document.getElementById('delConfirm').classList.add('open');
    }
    function closeDelConfirm() { document.getElementById('delConfirm').classList.remove('open'); }
</script>
@endpush