{{-- resources/views/superadmin/roles/index.blade.php --}}
@extends('superadmin.layouts.app')

@section('title', 'Manajemen Role – SINTEM Superadmin')
@section('header', 'Manajemen Role')
@section('subheader', 'Kelola role dan tetapkan ke akun pengguna.')

@push('styles')
<style>
    .sa-alert { display:flex; align-items:center; gap:9px; padding:11px 14px; border-radius:7px; font-size:13px; margin-bottom:16px; }
    .sa-alert-success { background:#f0fdf4; border:1px solid #bbf7d0; color:#15803d; }
    .sa-alert-error   { background:#fef2f2; border:1px solid #fecaca; color:#dc2626; }

    /* ── Two-panel layout: table left, add form right ── */
    .sa-two-panel { display:flex; gap:18px; align-items:flex-start; }
    .sa-panel-main { flex:1; min-width:0; }
    .sa-panel-side { width:300px; flex-shrink:0; }

    /* ── Card ── */
    .sa-card { background:#fff; border:1px solid #ebebf0; border-radius:10px; overflow:hidden; }
    .sa-card-head { padding:14px 18px; border-bottom:1px solid #f0f0f5; display:flex; align-items:center; justify-content:space-between; }
    .sa-card-title { font-size:13px; font-weight:700; color:#1a1a2e; }
    .sa-card-body  { padding:18px; }

    /* ── Table ── */
    .sa-table-wrap { overflow-x:auto; }
    .sa-table { width:100%; border-collapse:collapse; min-width:400px; }
    .sa-table thead th { padding:10px 14px; text-align:left; font-size:11.5px; font-weight:700; color:#6b7280; background:#f9f9fb; border-bottom:1px solid #ebebf0; }
    .sa-table tbody tr { border-bottom:1px solid #f5f5f7; transition:background 0.1s; }
    .sa-table tbody tr:last-child { border-bottom:none; }
    .sa-table tbody tr:hover { background:#fafafa; }
    .sa-table tbody td { padding:12px 14px; font-size:13px; color:#374151; vertical-align:middle; }
    .td-name  { font-weight:700; color:#1a1a2e; }
    .td-desc  { font-size:12px; color:#9ca3af; margin-top:2px; }
    .badge-count { display:inline-block; font-size:11px; font-weight:700; padding:2px 8px; border-radius:12px; background:#ede9fe; color:#5b21b6; }
    .badge-sys   { font-size:10px; font-weight:700; padding:1px 7px; border-radius:4px; background:rgba(144,37,251,0.12); color:#9025FB; border:1px solid rgba(144,37,251,0.25); }

    /* ── Action buttons ── */
    .sa-action-wrap { display:flex; align-items:center; gap:4px; }
    .sa-action-btn  { display:inline-flex; align-items:center; justify-content:center; width:28px; height:28px; border-radius:6px; border:1px solid #e5e7eb; background:#fff; cursor:pointer; color:#6b7280; transition:background 0.12s,color 0.12s; text-decoration:none; }
    .sa-action-btn.edit   { color:#d97706; border-color:#fde68a; background:#fffbeb; }
    .sa-action-btn.edit:hover { background:#fef3c7; }
    .sa-action-btn.danger { color:#dc2626; border-color:#fecaca; background:#fef2f2; }
    .sa-action-btn.danger:hover { background:#fee2e2; }

    /* ── Empty ── */
    .sa-empty { text-align:center; padding:36px 20px; color:#9ca3af; font-size:13px; }

    /* ── Form fields in card ── */
    .sa-field       { margin-bottom:14px; }
    .sa-field:last-child { margin-bottom:0; }
    .sa-label       { display:block; font-size:12.5px; font-weight:700; color:#374151; margin-bottom:5px; }
    .sa-input, .sa-textarea {
        width:100%; padding:8px 12px; border:1px solid #e5e7eb; border-radius:6px;
        font-size:13px; font-family:'Lato',sans-serif; color:#1a1a2e;
        background:#fff; outline:none; transition:border-color 0.12s, box-shadow 0.12s;
    }
    .sa-textarea { resize:vertical; min-height:72px; }
    .sa-input::placeholder, .sa-textarea::placeholder { color:#c4c4cc; }
    .sa-input:focus, .sa-textarea:focus { border-color:#7c3aed; box-shadow:0 0 0 2px rgba(124,58,237,0.1); }
    .sa-input.is-error { border-color:#f87171; }
    .sa-field-error { font-size:11.5px; color:#dc2626; margin-top:4px; }
    .sa-btn-save {
        width:100%; padding:9px; background:linear-gradient(135deg,#9025FB,#4617D3);
        color:#fff; font-size:13px; font-weight:700; font-family:'Lato',sans-serif;
        border:none; border-radius:6px; cursor:pointer; margin-top:14px;
        box-shadow:0 2px 8px rgba(109,40,217,0.2); transition:opacity 0.15s;
    }
    .sa-btn-save:hover { opacity:0.88; }

    /* ── Edit modal ── */
    .sa-modal-bg { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.35); z-index:500; align-items:center; justify-content:center; padding:16px; backdrop-filter:blur(3px); }
    .sa-modal-bg.open { display:flex; }
    .sa-modal { background:#fff; border-radius:12px; width:100%; max-width:420px; box-shadow:0 20px 60px rgba(0,0,0,0.18); overflow:hidden; animation:saModalIn 0.22s cubic-bezier(0.22,1,0.36,1); }
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
       ▸ Tablet  768–1023px : stack panels vertically
       ▸ Mobile  < 768px   : full-width
    ══════════════════════════════════════════════ */

    /* ── Tablet ── */
    @media (max-width: 1023px) {
        .sa-two-panel  { flex-direction:column; }
        .sa-panel-side { width:100%; }
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

    {{-- ── Left: role table ── --}}
    <div class="sa-panel-main">
        <div class="sa-card">
            <div class="sa-card-head">
                <span class="sa-card-title">Semua Role ({{ $roles->count() }})</span>
            </div>
            <div class="sa-table-wrap">
                <table class="sa-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Role</th>
                            <th>Deskripsi</th>
                            <th>Pengguna</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $i => $role)
                        <tr>
                            <td style="color:#9ca3af;font-size:12px;width:32px;">{{ $i + 1 }}</td>
                            <td>
                                <div class="td-name">{{ $role->role_name }}</div>
                                @if($role->id === 1)<span class="badge-sys">SYSTEM</span>@endif
                            </td>
                            <td style="font-size:12.5px;color:#6b7280;max-width:220px;">{{ $role->description ?: '–' }}</td>
                            <td><span class="badge-count">{{ $role->users_count }}</span></td>
                            <td>
                                <div class="sa-action-wrap">
                                    <button type="button" class="sa-action-btn edit" title="Edit"
                                            onclick="openEditRole({{ $role->id }}, '{{ addslashes($role->role_name) }}', '{{ addslashes($role->description ?? '') }}')">
                                        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    @if($role->users_count == 0 && $role->id !== 1)
                                    <button type="button" class="sa-action-btn danger" title="Hapus"
                                            onclick="openDeleteRole({{ $role->id }}, '{{ addslashes($role->role_name) }}')">
                                        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline stroke-linecap="round" stroke-linejoin="round" stroke-width="2" points="3 6 5 6 21 6"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6m5 0V4a1 1 0 011-1h2a1 1 0 011 1v2"/></svg>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5"><div class="sa-empty">Belum ada role.</div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ── Right: add role form ── --}}
    <div class="sa-panel-side">
        <div class="sa-card">
            <div class="sa-card-head">
                <span class="sa-card-title">Tambah Role Baru</span>
            </div>
            <div class="sa-card-body">
                <form method="POST" action="{{ route('superadmin.roles.store') }}">
                    @csrf
                    <div class="sa-field">
                        <label class="sa-label" for="role_name">Nama Role</label>
                        <input type="text" id="role_name" name="role_name"
                               class="sa-input {{ $errors->has('role_name') ? 'is-error' : '' }}"
                               placeholder="Contoh: Wali Kelas" value="{{ old('role_name') }}" required>
                        @error('role_name') <p class="sa-field-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="sa-field">
                        <label class="sa-label" for="description">Deskripsi <span style="font-weight:400;color:#9ca3af;">(opsional)</span></label>
                        <textarea id="description" name="description" class="sa-textarea"
                                  placeholder="Jelaskan tanggung jawab role ini...">{{ old('description') }}</textarea>
                    </div>
                    <button type="submit" class="sa-btn-save">Tambah Role</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ── Edit role modal ── --}}
<div class="sa-modal-bg" id="editRoleModal">
    <div class="sa-modal">
        <div class="sa-modal-head">
            <span class="sa-modal-title">Edit Role</span>
            <button class="sa-modal-close" onclick="closeModal('editRoleModal')">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="editRoleForm" method="POST">
            @csrf @method('PUT')
            <div class="sa-modal-body">
                <div class="sa-field">
                    <label class="sa-label">Nama Role</label>
                    <input type="text" name="role_name" id="editRoleName" class="sa-input" required>
                </div>
                <div class="sa-field">
                    <label class="sa-label">Deskripsi</label>
                    <textarea name="description" id="editRoleDesc" class="sa-textarea"></textarea>
                </div>
            </div>
            <div class="sa-modal-foot">
                <button type="button" class="sa-btn-cancel" onclick="closeModal('editRoleModal')">Batal</button>
                <button type="submit" class="sa-btn-save-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- ── Delete role confirmation modal ── --}}
<div class="sa-modal-bg" id="deleteRoleModal">
    <div class="sa-modal">
        <div class="sa-modal-head">
            <span class="sa-modal-title">Hapus Role</span>
            <button class="sa-modal-close" onclick="closeModal('deleteRoleModal')">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="sa-modal-body">
            <p style="font-size:13px;color:#374151;line-height:1.6;">Hapus role <strong id="deleteRoleName"></strong>? Tindakan ini tidak bisa dibatalkan.</p>
        </div>
        <div class="sa-modal-foot">
            <button class="sa-btn-cancel" onclick="closeModal('deleteRoleModal')">Batal</button>
            <form id="deleteRoleForm" method="POST">
                @csrf @method('DELETE')
                <button type="submit" class="sa-btn-danger-sm">Hapus</button>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // ── Open edit modal pre-filled ──
    function openEditRole(id, name, desc) {
        document.getElementById('editRoleForm').action = `/superadmin/roles/${id}`;
        document.getElementById('editRoleName').value = name;
        document.getElementById('editRoleDesc').value = desc;
        document.getElementById('editRoleModal').classList.add('open');
    }

    // ── Open delete confirmation modal ──
    function openDeleteRole(id, name) {
        document.getElementById('deleteRoleName').textContent = name;
        document.getElementById('deleteRoleForm').action = `/superadmin/roles/${id}`;
        document.getElementById('deleteRoleModal').classList.add('open');
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