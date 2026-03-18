<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk – SINTEM</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Lato', sans-serif;
            background: #ffffff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .bg-layer {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
        }

        .bg-layer::before {
            content: '';
            position: absolute; inset: 0;
            background:
                radial-gradient(ellipse 900px 650px at 65% -5%, rgba(144,37,251,0.07) 0%, transparent 68%),
                radial-gradient(ellipse 650px 550px at -5% 85%, rgba(70,23,211,0.05) 0%, transparent 68%);
        }

        .dots-pattern {
            position: fixed; inset: 0;
            background-image: radial-gradient(circle, rgba(144,37,251,0.18) 1.5px, transparent 1.5px);
            background-size: 30px 30px;
            pointer-events: none;
            z-index: 0;
            mask-image: radial-gradient(ellipse 80% 80% at 50% 50%, black 10%, transparent 100%);
            animation: driftDots 22s ease-in-out infinite;
        }
        @keyframes driftDots {
            0%, 100% { transform: translate(0, 0); }
            33%  { transform: translate(6px, -8px); }
            66%  { transform: translate(-5px, 6px); }
        }

        /* Grid lines */
        .grid-pattern {
            position: fixed; inset: 0;
            background-image:
                linear-gradient(rgba(144,37,251,0.06) 1px, transparent 1px),
                linear-gradient(90deg, rgba(144,37,251,0.06) 1px, transparent 1px);
            background-size: 64px 64px;
            pointer-events: none;
            z-index: 0;
            mask-image: radial-gradient(ellipse 75% 70% at 50% 50%, black 0%, transparent 100%);
        }

        /* Falling shapes canvas */
        #falling-canvas {
            position: fixed;
            inset: 0;
            pointer-events: none;
            overflow: hidden;
            z-index: 0;
        }

        /* ── ALL CONTENT above bg ── */
        .back-btn, .page-wrap { position: relative; z-index: 10; }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(14px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .back-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 700;
            font-family: 'Lato', sans-serif;
            color: #6b7280;
            text-decoration: none;
            padding: 8px 14px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            background: #fff;
            transition: color 0.18s, border-color 0.18s, background 0.18s, transform 0.18s;
            opacity: 0;
            animation: slideUp 0.45s ease 0.05s forwards;
            z-index: 50;
        }
        .back-btn:hover {
            color: #4f28d9;
            border-color: #c4b5fd;
            background: #faf5ff;
            transform: translateX(-2px);
        }

        .page-wrap {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            padding: 24px 16px;
        }

        .logo-area {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 24px;
            opacity: 0;
            animation: slideUp 0.55s cubic-bezier(0.22,1,0.36,1) 0.10s forwards;
        }
        .logo-img {
            height: 56px;
            width: auto;
            margin-bottom: 14px;
        }
        .welcome-title {
            font-size: 19px;
            font-weight: 900;
            color: #1a1a2e;
            letter-spacing: -0.2px;
        }

        .card {
            width: 100%;
            max-width: 400px;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 28px 28px 24px;
            background: #ffffff;
            opacity: 0;
            animation: slideUp 0.6s cubic-bezier(0.22,1,0.36,1) 0.22s forwards;
        }

        .card-title {
            font-size: 16px;
            font-weight: 900;
            color: #1a1a2e;
            margin-bottom: 4px;
        }
        .card-sub {
            font-size: 13px;
            color: #9ca3af;
            font-weight: 400;
            margin-bottom: 22px;
            line-height: 1.5;
        }

        .field-label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: #374151;
            margin-bottom: 6px;
        }

        .field-nis {
            margin-bottom: 14px;
            opacity: 0;
            animation: slideUp 0.5s cubic-bezier(0.22,1,0.36,1) 0.36s forwards;
        }
        .field-password {
            margin-bottom: 6px;
            opacity: 0;
            animation: slideUp 0.5s cubic-bezier(0.22,1,0.36,1) 0.46s forwards;
        }

        .field-input {
            width: 100%;
            padding: 11px 14px;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            font-size: 14px;
            font-family: 'Lato', sans-serif;
            color: #111;
            background: #fff;
            outline: none;
            transition: border-color 0.18s, box-shadow 0.18s;
        }
        .field-input::placeholder { color: #d1d5db; }
        .field-input:focus {
            border-color: #6d28d9;
            box-shadow: 0 0 0 3px rgba(109,40,217,0.10);
        }

        .pw-wrap { position: relative; }
        .pw-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #d1d5db;
            padding: 0;
            line-height: 0;
            transition: color 0.18s;
        }
        .pw-toggle:hover { color: #6d28d9; }

        .btn-continue {
            display: block;
            width: 100%;
            padding: 13px;
            background: #4f28d9;
            border: none;
            border-radius: 6px;
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            font-family: 'Lato', sans-serif;
            cursor: pointer;
            margin-top: 14px;
            transition: background 0.18s, transform 0.15s, box-shadow 0.18s;
            opacity: 0;
            animation: slideUp 0.5s cubic-bezier(0.22,1,0.36,1) 0.56s forwards;
        }
        .btn-continue:hover {
            background: #3b1fb5;
            box-shadow: 0 6px 20px rgba(79,40,217,0.28);
            transform: translateY(-1px);
        }
        .btn-continue:active { transform: translateY(0); box-shadow: none; }
        .btn-continue:disabled { opacity: 0.7; cursor: not-allowed; transform: none; }

        .forgot-wrap {
            text-align: center;
            margin-top: 16px;
            opacity: 0;
            animation: slideUp 0.45s ease 0.66s forwards;
        }
        .forgot-link {
            font-size: 13px;
            color: #7c3aed;
            text-decoration: underline;
            text-underline-offset: 2px;
            transition: color 0.18s;
        }
        .forgot-link:hover { color: #4f28d9; }

        .error-box {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 6px;
            padding: 10px 14px;
            font-size: 13px;
            color: #dc2626;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .field-error {
            font-size: 12px;
            color: #dc2626;
            margin-top: 4px;
        }
    </style>
</head>

<body>

    <div class="bg-layer"></div>
    <div class="dots-pattern"></div>
    <div class="grid-pattern"></div>
    <div id="falling-canvas"></div>

    {{-- Back button --}}
    <a href="{{ url('/') }}" class="back-btn">
        <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M10 12L6 8l4-4"/>
        </svg>
        Kembali
    </a>

    <div class="page-wrap">

        {{-- Logo --}}
        <div class="logo-area">
            <img src="{{ asset('assets/Logo SINTEM.png') }}" alt="SINTEM" class="logo-img">
            <div class="welcome-title">Selamat datang di Sintem!</div>
        </div>

        {{-- Card --}}
        <div class="card">
            <div class="card-title">Masuk dengan ID Anda</div>
            <div class="card-sub">Akses fitur laporan, barang hilang, dan pengumuman sekolah.</div>

            @if(session('error'))
            <div class="error-box">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke-width="2"/>
                    <path d="M12 8v4m0 4h.01" stroke-width="2.5" stroke-linecap="round"/>
                </svg>
                {{ session('error') }}
            </div>
            @endif

            <form method="POST" action="{{ url('/login') }}" id="loginForm">
                @csrf

                {{-- NIS --}}
                <div class="field-nis">
                    <label class="field-label" for="nis">NIS</label>
                    <input
                        type="text"
                        id="nis"
                        name="nis"
                        class="field-input"
                        placeholder="Masukkan NIS"
                        value="{{ old('nis') }}"
                        autocomplete="username"
                        required
                    >
                    @error('nis')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="field-password">
                    <label class="field-label" for="password">Password</label>
                    <div class="pw-wrap">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="field-input"
                            placeholder="Masukkan password"
                            autocomplete="current-password"
                            style="padding-right: 42px;"
                            required
                        >
                        <button type="button" class="pw-toggle" id="pwToggle" aria-label="Tampilkan password">
                            <svg id="eye-show" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg id="eye-hide" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="btn-continue" id="submitBtn">
                    Continue
                </button>
            </form>

            <div class="forgot-wrap">
                <a href="{{ url('/forgot-password') }}" class="forgot-link">Lupa password?</a>
            </div>
        </div>

    </div>

    <script>
        // Password toggle
        const pwToggle = document.getElementById('pwToggle');
        const pwInput  = document.getElementById('password');
        const eyeShow  = document.getElementById('eye-show');
        const eyeHide  = document.getElementById('eye-hide');
        pwToggle.addEventListener('click', () => {
            const hidden = pwInput.type === 'password';
            pwInput.type = hidden ? 'text' : 'password';
            eyeShow.style.display = hidden ? 'none' : '';
            eyeHide.style.display = hidden ? '' : 'none';
        });

        document.getElementById('loginForm').addEventListener('submit', function () {
            const btn = document.getElementById('submitBtn');
            btn.textContent = 'Memproses...';
            btn.disabled = true;
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

        function makeColor(i) { return COLORS[i % COLORS.length].replace('OP', cfg.opacityMax); }

        function circlePoints(r,cx,cy){return Array.from({length:N},(_,i)=>{const a=(2*Math.PI*i)/N-Math.PI/2;return[cx+r*Math.cos(a),cy+r*Math.sin(a)];});}
        function squarePoints(s,cx,cy){const h=s/2,c=[[cx-h,cy-h],[cx+h,cy-h],[cx+h,cy+h],[cx-h,cy+h]],pts=[];for(let i=0;i<4;i++){const a=c[i],b=c[(i+1)%4];pts.push(a,[(a[0]+b[0])/2,(a[1]+b[1])/2],[(a[0]*.25+b[0]*.75),(a[1]*.25+b[1]*.75)]);}return pts;}
        function trianglePoints(r,cx,cy){const c=Array.from({length:3},(_,i)=>{const a=(2*Math.PI*i)/3-Math.PI/2;return[cx+r*Math.cos(a),cy+r*Math.sin(a)];});const pts=[];for(let i=0;i<3;i++){const a=c[i],b=c[(i+1)%3];for(let t=0;t<4;t++){const f=t/4;pts.push([a[0]+(b[0]-a[0])*f,a[1]+(b[1]-a[1])*f]);}}return pts;}
        function diamondPoints(r,cx,cy){return Array.from({length:N},(_,i)=>{const a=(2*Math.PI*i)/N-Math.PI/2,st=Math.abs(Math.cos(2*a))*.4+.6;return[cx+r*Math.cos(a)*st,cy+r*Math.sin(a)/st];});}
        function hexPoints(r,cx,cy){const pts=[];for(let i=0;i<6;i++){const a=(2*Math.PI*i)/6-Math.PI/2,a2=(2*Math.PI*(i+.5))/6-Math.PI/2;pts.push([cx+r*Math.cos(a),cy+r*Math.sin(a)],[cx+r*.95*Math.cos(a2),cy+r*.95*Math.sin(a2)]);}return pts;}
        function starPoints(r,cx,cy){const inn=r*.45;return Array.from({length:N},(_,i)=>{const a=(Math.PI*i)/(N/2)-Math.PI/2,rad=i%2===0?r:inn;return[cx+rad*Math.cos(a),cy+rad*Math.sin(a)];});}

        const SHAPES = [circlePoints, squarePoints, trianglePoints, diamondPoints, hexPoints, starPoints];

        function lerp(a,b,t){return a.map((p,i)=>[p[0]+(b[i][0]-p[0])*t,p[1]+(b[i][1]-p[1])*t]);}
        function ease(t){return t<.5?2*t*t:-1+(4-2*t)*t;}
        function toD(pts){return pts.map((p,i)=>`${i===0?'M':'L'}${p[0].toFixed(2)},${p[1].toFixed(2)}`).join(' ')+' Z';}

        class Shape {
            constructor(index, total) {
                this.color = makeColor(index);
                this.laneX = (index / total) * 90 + 5;
                this._buildDOM();
                this._initFall(index, total);
            }

            _buildDOM() {
                this.size = cfg.sizeMin + Math.random() * (cfg.sizeMax - cfg.sizeMin);
                const s=this.size, c=s/2, r=s/2-2;

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

            _initFall(index, total) {
                const vh = window.innerHeight;
                this.speed    = cfg.speedMin + Math.random()*(cfg.speedMax - cfg.speedMin);
                this.rotSpeed = (Math.random()-.5)*0.04;
                this.rot      = Math.random()*360;
                this.x = (this.laneX / 100) * window.innerWidth + (Math.random()-0.5)*40;

                const fallDuration = (vh + this.size * 2) / (this.speed * (vh / 1000));
                const staggerMs = (index / total) * fallDuration;
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

                if (this.y > vh + this.size + 60) this.reset();

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
                    const s=this.size, c=s/2, r=s/2-2;
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