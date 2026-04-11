{{-- resources/views/components/sidebar.blade.php --}}

<aside id="sidebar" class="sintem-sidebar">

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
        <input type="text" class="sb-search" placeholder="Search">
    </div>

    {{-- ── NAV ── --}}
    <nav class="sb-nav">

        {{-- MAIN group --}}
        <p class="sb-group-label">MAIN</p>

        <a href="{{ route('pengumuman.index') }}"
           class="sb-item {{ request()->routeIs('pengumuman.*') ? 'active' : '' }}">
            <svg class="sb-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                      d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
            </svg>
            <span>Pengumuman</span>
        </a>

        <a href="{{ route('kalender.index') }}"
           class="sb-item {{ request()->routeIs('kalender.*') ? 'active' : '' }}">
            <svg class="sb-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke-width="1.8"/>
                <line x1="16" y1="2" x2="16" y2="6" stroke-width="1.8" stroke-linecap="round"/>
                <line x1="8"  y1="2" x2="8"  y2="6" stroke-width="1.8" stroke-linecap="round"/>
                <line x1="3"  y1="10" x2="21" y2="10" stroke-width="1.8"/>
            </svg>
            <span>Kalender</span>
        </a>

        <a href="{{ route('temuan.index') }}"
           class="sb-item {{ request()->routeIs('temuan.*') ? 'active' : '' }}">
            <svg class="sb-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" stroke-width="1.8"/>
                <line x1="12" y1="8"  x2="12" y2="12" stroke-width="2" stroke-linecap="round"/>
                <line x1="12" y1="16" x2="12.01" y2="16" stroke-width="2.5" stroke-linecap="round"/>
            </svg>
            <span>Informasi Temuan</span>
        </a>

        <a href="{{ route('laporan.buat') }}"
           class="sb-item {{ request()->routeIs('laporan.buat') ? 'active' : '' }}">
            <svg class="sb-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                      d="M11 4H6a2 2 0 00-2 2v13a2 2 0 002 2h11a2 2 0 002-2v-5"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                      d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
            <span>Buat Laporan</span>
        </a>

        {{-- ── Divider ── --}}
        <div class="sb-divider"></div>

        {{-- OTHER group --}}
        <p class="sb-group-label">OTHER</p>

        {{-- Manajemen Laporan dropdown --}}
        <div class="sb-dropdown" id="dropManajemen">
            <button class="sb-item sb-dropdown-trigger
                           {{ request()->routeIs('laporan.temuan') || request()->routeIs('laporan.anonim') ? 'active' : '' }}"
                    onclick="toggleDropdown('dropManajemen')">
                <svg class="sb-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                          d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/>
                </svg>
                <span>Manajemen Laporan</span>
                <svg class="sb-chevron" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div class="sb-dropdown-menu">

                {{-- Laporan Temuan --}}
                <a href="{{ route('laporan.temuan') }}"
                   class="sb-sub-item {{ request()->routeIs('laporan.temuan') ? 'active' : '' }}">
                    <svg class="sb-sub-icon" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="7" stroke-width="1.8"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-4-4"/>
                        <line x1="11" y1="8" x2="11" y2="14" stroke-width="1.8" stroke-linecap="round"/>
                        <line x1="8"  y1="11" x2="14" y2="11" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                    Laporan Temuan
                </a>

                {{-- Laporan Anonim --}}
                <a href="{{ route('laporan.anonim') }}"
                   class="sb-sub-item {{ request()->routeIs('laporan.anonim') ? 'active' : '' }}">
                    <svg class="sb-sub-icon" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                        <circle cx="12" cy="7" r="4" stroke-width="1.8"/>
                        <line x1="3" y1="3" x2="21" y2="21" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                    Laporan Anonim
                </a>

            </div>
        </div>

    </nav>

    {{-- ── Search empty state ── --}}
    <div id="sb-no-result" style="display:none; padding: 8px 10px; font-size:12px; color:#9ca3af; text-align:center;">
        Tidak ditemukan
    </div>

    {{-- ── USER PROFILE ── --}}
    <a href="{{ route('profile.show') }}" class="sb-user">
        <div class="sb-avatar-initial">
        {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
        {{ strtoupper(substr(strrchr(Auth::user()->name ?? '', ' '), 1, 1)) }}
        </div>
        <div class="sb-user-info">
        <span class="sb-user-name">{{ Auth::user()->name ?? 'Nickname' }}</span>
        <span class="sb-user-id">{{ Auth::user()->identifier ?? 'user id' }}</span>
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
        /* ── mobile: starts off-screen ── */
        transition: transform 0.28s cubic-bezier(0.22, 1, 0.36, 1);
    }

    /* ── LOGO ── */
    .sb-logo {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 8px;
        margin-bottom: 20px;
    }
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
    .sb-search-wrap {
        position: relative;
        margin-bottom: 20px;
    }
    .sb-search-icon {
        position: absolute;
        left: 11px;
        top: 50%;
        transform: translateY(-50%);
        color: #b0b0c0;
        pointer-events: none;
    }
    .sb-search {
        width: 100%;
        padding: 9px 14px 9px 34px;
        border: 1px solid #ebebf0;
        border-radius: 8px;
        font-size: 13px;
        font-family: 'Lato', sans-serif;
        color: #333;
        background: #f9f9fb;
        outline: none;
        transition: border-color 0.18s, box-shadow 0.18s;
    }
    .sb-search::placeholder { color: #c0c0cc; }
    .sb-search:focus {
        border-color: #6d28d9;
        box-shadow: 0 0 0 3px rgba(109,40,217,0.08);
        background: #fff;
    }

    /* ── GROUP LABEL ── */
    .sb-group-label {
        font-size: 10.5px;
        font-weight: 700;
        color: #b0b0c4;
        letter-spacing: 0.08em;
        padding: 0 8px;
        margin-bottom: 6px;
    }

    /* ── DIVIDER ── */
    .sb-divider {
        height: 1px;
        background: #f0f0f5;
        margin: 14px 8px;
    }

    /* ── NAV ── */
    .sb-nav {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    /* ── NAV ITEMS ── */
    .sb-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 9px 10px;
        border-radius: 8px;
        font-size: 13.5px;
        font-weight: 600;
        color: #555566;
        text-decoration: none;
        transition: background 0.15s, color 0.15s;
        cursor: pointer;
        border: none;
        background: none;
        width: 100%;
        text-align: left;
    }
    .sb-item:hover         { background: #f4f0ff; color: #4f28d9; }
    .sb-item.active        { background: #ede9fe; color: #4f28d9; font-weight: 700; }
    .sb-item.active .sb-icon { stroke: #4f28d9; }
    .sb-item:hover  .sb-icon { stroke: #4f28d9; }

    .sb-icon {
        stroke: #888899;
        flex-shrink: 0;
        transition: stroke 0.15s;
    }

    /* ── DROPDOWN ── */
    .sb-chevron {
        margin-left: auto;
        stroke: #b0b0c4;
        flex-shrink: 0;
        transition: transform 0.25s ease, stroke 0.15s;
    }
    .sb-dropdown.open .sb-chevron {
        transform: rotate(180deg);
        stroke: #4f28d9;
    }

    .sb-dropdown-menu {
        overflow: hidden;
        max-height: 0;
        transition: max-height 0.28s cubic-bezier(0.22,1,0.36,1), opacity 0.2s;
        opacity: 0;
        padding-left: 26px;
        display: flex;
        flex-direction: column;
        gap: 1px;
    }
    .sb-dropdown.open .sb-dropdown-menu {
        max-height: 200px;
        opacity: 1;
    }

    .sb-sub-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 10px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 500;
        color: #6b6b80;
        text-decoration: none;
        transition: background 0.15s, color 0.15s;
    }
    .sb-sub-item:hover  { background: #f4f0ff; color: #4f28d9; }
    .sb-sub-item.active { color: #4f28d9; font-weight: 700; }

    .sb-sub-icon { stroke: #aaaabc; flex-shrink: 0; transition: stroke 0.15s; }
    .sb-sub-item:hover  .sb-sub-icon { stroke: #4f28d9; }
    .sb-sub-item.active .sb-sub-icon { stroke: #4f28d9; }

    /* ── USER PROFILE ── */
    .sb-user {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px;
        border-radius: 10px;
        border: 1px solid #ebebf0;
        text-decoration: none;
        margin-top: auto;
        transition: background 0.15s, border-color 0.15s;
    }
    .sb-user:hover { background: #f4f0ff; border-color: #c4b5fd; }

    .sb-avatar-initial {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: linear-gradient(135deg, #9025FB, #4617D3);
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        letter-spacing: -0.5px;
    }
    .sb-user-info  { display: flex; flex-direction: column; flex: 1; min-width: 0; }
    .sb-user-name  {
        font-size: 13px;
        font-weight: 700;
        color: #1a1a2e;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .sb-user-id    { font-size: 11px; color: #9ca3af; font-weight: 400; }
    .sb-user-arrow { stroke: #c0c0cc; flex-shrink: 0; }
    .sb-user:hover .sb-user-arrow { stroke: #4f28d9; }

    /* ══════════════════════════════════════════════
       BREAKPOINTS
       ▸ Desktop  ≥ 1024px  : sidebar always visible
       ▸ Tablet   768–1023px: sidebar as full-height overlay (slides in)
       ▸ Mobile   < 768px   : same overlay, slightly narrower sidebar
    ══════════════════════════════════════════════ */

    /* ── Tablet & Mobile: sidebar becomes a drawer ── */
    @media (max-width: 1023px) {
        .sintem-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 200;
            transform: translateX(-100%);  /* hidden off-screen by default */
            box-shadow: 4px 0 24px rgba(0,0,0,0.12);
        }
        .sintem-sidebar.open {
            transform: translateX(0);      /* slides in when JS adds .open */
        }
        .sb-close-btn { display: flex; }
    }

    /* ── Mobile (< 768px): slightly narrower drawer ── */
    @media (max-width: 767px) {
        .sintem-sidebar { width: 80vw; min-width: 240px; max-width: 300px; }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {

        // ── Auto-open dropdown if a child item is active ──
        document.querySelectorAll('.sb-dropdown').forEach(drop => {
            if (drop.querySelector('.sb-sub-item.active')) {
                drop.classList.add('open');
            }
        });

        // ── Sidebar search filter ──
        const searchInput = document.querySelector('.sb-search');
        const noResult    = document.getElementById('sb-no-result');

        searchInput.addEventListener('input', () => {
            const query = searchInput.value.trim().toLowerCase();

            const mainItems = document.querySelectorAll('.sb-nav .sb-item:not(.sb-dropdown-trigger)');
            const dropdowns = document.querySelectorAll('.sb-dropdown');
            const subItems  = document.querySelectorAll('.sb-sub-item');
            const labels    = document.querySelectorAll('.sb-group-label');
            const divider   = document.querySelector('.sb-divider');

            let anyVisible = false;

            // ── Reset all visibility on empty query ──
            if (query === '') {
                mainItems.forEach(el => el.style.display = '');
                dropdowns.forEach(el => {
                    el.style.display = '';
                    if (!el.querySelector('.sb-sub-item.active')) {
                        el.classList.remove('open');
                    }
                });
                subItems.forEach(el => el.style.display = '');
                labels.forEach(el => el.style.display = '');
                if (divider) divider.style.display = '';
                noResult.style.display = 'none';
                return;
            }

            // ── Hide group labels and divider while searching ──
            labels.forEach(el => el.style.display = 'none');
            if (divider) divider.style.display = 'none';

            // ── Filter direct nav items ──
            mainItems.forEach(el => {
                const text  = el.querySelector('span') ? el.querySelector('span').textContent.toLowerCase() : '';
                const match = text.includes(query);
                el.style.display = match ? '' : 'none';
                if (match) anyVisible = true;
            });

            // ── Filter dropdown groups ──
            dropdowns.forEach(drop => {
                const triggerText  = drop.querySelector('.sb-dropdown-trigger span')
                    ? drop.querySelector('.sb-dropdown-trigger span').textContent.toLowerCase()
                    : '';
                const triggerMatch = triggerText.includes(query);

                let anySubMatch = false;
                drop.querySelectorAll('.sb-sub-item').forEach(sub => {
                    const subMatch = sub.textContent.trim().toLowerCase().includes(query);
                    sub.style.display = subMatch ? '' : 'none';
                    if (subMatch) anySubMatch = true;
                });

                if (triggerMatch || anySubMatch) {
                    drop.style.display = '';
                    drop.classList.add('open');
                    anyVisible = true;
                    // ── Show all sub-items when only the parent matched ──
                    if (triggerMatch && !anySubMatch) {
                        drop.querySelectorAll('.sb-sub-item').forEach(sub => sub.style.display = '');
                    }
                } else {
                    drop.style.display = 'none';
                }
            });

            noResult.style.display = anyVisible ? 'none' : '';
        });
    });

    // ── Toggle a sidebar dropdown open/closed ──
    function toggleDropdown(id) {
        document.getElementById(id).classList.toggle('open');
    }
</script>