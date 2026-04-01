{{-- resources/views/laporan/buat.blade.php --}}
{{-- This is the ANONYMOUS laporan page — accessible from sidebar "Buat Laporan" and topbar dropdown "Laporan Anonim" --}}
@extends('layouts.app')

@section('title', 'Buat Laporan – SINTEM')

@section('topbar')
    @include('components.topbar')
@endsection

@section('header', 'Buat Laporan')
@section('subheader', 'Unggah Laporan Aduan')

@push('styles')
<style>
    .main-content { background: #f7f7fb !important; }
    .page-header  { background: #ffffff !important; padding: 16px 32px 14px !important; }
    .page-body    { background: #f7f7fb !important; padding: 32px !important; overflow-y: auto !important; }

    .form-card {
        max-width: 680px; margin: 0 auto;
        background: #fff; border: 1px solid #ebebf0;
        border-radius: 10px; padding: 28px 32px 32px;
    }
    .form-card-title { font-size: 15px; font-weight: 700; color: #1a1a2e; margin-bottom: 20px; }
    .form-group  { margin-bottom: 18px; }
    .form-label  { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }

    /* ── Anonymous notice ── */
    .anon-notice {
        display: flex; align-items: flex-start; gap: 10px;
        padding: 12px 14px;
        background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 7px;
        margin-bottom: 20px;
    }
    .anon-notice-text { font-size: 12.5px; color: #166534; line-height: 1.5; }
    .anon-notice-title { font-weight: 700; margin-bottom: 2px; }

    /* ── Category dropdown ── */
    .cat-wrap { position: relative; }
    .cat-trigger {
        width: 100%; display: flex; align-items: center; justify-content: space-between;
        padding: 9px 12px; border: 1px solid #e5e7eb; border-radius: 6px;
        font-size: 13px; font-family: 'Lato', sans-serif; font-weight: 500;
        color: #c4c4cc; background: #fff; cursor: pointer; outline: none; text-align: left;
        transition: border-color 0.12s, box-shadow 0.12s;
    }
    .cat-trigger.has-value { color: #1a1a2e; }
    .cat-trigger:hover { border-color: #c4b5fd; }
    .cat-wrap.open .cat-trigger { border-color: #7c3aed; box-shadow: 0 0 0 2px rgba(124,58,237,0.1); }
    .cat-chevron { flex-shrink: 0; transition: transform 0.2s ease; }
    .cat-wrap.open .cat-chevron { transform: rotate(180deg); }

    .cat-menu {
        display: none; position: absolute; top: calc(100% + 4px); left: 0; right: 0;
        background: #fff; border: 1px solid #e5e7eb; border-radius: 8px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.09); z-index: 100; padding: 5px;
    }
    .cat-wrap.open .cat-menu { display: block; animation: catFadeIn 0.15s ease; }
    @keyframes catFadeIn { from{opacity:0;transform:translateY(-4px)} to{opacity:1;transform:translateY(0)} }

    .cat-option {
        display: flex; align-items: flex-start; gap: 10px;
        width: 100%; padding: 9px 10px; border: none; border-radius: 5px;
        background: none; cursor: pointer; text-align: left;
        transition: background 0.1s;
    }
    .cat-option:hover   { background: #f4f0ff; }
    .cat-option.sel     { background: #f4f0ff; }
    .cat-opt-icon {
        width: 30px; height: 30px; border-radius: 6px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center; margin-top: 1px;
    }
    .cat-c1 { background: #fce7f3; }
    .cat-c2 { background: #dbeafe; }
    .cat-c3 { background: #fef3c7; }
    .cat-c4 { background: #d1fae5; }
    .cat-opt-info { flex: 1; min-width: 0; }
    .cat-opt-name { font-size: 13px; font-family: 'Lato', sans-serif; font-weight: 600; color: #1a1a2e; line-height: 1.3; }
    .cat-option.sel .cat-opt-name { color: #4f28d9; }
    .cat-opt-desc { font-size: 11.5px; font-family: 'Lato', sans-serif; color: #9ca3af; margin-top: 2px; line-height: 1.4; }
    .cat-check { opacity: 0; flex-shrink: 0; margin-top: 7px; }
    .cat-option.sel .cat-check { opacity: 1; }

    /* input */
    .form-input {
        width: 100%; padding: 9px 12px; border: 1px solid #e5e7eb; border-radius: 6px;
        font-size: 13px; font-family: 'Lato', sans-serif; color: #1a1a2e; background: #fff;
        outline: none; transition: border-color 0.12s, box-shadow 0.12s;
    }
    .form-input::placeholder { color: #c4c4cc; }
    .form-input:focus { border-color: #7c3aed; box-shadow: 0 0 0 2px rgba(124,58,237,0.1); }

    /* editor */
    .editor-wrap { border: 1px solid #e5e7eb; border-radius: 6px; overflow: hidden; transition: border-color 0.12s, box-shadow 0.12s; }
    .editor-wrap:focus-within { border-color: #7c3aed; box-shadow: 0 0 0 2px rgba(124,58,237,0.1); }
    .editor-toolbar { display: flex; align-items: center; gap: 2px; padding: 7px 10px; background: #f9f9fc; border-bottom: 1px solid #f0f0f5; flex-wrap: wrap; }
    .toolbar-btn { display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border: none; border-radius: 4px; background: none; cursor: pointer; color: #6b7280; font-size: 13px; font-family: 'Lato', sans-serif; font-weight: 700; transition: background 0.1s, color 0.1s; }
    .toolbar-btn:hover { background: #ede9fe; color: #4f28d9; }
    .toolbar-btn.active { background: #ede9fe; color: #4f28d9; }
    .tb-div { width: 1px; height: 18px; background: #e5e7eb; margin: 0 3px; flex-shrink: 0; }
    .editor-body { min-height: 180px; padding: 12px 14px; font-size: 13px; font-family: 'Lato', sans-serif; color: #374151; line-height: 1.65; outline: none; background: #fff; }
    .editor-body:empty::before { content: attr(data-placeholder); color: #c4c4cc; pointer-events: none; }
    #an-hidden { display: none; }

    /* upload */
    .upload-zone { border: 1.5px dashed #d1d5db; border-radius: 6px; padding: 32px 20px; text-align: center; cursor: pointer; transition: border-color 0.15s, background 0.15s; position: relative; }
    .upload-zone:hover, .upload-zone.drag-over { border-color: #7c3aed; background: #faf8ff; }
    .upload-zone input[type="file"] { position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%; }
    .up-icon { width: 36px; height: 36px; margin: 0 auto 10px; color: #9ca3af; }
    .up-text { font-size: 13px; color: #6b7280; margin-bottom: 10px; }
    .up-btn  { display: inline-flex; align-items: center; gap: 6px; padding: 7px 16px; background: linear-gradient(135deg, #9025FB, #4617D3); color: #fff; font-size: 12.5px; font-weight: 700; font-family: 'Lato', sans-serif; border-radius: 5px; border: none; pointer-events: none; }
    .up-hint { margin-top: 10px; font-size: 11.5px; color: #9ca3af; }
    .up-previews { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 10px; }
    .up-thumb { position: relative; width: 72px; height: 72px; border-radius: 6px; overflow: hidden; border: 1px solid #e5e7eb; }
    .up-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .up-remove { position: absolute; top: 3px; right: 3px; width: 18px; height: 18px; background: rgba(0,0,0,0.5); border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; border: none; color: #fff; }

    /* actions */
    .form-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px; padding-top: 20px; border-top: 1px solid #f0f0f5; }
    .btn-batal { padding: 9px 22px; border: 1px solid #e5e7eb; border-radius: 6px; font-size: 13px; font-weight: 600; font-family: 'Lato', sans-serif; color: #374151; background: #fff; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; transition: border-color 0.12s, background 0.12s, color 0.12s; }
    .btn-batal:hover { border-color: #c4b5fd; background: #faf8ff; color: #4f28d9; }
    .btn-unggah { padding: 9px 22px; background: linear-gradient(135deg, #9025FB, #4617D3); color: #fff; font-size: 13px; font-weight: 700; font-family: 'Lato', sans-serif; border: none; border-radius: 6px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 2px 8px rgba(109,40,217,0.2); transition: opacity 0.15s, transform 0.15s; }
    .btn-unggah:hover { opacity: 0.88; transform: translateY(-1px); }
</style>
@endpush

@section('content')
<div class="form-card">
    <div class="form-card-title">Unggah Laporan</div>

    {{-- Anonymous notice --}}
    <div class="anon-notice">
        <svg width="15" height="15" fill="none" stroke="#16a34a" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
        </svg>
        <div class="anon-notice-text">
            <div class="anon-notice-title">Laporan ini bersifat anonim</div>
            Identitasmu tidak akan ditampilkan kepada siapapun. Simpan nomor tiket yang akan diberikan setelah pengiriman untuk melacak status laporanmu.
        </div>
    </div>

    <form method="POST" action="{{ route('laporan.store') }}" enctype="multipart/form-data" id="aduanForm">
        @csrf

        {{-- Kategori dropdown --}}
        <div class="form-group">
            <label class="form-label">Kategori Laporan</label>
            <div class="cat-wrap" id="catWrap">
                <button type="button" class="cat-trigger" id="catTrigger" onclick="toggleCat()">
                    <span id="catLabel">Pilih kategori laporan...</span>
                    <svg class="cat-chevron" width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="cat-menu" id="catMenu">

                    <button type="button" class="cat-option" data-id="1" data-name="Perundungan / Bullying" onclick="pickCat(this)">
                        <div class="cat-opt-icon cat-c1">
                            <svg width="13" height="13" fill="none" stroke="#9d174d" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4" stroke-width="1.8"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                        </div>
                        <div class="cat-opt-info">
                            <div class="cat-opt-name">Perundungan / Bullying</div>
                            <div class="cat-opt-desc">Intimidasi, kekerasan fisik atau verbal antar siswa</div>
                        </div>
                        <svg class="cat-check" width="13" height="13" fill="none" stroke="#4f28d9" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/></svg>
                    </button>

                    <button type="button" class="cat-option" data-id="2" data-name="Fasilitas Sekolah" onclick="pickCat(this)">
                        <div class="cat-opt-icon cat-c2">
                            <svg width="13" height="13" fill="none" stroke="#1d4ed8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" points="9 22 9 12 15 12 15 22"/></svg>
                        </div>
                        <div class="cat-opt-info">
                            <div class="cat-opt-name">Fasilitas Sekolah</div>
                            <div class="cat-opt-desc">Kerusakan atau masalah pada infrastruktur sekolah</div>
                        </div>
                        <svg class="cat-check" width="13" height="13" fill="none" stroke="#4f28d9" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/></svg>
                    </button>

                    <button type="button" class="cat-option" data-id="3" data-name="Kedisiplinan" onclick="pickCat(this)">
                        <div class="cat-opt-icon cat-c3">
                            <svg width="13" height="13" fill="none" stroke="#92400e" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1" stroke-width="1.8"/><line x1="9" y1="12" x2="15" y2="12" stroke-width="1.8" stroke-linecap="round"/><line x1="9" y1="16" x2="13" y2="16" stroke-width="1.8" stroke-linecap="round"/></svg>
                        </div>
                        <div class="cat-opt-info">
                            <div class="cat-opt-name">Kedisiplinan</div>
                            <div class="cat-opt-desc">Pelanggaran tata tertib sekolah oleh siswa</div>
                        </div>
                        <svg class="cat-check" width="13" height="13" fill="none" stroke="#4f28d9" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/></svg>
                    </button>

                    <button type="button" class="cat-option" data-id="4" data-name="Masalah KBM" onclick="pickCat(this)">
                        <div class="cat-opt-icon cat-c4">
                            <svg width="13" height="13" fill="none" stroke="#065f46" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 20h9M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                        </div>
                        <div class="cat-opt-info">
                            <div class="cat-opt-name">Masalah KBM</div>
                            <div class="cat-opt-desc">Gangguan dalam kegiatan belajar mengajar</div>
                        </div>
                        <svg class="cat-check" width="13" height="13" fill="none" stroke="#4f28d9" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/></svg>
                    </button>

                </div>
            </div>
            <input type="hidden" name="category_id" id="catInput" value="{{ old('category_id') }}">
        </div>

        {{-- Judul --}}
        <div class="form-group">
            <label class="form-label" for="an-title">Judul</label>
            <input type="text" id="an-title" name="title" class="form-input"
                   placeholder="Masukkan judul laporan..." value="{{ old('title') }}" required>
        </div>

        {{-- Deskripsi --}}
        <div class="form-group">
            <label class="form-label">Deskripsi</label>
            <div class="editor-wrap">
                <div class="editor-toolbar">
                    <button type="button" class="toolbar-btn" data-cmd="bold"><b>B</b></button>
                    <button type="button" class="toolbar-btn" data-cmd="italic"><i>I</i></button>
                    <button type="button" class="toolbar-btn" data-cmd="underline"><u>U</u></button>
                    <div class="tb-div"></div>
                    <button type="button" class="toolbar-btn" data-cmd="justifyLeft"><svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6" stroke-width="2" stroke-linecap="round"/><line x1="3" y1="12" x2="15" y2="12" stroke-width="2" stroke-linecap="round"/><line x1="3" y1="18" x2="18" y2="18" stroke-width="2" stroke-linecap="round"/></svg></button>
                    <button type="button" class="toolbar-btn" data-cmd="justifyCenter"><svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6" stroke-width="2" stroke-linecap="round"/><line x1="6" y1="12" x2="18" y2="12" stroke-width="2" stroke-linecap="round"/><line x1="4" y1="18" x2="20" y2="18" stroke-width="2" stroke-linecap="round"/></svg></button>
                    <button type="button" class="toolbar-btn" data-cmd="justifyRight"><svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6" stroke-width="2" stroke-linecap="round"/><line x1="9" y1="12" x2="21" y2="12" stroke-width="2" stroke-linecap="round"/><line x1="6" y1="18" x2="21" y2="18" stroke-width="2" stroke-linecap="round"/></svg></button>
                    <button type="button" class="toolbar-btn" data-cmd="insertOrderedList"><svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><line x1="10" y1="6" x2="21" y2="6" stroke-width="2" stroke-linecap="round"/><line x1="10" y1="12" x2="21" y2="12" stroke-width="2" stroke-linecap="round"/><line x1="10" y1="18" x2="21" y2="18" stroke-width="2" stroke-linecap="round"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h1v4M4 10h2"/></svg></button>
                    <div class="tb-div"></div>
                    <button type="button" class="toolbar-btn"><svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="1.8"/><path stroke-linecap="round" stroke-width="1.8" d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9" stroke-width="2.5" stroke-linecap="round"/><line x1="15" y1="9" x2="15.01" y2="9" stroke-width="2.5" stroke-linecap="round"/></svg></button>
                </div>
                <div class="editor-body" id="an-editor" contenteditable="true" data-target="an-hidden"
                     data-placeholder="Masukkan deskripsi laporan..."></div>
            </div>
            <textarea name="report_content" id="an-hidden">{{ old('report_content') }}</textarea>
        </div>

        {{-- Upload (optional) --}}
        <div class="form-group">
            <label class="form-label">Upload File <span style="font-weight:400;color:#9ca3af;font-size:12px;">(opsional)</span></label>
            <div class="upload-zone" id="up-zone" ondragover="upOver(event)" ondragleave="upLeave()" ondrop="upDrop(event)">
                <input type="file" name="photo" id="up-input" accept="image/png,image/jpeg" onchange="upHandle(this.files)">
                <div id="up-ph">
                    <div class="up-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:100%;height:100%"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg></div>
                    <div class="up-text">Unggah gambar anda disini, atau</div>
                    <div class="up-btn">Cari di komputer</div>
                </div>
                <div class="up-previews" id="up-prev"></div>
            </div>
            <div class="up-hint">Upload file max 5mb, PNG/JPG format</div>
        </div>

        <div class="form-actions">
            <a href="{{ route('pengumuman.index') }}" class="btn-batal">Batal</a>
            <button type="submit" class="btn-unggah">
                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Unggah
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // ── Rich text editor ──
    document.querySelectorAll('.toolbar-btn[data-cmd]').forEach(btn => {
        btn.addEventListener('mousedown', e => {
            e.preventDefault(); // don't steal focus
        });
        btn.addEventListener('click', () => {
            const cmd = btn.dataset.cmd;
            const editor = btn.closest('.editor-wrap').querySelector('.editor-body');
            editor.focus();
            document.execCommand(cmd, false, null);
            syncEditors();
            updateToolbarStates();
        });
    });

    function syncEditors() {
        document.querySelectorAll('.editor-body').forEach(editor => {
            const hidden = document.getElementById(editor.dataset.target);
            if (hidden) hidden.value = editor.innerHTML;
        });
    }

    function updateToolbarStates() {
        ['bold','italic','underline'].forEach(cmd => {
            document.querySelectorAll(`[data-cmd="${cmd}"]`).forEach(btn => {
                btn.classList.toggle('active', document.queryCommandState(cmd));
            });
        });
    }

    document.querySelectorAll('.editor-body').forEach(editor => {
        editor.addEventListener('keyup', updateToolbarStates);
        editor.addEventListener('mouseup', updateToolbarStates);
        editor.addEventListener('input', syncEditors);
    });

    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', syncEditors);
    });

    // ── Category dropdown ──
    function toggleCat() { document.getElementById('catWrap').classList.toggle('open'); }
    function pickCat(el) {
        const id   = el.dataset.id;
        const name = el.dataset.name;
        document.getElementById('catInput').value = id;
        document.getElementById('catLabel').textContent = name;
        document.getElementById('catTrigger').classList.add('has-value');
        document.querySelectorAll('.cat-option').forEach(o => o.classList.remove('sel'));
        el.classList.add('sel');
        document.getElementById('catWrap').classList.remove('open');
    }
    document.addEventListener('click', e => {
        const w = document.getElementById('catWrap');
        if (w && !w.contains(e.target)) w.classList.remove('open');
    });

    // ── Editor ── (handled by syncEditors above)

    // ── Upload ──
    let upFiles=[];
    function upHandle(list){Array.from(list).forEach(f=>{if(!['image/png','image/jpeg'].includes(f.type)||f.size>5*1024*1024)return;upFiles.push(f);});upRender();}
    function upRender(){const c=document.getElementById('up-prev'),p=document.getElementById('up-ph');c.innerHTML='';if(upFiles.length){p.style.display='none';upFiles.forEach((f,i)=>{const r=new FileReader();r.onload=e=>{const d=document.createElement('div');d.className='up-thumb';d.innerHTML=`<img src="${e.target.result}"><button type="button" class="up-remove" onclick="upRm(${i})"><svg width="8" height="8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg></button>`;c.appendChild(d);};r.readAsDataURL(f);});}else{p.style.display='';}}
    function upRm(i){upFiles.splice(i,1);upRender();}
    function upOver(e){e.preventDefault();document.getElementById('up-zone').classList.add('drag-over');}
    function upLeave(){document.getElementById('up-zone').classList.remove('drag-over');}
    function upDrop(e){e.preventDefault();document.getElementById('up-zone').classList.remove('drag-over');upHandle(e.dataTransfer.files);}
</script>
@endpush