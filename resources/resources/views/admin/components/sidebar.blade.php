{{-- resources/views/admin/components/sidebar.blade.php --}}

<aside id="adminSidebar" class="sintem-sidebar">

    {{-- ── LOGO ── --}}
    <div class="sb-logo">
        <img src="{{ asset('assets/Logo SINTEM.png') }}" alt="SINTEM" class="sb-logo-img">
        {{-- Close button: visible only on mobile/tablet overlay mode --}}
        <button class="sb-close-btn" onclick="closeSidebar()" aria-label="Tutup menu">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- ── SEARCH ── --}}
    <div class="sb-search-wrap">
        <svg class="sb-search-icon" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="8" stroke-width="2"/>
            <path d="M21 21l-4.35-4.35" stroke-width="2" stroke-linecap="round"/>
        </svg>
        <input type="text" class="sb-search" placeholder="Search" id="adminSidebarSearch">
    </div>

    {{-- ── NAV ── --}}
    <nav class="sb-nav" id="adminSidebarNav">

        <p class="sb-group-label">MAIN</p>

        <a href="{{ route('admin.pengumuman.index') }}"
           class="sb-item {{ request()->routeIs('admin.pengumuman.*') ? 'active' : '' }}">
            <svg class="sb-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                      d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
            </svg>
            <span>Pengumuman</span>
        </a>

        <a href="{{ route('admin.kalender.index') }}"
           class="sb-item {{ request()->routeIs('admin.kalender.*') ? 'active' : '' }}">
            <svg class="sb-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <rect x="3" y="4" width="18" height="18" rx="2" stroke-width="1.8"/>
                <line x1="16" y1="2" x2="16" y2="6" stroke-width="1.8" stroke-linecap="round"/>
                <line x1="8"  y1="2" x2="8"  y2="6" stroke-width="1.8" stroke-linecap="round"/>
                <line x1="3"  y1="10" x2="21" y2="10" stroke-width="1.8"/>
            </svg>
            <span>Kalender Kegiatan</span>
        </a>

        <a href="{{ route('admin.temuan.index') }}"
           class="sb-item {{ request()->routeIs('admin.temuan.*') ? 'active' : '' }}">
            <svg class="sb-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="7" stroke-width="1.8"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-4-4"/>
                <line x1="11" y1="8" x2="11" y2="14" stroke-width="1.8" stroke-linecap="round"/>
                <line x1="8"  y1="11" x2="14" y2="11" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
            <span>Informasi Temuan</span>
        </a>

        {{-- ── Divider ── --}}
        <div class="sb-divider"></div>

        <p class="sb-group-label">LAPORAN</p>

        <a href="{{ route('admin.laporan.anonim') }}"
           class="sb-item {{ request()->routeIs('admin.laporan.anonim*') ? 'active' : '' }}">
            <svg class="sb-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                      d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                <circle cx="12" cy="7" r="4" stroke-width="1.8"/>
                <line x1="3" y1="3" x2="21" y2="21" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
            <span>Laporan Anonim</span>
        </a>

    </nav>

    {{-- ── USER PROFILE ── --}}
    <a href="{{ route('admin.profile.show') }}" class="sb-user">
        <div class="sb-avatar-initial">
            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}{{ strtoupper(substr(strrchr(Auth::user()->name ?? '', ' '), 1, 1)) }}
        </div>
        <div class="sb-user-info">
            <span class="sb-user-name">{{ Auth::user()->name ?? 'Admin' }}</span>
            <span class="sb-user-id">{{ Auth::user()->role->role_name ?? 'admin' }}</span>
        </div>
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="sb-user-arrow">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
    </a>

</aside>

