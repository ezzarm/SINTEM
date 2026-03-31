{{-- resources/views/profile/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Profil – SINTEM')
@section('topbar')
    @include('components.topbar')
@endsection

@section('header', 'Profil Saya')
@section('subheader', 'Informasi akun dan pengaturan keamanan')

@push('styles')
<style>
    .profile-wrap {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    /* ── Card ── */
    .pf-card {
        background: #fff;
        border: 1px solid #ebebf0;
        border-radius: 10px;
        overflow: hidden;
    }
    .pf-card-head {
        padding: 14px 20px 13px;
        border-bottom: 1px solid #f0f0f5;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .pf-card-title {
        font-size: 13px;
        font-weight: 700;
        color: #1a1a2e;
        letter-spacing: 0.01em;
    }
    .pf-card-body { padding: 20px; }

    /* ── Avatar + info ── */
    .pf-identity {
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .pf-avatar-lg {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: linear-gradient(135deg, #9025FB, #4617D3);
        color: #fff;
        font-size: 18px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        letter-spacing: -0.5px;
    }
    .pf-name {
        font-size: 16px;
        font-weight: 700;
        color: #1a1a2e;
        line-height: 1.3;
    }
    .pf-role {
        font-size: 12px;
        color: #9ca3af;
        margin-top: 2px;
    }
    .pf-role span {
        display: inline-block;
        background: #ede9fe;
        color: #5b21b6;
        font-size: 11px;
        font-weight: 700;
        padding: 1px 8px;
        border-radius: 4px;
        margin-top: 4px;
    }

    /* ── Info rows ── */
    .pf-rows { display: flex; flex-direction: column; gap: 0; }
    .pf-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 11px 0;
        border-bottom: 1px solid #f7f7f9;
    }
    .pf-row:last-child { border-bottom: none; padding-bottom: 0; }
    .pf-row:first-child { padding-top: 0; }
    .pf-row-label { font-size: 12.5px; color: #9ca3af; font-weight: 500; }
    .pf-row-value { font-size: 13px; font-weight: 600; color: #1a1a2e; }

    /* ── Form fields ── */
    .pf-field { margin-bottom: 14px; }
    .pf-field:last-child { margin-bottom: 0; }
    .pf-label {
        display: block;
        font-size: 12.5px;
        font-weight: 700;
        color: #374151;
        margin-bottom: 6px;
    }
    .pf-input-wrap { position: relative; }
    .pf-input {
        width: 100%;
        padding: 9px 12px;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        font-size: 13px;
        font-family: 'Lato', sans-serif;
        color: #111;
        background: #fff;
        outline: none;
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    .pf-input::placeholder { color: #d1d5db; }
    .pf-input:focus {
        border-color: #7c3aed;
        box-shadow: 0 0 0 2px rgba(124,58,237,0.1);
    }
    .pf-input.is-error { border-color: #f87171; }

    /* Eye toggle */
    .pf-eye {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        cursor: pointer;
        color: #c4c4d4;
        padding: 0;
        line-height: 0;
        transition: color 0.15s;
    }
    .pf-eye:hover { color: #7c3aed; }

    .pf-hint {
        font-size: 11.5px;
        color: #9ca3af;
        margin-top: 5px;
    }
    .pf-error {
        font-size: 11.5px;
        color: #dc2626;
        margin-top: 4px;
    }

    /* ── Buttons ── */
    .pf-btn-row {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        margin-top: 18px;
    }
    .pf-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 9px 20px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 700;
        font-family: 'Lato', sans-serif;
        cursor: pointer;
        border: none;
        transition: opacity 0.15s, transform 0.15s;
        text-decoration: none;
    }
    .pf-btn:hover { opacity: 0.88; transform: translateY(-1px); }
    .pf-btn:active { transform: translateY(0); }

    .pf-btn-primary {
        background: linear-gradient(135deg, #9025FB, #4617D3);
        color: #fff;
        box-shadow: 0 2px 8px rgba(109,40,217,0.2);
    }
    .pf-btn-danger {
        background: #fff;
        color: #dc2626;
        border: 1px solid #fecaca;
    }
    .pf-btn-danger:hover { background: #fef2f2; }

    /* ── Alert boxes ── */
    .pf-alert {
        padding: 10px 14px;
        border-radius: 6px;
        font-size: 13px;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .pf-alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; }
    .pf-alert-error   { background: #fef2f2; border: 1px solid #fecaca;  color: #dc2626; }

    /* ── Logout section ── */
    .pf-logout-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 4px 0;
    }
    .pf-logout-info { font-size: 13px; color: #6b7280; }
    .pf-logout-info strong { color: #1a1a2e; font-weight: 700; }
</style>
@endpush

@section('content')
<div class="profile-wrap">

    {{-- ── Success / Error alerts ── --}}
    @if(session('success'))
    <div class="pf-alert pf-alert-success">
        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="pf-alert pf-alert-error">
        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10" stroke-width="2"/>
            <path d="M12 8v4m0 4h.01" stroke-width="2.5" stroke-linecap="round"/>
        </svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- ── Identity card ── --}}
    <div class="pf-card">
        <div class="pf-card-head">
            <svg width="14" height="14" fill="none" stroke="#7c3aed" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <span class="pf-card-title">Informasi Akun</span>
        </div>
        <div class="pf-card-body">
            <div class="pf-identity">
                <div class="pf-avatar-lg">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}{{ strtoupper(substr(strrchr(Auth::user()->name ?? '', ' '), 1, 1)) }}
                </div>
                <div>
                    <div class="pf-name">{{ Auth::user()->name }}</div>
                    <div class="pf-role">
                        <span>{{ Auth::user()->role->role_name ?? 'user' }}</span>
                    </div>
                </div>
            </div>

            <div class="pf-rows" style="margin-top: 20px;">
                <div class="pf-row">
                    <span class="pf-row-label">Identifier / NIS</span>
                    <span class="pf-row-value">{{ Auth::user()->identifier }}</span>
                </div>
                <div class="pf-row">
                    <span class="pf-row-label">Nama Lengkap</span>
                    <span class="pf-row-value">{{ Auth::user()->name }}</span>
                </div>
                <div class="pf-row">
                    <span class="pf-row-label">Status Akun</span>
                    <span class="pf-row-value" style="color: {{ Auth::user()->status === 'active' ? '#16a34a' : '#dc2626' }};">
                        {{ ucfirst(Auth::user()->status) }}
                    </span>
                </div>
                <div class="pf-row">
                    <span class="pf-row-label">Role</span>
                    <span class="pf-row-value">{{ Auth::user()->role->role_name ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Change password card ── --}}
    <div class="pf-card">
        <div class="pf-card-head">
            <svg width="14" height="14" fill="none" stroke="#7c3aed" viewBox="0 0 24 24">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" stroke-width="1.8"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 11V7a5 5 0 0110 0v4"/>
            </svg>
            <span class="pf-card-title">Ganti Password</span>
        </div>
        <div class="pf-card-body">
            <form method="POST" action="{{ route('profile.password') }}">
                @csrf
                @method('PUT')

                {{-- Current password --}}
                <div class="pf-field">
                    <label class="pf-label" for="current_password">Password Saat Ini</label>
                    <div class="pf-input-wrap">
                        <input type="password" id="current_password" name="current_password"
                               class="pf-input {{ $errors->has('current_password') ? 'is-error' : '' }}"
                               placeholder="Masukkan password saat ini"
                               style="padding-right: 36px;" required>
                        <button type="button" class="pf-eye" onclick="togglePw('current_password', this)">
                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    @error('current_password')
                    <p class="pf-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- New password --}}
                <div class="pf-field">
                    <label class="pf-label" for="new_password">Password Baru</label>
                    <div class="pf-input-wrap">
                        <input type="password" id="new_password" name="new_password"
                               class="pf-input {{ $errors->has('new_password') ? 'is-error' : '' }}"
                               placeholder="Minimal 6 karakter"
                               style="padding-right: 36px;" required>
                        <button type="button" class="pf-eye" onclick="togglePw('new_password', this)">
                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    @error('new_password')
                    <p class="pf-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirm new password --}}
                <div class="pf-field">
                    <label class="pf-label" for="new_password_confirmation">Konfirmasi Password Baru</label>
                    <div class="pf-input-wrap">
                        <input type="password" id="new_password_confirmation" name="new_password_confirmation"
                               class="pf-input"
                               placeholder="Ulangi password baru"
                               style="padding-right: 36px;" required>
                        <button type="button" class="pf-eye" onclick="togglePw('new_password_confirmation', this)">
                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    <p class="pf-hint">Password baru harus sama di kedua kolom.</p>
                </div>

                <div class="pf-btn-row">
                    <button type="submit" class="pf-btn pf-btn-primary">
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan Password
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Logout card ── --}}
    <div class="pf-card">
        <div class="pf-card-head">
            <svg width="14" height="14" fill="none" stroke="#dc2626" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            <span class="pf-card-title" style="color: #dc2626;">Keluar</span>
        </div>
        <div class="pf-card-body">
            <div class="pf-logout-row">
                <div class="pf-logout-info">
                    Keluar dari <strong>{{ Auth::user()->name }}</strong> · {{ Auth::user()->identifier }}
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="pf-btn pf-btn-danger">
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function togglePw(id, btn) {
        const input = document.getElementById(id);
        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';
        btn.style.color = isHidden ? '#7c3aed' : '#c4c4d4';
    }
</script>
@endpush