{{-- resources/views/components/topbar.blade.php --}}
{{-- Include on every user page with: @include('components.topbar') --}}

<div style="display:flex; align-items:center; justify-content:space-between; padding: 14px 32px; border-bottom: 1px solid #f0f0f5; background:#ffffff; position:relative; z-index:50;">
    <p style="font-size:13.5px; font-weight:700; color:#1a1a2e;">
        Selamat datang, {{ Auth::user()->name ?? 'Pengguna' }}!
    </p>

    {{-- ── Buat Laporan dropdown button ── --}}
    <div style="position:relative;" id="tb-dd-wrap">
        <button type="button" id="tb-dd-btn" onclick="toggleTbDD()" style="
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 14px;
            background: linear-gradient(135deg, #9025FB, #4617D3);
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            font-family: 'Lato', sans-serif;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(109,40,217,0.22);
            transition: opacity 0.15s, transform 0.15s;
            outline: none;
        " onmouseover="this.style.opacity='.88';this.style.transform='translateY(-1px)'"
           onmouseout="this.style.opacity='1';this.style.transform='translateY(0)'">
            {{-- pen icon --}}
            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M11 4H6a2 2 0 00-2 2v13a2 2 0 002 2h11a2 2 0 002-2v-5"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
            Buat Laporan
            <svg id="tb-dd-chevron" width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                 style="transition: transform 0.2s ease; margin-left: 1px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        {{-- dropdown menu --}}
        <div id="tb-dd-menu" style="
            display: none;
            position: absolute;
            top: calc(100% + 6px);
            right: 0;
            min-width: 200px;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.09);
            z-index: 999;
            padding: 5px;
            animation: tbDDFadeIn 0.15s ease;
        ">
            {{-- Laporan Temuan --}}
            <a href="{{ route('temuan.buat') }}" style="
                display: flex;
                align-items: center;
                gap: 9px;
                padding: 9px 11px;
                border-radius: 5px;
                font-size: 13px;
                font-family: 'Lato', sans-serif;
                font-weight: 600;
                color: #374151;
                text-decoration: none;
                transition: background 0.1s, color 0.1s;
            " onmouseover="this.style.background='#f4f0ff';this.style.color='#4f28d9';this.querySelector('svg').style.stroke='#4f28d9'"
               onmouseout="this.style.background='';this.style.color='#374151';this.querySelector('svg').style.stroke='#6b7280'">
                <svg width="14" height="14" fill="none" stroke="#6b7280" viewBox="0 0 24 24" style="flex-shrink:0; transition:stroke 0.1s;">
                    <circle cx="11" cy="11" r="8" stroke-width="1.8"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-4.35-4.35"/>
                    <line x1="11" y1="8" x2="11" y2="14" stroke-width="1.8" stroke-linecap="round"/>
                    <line x1="8"  y1="11" x2="14" y2="11" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
                Laporan Temuan
            </a>

            {{-- Laporan Anonim --}}
            <a href="{{ route('laporan.buat') }}" style="
                display: flex;
                align-items: center;
                gap: 9px;
                padding: 9px 11px;
                border-radius: 5px;
                font-size: 13px;
                font-family: 'Lato', sans-serif;
                font-weight: 600;
                color: #374151;
                text-decoration: none;
                transition: background 0.1s, color 0.1s;
            " onmouseover="this.style.background='#f4f0ff';this.style.color='#4f28d9';this.querySelector('svg').style.stroke='#4f28d9'"
               onmouseout="this.style.background='';this.style.color='#374151';this.querySelector('svg').style.stroke='#6b7280'">
                <svg width="14" height="14" fill="none" stroke="#6b7280" viewBox="0 0 24 24" style="flex-shrink:0; transition:stroke 0.1s;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                    <circle cx="12" cy="7" r="4" stroke-width="1.8"/>
                    <line x1="3" y1="3" x2="21" y2="21" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
                Laporan Anonim
            </a>
        </div>
    </div>
</div>

<style>
    @keyframes tbDDFadeIn {
        from { opacity: 0; transform: translateY(-5px); }
        to   { opacity: 1; transform: translateY(0); }
    }
</style>

<script>
    function toggleTbDD() {
        const menu    = document.getElementById('tb-dd-menu');
        const chevron = document.getElementById('tb-dd-chevron');
        const isOpen  = menu.style.display === 'block';
        menu.style.display    = isOpen ? 'none' : 'block';
        chevron.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
    }
    document.addEventListener('click', function(e) {
        const wrap = document.getElementById('tb-dd-wrap');
        if (wrap && !wrap.contains(e.target)) {
            document.getElementById('tb-dd-menu').style.display = 'none';
            document.getElementById('tb-dd-chevron').style.transform = 'rotate(0deg)';
        }
    });
</script>