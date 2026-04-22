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

        .headline { font-family: 'Space Grotesk', sans-serif; }

        .text-sintem {
            background: var(--gradient-text);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .navbar-glass {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border-bottom: 1px solid rgba(144, 37, 251, 0.08);
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
            transition: transform 0.2s ease, box-shadow 0.2s ease, opacity 0.2s ease;
        }
        .btn-sintem:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 28px rgba(144, 37, 251, 0.35);
            opacity: 0.9;
        }
        .btn-sintem:active { transform: translateY(0); }

        .hero-bg {
            position: relative;
            overflow: hidden;
            min-height: 100vh;
        }

        .hero-bg::before {
            content: '';
            position: absolute; inset: 0;
            background:
                radial-gradient(ellipse 900px 650px at 65% -5%, rgba(144,37,251,0.09) 0%, transparent 68%),
                radial-gradient(ellipse 650px 550px at -5% 85%, rgba(70,23,211,0.07) 0%, transparent 68%);
            pointer-events: none;
        }

        .dots-pattern {
            position: absolute; inset: 0;
            background-image: radial-gradient(circle, rgba(144,37,251,0.18) 1.5px, transparent 1.5px);
            background-size: 30px 30px;
            pointer-events: none;
            mask-image: radial-gradient(ellipse 80% 80% at 50% 50%, black 10%, transparent 100%);
            animation: driftDots 22s ease-in-out infinite;
        }
        @keyframes driftDots {
            0%, 100% { transform: translate(0, 0); }
            33%  { transform: translate(6px, -8px); }
            66%  { transform: translate(-5px, 6px); }
        }

        .grid-pattern {
            position: absolute; inset: 0;
            background-image:
                linear-gradient(rgba(144,37,251,0.06) 1px, transparent 1px),
                linear-gradient(90deg, rgba(144,37,251,0.06) 1px, transparent 1px);
            background-size: 64px 64px;
            pointer-events: none;
            mask-image: radial-gradient(ellipse 75% 70% at 50% 50%, black 0%, transparent 100%);
        }

        #falling-canvas {
            position: absolute;
            inset: 0;
            pointer-events: none;
            overflow: hidden;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .animate-line-1 { animation: slideUp 0.8s cubic-bezier(0.22,1,0.36,1) 0.1s forwards; opacity: 0; }
        .animate-line-2 { animation: slideUp 0.8s cubic-bezier(0.22,1,0.36,1) 0.25s forwards; opacity: 0; }
        .animate-sub    { animation: slideUp 0.8s cubic-bezier(0.22,1,0.36,1) 0.4s forwards; opacity: 0; }
        .animate-btn    { animation: slideUp 0.8s cubic-bezier(0.22,1,0.36,1) 0.55s forwards; opacity: 0; }

        #mobile-menu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.38s cubic-bezier(0.22, 1, 0.36, 1);
        }
        #mobile-menu.open { max-height: 380px; }

        .footer-divider {
            height: 1px;
            background: linear-gradient(90deg, transparent 0%, rgba(144,37,251,0.15) 30%, rgba(70,23,211,0.2) 50%, rgba(144,37,251,0.15) 70%, transparent 100%);
        }
    </style>
</head>

