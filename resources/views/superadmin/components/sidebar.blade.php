{{-- resources/views/superadmin/components/sidebar.blade.php --}}

<aside id="saSidebar" class="sa-sidebar">

    {{-- ── Logo + close button ── --}}
    <div class="sa-sb-logo">
        <img src="{{ asset('assets/Logo SINTEM.png') }}" alt="SINTEM" class="sa-sb-logo-img">
        <button class="sa-sb-close" onclick="closeSaSidebar()" aria-label="Tutup">
            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- ── Navigation ── --}}
    <nav class="sa-sb-nav">

        <p class="sa-sb-label">MANAJEMEN</p>

        <a href="{{ route('superadmin.accounts.index') }}"
           class="sa-sb-item {{ request()->routeIs('superadmin.accounts.*') ? 'active' : '' }}">
            <svg class="sa-sb-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                <circle cx="9" cy="7" r="4" stroke-width="1.8"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
            </svg>
            <span>Akun</span>
        </a>

        <a href="{{ route('superadmin.roles.index') }}"
           class="sa-sb-item {{ request()->routeIs('superadmin.roles.*') ? 'active' : '' }}">
            <svg class="sa-sb-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
            </svg>
            <span>Role</span>
        </a>

        <a href="{{ route('superadmin.categories.index') }}"
           class="sa-sb-item {{ request()->routeIs('superadmin.categories.*') ? 'active' : '' }}">
            <svg class="sa-sb-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/>
            </svg>
            <span>Kategori Laporan</span>
        </a>

    </nav>

    {{-- ── User profile footer (FULL CLICKABLE) ── --}}
    <a href="{{ route('superadmin.profile.show') }}"
       class="sb-user {{ request()->routeIs('superadmin.profile.show') ? 'sb-user-active' : '' }}">

        <div class="sb-avatar-initial">
            {{ strtoupper(substr(Auth::user()->name ?? 'S', 0, 1)) }}
        </div>

        <div class="sb-user-info">
            <span class="sb-user-name">{{ Auth::user()->name ?? 'Superadmin' }}</span>
            <span class="sb-user-id" style="color:#7c3aed;font-weight:700;">Superadmin</span>
        </div>

        <div class="sa-sb-chevron">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 18l6-6-6-6"/>
            </svg>
        </div>

    </a>

</aside>

<style>
    .sa-sidebar {
        width: 248px;
        min-width: 248px;
        height: 100vh;
        position: sticky;
        top: 0;
        display: flex;
        flex-direction: column;
        background: #ffffff;
        border-right: 1px solid #f0f0f5;
        padding: 20px 12px 16px;
        font-family: 'Lato', sans-serif;
        overflow-y: auto;
        overflow-x: hidden;
        transition: transform 0.28s cubic-bezier(0.22, 1, 0.36, 1);
    }

    .sa-sb-logo {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 8px;
        margin-bottom: 14px;
    }

    .sa-sb-logo-img { height: 36px; }

    .sa-sb-close {
        display: none;
        width: 28px;
        height: 28px;
        border: none;
        background: none;
        border-radius: 6px;
        cursor: pointer;
        color: #555566;
    }

    .sa-sb-close:hover { background: #f4f0ff; color: #4f28d9; }

    .sa-sb-label {
        font-size: 10.5px;
        font-weight: 700;
        color: #b0b0c4;
        padding: 0 8px;
        margin-bottom: 6px;
    }

    .sa-sb-nav {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .sa-sb-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 9px 10px;
        border-radius: 8px;
        font-size: 13.5px;
        font-weight: 600;
        color: #555566;
        text-decoration: none;
    }

    .sa-sb-item:hover { background: #f4f0ff; color: #4f28d9; }
    .sa-sb-item.active { background: #ede9fe; color: #4f28d9; }

    .sa-sb-icon { stroke: #888899; }

    /* USER */
    .sb-user {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px;
        border-radius: 10px;
        border: 1px solid #ebebf0;
        margin-top: auto;
        text-decoration: none;
        cursor: pointer;
        transition: 0.15s;
    }

    .sb-user:hover {
        background: #f4f0ff;
        border-color: #c4b5fd;
    }

    .sb-user-active {
        background: #ede9fe !important;
        border-color: #c4b5fd !important;
    }

    .sb-avatar-initial {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: linear-gradient(135deg, #9025FB, #4617D3);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
    }

    .sb-user-info {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .sb-user-name {
        font-size: 13px;
        font-weight: 700;
        color: #1a1a2e;
    }

    .sb-user-id {
        font-size: 11px;
        color: #9ca3af;
    }

    .sa-sb-chevron {
        color: #c0c0cc;
    }

    @media (max-width: 1023px) {
        .sa-sidebar {
            position: fixed;
            left: 0;
            transform: translateX(-100%);
        }

        .sa-sidebar.open {
            transform: translateX(0);
        }

        .sa-sb-close {
            display: flex;
        }
    }
</style>