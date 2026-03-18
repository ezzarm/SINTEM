<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SINTEM – Semua Informasi, Dalam Satu Tempat</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&family=Space+Grotesk:wght@700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --gradient: linear-gradient(135deg, #9025FB 0%, #4617D3 100%);
            --gradient-text: linear-gradient(135deg, #9025FB 0%, #4617D3 100%);
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Lato', sans-serif;
            background: #ffffff;
            color: #111111;
            overflow-x: hidden;
        }

        /* Headline only */
        .headline {
            font-family: 'Space Grotesk', sans-serif;
        }

        .text-sintem {
            background: var(--gradient-text);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Navbar */
        .navbar-glass {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border-bottom: 1px solid rgba(144, 37, 251, 0.08);
        }

        .logo-icon {
            width: 34px; height: 34px;
            background: var(--gradient);
            border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        .nav-link {
            position: relative;
            color: #444;
            font-size: 14px;
            font-weight: 400;
            transition: color 0.2s;
            text-decoration: none;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -4px; left: 0;
            width: 0; height: 2px;
            background: var(--gradient);
            border-radius: 2px;
            transition: width 0.25s ease;
        }
        .nav-link:hover { color: #9025FB; }
        .nav-link:hover::after { width: 100%; }

        .btn-sintem {
            background: var(--gradient);
            font-family: 'Lato', sans-serif;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .btn-sintem:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 28px rgba(144, 37, 251, 0.40);
        }
        .btn-sintem:active { transform: translateY(0); }

        .btn-outline {
            border: 1.5px solid rgba(144, 37, 251, 0.35);
            font-family: 'Lato', sans-serif;
            transition: border-color 0.2s, background 0.2s;
        }
        .btn-outline:hover {
            border-color: #9025FB;
            background: rgba(144, 37, 251, 0.05);
        }

        /* Dropdown */
        .dropdown-menu {
            display: none;
            position: absolute;
            top: calc(100% + 10px); left: 0;
            min-width: 170px;
            background: #fff;
            border: 1px solid rgba(144, 37, 251, 0.10);
            border-radius: 12px;
            box-shadow: 0 12px 40px rgba(0,0,0,0.09);
            padding: 5px;
            z-index: 100;
        }
        .dropdown-wrapper:hover .dropdown-menu,
        .dropdown-wrapper:focus-within .dropdown-menu {
            display: block;
            animation: fadeDown 0.18s ease forwards;
        }
        @keyframes fadeDown {
            from { opacity: 0; transform: translateY(-6px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .dropdown-item {
            display: block;
            padding: 8px 13px;
            border-radius: 8px;
            font-size: 13.5px;
            color: #333;
            text-decoration: none;
            font-family: 'Lato', sans-serif;
            transition: background 0.15s, color 0.15s;
        }
        .dropdown-item:hover {
            background: rgba(144,37,251,0.06);
            color: #9025FB;
        }

        /* Mobile menu */
        #mobile-menu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.38s cubic-bezier(0.22, 1, 0.36, 1);
        }
        #mobile-menu.open { max-height: 380px; }

        /* Hero */
        .hero-bg {
            position: relative;
            overflow: hidden;
            min-height: 100vh;
        }
        .hero-bg::before {
            content: '';
            position: absolute; inset: 0;
            background:
                radial-gradient(ellipse 900px 650px at 65% -5%,  rgba(144,37,251,0.09) 0%, transparent 68%),
                radial-gradient(ellipse 650px 550px at -5% 85%,  rgba(70,23,211,0.07)  0%, transparent 68%);
            pointer-events: none;
        }

        .dots-pattern {
            position: absolute; inset: 0;
            background-image: radial-gradient(circle, rgba(144,37,251,0.11) 1px, transparent 1px);
            background-size: 30px 30px;
            pointer-events: none;
            mask-image: radial-gradient(ellipse 75% 75% at 50% 50%, black 10%, transparent 100%);
        }

        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(90px);
            pointer-events: none;
            animation: floatOrb 9s ease-in-out infinite;
        }
        .orb-1 {
            width: 520px; height: 520px;
            background: radial-gradient(circle, rgba(144,37,251,0.13) 0%, transparent 70%);
            top: -160px; right: -120px;
        }
        .orb-2 {
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(70,23,211,0.10) 0%, transparent 70%);
            bottom: -100px; left: -80px;
            animation-delay: -4.5s;
        }
        .orb-3 {
            width: 240px; height: 240px;
            background: radial-gradient(circle, rgba(144,37,251,0.09) 0%, transparent 70%);
            top: 45%; left: 48%;
            animation-delay: -2s;
        }
        @keyframes floatOrb {
            0%, 100% { transform: translate(0,0) scale(1); }
            33%       { transform: translate(18px,-28px) scale(1.04); }
            66%       { transform: translate(-14px,18px) scale(0.96); }
        }

        /* Hero animations */
        .hero-badge {
            opacity: 0; transform: translateY(16px);
            animation: slideUp 0.65s cubic-bezier(0.22,1,0.36,1) 0.05s forwards;
        }
        .hero-title-line-1 {
            display: block; opacity: 0; transform: translateY(38px);
            animation: slideUp 0.80s cubic-bezier(0.22,1,0.36,1) 0.18s forwards;
        }
        .hero-title-line-2 {
            display: block; opacity: 0; transform: translateY(38px);
            animation: slideUp 0.80s cubic-bezier(0.22,1,0.36,1) 0.32s forwards;
        }
        .hero-sub {
            opacity: 0; transform: translateY(22px);
            animation: slideUp 0.75s cubic-bezier(0.22,1,0.36,1) 0.52s forwards;
        }
        .hero-cta {
            opacity: 0; transform: translateY(22px);
            animation: slideUp 0.75s cubic-bezier(0.22,1,0.36,1) 0.68s forwards;
        }
        .hero-trust {
            opacity: 0; transform: translateY(14px);
            animation: slideUp 0.65s cubic-bezier(0.22,1,0.36,1) 0.86s forwards;
        }
        @keyframes slideUp {
            to { opacity: 1; transform: translateY(0); }
        }

        .sintem-input { font-family: 'Lato', sans-serif; }
        .sintem-input:focus {
            outline: none;
            box-shadow: none;
        }

        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: #f3f3f3; }
        ::-webkit-scrollbar-thumb { background: var(--gradient); border-radius: 3px; }

        /* Footer */
        .footer-divider {
            height: 1px;
            background: linear-gradient(90deg, transparent 0%, rgba(144,37,251,0.20) 30%, rgba(70,23,211,0.25) 50%, rgba(144,37,251,0.20) 70%, transparent 100%);
        }
        .footer-fade-in {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.7s ease, transform 0.7s ease;
        }
        .footer-fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }
        .footer-dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: var(--gradient);
            display: inline-block;
            animation: pulseDot 2.4s ease-in-out infinite;
        }
        @keyframes pulseDot {
            0%, 100% { transform: scale(1); opacity: 1; }
            50%       { transform: scale(1.5); opacity: 0.6; }
        }
        .footer-link {
            color: #9ca3af;
            font-size: 13px;
            text-decoration: none;
            transition: color 0.2s;
        }
        .footer-link:hover { color: #9025FB; }
    </style>
</head>

<body class="antialiased">

    {{-- NAVBAR --}}
    <header class="navbar-glass fixed top-0 left-0 right-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                <a href="/" class="flex items-center flex-shrink-0">
                    <img src="{{ asset('assets/Logo SINTEM.png') }}" alt="SINTEM" class="h-9 w-auto">
                </a>

                <nav class="hidden md:flex items-center gap-7">
                    <a href="#" class="nav-link">Pengumuman</a>
                    <a href="#" class="nav-link">Kalender Event</a>
                    <a href="#" class="nav-link">Laporkan</a>
                    <a href="#" class="nav-link">Lost &amp; Found</a>
                </nav>

                <button id="menu-toggle" class="md:hidden p-2 rounded-lg hover:bg-purple-50 transition-colors" aria-label="Menu">
                    <svg id="icon-open" class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg id="icon-close" class="w-5 h-5 text-gray-700 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <div id="mobile-menu" class="md:hidden border-t border-purple-50/60">
            <div class="px-4 py-4 space-y-0.5 bg-white/96">
                <a href="#" class="block px-3 py-2.5 rounded-lg text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-700 transition-colors">Pengumuman</a>
                <a href="#" class="block px-3 py-2.5 rounded-lg text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-700 transition-colors">Kalender Event</a>
                <a href="#" class="block px-3 py-2.5 rounded-lg text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-700 transition-colors">Laporkan</a>
                <a href="#" class="block px-3 py-2.5 rounded-lg text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-700 transition-colors">Lost &amp; Found</a>
            </div>
        </div>
    </header>

    {{-- HERO --}}
    <section class="hero-bg flex flex-col items-center justify-center pt-16 px-4 sm:px-6">
        <div class="dots-pattern"></div>
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>

        <div class="relative z-10 max-w-2xl mx-auto text-center py-28 sm:py-36">

            <h1 class="headline font-extrabold leading-[1.08] tracking-tight mb-6
                        text-[44px] sm:text-[60px] lg:text-[72px]">
                <span class="hero-title-line-1 text-gray-900">Semua Informasi,</span>
                <span class="hero-title-line-2">Dalam <span class="text-sintem">Satu Tempat</span></span>
            </h1>

            <p class="hero-sub text-black-500 leading-relaxed mb-10 text-[15px] sm:text-[17px]">
                <span class="text-black-700">Pengumuman real-time</span>
                <span class="mx-2 text-black-400">•</span>
                <span class="text-black-700">Laporan aduan cepat</span>
                <span class="mx-2 text-black-400">•</span>
                <span class="text-black-700">Data sekolah terintegrasi</span>
            </p>

            <div class="hero-cta flex items-stretch max-w-md mx-auto mb-9 border border-gray-200 bg-white shadow-sm overflow-hidden">
                <input
                    type="text"
                    placeholder="Masuk Dengan NIS"
                    class="sintem-input flex-1 px-5 py-3.5 bg-transparent text-sm text-gray-800 placeholder-gray-400 border-0 focus:ring-0"
                    style="outline:none; box-shadow:none;"
                />
                <button class="btn-sintem px-6 py-3.5 text-sm font-bold text-white whitespace-nowrap flex-shrink-0" style="border-radius:0;">
                    Masuk ke SINTEM
                </button>
            </div>
        </div>
    </section>

    {{-- FOOTER --}}
    <footer class="w-full mt-auto">
        <div class="footer-divider"></div>
        <div class="max-w-7xl mx-auto px-6 py-10">
            <div class="footer-fade-in flex flex-col sm:flex-row items-center justify-between gap-5">

                <div class="flex items-center gap-2.5">
                    <img src="{{ asset('assets/Logo SINTEM.png') }}" alt="SINTEM" class="h-7 w-auto">
                    <span class="text-gray-300 text-sm">—</span>
                    <span class="text-gray-400 text-xs">Sistem Informasi Terpadu Stemba</span>
                </div>

                <div class="text-center">
                    <p class="text-gray-400 text-sm">&copy; {{ date('Y') }} SINTEM Portal. All rights reserved.</p>
                    <p class="text-xs text-gray-400 mt-1 flex items-center justify-center gap-1.5">
                        Built with by <span class="text-sintem font-bold">13 SIJA 1</span>
                    </p>
                </div>

                <div class="flex items-center gap-5">
                    <a href="#" class="footer-link">Kebijakan Privasi</a>
                    <a href="#" class="footer-link">Kontak</a>
                    <a href="#" class="footer-link">Bantuan</a>
                </div>

            </div>
        </div>
    </footer>


    <script>
        const toggle    = document.getElementById('menu-toggle');
        const menu      = document.getElementById('mobile-menu');
        const iconOpen  = document.getElementById('icon-open');
        const iconClose = document.getElementById('icon-close');

        toggle.addEventListener('click', () => {
            const isOpen = menu.classList.toggle('open');
            iconOpen.classList.toggle('hidden', isOpen);
            iconClose.classList.toggle('hidden', !isOpen);
        });

        // Footer scroll-in
        const footerObserver = new IntersectionObserver((entries) => {
            entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('visible'); });
        }, { threshold: 0.2 });
        document.querySelectorAll('.footer-fade-in').forEach(el => footerObserver.observe(el));
    </script>

</body>
</html>