<body class="antialiased flex flex-col min-h-screen">

    {{-- NAVBAR --}}
    <header class="navbar-glass fixed top-0 left-0 right-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="/" class="flex items-center flex-shrink-0 transition hover:opacity-80">
                    <img src="{{ asset('assets/Logo SINTEM.png') }}" alt="SINTEM" class="h-9 w-auto">
                </a>
                <nav class="hidden md:flex items-center gap-7">
                    <a href="{{ Auth::check() ? route('pengumuman.index') : route('login') }}" class="nav-link">Pengumuman</a>
                    <a href="{{ Auth::check() ? route('kalender.index') : route('login') }}" class="nav-link">Kalender Event</a>
                    <a href="{{ Auth::check() ? route('laporan.buat') : route('login') }}" class="nav-link">Laporkan</a>
                    <a href="{{ Auth::check() ? route('temuan.index') : route('login') }}" class="nav-link">Lost & Found</a>
                </nav>
                <button id="menu-toggle" class="md:hidden p-2 rounded-lg hover:bg-purple-50 transition-colors">
                    <svg id="icon-open" class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg id="icon-close" class="w-6 h-6 text-gray-700 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <div id="mobile-menu" class="md:hidden bg-white/95 backdrop-blur-md">
            <div class="px-4 py-4 space-y-1">
                <a href="{{ Auth::check() ? route('pengumuman.index') : route('login') }}" class="block px-3 py-3 rounded-lg text-base text-gray-700 hover:bg-purple-50 hover:text-purple-700 transition-colors">Pengumuman</a>
                <a href="{{ Auth::check() ? route('kalender.index') : route('login') }}" class="block px-3 py-3 rounded-lg text-base text-gray-700 hover:bg-purple-50 hover:text-purple-700 transition-colors">Kalender Event</a>
                <a href="{{ Auth::check() ? route('laporan.buat') : route('login') }}" class="block px-3 py-3 rounded-lg text-base text-gray-700 hover:bg-purple-50 hover:text-purple-700 transition-colors">Laporkan</a>
                <a href="{{ Auth::check() ? route('temuan.index') : route('login') }}" class="block px-3 py-3 rounded-lg text-base text-gray-700 hover:bg-purple-50 hover:text-purple-700 transition-colors">Lost & Found</a>
            </div>
        </div>
        </div>
    </header>

    {{-- HERO --}}
    <section class="hero-bg flex flex-col items-center justify-center px-4 sm:px-6 flex-grow">
        <div class="grid-pattern"></div>
        <div class="dots-pattern"></div>
        <div id="falling-canvas"></div>

        <div class="relative z-10 max-w-4xl mx-auto text-center">
            <h1 class="headline font-extrabold leading-[1.1] tracking-tight mb-8 text-[48px] sm:text-[64px] lg:text-[80px]">
                <span class="animate-line-1 text-gray-900 block">Semua Informasi,</span>
                <span class="animate-line-2 block">Dalam <span class="text-sintem">Satu Tempat</span></span>
            </h1>

            <p class="animate-sub text-black-600 leading-relaxed mb-12 text-[16px] sm:text-[18px]">
                <span>Pengumuman real-time</span>
                <span class="mx-3 text-black-300">•</span>
                <span>Laporan aduan cepat</span>
                <span class="mx-3 text-black-300">•</span>
                <span>Data sekolah terintegrasi</span>
            </p>

            <div class="animate-btn">
                <a href="{{ route('login') }}" class="btn-sintem inline-flex items-center justify-center px-10 py-4 rounded text-white font-bold text-lg shadow-xl shadow-purple-500/20 group">
                    Masuk ke SINTEM
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
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
                        Build by <span class="text-sintem font-bold">13 SIJA 1</span>
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
        const toggle = document.getElementById('menu-toggle');
        const menu = document.getElementById('mobile-menu');
        const iconOpen  = document.getElementById('icon-open');
        const iconClose = document.getElementById('icon-close');
        toggle.addEventListener('click', () => {
            const isOpen = menu.classList.toggle('open');
            iconOpen.classList.toggle('hidden', isOpen);
            iconClose.classList.toggle('hidden', !isOpen);
        });

        const vw = window.innerWidth;
        let cfg;
        if (vw >= 1024) {
            cfg = { count: 10, sizeMin: 26, sizeMax: 46, speedMin: 0.038, speedMax: 0.075, opacityMax: 0.55, strokeW: 1.5, morphMin: 1400, morphMax: 3200 };
        } else if (vw >= 640) {
            cfg = { count: 5,  sizeMin: 18, sizeMax: 30, speedMin: 0.030, speedMax: 0.055, opacityMax: 0.35, strokeW: 1.2, morphMin: 1800, morphMax: 3500 };
        } else {
            cfg = { count: 3,  sizeMin: 14, sizeMax: 22, speedMin: 0.025, speedMax: 0.045, opacityMax: 0.22, strokeW: 1.0, morphMin: 2000, morphMax: 4000 };
        }

        const canvas = document.getElementById('falling-canvas');
        const NS = 'http://www.w3.org/2000/svg';
        const COLORS = ['rgba(144,37,251,OP)','rgba(70,23,211,OP)','rgba(168,85,247,OP)','rgba(109,40,217,OP)'];
        const N = 12;

        function makeColor(i) {
            return COLORS[i % COLORS.length].replace('OP', cfg.opacityMax);
        }

        function circlePoints(r,cx,cy){return Array.from({length:N},(_,i)=>{const a=(2*Math.PI*i)/N-Math.PI/2;return[cx+r*Math.cos(a),cy+r*Math.sin(a)];});}
        function squarePoints(s,cx,cy){const h=s/2,c=[[cx-h,cy-h],[cx+h,cy-h],[cx+h,cy+h],[cx-h,cy+h]],pts=[];for(let i=0;i<4;i++){const a=c[i],b=c[(i+1)%4];pts.push(a,[(a[0]+b[0])/2,(a[1]+b[1])/2],[(a[0]*.25+b[0]*.75),(a[1]*.25+b[1]*.75)]);}return pts;}
        function trianglePoints(r,cx,cy){const c=Array.from({length:3},(_,i)=>{const a=(2*Math.PI*i)/3-Math.PI/2;return[cx+r*Math.cos(a),cy+r*Math.sin(a)];});const pts=[];for(let i=0;i<3;i++){const a=c[i],b=c[(i+1)%3];for(let t=0;t<4;t++){const f=t/4;pts.push([a[0]+(b[0]-a[0])*f,a[1]+(b[1]-a[1])*f]);}}return pts;}
        function diamondPoints(r,cx,cy){return Array.from({length:N},(_,i)=>{const a=(2*Math.PI*i)/N-Math.PI/2,st=Math.abs(Math.cos(2*a))*.4+.6;return[cx+r*Math.cos(a)*st,cy+r*Math.sin(a)/st];});}
        function hexPoints(r,cx,cy){const pts=[];for(let i=0;i<6;i++){const a=(2*Math.PI*i)/6-Math.PI/2,a2=(2*Math.PI*(i+.5))/6-Math.PI/2;pts.push([cx+r*Math.cos(a),cy+r*Math.sin(a)],[cx+r*.95*Math.cos(a2),cy+r*.95*Math.sin(a2)]);}return pts;}
        function starPoints(r,cx,cy){const inn=r*.45;return Array.from({length:N},(_,i)=>{const a=(Math.PI*i)/(N/2)-Math.PI/2,rad=i%2===0?r:inn;return[cx+rad*Math.cos(a),cy+rad*Math.sin(a)];});}

        const SHAPES = [circlePoints, squarePoints, trianglePoints, diamondPoints, hexPoints, starPoints];

        function lerp(a,b,t){return a.map((p,i)=>[p[0]+(b[i][0]-p[0])*t, p[1]+(b[i][1]-p[1])*t]);}
        function ease(t){return t<.5?2*t*t:-1+(4-2*t)*t;}
        function toD(pts){return pts.map((p,i)=>`${i===0?'M':'L'}${p[0].toFixed(2)},${p[1].toFixed(2)}`).join(' ')+' Z';}

        class Shape {
            constructor(index, totalCount) {
                this.color = makeColor(index);
                this.laneX = (index / totalCount) * 90 + 5; // 5%–95%

                this._buildDOM();

                this._initFall(index, totalCount);
            }

            _buildDOM() {
                this.size = cfg.sizeMin + Math.random() * (cfg.sizeMax - cfg.sizeMin);
                const s = this.size, c = s/2, r = s/2-2;

                this.svg = document.createElementNS(NS,'svg');
                this.svg.setAttribute('width', s);
                this.svg.setAttribute('height', s);
                this.svg.setAttribute('viewBox', `0 0 ${s} ${s}`);
                this.svg.setAttribute('overflow','visible');
                this.svg.style.cssText = `position:absolute;top:0;left:0;will-change:transform;filter:drop-shadow(0 0 3px ${this.color});`;

                this.pathEl = document.createElementNS(NS,'path');
                this.pathEl.setAttribute('fill','none');
                this.pathEl.setAttribute('stroke', this.color);
                this.pathEl.setAttribute('stroke-width', cfg.strokeW);
                this.pathEl.setAttribute('stroke-linejoin','round');
                this.svg.appendChild(this.pathEl);
                canvas.appendChild(this.svg);

                this.shapeSeq = [0,1,2,3,4,5].sort(()=>Math.random()-.5);
                this.seqIdx = 0;
                this.fromPts = SHAPES[this.shapeSeq[0]](r,c,c);
                this.toPts   = SHAPES[this.shapeSeq[1]](r,c,c);
                this.morphT  = 0;
                this.morphSpeed = cfg.morphMin + Math.random()*(cfg.morphMax - cfg.morphMin);
                this.morphStartTime = null;
            }

            _initFall(index, totalCount) {
                const vh = window.innerHeight;
                this.speed    = cfg.speedMin + Math.random()*(cfg.speedMax - cfg.speedMin);
                this.rotSpeed = (Math.random()-.5)*0.04;
                this.rot      = Math.random()*360;
                this.x = (this.laneX / 100) * window.innerWidth + (Math.random()-0.5)*40;

                const fallDuration = (vh + this.size * 2) / (this.speed * (vh / 1000)); // ms for full fall
                const staggerFraction = index / totalCount; // 0..1
                const staggerMs = staggerFraction * fallDuration;

                this.y = -this.size - 60 + this.speed * staggerMs * (vh / 1000);
                this._lastTs = null;
            }

            reset() {
                this.y = -this.size - 60;
                this.x = (this.laneX / 100) * window.innerWidth + (Math.random()-0.5)*40;
                this.speed    = cfg.speedMin + Math.random()*(cfg.speedMax - cfg.speedMin);
                this.rotSpeed = (Math.random()-.5)*0.04;
                this.morphSpeed = cfg.morphMin + Math.random()*(cfg.morphMax - cfg.morphMin);

                const s=this.size, c=s/2, r=s/2-2;
                this.shapeSeq = [0,1,2,3,4,5].sort(()=>Math.random()-.5);
                this.seqIdx = 0;
                this.fromPts = SHAPES[this.shapeSeq[0]](r,c,c);
                this.toPts   = SHAPES[this.shapeSeq[1]](r,c,c);
                this.morphT  = 0;
                this.morphStartTime = null;
            }

            update(ts) {
                const vh = window.innerHeight;

                if (!this._lastTs) this._lastTs = ts;
                const dt = Math.min(ts - this._lastTs, 50);
                this._lastTs = ts;

                this.y   += this.speed * dt * (vh / 1000);
                this.rot += this.rotSpeed * dt;

                if (this.y > vh + this.size + 60) {
                    this.reset();
                }

                const travel = vh + this.size * 2 + 120;
                const progress = (this.y + this.size + 60) / travel;
                let opacity;
                if (progress < 0.08)      opacity = (progress / 0.08) * cfg.opacityMax;
                else if (progress > 0.88) opacity = ((1 - (progress - 0.88) / 0.12)) * cfg.opacityMax;
                else                      opacity = cfg.opacityMax;

                if (!this.morphStartTime) this.morphStartTime = ts;
                const elapsed = ts - this.morphStartTime;
                this.morphT = ease(Math.min(elapsed / this.morphSpeed, 1));

                if (elapsed >= this.morphSpeed) {
                    this.seqIdx = (this.seqIdx + 1) % this.shapeSeq.length;
                    const next = (this.seqIdx + 1) % this.shapeSeq.length;
                    const s=this.size,c=s/2,r=s/2-2;
                    this.fromPts = SHAPES[this.shapeSeq[this.seqIdx]](r,c,c);
                    this.toPts   = SHAPES[this.shapeSeq[next]](r,c,c);
                    this.morphT  = 0;
                    this.morphStartTime = ts;
                }

                const pts = lerp(this.fromPts, this.toPts, this.morphT);
                this.pathEl.setAttribute('d', toD(pts));

                this.svg.style.transform = `translate(${this.x}px,${this.y}px) rotate(${this.rot}deg)`;
                this.svg.style.opacity = opacity;
            }
        }

        const shapes = Array.from({length: cfg.count}, (_, i) => new Shape(i, cfg.count));

        function loop(ts) {
            shapes.forEach(s => s.update(ts));
            requestAnimationFrame(loop);
        }
        requestAnimationFrame(loop);
    </script>
</body>
</html>