{{-- resources/views/temuan/buat.blade.php --}}
@extends('layouts.app')

@section('title', 'Lapor Temuan – SINTEM')

@section('topbar')
    @include('components.topbar')
@endsection

@section('header', 'Lapor Temuan')
@section('subheader', 'Unggah Laporan Temuan atau Kehilangan')

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
    .toolbar-btn.active { background: #ede9fe; color: #4f28d9; }
    .tb-div { width: 1px; height: 18px; background: #e5e7eb; margin: 0 3px; flex-shrink: 0; }
    .editor-body { min-height: 150px; padding: 12px 14px; font-size: 13px; font-family: 'Lato', sans-serif; color: #374151; line-height: 1.65; outline: none; background: #fff; }
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
    <div class="form-card-title">Unggah Laporan Temuan / Kehilangan</div>

    {{-- ACTION DISESUAIKAN KE temuan.store AGAR SESUAI DENGAN web.php --}}
    <form method="POST" action="{{ route('temuan.store') }}" enctype="multipart/form-data" id="temuanForm">
        @csrf

        {{-- Selector Tipe --}}
        <div class="type-selector">
            <button type="button" class="type-option active-temuan" id="btn-temuan" onclick="selectType('found')">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8" stroke-width="1.8"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-4.35-4.35"/>
                </svg>
                Temuan
            </button>
            <button type="button" class="type-option" id="btn-hilang" onclick="selectType('lost')">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke-width="1.8"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4"/>
                </svg>
                Kehilangan
            </button>
        </div>
        <input type="hidden" name="type" id="typeInput" value="found">

        {{-- Nama Barang / Judul --}}
        <div class="form-group">
            <label class="form-label" for="item_name">Nama Barang</label>
            <input type="text" id="item_name" name="item_name" class="form-input" 
                   placeholder="Contoh: Kunci Motor Beat Hitam" value="{{ old('item_name') }}" required>
        </div>

        {{-- Lokasi Temu/Hilang --}}
        <div class="form-group">
            <label class="form-label" for="found_at">Lokasi Kejadian</label>
            <input type="text" id="found_at" name="found_at" class="form-input" 
                   placeholder="Contoh: Parkiran depan, Kantin, atau Gedung A..." value="{{ old('found_at') }}">
        </div>

        {{-- Deskripsi Rich Text --}}
        <div class="form-group">
            <label class="form-label">Keterangan / Ciri-ciri Barang</label>
            <div class="editor-wrap">
                <div class="editor-toolbar">
                    <button type="button" class="toolbar-btn" data-cmd="bold"><b>B</b></button>
                    <button type="button" class="toolbar-btn" data-cmd="italic"><i>I</i></button>
                    <button type="button" class="toolbar-btn" data-cmd="underline"><u>U</u></button>
                    <div class="tb-div"></div>
                    <button type="button" class="toolbar-btn" data-cmd="insertOrderedList">List</button>
                </div>
                <div class="editor-body" id="tb-editor" contenteditable="true" data-target="tb-hidden-desc"
                     data-placeholder="Masukkan detail barang..."></div>
            </div>
            <textarea name="description" id="tb-hidden-desc" style="display:none;">{{ old('description') }}</textarea>
        </div>

        {{-- Upload Foto --}}
        <div class="form-group">
            <label class="form-label">Foto Barang</label>
            <div class="upload-zone" id="up-zone" ondragover="upOver(event)" ondragleave="upLeave()" ondrop="upDrop(event)">
                <input type="file" name="photo" id="up-input" accept="image/png,image/jpeg" onchange="upHandle(this.files)">
                <div id="up-ph">
                    <div class="up-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:100%;height:100%"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4"/></svg></div>
                    <div class="up-text">Klik atau tarik foto ke sini</div>
                    <div class="up-btn">Pilih File</div>
                </div>
                <div class="up-previews" id="up-prev"></div>
            </div>
            <div class="up-hint">Maksimal 5MB, format PNG/JPG</div>
        </div>

        <div class="form-actions">
            <a href="{{ route('temuan.index') }}" class="btn-batal">Batal</a>
            <button type="submit" class="btn-unggah">
                Simpan Laporan
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // Rich text editor sync
    const editor = document.getElementById('tb-editor');
    const hiddenInput = document.getElementById('tb-hidden-desc');

    editor.addEventListener('input', () => {
        hiddenInput.value = editor.innerHTML;
    });

    document.querySelectorAll('.toolbar-btn[data-cmd]').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const cmd = btn.dataset.cmd;
            document.execCommand(cmd, false, null);
            editor.focus();
            hiddenInput.value = editor.innerHTML;
        });
    });

    // Toggle Type
    function selectType(t) {
        document.getElementById('typeInput').value = t;
        document.getElementById('btn-temuan').className = 'type-option' + (t==='found' ? ' active-temuan' : '');
        document.getElementById('btn-hilang').className = 'type-option' + (t==='lost'  ? ' active-kehilangan' : '');
    }

    // Preview Photo
    let upFiles=[];
    function upHandle(list){
        Array.from(list).forEach(f=>{
            if(!['image/png','image/jpeg'].includes(f.type)||f.size>5*1024*1024) return;
            upFiles = [f]; 
        });
        upRender();
    }
    function upRender(){
        const c=document.getElementById('up-prev'), p=document.getElementById('up-ph');
        c.innerHTML='';
        if(upFiles.length){
            p.style.display='none';
            const f = upFiles[0];
            const r=new FileReader();
            r.onload=e=>{
                const d=document.createElement('div');
                d.className='up-thumb';
                d.innerHTML=`<img src="${e.target.result}"><button type="button" class="up-remove" onclick="upRm(0)">×</button>`;
                c.appendChild(d);
            };
            r.readAsDataURL(f);
        } else {
            p.style.display='';
        }
    }
    function upRm(i){ upFiles=[]; upRender(); document.getElementById('up-input').value = ''; }
    function upOver(e){ e.preventDefault(); document.getElementById('up-zone').classList.add('drag-over'); }
    function upLeave(){ document.getElementById('up-zone').classList.remove('drag-over'); }
    function upDrop(e){ e.preventDefault(); upLeave(); upHandle(e.dataTransfer.files); }
</script>
@endpush