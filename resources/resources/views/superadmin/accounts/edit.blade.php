{{-- resources/views/superadmin/accounts/edit.blade.php --}}
@extends('superadmin.layouts.app')

@section('title', 'Edit Akun – SINTEM Superadmin')
@section('header', 'Edit Akun')
@section('subheader', 'Perbarui informasi akun dan role pengguna.')

@push('styles')
<style>
    .sa-form-card {
        background:#fff; border:1px solid #ebebf0; border-radius:10px;
        padding:28px 32px 32px; max-width:560px; margin:0 auto;
    }
    .sa-form-title { font-size:14px; font-weight:700; color:#1a1a2e; margin-bottom:20px; }
    .sa-field       { margin-bottom:16px; }
    .sa-label       { display:block; font-size:12.5px; font-weight:700; color:#374151; margin-bottom:5px; }
    .sa-label-hint  { font-weight:400; color:#9ca3af; font-size:11.5px; }
    /* ── Text inputs ── */
    .sa-input {
        width: 100%; padding: 9px 12px; border: 1px solid #e5e7eb; border-radius: 6px;
        font-size: 13px; font-family: 'Lato', sans-serif; color: #1a1a2e;
        background: #fff; outline: none; transition: border-color 0.15s, box-shadow 0.15s;
    }
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
    .sa-input::placeholder { color:#c4c4cc; }
    .sa-input:focus { border-color: #7c3aed; box-shadow: 0 0 0 2px rgba(124,58,237,0.1); }
    .sa-input.is-error { border-color:#f87171; }
    .sa-field-error { font-size:11.5px; color:#dc2626; margin-top:4px; }
    .sa-row { display:flex; gap:14px; }
    .sa-row .sa-field { flex:1; }
    .sa-hint { font-size:11.5px; color:#9ca3af; margin-top:4px; }
    .sa-form-actions {
        display:flex; justify-content:flex-end; gap:8px;
        margin-top:24px; padding-top:20px; border-top:1px solid #f0f0f5; flex-wrap:wrap;
    }
    .sa-btn-back {
        padding:9px 18px; border:1px solid #e5e7eb; border-radius:6px;
        font-size:13px; font-weight:600; font-family:'Lato',sans-serif;
        color:#374151; background:#fff; cursor:pointer; text-decoration:none;
        display:inline-flex; align-items:center; transition:border-color 0.12s;
    }
    .sa-btn-back:hover { border-color:#c4b5fd; color:#4f28d9; }
    .sa-btn-save {
        padding:9px 22px; background:linear-gradient(135deg,#9025FB,#4617D3);
        color:#fff; font-size:13px; font-weight:700; font-family:'Lato',sans-serif;
        border:none; border-radius:6px; cursor:pointer;
        box-shadow:0 2px 8px rgba(109,40,217,0.2); transition:opacity 0.15s;
    }
    .sa-btn-save:hover { opacity:0.88; }
    .sa-section-divider { height:1px; background:#f0f0f5; margin:20px 0; }

    /* ── Mobile ── */
    @media (max-width: 767px) {
        .sa-form-card    { padding:18px 16px 20px; }
        .sa-row          { flex-direction:column; gap:0; }
        .sa-form-actions { justify-content:stretch; }
        .sa-form-actions > * { flex:1; text-align:center; justify-content:center; }
    }
</style>
@endpush

@section('content')
<div class="sa-form-card">
    <div class="sa-form-title">Edit Akun: {{ $user->name }}</div>

    <form method="POST" action="{{ route('superadmin.accounts.update', $user->id) }}">
        @csrf
        @method('PUT')

        {{-- ── Name ── --}}
        <div class="sa-field">
            <label class="sa-label" for="name">Nama Lengkap</label>
            <input type="text" id="name" name="name" class="sa-input {{ $errors->has('name') ? 'is-error' : '' }}"
                   value="{{ old('name', $user->name) }}" required>
            @error('name') <p class="sa-field-error">{{ $message }}</p> @enderror
        </div>

        {{-- ── Identifier + Role ── --}}
        <div class="sa-row">
            <div class="sa-field">
                <label class="sa-label" for="identifier">Identifier / NIS</label>
                <input type="text" id="identifier" name="identifier"
                       class="sa-input {{ $errors->has('identifier') ? 'is-error' : '' }}"
                       value="{{ old('identifier', $user->identifier) }}" required>
                @error('identifier') <p class="sa-field-error">{{ $message }}</p> @enderror
            </div>
            <div class="sa-field">
                <label class="sa-label" for="role_id">Role</label>
                <select id="role_id" name="role_id" class="sa-select-full" required>
                    @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                        {{ $role->role_name }}
                    </option>
                    @endforeach
                </select>
                @error('role_id') <p class="sa-field-error">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- ── Status ── --}}
        <div class="sa-field">
            <label class="sa-label" for="status">Status</label>
            <select id="status" name="status" class="sa-select-full" required>
                <option value="active"   {{ old('status', $user->status) === 'active'   ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status', $user->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        <div class="sa-section-divider"></div>
        <p style="font-size:12.5px;color:#6b7280;margin-bottom:14px;">Kosongkan kolom password jika tidak ingin mengubahnya.</p>

        {{-- ── Password (optional) ── --}}
        <div class="sa-row">
            <div class="sa-field">
                <label class="sa-label" for="password">Password Baru <span class="sa-label-hint">(opsional)</span></label>
                <input type="password" id="password" name="password"
                       class="sa-input {{ $errors->has('password') ? 'is-error' : '' }}"
                       placeholder="Min. 6 karakter">
                @error('password') <p class="sa-field-error">{{ $message }}</p> @enderror
            </div>
            <div class="sa-field">
                <label class="sa-label" for="password_confirmation">Konfirmasi Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation"
                       class="sa-input" placeholder="Ulangi password baru">
            </div>
        </div>

        <div class="sa-form-actions">
            <a href="{{ route('superadmin.accounts.index') }}" class="sa-btn-back">Batal</a>
            <button type="submit" class="sa-btn-save">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection