{{-- resources/views/superadmin/accounts/index.blade.php --}}
@extends('superadmin.layouts.app')

@section('title', 'Manajemen Akun – SINTEM Superadmin')
@section('header', 'Manajemen Akun')
@section('subheader', 'CRUD akun pengguna dan penetapan role.')

@push('styles')
<style>
    /* ── Alert banners ── */
    .sa-alert { display:flex; align-items:center; gap:9px; padding:11px 14px; border-radius:7px; font-size:13px; margin-bottom:16px; }
    .sa-alert-success { background:#f0fdf4; border:1px solid #bbf7d0; color:#15803d; }
    .sa-alert-error   { background:#fef2f2; border:1px solid #fecaca; color:#dc2626; }

    /* ── Toolbar ── */
    .adm-toolbar {
        flex-shrink: 0; padding: 14px 0 12px;
        display: flex; align-items: center; justify-content: space-between;
        gap: 8px; flex-wrap: wrap; margin-bottom: 16px;
    }
    .adm-toolbar-left  { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
    .adm-toolbar-right { display: flex; align-items: center; gap: 8px; }

    /* ── Search ── */
    .adm-search-wrap { position: relative; display: flex; align-items: center; }
    .adm-search-wrap .adm-si { position: absolute; left: 9px; color: #b0b0c0; pointer-events: none; display: flex; }
    .adm-search {
        padding: 6px 12px 6px 30px; border: 1px solid #e5e7eb; border-radius: 5px;
        font-size: 12.5px; font-family: 'Lato', sans-serif; color: #374151;
        background: #fff; width: 220px; outline: none;
        transition: border-color 0.12s, box-shadow 0.12s;
    }
    .adm-search::placeholder { color: #c4c4cc; }
    .adm-search:focus { border-color: #7c3aed; box-shadow: 0 0 0 2px rgba(124,58,237,0.1); }

    /* ── Toolbar filter select (compact) ── */
    /* ── Toolbar filter select (compact) ── */
    .adm-select {
        display: inline-flex;
        padding: 6px 30px 6px 10px;
        border: 1px solid #e5e7eb;
        border-radius: 5px;
        font-size: 12.5px;
        font-family: 'Lato', sans-serif;
        font-weight: 600;
        color: #374151;
        background: #fff;
        outline: none;
        cursor: pointer;
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' viewBox='0 0 10 6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%236b7280'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 9px center;
        background-size: 10px;
        transition: border-color 0.15s, box-shadow 0.15s;
        white-space: nowrap;
        min-width: 110px;
    }
    .adm-select:hover { border-color: #c4b5fd; }
    .adm-select:focus {
        border-color: #7c3aed;
        box-shadow: 0 0 0 2px rgba(124,58,237,0.1);
        outline: none;
    }
    .adm-select option { font-weight: 500; }
    
    /* ── Primary button ── */
    .sa-btn-primary {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 7px 14px; background: linear-gradient(135deg, #9025FB, #4617D3);
        color: #fff; font-size: 13px; font-weight: 700; font-family: 'Lato', sans-serif;
        border: none; border-radius: 6px; cursor: pointer; text-decoration: none;
        box-shadow: 0 2px 8px rgba(109,40,217,0.22);
        transition: opacity 0.15s, transform 0.15s; white-space: nowrap;
    }
    .sa-btn-primary:hover { opacity: 0.88; transform: translateY(-1px); }

    /* ── Table card ── */
    .adm-card {
        background: #fff; border: 1px solid #ebebf0;
        border-radius: 10px; overflow: hidden;
    }

    /* ── Table ── */
    .adm-table-wrap { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; min-width: 560px; }
    thead th {
        padding: 10px 14px; text-align: left;
        font-size: 12px; font-weight: 700; color: #6b7280;
        background: #f9f9fb; border-bottom: 1px solid #ebebf0;
        white-space: nowrap;
    }
    thead th:first-child { border-radius: 8px 0 0 0; }
    thead th:last-child  { border-radius: 0 8px 0 0; }
    tbody tr { border-bottom: 1px solid #f5f5f7; transition: background 0.1s; }
    tbody tr:last-child { border-bottom: none; }
    tbody tr:hover { background: #fafafa; }
    tbody td { padding: 12px 14px; font-size: 13px; color: #374151; vertical-align: middle; }

    /* ── Cell types ── */
    .td-num  { color: #9ca3af; font-size: 12px; width: 40px; }
    .td-name { font-weight: 700; color: #1a1a2e; }
    .td-id   { font-size: 12px; color: #9ca3af; margin-top: 1px; }

    /* ── Badges ── */
    .badge { font-size: 11px; font-weight: 700; padding: 2px 10px; border-radius: 4px; border: 1px solid; display: inline-block; white-space: nowrap; }
    .badge-active   { background: #f0fdf4; color: #16a34a; border-color: #bbf7d0; }
    .badge-inactive { background: #f9fafb; color: #6b7280; border-color: #e5e7eb; }
    .badge-role     { background: #ede9fe; color: #5b21b6; border-color: #ddd6fe; }
    .badge-super    { background: #faf5ff; color: #7c3aed; border-color: #e9d5ff; }

    /* ── Action buttons ── */
    .action-wrap { display: flex; align-items: center; gap: 4px; }
    .action-btn {
        display: inline-flex; align-items: center; justify-content: center;
        width: 28px; height: 28px; border-radius: 5px;
        border: 1px solid #e5e7eb; background: #fff;
        cursor: pointer; color: #6b7280;
        transition: background 0.12s, color 0.12s, border-color 0.12s;
        text-decoration: none;
    }
    .action-btn.edit   { color: #d97706; border-color: #fde68a; background: #fffbeb; }
    .action-btn.edit:hover { background: #fef3c7; color: #92400e; }
    .action-btn.danger { color: #dc2626; border-color: #fecaca; background: #fef2f2; }
    .action-btn.danger:hover { background: #fee2e2; }

    /* ── Empty state ── */
    .adm-empty { text-align: center; padding: 56px 20px; color: #9ca3af; font-size: 13px; }
    .adm-empty svg { margin: 0 auto 12px; display: block; }

    /* ── Pagination ── */
    .adm-pagination {
        display: flex; align-items: center; justify-content: space-between;
        padding: 12px 16px; border-top: 1px solid #f0f0f5;
        font-size: 12.5px; color: #6b7280; flex-wrap: wrap; gap: 8px;
        background: #fff;
    }
    .adm-page-btns { display: flex; align-items: center; gap: 3px; }
    .adm-page-btn {
        padding: 5px 10px; border: 1px solid #e5e7eb; border-radius: 5px;
        font-size: 12.5px; font-family: 'Lato', sans-serif; font-weight: 600;
        color: #374151; background: #fff; text-decoration: none; cursor: pointer;
        transition: background 0.1s, border-color 0.1s;
    }
    .adm-page-btn:hover    { background: #f4f0ff; border-color: #c4b5fd; color: #4f28d9; }
    .adm-page-btn.active   { background: #4f28d9; color: #fff; border-color: #4f28d9; }
    .adm-page-btn[disabled]{ opacity: 0.4; cursor: not-allowed; pointer-events: none; }

    /* ── Modal ── */
    .sa-modal-bg {
        display: none; position: fixed; inset: 0;
        background: rgba(0,0,0,0.35); z-index: 500;
        align-items: center; justify-content: center;
        padding: 16px; backdrop-filter: blur(2px);
    }
    .sa-modal-bg.open { display: flex; }
    .sa-modal {
        background: #fff; border-radius: 12px; width: 100%; max-width: 440px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.14); overflow: hidden;
        animation: saModalIn 0.22s cubic-bezier(0.22,1,0.36,1);
    }
    @keyframes saModalIn { from{opacity:0;transform:scale(0.95) translateY(10px)} to{opacity:1;transform:scale(1) translateY(0)} }
    .sa-modal-head {
        display: flex; align-items: center; justify-content: space-between;
        padding: 16px 20px; border-bottom: 1px solid #f0f0f5;
    }
    .sa-modal-title { font-size: 15px; font-weight: 900; color: #1a1a2e; }
    .sa-modal-close {
        width: 28px; height: 28px; border: none; background: none;
        cursor: pointer; color: #9ca3af; border-radius: 6px;
        display: flex; align-items: center; justify-content: center;
        transition: background 0.12s, color 0.12s;
    }
    .sa-modal-close:hover { background: #f4f0ff; color: #4f28d9; }
    .sa-modal-body { padding: 18px 20px; font-size: 13px; color: #374151; line-height: 1.65; }
    .sa-modal-foot {
        padding: 12px 20px; border-top: 1px solid #f0f0f5;
        display: flex; justify-content: flex-end; gap: 8px;
    }
    .sa-btn-cancel {
        padding: 8px 16px; border: 1px solid #e5e7eb; border-radius: 6px;
        font-size: 13px; font-weight: 600; font-family: 'Lato', sans-serif;
        color: #374151; background: #fff; cursor: pointer;
        transition: border-color 0.12s, color 0.12s;
    }
    .sa-btn-cancel:hover { border-color: #c4b5fd; color: #4f28d9; }
    .sa-btn-danger-sm {
        padding: 8px 16px; background: #dc2626; color: #fff;
        font-size: 13px; font-weight: 700; font-family: 'Lato', sans-serif;
        border: none; border-radius: 6px; cursor: pointer; transition: opacity 0.15s;
    }
    .sa-btn-danger-sm:hover { opacity: 0.88; }

    /* ══════════════════════════════════════════════
       BREAKPOINTS
       ▸ Tablet  768–1023px : narrower search
       ▸ Mobile  < 768px   : stack toolbar, hide some columns
       ▸ XS      < 480px   : modal from bottom
    ══════════════════════════════════════════════ */

    /* ── Tablet ── */
    @media (max-width: 1023px) {
        .adm-search { width: 180px; }
    }

    /* ── Mobile ── */
    @media (max-width: 767px) {
        .adm-toolbar      { flex-direction: column; align-items: stretch; }
        .adm-toolbar-left { flex-wrap: wrap; }
        .adm-toolbar-right { justify-content: flex-end; }
        .adm-search       { width: 100%; }
        .adm-search-wrap  { flex: 1; min-width: 0; }
        .th-hide-mobile, .td-hide-mobile { display: none; }
    }

    /* ── Small mobile ── */
    @media (max-width: 479px) {
        .sa-modal     { border-radius: 12px 12px 0 0; }
        .sa-modal-bg  { align-items: flex-end; padding: 0; }
    }
</style>
@endpush

@section('content')

{{-- ── Flash messages ── --}}
@if(session('success'))
<div class="sa-alert sa-alert-success">
    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="sa-alert sa-alert-error">
    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><path d="M12 8v4m0 4h.01" stroke-width="2.5" stroke-linecap="round"/></svg>
    {{ session('error') }}
</div>
@endif

{{-- ── Toolbar ── --}}
<form method="GET" action="{{ route('superadmin.accounts.index') }}" id="filterForm">
<div class="adm-toolbar">
    <div class="adm-toolbar-left">

        {{-- ── Search ── --}}
        <div class="adm-search-wrap">
            <span class="adm-si">
                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8" stroke-width="2"/><path d="M21 21l-4.35-4.35" stroke-width="2" stroke-linecap="round"/></svg>
            </span>
            <input type="text" name="search" class="adm-search"
                   placeholder="Cari nama atau ID..."
                   value="{{ $search }}" oninput="debounce(this.form)">
        </div>

        {{-- ── Role filter ── --}}
        <select name="role" class="adm-select" onchange="this.form.submit()">
            <option value="">Semua Role</option>
            @foreach($roles as $r)
            <option value="{{ $r->id }}" {{ $roleFilter == $r->id ? 'selected' : '' }}>{{ $r->role_name }}</option>
            @endforeach
        </select>

        {{-- ── Status filter ── --}}
        <select name="status" class="adm-select" onchange="this.form.submit()">
            <option value="">Semua Status</option>
            <option value="active"   {{ $statusFilter === 'active'   ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ $statusFilter === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>

    </div>
    <div class="adm-toolbar-right">
        <a href="{{ route('superadmin.accounts.create') }}" class="sa-btn-primary">
            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 5v14M5 12h14"/></svg>
            Tambah Akun
        </a>
    </div>
</div>
</form>

{{-- ── Table card ── --}}
<div class="adm-card">
    <div class="adm-table-wrap">
        <table>
            <thead>
                <tr>
                    <th class="th-hide-mobile">#</th>
                    <th>Nama / Identifier</th>
                    <th>Role</th>
                    <th class="th-hide-mobile">Status</th>
                    <th class="th-hide-mobile">Bergabung</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $i => $user)
                <tr>
                    <td class="td-num td-hide-mobile">{{ $users->firstItem() + $i }}</td>
                    <td>
                        <div class="td-name">{{ $user->name }}</div>
                        <div class="td-id">{{ $user->identifier }}</div>
                    </td>
                    <td>
                        <span class="badge {{ $user->role_id == 1 ? 'badge-super' : 'badge-role' }}">
                            {{ $user->role->role_name ?? '–' }}
                        </span>
                    </td>
                    <td class="td-hide-mobile">
                        <span class="badge {{ $user->status === 'active' ? 'badge-active' : 'badge-inactive' }}">
                            {{ ucfirst($user->status) }}
                        </span>
                    </td>
                    <td class="td-hide-mobile" style="font-size:12px;color:#9ca3af;">
                        {{ \Carbon\Carbon::parse($user->created_at)->format('d M Y') }}
                    </td>
                    <td>
                        <div class="action-wrap">
                            <a href="{{ route('superadmin.accounts.edit', $user->id) }}" class="action-btn edit" title="Edit">
                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            @if($user->id !== auth()->id())
                            <button type="button" class="action-btn danger" title="Hapus"
                                    onclick="confirmDelete({{ $user->id }}, '{{ addslashes($user->name) }}')">
                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline stroke-linecap="round" stroke-linejoin="round" stroke-width="2" points="3 6 5 6 21 6"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6m5 0V4a1 1 0 011-1h2a1 1 0 011 1v2"/></svg>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="adm-empty">
                            <svg width="36" height="36" fill="none" stroke="#d1d5db" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4" stroke-width="1.4"/></svg>
                            Tidak ada akun ditemukan.
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ── Pagination ── --}}
    @if($users->hasPages())
    <div class="adm-pagination">
        <span>Menampilkan {{ $users->firstItem() }}–{{ $users->lastItem() }} dari {{ $users->total() }} akun</span>
        <div class="adm-page-btns">
            @if($users->onFirstPage())
                <span class="adm-page-btn" disabled>‹</span>
            @else
                <a href="{{ $users->previousPageUrl() }}" class="adm-page-btn">‹</a>
            @endif
            @foreach($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                <a href="{{ $url }}" class="adm-page-btn {{ $users->currentPage() == $page ? 'active' : '' }}">{{ $page }}</a>
            @endforeach
            @if($users->hasMorePages())
                <a href="{{ $users->nextPageUrl() }}" class="adm-page-btn">›</a>
            @else
                <span class="adm-page-btn" disabled>›</span>
            @endif
        </div>
    </div>
    @endif
</div>

{{-- ── Delete confirmation modal ── --}}
<div class="sa-modal-bg" id="deleteModal">
    <div class="sa-modal">
        <div class="sa-modal-head">
            <span class="sa-modal-title">Hapus Akun</span>
            <button class="sa-modal-close" onclick="closeModal('deleteModal')">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="sa-modal-body">
            Kamu yakin ingin menghapus akun <strong id="deleteUserName"></strong>?
            Tindakan ini tidak bisa dibatalkan.
        </div>
        <div class="sa-modal-foot">
            <button class="sa-btn-cancel" onclick="closeModal('deleteModal')">Batal</button>
            <form id="deleteForm" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="sa-btn-danger-sm">Ya, Hapus</button>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // ── Debounced search ──
    let t;
    function debounce(form) {
        clearTimeout(t);
        t = setTimeout(() => form.submit(), 500);
    }

    // ── Show delete confirmation modal ──
    function confirmDelete(id, name) {
        document.getElementById('deleteUserName').textContent = name;
        document.getElementById('deleteForm').action = `/superadmin/accounts/${id}`;
        document.getElementById('deleteModal').classList.add('open');
    }

    // ── Close any modal ──
    function closeModal(id) {
        document.getElementById(id).classList.remove('open');
    }

    // ── Close modal on backdrop click ──
    document.querySelectorAll('.sa-modal-bg').forEach(bg => {
        bg.addEventListener('click', e => { if (e.target === bg) bg.classList.remove('open'); });
    });
</script>
@endpush