{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SINTEM')</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            height: 100%;
            overflow: hidden; /* prevent body scroll — pages handle their own */
        }

        body {
            font-family: 'Lato', sans-serif;
            background: #ffffff;
            display: flex;
        }

        /* ── Layout shell: sidebar + main side-by-side ── */
        .layout-wrap {
            display: flex;
            width: 100%;
            height: 100vh;
            overflow: hidden;
        }

        /* ── Main area: fixed height, no scroll ── */
        .main-content {
            flex: 1;
            min-width: 0;
            height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            background: #ffffff;
        }

        /* ── Fixed top sections (topbar + page header) ── */
        .page-topbar  { flex-shrink: 0; }
        .page-header  {
            flex-shrink: 0;
            padding: 16px 32px 14px;
            border-bottom: 1px solid #f0f0f5;
            background: #ffffff;
        }
        .page-header-title {
            font-size: 20px;
            font-weight: 400;
            color: #1a1a2e;
            letter-spacing: -0.2px;
        }
        .page-header-sub {
            font-size: 13px;
            color: #9ca3af;
            margin-top: 2px;
        }

        /* ── Page body: fills remaining height, default scrollable ── */
        .page-body {
            flex: 1;
            min-height: 0;       /* critical — allows flex child to shrink below content size */
            padding: 28px 32px;
            background: #ffffff;
            overflow-y: auto;
            width: 100%;
        }

        /* ── Mobile overlay backdrop ── */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.35);
            z-index: 199;
            backdrop-filter: blur(2px);
        }
        .sidebar-overlay.active { display: block; }

        /* ── Mobile hamburger button ── */
        .mobile-menu-btn {
            display: none;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border: none;
            background: none;
            cursor: pointer;
            border-radius: 8px;
            color: #1a1a2e;
            transition: background 0.15s;
            flex-shrink: 0;
        }
        .mobile-menu-btn:hover { background: #f4f0ff; }

        /* ── Mobile topbar: hamburger + logo (hidden on desktop) ── */
        .mobile-topbar {
            display: none;
            align-items: center;
            justify-content: space-between;
            padding: 12px 16px;
            border-bottom: 1px solid #f0f0f5;
            background: #ffffff;
            flex-shrink: 0;
        }
        .mobile-topbar-logo { height: 28px; width: auto; }

        /* ══════════════════════════════════════════════
           BREAKPOINTS
           ▸ Desktop  ≥ 1024px  : sidebar always visible, full padding
           ▸ Tablet   768–1023px: sidebar as overlay, reduced padding
           ▸ Mobile   < 768px   : compact header and body padding
           ▸ XS       < 480px   : tightest padding, smallest font
        ══════════════════════════════════════════════ */

        /* ── Tablet (768px–1023px) ── */
        @media (max-width: 1023px) {
            .mobile-topbar      { display: flex; }
            .mobile-menu-btn    { display: flex; }
            .page-header        { padding: 14px 20px 12px; }
            .page-header-title  { font-size: 18px; }
            .page-body          { padding: 20px; }
        }

        /* ── Mobile (< 768px) ── */
        @media (max-width: 767px) {
            .page-header        { padding: 12px 16px 10px; }
            .page-header-title  { font-size: 16px; }
            .page-header-sub    { font-size: 12px; }
            .page-body          { padding: 14px 16px; }
        }

        /* ── Small mobile (< 480px) ── */
        @media (max-width: 479px) {
            .page-body { padding: 12px; }
        }
    </style>

    @stack('styles')
</head>

<body>

{{-- ── Backdrop: tapping it closes the sidebar on mobile/tablet ── --}}
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<div class="layout-wrap">

    @include('components.sidebar')

    <div class="main-content">

        {{-- ── Mobile topbar (hamburger + logo), hidden on desktop ── --}}
        <div class="mobile-topbar" id="mobileTopbar">
            <button class="mobile-menu-btn" onclick="openSidebar()" aria-label="Buka menu">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <img src="{{ asset('assets/Logo SINTEM.png') }}" alt="SINTEM" class="mobile-topbar-logo">
            {{-- spacer keeps logo visually centered --}}
            <div style="width:38px;"></div>
        </div>

        {{-- ── Topbar (optional per page) ── --}}
        @hasSection('topbar')
        <div class="page-topbar">
            @yield('topbar')
        </div>
        @endif

        {{-- ── Page header ── --}}
        @hasSection('header')
        <div class="page-header">
            <h1 class="page-header-title">@yield('header')</h1>
            @hasSection('subheader')
            <p class="page-header-sub">@yield('subheader')</p>
            @endif
        </div>
        @endif

        {{-- ── Page content ── --}}
        <div class="page-body">
            @yield('content')
        </div>

    </div>
</div>

@stack('scripts')

<script>
    // ── Sidebar open/close helpers ──
    function openSidebar() {
        document.getElementById('sidebar').classList.add('open');
        document.getElementById('sidebarOverlay').classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    function closeSidebar() {
        document.getElementById('sidebar').classList.remove('open');
        document.getElementById('sidebarOverlay').classList.remove('active');
        document.body.style.overflow = '';
    }

    // ── Reset sidebar state when resizing back to desktop ──
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024) closeSidebar();
    });
</script>

</body>
</html>