{{-- resources/views/laporan/anonim.blade.php --}}
@extends('layouts.app')

@section('title', 'Lapor Aduan – SINTEM')

@section('topbar')
<div style="display:flex; align-items:center; justify-content:space-between; padding: 14px 32px; border-bottom: 1px solid #f0f0f5; background:#ffffff;">
    <p style="font-size:13.5px; font-weight:700; color:#1a1a2e;">
        Selamat datang, {{ Auth::user()->name ?? 'Pengguna' }}!
    </p>
    <a href="{{ route('temuan.buat') }}" class="btn-laporkan">
        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="8" stroke-width="1.8"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-4.35-4.35"/>
        </svg>
        Lapor Temuan
    </a>
</div>
@endsection

@section('header', 'Lapor Aduan')
@section('subheader', 'Unggah Laporan Aduan')

@push('styles')
<style>
    .main-content { background: #f7f7fb !important; }
    .page-header  { background: #ffffff !important; padding: 16px 32px 14px !important; }
    .page-body    { background: #f7f7fb !important; padding: 32px !important; }

    .btn-laporkan {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        background: linear-gradient(135deg, #9025FB, #4617D3);
        color: #fff;
        font-size: 13px;
        font-weight: 700;
        font-family: 'Lato', sans-serif;
        border-radius: 6px;
        text-decoration: none;
        box-shadow: 0 2px 8px rgba(109,40,217,0.2);
        transition: opacity 0.15s, transform 0.15s;
    }
    .btn-laporkan:hover { opacity: 0.88; transform: translateY(-1px); }

    /* ── Form card ── */
    .form-card {
        max-width: 680px;
        margin: 0 auto;
        background: #fff;
        border: 1px solid #ebebf0;
        border-radius: 10px;
        padding: 28px 32px 32px;
    }
    .form-card-title {
        font-size: 15px;
        font-weight: 700;
        color: #1a1a2e;
        margin-bottom: 20px;
    }
    .form-group { margin-bottom: 18px; }
    .form-label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 6px;
    }

    /* ── Anonymous notice banner ── */
    .anon-notice {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 12px 14px;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 7px;
        margin-bottom: 20px;
    }
    .anon-notice svg { flex-shrink: 0; margin-top: 1px; }
    .anon-notice-text {
        font-size: 12.5px;
        color: #166534;
        line-height: 1.5;
    }
    .anon-notice-title {
        font-weight: 700;
        margin-bottom: 2px;
    }

    /* ── Category custom dropdown ── */
    .cat-dd-wrap {
        position: relative;
    }
    .cat-dd-trigger {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 9px 12px;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        font-size: 13px;
        font-family: 'Lato', sans-serif;
        font-weight: 500;
        color: #374151;
        background: #fff;
        cursor: pointer;
        outline: none;
        transition: border-color 0.12s, box-shadow 0.12s;
        text-align: left;
    }
    .cat-dd-trigger.placeholder { color: #c4c4cc; }
    .cat-dd-trigger:hover { border-color: #c4b5fd; }
    .cat-dd-wrap.open .cat-dd-trigger {
        border-color: #7c3aed;
        box-shadow: 0 0 0 2px rgba(124,58,237,0.1);
    }
    .cat-dd-chevron { transition: transform 0.2s ease; flex-shrink: 0; }
    .cat-dd-wrap.open .cat-dd-chevron { transform: rotate(180deg); }

    .cat-dd-menu {
        display: none;
        position: absolute;
        top: calc(100% + 4px);
        left: 0;
        right: 0;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        z-index: 100;
        overflow: hidden;
        padding: 4px;
    }
    .cat-dd-wrap.open .cat-dd-menu {
        display: block;
        animation: ddFadeIn 0.15s ease;
    }
    @keyframes ddFadeIn {
        from { opacity: 0; transform: translateY(-4px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .cat-dd-option {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        width: 100%;
        padding: 9px 10px;
        border: none;
        border-radius: 4px;
        background: none;
        cursor: pointer;
        text-align: left;
        transition: background 0.1s;
    }
    .cat-dd-option:hover { background: #f4f0ff; }
    .cat-dd-option.selected { background: #f4f0ff; }
    .cat-dd-option-icon {
        width: 28px;
        height: 28px;
        border-radius: 6px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: 1px;
    }
    .cat-dd-option-info { flex: 1; }
    .cat-dd-option-name {
        font-size: 13px;
        font-family: 'Lato', sans-serif;
        font-weight: 600;
        color: #1a1a2e;
        line-height: 1.3;
    }
    .cat-dd-option.selected .cat-dd-option-name { color: #4f28d9; }
    .cat-dd-option-desc {
        font-size: 11.5px;
        font-family: 'Lato', sans-serif;
        color: #9ca3af;
        margin-top: 1px;
        line-height: 1.4;
    }
    .cat-check { opacity: 0; flex-shrink: 0; margin-top: 6px; }
    .cat-dd-option.selected .cat-check { opacity: 1; }

    /* category icon colors */
    .cat-icon-1 { background: #fce7f3; }
    .cat-icon-2 { background: #dbeafe; }
    .cat-icon-3 { background: #fef3c7; }
    .cat-icon-4 { background: #d1fae5; }

    /* ── Text input ── */
    .form-input {
        width: 100%;
        padding: 9px 12px;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        font-size: 13px;
        font-family: 'Lato', sans-serif;
        color: #1a1a2e;
        background: #fff;
        outline: none;
        transition: border-color 0.12s, box-shadow 0.12s;
    }
    .form-input::placeholder { color: #c4c4cc; }
    .form-input:focus {
        border-color: #7c3aed;
        box-shadow: 0 0 0 2px rgba(124,58,237,0.1);
    }

    /* ── Rich text editor ── */
    .editor-wrap {
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        overflow: hidden;
        transition: border-color 0.12s, box-shadow 0.12s;
    }
    .editor-wrap:focus-within {
        border-color: #7c3aed;
        box-shadow: 0 0 0 2px rgba(124,58,237,0.1);
    }
    .editor-toolbar {
        display: flex;
        align-items: center;
        gap: 2px;
        padding: 7px 10px;
        background: #f9f9fc;
        border-bottom: 1px solid #f0f0f5;
        flex-wrap: wrap;
    }
    .toolbar-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border: none;
        border-radius: 4px;
        background: none;
        cursor: pointer;
        color: #6b7280;
        font-size: 13px;
        font-family: 'Lato', sans-serif;
        font-weight: 700;
        transition: background 0.1s, color 0.1s;
    }
    .toolbar-btn:hover { background: #ede9fe; color: #4f28d9; }
    .toolbar-divider { width: 1px; height: 18px; background: #e5e7eb; margin: 0 3px; flex-shrink: 0; }
    .editor-body {
        min-height: 180px;
        padding: 12px 14px;
        font-size: 13px;
        font-family: 'Lato', sans-serif;
        color: #374151;
        line-height: 1.65;
        outline: none;
        background: #fff;
    }
    .editor-body:empty::before {
        content: attr(data-placeholder);
        color: #c4c4cc;
        pointer-events: none;
    }
    #reportContentInput { display: none; }

    /* ── Upload zone ── */
    .upload-zone {
        border: 1.5px dashed #d1d5db;
        border-radius: 6px;
        padding: 32px 20px;
        text-align: center;
        cursor: pointer;
        transition: border-color 0.15s, background 0.15s;
        position: relative;
    }
    .upload-zone:hover, .upload-zone.dragging { border-color: #7c3aed; background: #faf8ff; }
    .upload-zone input[type="file"] {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
        width: 100%;
        height: 100%;
    }
    .upload-icon { width: 36px; height: 36px; margin: 0 auto 10px; color: #9ca3af; }
    .upload-text { font-size: 13px; color: #6b7280; margin-bottom: 10px; }
    .upload-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 16px;
        background: linear-gradient(135deg, #9025FB, #4617D3);
        color: #fff;
        font-size: 12.5px;
        font-weight: 700;
        font-family: 'Lato', sans-serif;
        border-radius: 5px;
        border: none;
        pointer-events: none;
    }
    .upload-hint { margin-top: 10px; font-size: 11.5px; color: #9ca3af; }
    .upload-previews { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 10px; }
    .upload-preview-item {
        position: relative;
        width: 72px; height: 72px;
        border-radius: 6px;
        overflow: hidden;
        border: 1px solid #e5e7eb;
    }
    .upload-preview-item img { width: 100%; height: 100%; object-fit: cover; }
    .upload-preview-remove {
        position: absolute;
        top: 3px; right: 3px;
        width: 18px; height: 18px;
        background: rgba(0,0,0,0.5);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border: none;
        color: #fff;
    }

    /* ── Actions ── */
    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 24px;
        padding-top: 20px;
        border-top: 1px solid #f0f0f5;
    }
    .btn-batal {
        padding: 9px 22px;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        font-family: 'Lato', sans-serif;
        color: #374151;
        background: #fff;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        transition: border-color 0.12s, background 0.12s;
    }
    .btn-batal:hover { border-color: #c4b5fd; background: #faf8ff; color: #4f28d9; }
    .btn-unggah {
        padding: 9px 22px;
        background: linear-gradient(135deg, #9025FB, #4617D3);
        color: #fff;
        font-size: 13px;
        font-weight: 700;
        font-family: 'Lato', sans-serif;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 2px 8px rgba(109,40,217,0.2);
        transition: opacity 0.15s, transform 0.15s;
    }
    .btn-unggah:hover { opacity: 0.88; transform: translateY(-1px); }
</style>
@endpush

@section('content')
<div class="form-card">
    <div class="form-card-title">Unggah Laporan</div>

    {{-- Anonymous notice --}}
    <div class="anon-notice">
        <svg width="15" height="15" fill="none" stroke="#16a34a" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
        </svg>
        <div class="anon-notice-text">
            <div class="anon-notice-title">Laporan ini bersifat anonim</div>
            Identitasmu tidak akan ditampilkan kepada siapapun. Simpan nomor tiket yang akan diberikan setelah pengiriman untuk melacak status laporanmu.
        </div>
    </div>

    <form method="POST" action="{{ route('laporan.anonim.store') }}" enctype="multipart/form-data" id="aduanForm">
        @csrf

        {{-- Category dropdown --}}
        <div class="form-group">
            <label class="form-label">Kategori Laporan</label>
            <div class="cat-dd-wrap" id="catDropdown">
                <button type="button" class="cat-dd-trigger placeholder" id="catTrigger" onclick="toggleCatDD()">
                    <span id="catLabel">Pilih kategori laporan...</span>
                    <svg class="cat-dd-chevron" width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="cat-dd-menu" id="catMenu">

                    <button type="button" class="cat-dd-option" onclick="selectCat(1, 'Perundungan / Bullying')">
                        <div class="cat-dd-option-icon cat-icon-1">
                            <svg width="13" height="13" fill="none" stroke="#9d174d" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                                <circle cx="9" cy="7" r="4" stroke-width="1.8"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
                            </svg>
                        </div>
                        <div class="cat-dd-option-info">
                            <div class="cat-dd-option-name">Perundungan / Bullying</div>
                            <div class="cat-dd-option-desc">Intimidasi, kekerasan fisik/verbal antar siswa</div>
                        </div>
                        <svg class="cat-check" width="13" height="13" fill="none" stroke="#4f28d9" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </button>

                    <button type="button" class="cat-dd-option" onclick="selectCat(2, 'Fasilitas Sekolah')">
                        <div class="cat-dd-option-icon cat-icon-2">
                            <svg width="13" height="13" fill="none" stroke="#1d4ed8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                                <polyline stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" points="9 22 9 12 15 12 15 22"/>
                            </svg>
                        </div>
                        <div class="cat-dd-option-info">
                            <div class="cat-dd-option-name">Fasilitas Sekolah</div>
                            <div class="cat-dd-option-desc">Kerusakan atau masalah pada infrastruktur sekolah</div>
                        </div>
                        <svg class="cat-check" width="13" height="13" fill="none" stroke="#4f28d9" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </button>

                    <button type="button" class="cat-dd-option" onclick="selectCat(3, 'Kedisiplinan')">
                        <div class="cat-dd-option-icon cat-icon-3">
                            <svg width="13" height="13" fill="none" stroke="#92400e" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                                <rect x="9" y="3" width="6" height="4" rx="1" stroke-width="1.8"/>
                                <line x1="9" y1="12" x2="15" y2="12" stroke-width="1.8" stroke-linecap="round"/>
                                <line x1="9" y1="16" x2="13" y2="16" stroke-width="1.8" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <div class="cat-dd-option-info">
                            <div class="cat-dd-option-name">Kedisiplinan</div>
                            <div class="cat-dd-option-desc">Pelanggaran tata tertib sekolah oleh siswa</div>
                        </div>
                        <svg class="cat-check" width="13" height="13" fill="none" stroke="#4f28d9" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </button>

                    <button type="button" class="cat-dd-option" onclick="selectCat(4, 'Masalah KBM')">
                        <div class="cat-dd-option-icon cat-icon-4">
                            <svg width="13" height="13" fill="none" stroke="#065f46" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 20h9M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/>
                            </svg>
                        </div>
                        <div class="cat-dd-option-info">
                            <div class="cat-dd-option-name">Masalah KBM</div>
                            <div class="cat-dd-option-desc">Gangguan dalam kegiatan belajar mengajar di kelas</div>
                        </div>
                        <svg class="cat-check" width="13" height="13" fill="none" stroke="#4f28d9" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </button>

                </div>
            </div>
            <input type="hidden" name="category_id" id="categoryInput" value="{{ old('category_id') }}">
        </div>

        {{-- Judul --}}
        <div class="form-group">
            <label class="form-label" for="judul">Judul</label>
            <input type="text" id="judul" name="title" class="form-input"
                   placeholder="Masukkan judul laporan..."
                   value="{{ old('title') }}" required>
        </div>

        {{-- Deskripsi --}}
        <div class="form-group">
            <label class="form-label">Deskripsi</label>
            <div class="editor-wrap">
                <div class="editor-toolbar">
                    <button type="button" class="toolbar-btn" onclick="execCmd('bold')"><b>B</b></button>
                    <button type="button" class="toolbar-btn" onclick="execCmd('italic')"><i>I</i></button>
                    <button type="button" class="toolbar-btn" onclick="execCmd('underline')"><u>U</u></button>
                    <div class="toolbar-divider"></div>
                    <button type="button" class="toolbar-btn" onclick="execCmd('justifyLeft')">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6" stroke-width="2" stroke-linecap="round"/><line x1="3" y1="12" x2="15" y2="12" stroke-width="2" stroke-linecap="round"/><line x1="3" y1="18" x2="18" y2="18" stroke-width="2" stroke-linecap="round"/></svg>
                    </button>
                    <button type="button" class="toolbar-btn" onclick="execCmd('justifyCenter')">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6" stroke-width="2" stroke-linecap="round"/><line x1="6" y1="12" x2="18" y2="12" stroke-width="2" stroke-linecap="round"/><line x1="4" y1="18" x2="20" y2="18" stroke-width="2" stroke-linecap="round"/></svg>
                    </button>
                    <button type="button" class="toolbar-btn" onclick="execCmd('justifyRight')">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6" stroke-width="2" stroke-linecap="round"/><line x1="9" y1="12" x2="21" y2="12" stroke-width="2" stroke-linecap="round"/><line x1="6" y1="18" x2="21" y2="18" stroke-width="2" stroke-linecap="round"/></svg>
                    </button>
                    <button type="button" class="toolbar-btn" onclick="execCmd('insertOrderedList')">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><line x1="10" y1="6" x2="21" y2="6" stroke-width="2" stroke-linecap="round"/><line x1="10" y1="12" x2="21" y2="12" stroke-width="2" stroke-linecap="round"/><line x1="10" y1="18" x2="21" y2="18" stroke-width="2" stroke-linecap="round"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h1v4M4 10h2"/></svg>
                    </button>
                    <div class="toolbar-divider"></div>
                    <button type="button" class="toolbar-btn">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="1.8"/><path stroke-linecap="round" stroke-width="1.8" d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9" stroke-width="2.5" stroke-linecap="round"/><line x1="15" y1="9" x2="15.01" y2="9" stroke-width="2.5" stroke-linecap="round"/></svg>
                    </button>
                </div>
                <div class="editor-body" id="editorBody" contenteditable="true"
                     data-placeholder="Masukkan deskripsi laporan..."
                     oninput="syncEditor()"></div>
            </div>
            <textarea name="report_content" id="reportContentInput">{{ old('report_content') }}</textarea>
        </div>

        {{-- Upload --}}
        <div class="form-group">
            <label class="form-label">Upload File</label>
            <div class="upload-zone" id="uploadZone"
                 ondragover="onDragOver(event)" ondragleave="onDragLeave(event)" ondrop="onDrop(event)">
                <input type="file" name="photo" id="fileInput" accept="image/png,image/jpeg"
                       onchange="handleFiles(this.files)">
                <div id="uploadPlaceholder">
                    <div class="upload-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:100%;height:100%;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    </div>
                    <div class="upload-text">Unggah gambar anda disini, atau</div>
                    <div class="upload-btn">Cari di komputer</div>
                </div>
                <div class="upload-previews" id="uploadPreviews"></div>
            </div>
            <div class="upload-hint">Upload file max 5mb, PNG/JPG format</div>
        </div>

        {{-- Actions --}}
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
    // ── Category dropdown ──
    function toggleCatDD() {
        document.getElementById('catDropdown').classList.toggle('open');
    }
    function selectCat(id, name) {
        document.getElementById('categoryInput').value = id;
        document.getElementById('catLabel').textContent = name;
        document.getElementById('catTrigger').classList.remove('placeholder');
        document.querySelectorAll('.cat-dd-option').forEach(opt => {
            opt.classList.toggle('selected', opt.getAttribute('onclick').includes('selectCat(' + id + ','));
        });
        document.getElementById('catDropdown').classList.remove('open');
    }
    document.addEventListener('click', e => {
        const dd = document.getElementById('catDropdown');
        if (dd && !dd.contains(e.target)) dd.classList.remove('open');
    });

    // ── Rich text editor ──
    function execCmd(cmd) {
        document.execCommand(cmd, false, null);
        document.getElementById('editorBody').focus();
        syncEditor();
    }
    function syncEditor() {
        document.getElementById('reportContentInput').value = document.getElementById('editorBody').innerText;
    }
    document.getElementById('aduanForm').addEventListener('submit', syncEditor);

    // ── File upload ──
    let selectedFiles = [];
    function handleFiles(fileList) {
        Array.from(fileList).forEach(file => {
            if (!['image/png','image/jpeg'].includes(file.type)) return;
            if (file.size > 5 * 1024 * 1024) return;
            selectedFiles.push(file);
        });
        renderPreviews();
    }
    function renderPreviews() {
        const container = document.getElementById('uploadPreviews');
        const placeholder = document.getElementById('uploadPlaceholder');
        container.innerHTML = '';
        if (selectedFiles.length) {
            placeholder.style.display = 'none';
            selectedFiles.forEach((file, idx) => {
                const reader = new FileReader();
                reader.onload = e => {
                    const item = document.createElement('div');
                    item.className = 'upload-preview-item';
                    item.innerHTML = `<img src="${e.target.result}" alt="${file.name}"><button type="button" class="upload-preview-remove" onclick="removeFile(${idx})"><svg width="8" height="8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg></button>`;
                    container.appendChild(item);
                };
                reader.readAsDataURL(file);
            });
        } else {
            placeholder.style.display = '';
        }
    }
    function removeFile(idx) { selectedFiles.splice(idx, 1); renderPreviews(); }
    function onDragOver(e)  { e.preventDefault(); document.getElementById('uploadZone').classList.add('dragging'); }
    function onDragLeave()  { document.getElementById('uploadZone').classList.remove('dragging'); }
    function onDrop(e)      { e.preventDefault(); document.getElementById('uploadZone').classList.remove('dragging'); handleFiles(e.dataTransfer.files); }
</script>
@endpush