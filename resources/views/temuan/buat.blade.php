{{-- resources/views/temuan/buat.blade.php --}}
@extends('layouts.app')

@section('title', 'Lapor Temuan – SINTEM')

@section('topbar')
    @include('components.topbar')
@endsection

@section('header', 'Lapor Temuan')
@section('subheader', 'Unggah Laporan Temuan')

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

    /* type toggle */
    .type-selector { display: flex; gap: 8px; margin-bottom: 18px; }
    .type-option {
        flex: 1; display: flex; align-items: center; justify-content: center; gap: 7px;
        padding: 9px 14px; border: 1.5px solid #e5e7eb; border-radius: 7px;
        font-size: 13px; font-weight: 600; font-family: 'Lato', sans-serif;
        color: #6b7280; background: #fff; cursor: pointer;
        transition: border-color 0.15s, color 0.15s, background 0.15s;
    }
    .type-option:hover             { border-color: #c4b5fd; color: #4f28d9; background: #faf8ff; }
    .type-option.active-temuan     { border-color: #d97706; color: #92400e; background: #fffbeb; }
    .type-option.active-kehilangan { border-color: #db2777; color: #9d174d; background: #fdf2f8; }

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
    .tb-div { width: 1px; height: 18px; background: #e5e7eb; margin: 0 3px; flex-shrink: 0; }
    .editor-body { min-height: 180px; padding: 12px 14px; font-size: 13px; font-family: 'Lato', sans-serif; color: #374151; line-height: 1.65; outline: none; background: #fff; }
    .editor-body:empty::before { content: attr(data-placeholder); color: #c4c4cc; pointer-events: none; }
    #tb-hidden-desc { display: none; }

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

    <form method="POST" action="{{ route('temuan.store') }}" enctype="multipart/form-data" id="temuanForm">
        @csrf

        {{-- Temuan / Kehilangan --}}
        <div class="type-selector">
            <button type="button" class="type-option active-temuan" id="btn-temuan" onclick="selectType('found')">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8" stroke-width="1.8"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-4.35-4.35"/>
                    <line x1="11" y1="8" x2="11" y2="14" stroke-width="1.8" stroke-linecap="round"/>
                    <line x1="8"  y1="11" x2="14" y2="11" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
                Temuan
            </button>
            <button type="button" class="type-option" id="btn-hilang" onclick="selectType('lost')">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke-width="1.8"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4"/>
                    <line x1="12" y1="16" x2="12.01" y2="16" stroke-width="2.5" stroke-linecap="round"/>
                </svg>
                Kehilangan
            </button>
        </div>
        <input type="hidden" name="type" id="typeInput" value="found">

        {{-- Judul --}}
        <div class="form-group">
            <label class="form-label" for="item_name">Judul</label>
            <input type="text" id="item_name" name="item_name" class="form-input"
                   placeholder="Masukkan judul pengumuman..." value="{{ old('item_name') }}" required>
        </div>

        {{-- Deskripsi --}}
        <div class="form-group">
            <label class="form-label">Deskripsi</label>
            <div class="editor-wrap">
                <div class="editor-toolbar">
                    <button type="button" class="toolbar-btn" onclick="ec('bold')"><b>B</b></button>
                    <button type="button" class="toolbar-btn" onclick="ec('italic')"><i>I</i></button>
                    <button type="button" class="toolbar-btn" onclick="ec('underline')"><u>U</u></button>
                    <div class="tb-div"></div>
                    <button type="button" class="toolbar-btn" onclick="ec('justifyLeft')"><svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6" stroke-width="2" stroke-linecap="round"/><line x1="3" y1="12" x2="15" y2="12" stroke-width="2" stroke-linecap="round"/><line x1="3" y1="18" x2="18" y2="18" stroke-width="2" stroke-linecap="round"/></svg></button>
                    <button type="button" class="toolbar-btn" onclick="ec('justifyCenter')"><svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6" stroke-width="2" stroke-linecap="round"/><line x1="6" y1="12" x2="18" y2="12" stroke-width="2" stroke-linecap="round"/><line x1="4" y1="18" x2="20" y2="18" stroke-width="2" stroke-linecap="round"/></svg></button>
                    <button type="button" class="toolbar-btn" onclick="ec('justifyRight')"><svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6" stroke-width="2" stroke-linecap="round"/><line x1="9" y1="12" x2="21" y2="12" stroke-width="2" stroke-linecap="round"/><line x1="6" y1="18" x2="21" y2="18" stroke-width="2" stroke-linecap="round"/></svg></button>
                    <button type="button" class="toolbar-btn" onclick="ec('insertOrderedList')"><svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><line x1="10" y1="6" x2="21" y2="6" stroke-width="2" stroke-linecap="round"/><line x1="10" y1="12" x2="21" y2="12" stroke-width="2" stroke-linecap="round"/><line x1="10" y1="18" x2="21" y2="18" stroke-width="2" stroke-linecap="round"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h1v4M4 10h2"/></svg></button>
                    <div class="tb-div"></div>
                    <button type="button" class="toolbar-btn"><svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="1.8"/><path stroke-linecap="round" stroke-width="1.8" d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9" stroke-width="2.5" stroke-linecap="round"/><line x1="15" y1="9" x2="15.01" y2="9" stroke-width="2.5" stroke-linecap="round"/></svg></button>
                </div>
                <div class="editor-body" id="tb-editor" contenteditable="true"
                     data-placeholder="Masukkan deskripsi pengumuman..." oninput="syncDesc()"></div>
            </div>
            <textarea name="description" id="tb-hidden-desc">{{ old('description') }}</textarea>
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
            <a href="{{ route('temuan.index') }}" class="btn-batal">Batal</a>
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
    function selectType(t) {
        document.getElementById('typeInput').value = t;
        document.getElementById('btn-temuan').className = 'type-option' + (t==='found' ? ' active-temuan' : '');
        document.getElementById('btn-hilang').className = 'type-option' + (t==='lost'  ? ' active-kehilangan' : '');
    }
    function ec(cmd) { document.execCommand(cmd,false,null); document.getElementById('tb-editor').focus(); syncDesc(); }
    function syncDesc() { document.getElementById('tb-hidden-desc').value = document.getElementById('tb-editor').innerText; }
    document.getElementById('temuanForm').addEventListener('submit', syncDesc);

    let upFiles=[];
    function upHandle(list){Array.from(list).forEach(f=>{if(!['image/png','image/jpeg'].includes(f.type)||f.size>5*1024*1024)return;upFiles.push(f);});upRender();}
    function upRender(){const c=document.getElementById('up-prev'),p=document.getElementById('up-ph');c.innerHTML='';if(upFiles.length){p.style.display='none';upFiles.forEach((f,i)=>{const r=new FileReader();r.onload=e=>{const d=document.createElement('div');d.className='up-thumb';d.innerHTML=`<img src="${e.target.result}"><button type="button" class="up-remove" onclick="upRm(${i})"><svg width="8" height="8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg></button>`;c.appendChild(d);};r.readAsDataURL(f);});}else{p.style.display='';}}
    function upRm(i){upFiles.splice(i,1);upRender();}
    function upOver(e){e.preventDefault();document.getElementById('up-zone').classList.add('drag-over');}
    function upLeave(){document.getElementById('up-zone').classList.remove('drag-over');}
    function upDrop(e){e.preventDefault();document.getElementById('up-zone').classList.remove('drag-over');upHandle(e.dataTransfer.files);}
</script>
@endpush