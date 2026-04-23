{{-- resources/views/superadmin/categories/index.blade.php --}}
@extends('superadmin.layouts.app')

@section('title', 'Kategori Laporan – SINTEM Superadmin')
@section('header', 'Kategori Laporan')
@section('subheader', 'Tetapkan role yang bertanggung jawab atas setiap kategori laporan anonim.')

@push('styles')
<style>
    .sa-alert { display:flex; align-items:center; gap:9px; padding:11px 14px; border-radius:7px; font-size:13px; margin-bottom:16px; }
    .sa-alert-success { background:#f0fdf4; border:1px solid #bbf7d0; color:#15803d; }
    .sa-alert-error   { background:#fef2f2; border:1px solid #fecaca; color:#dc2626; }

    /* ── Two-panel layout ── */
    .sa-two-panel { display:flex; gap:18px; align-items:flex-start; }
    .sa-panel-main { flex:1; min-width:0; }
    .sa-panel-side { width:320px; flex-shrink:0; }

    /* ── Card ── */
    .sa-card { background:#fff; border:1px solid #ebebf0; border-radius:10px; overflow:hidden; }
    .sa-card-head { padding:14px 18px; border-bottom:1px solid #f0f0f5; display:flex; align-items:center; justify-content:space-between; }
    .sa-card-title { font-size:13px; font-weight:700; color:#1a1a2e; }
    .sa-card-body  { padding:18px; }

    /* ── Category grid (cards not table) ── */
    .cat-grid { display:flex; flex-direction:column; gap:0; }
    .cat-row {
        display:flex; align-items:center; gap:12px;
        padding:14px 18px; border-bottom:1px solid #f5f5f7;
        transition:background 0.1s;
    }
    .cat-row:last-child { border-bottom:none; }
    .cat-row:hover { background:#fafafa; }
    .cat-icon { width:36px; height:36px; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0; background:#ede9fe; }
    .cat-info { flex:1; min-width:0; }
    .cat-name { font-size:13px; font-weight:700; color:#1a1a2e; }
    .cat-desc { font-size:12px; color:#9ca3af; margin-top:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .cat-role { display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:5px; background:#ede9fe; border:1px solid #ddd6fe; font-size:11.5px; font-weight:700; color:#5b21b6; white-space:nowrap; }
    .sa-action-wrap { display:flex; align-items:center; gap:4px; flex-shrink:0; }
    .sa-action-btn  { display:inline-flex; align-items:center; justify-content:center; width:28px; height:28px; border-radius:6px; border:1px solid #e5e7eb; background:#fff; cursor:pointer; color:#6b7280; transition:background 0.12s; text-decoration:none; }
    .sa-action-btn.edit   { color:#d97706; border-color:#fde68a; background:#fffbeb; }
    .sa-action-btn.edit:hover { background:#fef3c7; }
    .sa-action-btn.danger { color:#dc2626; border-color:#fecaca; background:#fef2f2; }
    .sa-action-btn.danger:hover { background:#fee2e2; }

    /* ── Empty ── */
    .sa-empty { text-align:center; padding:40px 20px; color:#9ca3af; font-size:13px; }

    /* ── Form fields ── */
    .sa-field       { margin-bottom:14px; }
    .sa-field:last-child { margin-bottom:0; }
    .sa-label       { display:block; font-size:12.5px; font-weight:700; color:#374151; margin-bottom:5px; }
    /* ── Text inputs & textarea ── */
    .sa-input, .sa-textarea {
        width: 100%; padding: 9px 12px; border: 1px solid #e5e7eb; border-radius: 6px;
        font-size: 13px; font-family: 'Lato', sans-serif; color: #1a1a2e;
        background: #fff; outline: none; transition: border-color 0.15s, box-shadow 0.15s;
    }
    .sa-textarea { resize: vertical; min-height: 64px; }
    .sa-input::placeholder, .sa-textarea::placeholder { color: #c4c4cc; }
    .sa-input:focus, .sa-textarea:focus { border-color: #7c3aed; box-shadow: 0 0 0 2px rgba(124,58,237,0.1); }
    .sa-input.is-error { border-color: #f87171; }

    /* ── Full-width form select (cards/modals) ── */
    .sa-select-full {
        width: 100%;
        padding: 9px 34px 9px 12px;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        font-size: 13px;
        font-family: 'Lato', sans-serif;
        font-weight: 500;
        color: #1a1a2e;
        background: #fff;
        outline: none;
        cursor: pointer;
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='11' height='7' viewBox='0 0 11 7'%3E%3Cpath d='M0.5 0.5L5.5 6.5L10.5 0.5' stroke='%236b7280' stroke-width='1.5' fill='none' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 11px center;
        background-size: 11px;
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    .sa-select-full:hover { border-color: #c4b5fd; }
    .sa-select-full:focus {
        border-color: #7c3aed;
        box-shadow: 0 0 0 2px rgba(124,58,237,0.1);
        outline: none;
    }
    .sa-select-full.is-error { border-color: #f87171; }
    .sa-field-error { font-size:11.5px; color:#dc2626; margin-top:4px; }
    .sa-btn-save { width:100%; padding:9px; background:linear-gradient(135deg,#9025FB,#4617D3); color:#fff; font-size:13px; font-weight:700; font-family:'Lato',sans-serif; border:none; border-radius:6px; cursor:pointer; margin-top:14px; box-shadow:0 2px 8px rgba(109,40,217,0.2); transition:opacity 0.15s; }
    .sa-btn-save:hover { opacity:0.88; }

    /* ── Modals ── */
    .sa-modal-bg { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.35); z-index:500; align-items:center; justify-content:center; padding:16px; backdrop-filter:blur(3px); }
    .sa-modal-bg.open { display:flex; }
    .sa-modal { background:#fff; border-radius:12px; width:100%; max-width:440px; box-shadow:0 20px 60px rgba(0,0,0,0.18); overflow:hidden; animation:saModalIn 0.22s cubic-bezier(0.22,1,0.36,1); }
    @keyframes saModalIn { from{opacity:0;transform:scale(0.94) translateY(12px)} to{opacity:1;transform:scale(1) translateY(0)} }
    .sa-modal-head { display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid #f0f0f5; }
    .sa-modal-title { font-size:14px; font-weight:900; color:#1a1a2e; }
    .sa-modal-close { width:26px; height:26px; border:none; background:none; cursor:pointer; color:#9ca3af; border-radius:6px; display:flex; align-items:center; justify-content:center; transition:background 0.12s; }
    .sa-modal-close:hover { background:#f4f0ff; }
    .sa-modal-body { padding:18px 20px; }
    .sa-modal-foot { padding:12px 20px; border-top:1px solid #f0f0f5; display:flex; justify-content:flex-end; gap:8px; flex-wrap:wrap; }
    .sa-btn-cancel { padding:8px 16px; border:1px solid #e5e7eb; border-radius:6px; font-size:13px; font-weight:600; font-family:'Lato',sans-serif; color:#374151; background:#fff; cursor:pointer; }
    .sa-btn-cancel:hover { border-color:#c4b5fd; color:#4f28d9; }
    .sa-btn-save-sm { padding:8px 18px; background:linear-gradient(135deg,#9025FB,#4617D3); color:#fff; font-size:13px; font-weight:700; font-family:'Lato',sans-serif; border:none; border-radius:6px; cursor:pointer; transition:opacity 0.15s; }
    .sa-btn-save-sm:hover { opacity:0.88; }
    .sa-btn-danger-sm { padding:8px 16px; background:#dc2626; color:#fff; font-size:13px; font-weight:700; font-family:'Lato',sans-serif; border:none; border-radius:6px; cursor:pointer; transition:opacity 0.15s; }
    .sa-btn-danger-sm:hover { opacity:0.88; }

    /* ══════════════════════════════════════════════
       BREAKPOINTS
       ▸ Tablet  768–1023px : stack panels
       ▸ Mobile  < 768px   : compact cat rows
       ▸ XS      < 480px   : modal sheet from bottom
    ══════════════════════════════════════════════ */

    /* ── Tablet ── */
    @media (max-width: 1023px) {
        .sa-two-panel  { flex-direction:column; }
        .sa-panel-side { width:100%; }
    }

    /* ── Mobile ── */
    @media (max-width: 767px) {
        .cat-row    { flex-wrap:wrap; gap:8px; }
        .cat-role   { font-size:11px; }
    }

    /* ── Small mobile ── */
    @media (max-width: 479px) {
        .sa-modal { border-radius:12px 12px 0 0; max-width:100%; }
        .sa-modal-bg { align-items:flex-end; padding:0; }
    }
</style>
@endpush

@section('content')

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

<div class="sa-two-panel">

    {{-- ── Left: category list ── --}}
    <div class="sa-panel-main">
        <div class="sa-card">
            <div class="sa-card-head">
                <span class="sa-card-title">Kategori Laporan ({{ $categories->count() }})</span>
            </div>
            <div class="cat-grid">
                @forelse($categories as $cat)
                <div class="cat-row">
                    <div class="cat-icon">
                        <svg width="15" height="15" fill="none" stroke="#5b21b6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/></svg>
                    </div>
                    <div class="cat-info">
                        <div class="cat-name">{{ $cat->category_name }}</div>
                        <div class="cat-desc">{{ $cat->description ?: '–' }}</div>
                    </div>
                    {{-- ── Role assignment badge ── --}}
                    <span class="cat-role">
                        <svg width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        {{ $cat->role_name }}
                    </span>
                    <div class="sa-action-wrap">
                        <button type="button" class="sa-action-btn edit" title="Edit"
                                onclick="openEditCat({{ $cat->id }}, '{{ addslashes($cat->category_name) }}', '{{ addslashes($cat->description ?? '') }}', {{ $cat->responsible_role_id }})">
                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>
                        <button type="button" class="sa-action-btn danger" title="Hapus"
                                onclick="openDeleteCat({{ $cat->id }}, '{{ addslashes($cat->category_name) }}')">
                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline stroke-linecap="round" stroke-linejoin="round" stroke-width="2" points="3 6 5 6 21 6"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6m5 0V4a1 1 0 011-1h2a1 1 0 011 1v2"/></svg>
                        </button>
                    </div>
                </div>
                @empty
                <div class="sa-empty">Belum ada kategori laporan.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ── Right: add category form ── --}}
    <div class="sa-panel-side">
        <div class="sa-card">
            <div class="sa-card-head">
                <span class="sa-card-title">Tambah Kategori Baru</span>
            </div>
            <div class="sa-card-body">
                <form method="POST" action="{{ route('superadmin.categories.store') }}">
                    @csrf
                    <div class="sa-field">
                        <label class="sa-label" for="category_name">Nama Kategori</label>
                        <input type="text" id="category_name" name="category_name"
                               class="sa-input {{ $errors->has('category_name') ? 'is-error' : '' }}"
                               placeholder="Contoh: Perundungan" value="{{ old('category_name') }}" required>
                        @error('category_name') <p class="sa-field-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="sa-field">
                        <label class="sa-label" for="description">Deskripsi <span style="font-weight:400;color:#9ca3af;">(opsional)</span></label>
                        <textarea id="description" name="description" class="sa-textarea"
                                  placeholder="Jenis laporan apa yang masuk ke kategori ini...">{{ old('description') }}</textarea>
                    </div>
                    <div class="sa-field">
                        <label class="sa-label" for="responsible_role_id">Role Penanggung Jawab</label>
                        <select id="responsible_role_id" name="responsible_role_id" class="sa-select-full {{ $errors->has('responsible_role_id') ? 'is-error' : '' }}" required>
                            <option value="">Pilih role...</option>
                            @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('responsible_role_id') == $role->id ? 'selected' : '' }}>
                                {{ $role->role_name }}
                            </option>
                            @endforeach
                        </select>
                        @error('responsible_role_id') <p class="sa-field-error">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit" class="sa-btn-save">Tambah Kategori</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ── Edit category modal ── --}}
<div class="sa-modal-bg" id="editCatModal">
    <div class="sa-modal">
        <div class="sa-modal-head">
            <span class="sa-modal-title">Edit Kategori</span>
            <button class="sa-modal-close" onclick="closeModal('editCatModal')">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="editCatForm" method="POST">
            @csrf @method('PUT')
            <div class="sa-modal-body">
                <div class="sa-field">
                    <label class="sa-label">Nama Kategori</label>
                    <input type="text" name="category_name" id="editCatName" class="sa-input" required>
                </div>
                <div class="sa-field">
                    <label class="sa-label">Deskripsi</label>
                    <textarea name="description" id="editCatDesc" class="sa-textarea"></textarea>
                </div>
                <div class="sa-field">
                    <label class="sa-label">Role Penanggung Jawab</label>
                    <select name="responsible_role_id" id="editCatRole" class="sa-select-full" required>
                        @foreach($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->role_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="sa-modal-foot">
                <button type="button" class="sa-btn-cancel" onclick="closeModal('editCatModal')">Batal</button>
                <button type="submit" class="sa-btn-save-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- ── Delete category confirmation modal ── --}}
<div class="sa-modal-bg" id="deleteCatModal">
    <div class="sa-modal">
        <div class="sa-modal-head">
            <span class="sa-modal-title">Hapus Kategori</span>
            <button class="sa-modal-close" onclick="closeModal('deleteCatModal')">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="sa-modal-body">
            <p style="font-size:13px;color:#374151;line-height:1.6;">Hapus kategori <strong id="deleteCatName"></strong>? Kategori yang masih memiliki laporan tidak bisa dihapus.</p>
        </div>
        <div class="sa-modal-foot">
            <button class="sa-btn-cancel" onclick="closeModal('deleteCatModal')">Batal</button>
            <form id="deleteCatForm" method="POST">
                @csrf @method('DELETE')
                <button type="submit" class="sa-btn-danger-sm">Ya, Hapus</button>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // ── Open edit category modal pre-filled ──
    function openEditCat(id, name, desc, roleId) {
        document.getElementById('editCatForm').action = `/superadmin/categories/${id}`;
        document.getElementById('editCatName').value = name;
        document.getElementById('editCatDesc').value = desc;
        document.getElementById('editCatRole').value = roleId;
        document.getElementById('editCatModal').classList.add('open');
    }

    // ── Open delete confirmation modal ──
    function openDeleteCat(id, name) {
        document.getElementById('deleteCatName').textContent = name;
        document.getElementById('deleteCatForm').action = `/superadmin/categories/${id}`;
        document.getElementById('deleteCatModal').classList.add('open');
    }

    // ── Close any modal ──
    function closeModal(id) {
        document.getElementById(id).classList.remove('open');
    }

    // ── Close on backdrop click ──
    document.querySelectorAll('.sa-modal-bg').forEach(bg => {
        bg.addEventListener('click', e => { if (e.target === bg) bg.classList.remove('open'); });
    });
</script>
@endpush