<style>
    /* ── SIDEBAR SHELL ── */
    .sintem-sidebar {
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

    /* ── LOGO ROW ── */
    .sb-logo { display: flex; align-items: center; justify-content: space-between; padding: 0 8px; margin-bottom: 20px; }
    .sb-logo-img { height: 36px; width: auto; }

    /* ── Close button (hidden on desktop) ── */
    .sb-close-btn {
        display: none;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        border: none;
        background: none;
        cursor: pointer;
        border-radius: 6px;
        color: #555566;
        transition: background 0.15s;
        flex-shrink: 0;
    }
    .sb-close-btn:hover { background: #f4f0ff; color: #4f28d9; }

    /* ── SEARCH ── */
    .sb-search-wrap { position: relative; margin-bottom: 20px; }
    .sb-search-icon { position: absolute; left: 11px; top: 50%; transform: translateY(-50%); color: #b0b0c0; pointer-events: none; }
    .sb-search {
        width: 100%; padding: 9px 14px 9px 34px;
        border: 1px solid #ebebf0; border-radius: 8px;
        font-size: 13px; font-family: 'Lato', sans-serif;
        color: #333; background: #f9f9fb; outline: none;
        transition: border-color 0.18s, box-shadow 0.18s;
    }
    .sb-search::placeholder { color: #c0c0cc; }
    .sb-search:focus { border-color: #6d28d9; box-shadow: 0 0 0 3px rgba(109,40,217,0.08); background: #fff; }

    /* ── GROUP LABELS & DIVIDER ── */
    .sb-group-label { font-size: 10.5px; font-weight: 700; color: #b0b0c4; letter-spacing: 0.08em; padding: 0 8px; margin-bottom: 6px; }
    .sb-divider     { height: 1px; background: #f0f0f5; margin: 14px 8px; }

    /* ── NAV ── */
    .sb-nav { flex: 1; display: flex; flex-direction: column; gap: 2px; }

    /* ── NAV ITEMS ── */
    .sb-item {
        display: flex; align-items: center; gap: 10px;
        padding: 9px 10px; border-radius: 8px;
        font-size: 13.5px; font-weight: 600; color: #555566;
        text-decoration: none; transition: background 0.15s, color 0.15s;
        cursor: pointer; border: none; background: none; width: 100%; text-align: left;
    }
    .sb-item:hover         { background: #f4f0ff; color: #4f28d9; }
    .sb-item.active        { background: #ede9fe; color: #4f28d9; font-weight: 700; }
    .sb-item.active .sb-icon { stroke: #4f28d9; }
    .sb-item:hover  .sb-icon { stroke: #4f28d9; }
    .sb-icon { stroke: #888899; flex-shrink: 0; transition: stroke 0.15s; }

    /* ── USER PROFILE ── */
    .sb-user {
        display: flex; align-items: center; gap: 10px;
        padding: 10px; border-radius: 10px; border: 1px solid #ebebf0;
        text-decoration: none; margin-top: auto;
        transition: background 0.15s, border-color 0.15s;
    }
    .sb-user:hover { background: #f4f0ff; border-color: #c4b5fd; }

    .sb-avatar-initial {
        width: 34px; height: 34px; border-radius: 50%;
        background: linear-gradient(135deg, #9025FB, #4617D3);
        color: #fff; font-size: 12px; font-weight: 700;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; letter-spacing: -0.5px;
    }
    .sb-user-info  { display: flex; flex-direction: column; flex: 1; min-width: 0; }
    .sb-user-name  { font-size: 13px; font-weight: 700; color: #1a1a2e; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .sb-user-id    { font-size: 11px; color: #9ca3af; }
    .sb-user-arrow { stroke: #c0c0cc; flex-shrink: 0; }
    .sb-user:hover .sb-user-arrow { stroke: #4f28d9; }

    /* ══════════════════════════════════════════════
       BREAKPOINTS
       ▸ Desktop  ≥ 1024px  : sidebar always visible
       ▸ Tablet   768–1023px: sidebar as full-height overlay
       ▸ Mobile   < 768px   : slightly narrower overlay
    ══════════════════════════════════════════════ */

    /* ── Tablet & Mobile: sidebar becomes a slide-in drawer ── */
    @media (max-width: 1023px) {
        .sintem-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 200;
            transform: translateX(-100%);
            box-shadow: 4px 0 24px rgba(0,0,0,0.12);
        }
        .sintem-sidebar.open { transform: translateX(0); }
        .sb-close-btn { display: flex; }
    }

    /* ── Mobile: narrower drawer ── */
    @media (max-width: 767px) {
        .sintem-sidebar { width: 80vw; min-width: 240px; max-width: 300px; }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const search = document.getElementById('adminSidebarSearch');
        const nav    = document.getElementById('adminSidebarNav');
        if (!search || !nav) return;

        // ── Sidebar search filter ──
        search.addEventListener('input', () => {
            const q       = search.value.trim().toLowerCase();
            const items   = nav.querySelectorAll('.sb-item');
            const labels  = nav.querySelectorAll('.sb-group-label');
            const divider = nav.querySelector('.sb-divider');
            let any = false;

            // ── Reset all visibility on empty query ──
            if (!q) {
                items.forEach(el  => el.style.display = '');
                labels.forEach(el => el.style.display = '');
                if (divider) divider.style.display = '';
                return;
            }

            // ── Hide labels and divider while searching ──
            labels.forEach(el => el.style.display = 'none');
            if (divider) divider.style.display = 'none';

            // ── Filter nav items by span text ──
            items.forEach(el => {
                const span  = el.querySelector('span');
                const match = span && span.textContent.toLowerCase().includes(q);
                el.style.display = match ? '' : 'none';
                if (match) any = true;
            });
        });
    });
</script>