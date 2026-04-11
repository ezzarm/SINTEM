{{-- resources/views/admin/pengumuman/buat.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Buat Pengumuman – Admin SINTEM')

@section('topbar')
<div style="display:flex;align-items:center;justify-content:space-between;padding:14px 32px;border-bottom:1px solid #f0f0f5;background:#fff;">
    <p style="font-size:13.5px;font-weight:700;color:#1a1a2e;">Selamat Datang, {{ Auth::user()->name }}!</p>
</div>
@endsection

@section('header', 'Buat Pengumuman')
@section('subheader', 'Publish pengumuman baru untuk siswa.')

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

    .pub-notice {
        display: flex; align-items: flex-start; gap: 10px;
        padding: 12px 14px;
        background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 7px;
        margin-bottom: 20px;
    }
    .pub-notice-text { font-size: 12.5px; color: #1d4ed8; line-height: 1.5; }
    .pub-notice-title { font-weight: 700; margin-bottom: 2px; }

    .form-input {
        width: 100%; padding: 9px 12px; border: 1px solid #e5e7eb; border-radius: 6px;
        font-size: 13px; font-family: 'Lato', sans-serif; color: #1a1a2e; background: #fff;
        outline: none; transition: border-color 0.12s, box-shadow 0.12s;
        box-sizing: border-box;
    }
    .form-input::placeholder { color: #c4c4cc; }
    .form-input:focus { border-color: #7c3aed; box-shadow: 0 0 0 2px rgba(124,58,237,0.1); }

    .editor-wrap {
        border: 1px solid #e5e7eb; border-radius: 6px; overflow: hidden;
        transition: border-color 0.12s, box-shadow 0.12s;
    }
    .editor-wrap:focus-within { border-color: #7c3aed; box-shadow: 0 0 0 2px rgba(124,58,237,0.1); }
    .editor-toolbar {
        display: flex; align-items: center; gap: 2px;
        padding: 7px 10px; background: #f9f9fc;
        border-bottom: 1px solid #f0f0f5; flex-wrap: wrap;
    }
    .toolbar-btn {
        display: inline-flex; align-items: center; justify-content: center;
        width: 28px; height: 28px; border: none; border-radius: 4px;
        background: none; cursor: pointer; color: #6b7280;
        font-size: 13px; font-family: 'Lato', sans-serif; font-weight: 700;
        transition: background 0.1s, color 0.1s;
    }
    .toolbar-btn:hover { background: #ede9fe; color: #4f28d9; }
    .toolbar-btn.active { background: #ede9fe; color: #4f28d9; }
    .tb-div { width: 1px; height: 18px; background: #e5e7eb; margin: 0 3px; flex-shrink: 0; }
    .editor-body {
        min-height: 180px; padding: 12px 14px;
        font-size: 13px; font-family: 'Lato', sans-serif;
        color: #374151; line-height: 1.65; outline: none; background: #fff;
    }
    .editor-body:empty::before { content: attr(data-placeholder); color: #c4c4cc; pointer-events: none; }
    #content-hidden { display: none; }

    .upload-zone {
        border: 1.5px dashed #d1d5db; border-radius: 6px; padding: 32px 20px;
        text-align: center; cursor: pointer; transition: border-color 0.15s, background 0.15s;
        display: block;
    }
    .upload-zone:hover, .upload-zone.drag-over { border-color: #7c3aed; background: #faf8ff; }
    #bannerInput { display: none; }
    .up-icon { width: 36px; height: 36px; margin: 0 auto 10px; color: #9ca3af; }
    .up-text { font-size: 13px; color: #6b7280; margin-bottom: 10px; }
    .up-btn {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 7px 16px; background: linear-gradient(135deg, #9025FB, #4617D3);
        color: #fff; font-size: 12.5px; font-weight: 700;
        font-family: 'Lato', sans-serif; border-radius: 5px; border: none;
    }
    .up-hint { margin-top: 10px; font-size: 11.5px; color: #9ca3af; }
    .up-preview { margin-top: 10px; display: none; }
    .up-preview img { width: 100%; max-height: 200px; object-fit: cover; border-radius: 6px; }
    .up-preview-remove {
        display: inline-flex; align-items: center; gap: 5px;
        margin-top: 8px; font-size: 12px; color: #dc2626; cursor: pointer;
        background: none; border: none; font-family: 'Lato', sans-serif;
    }

    .attach-tabs { display: flex; gap: 4px; margin-bottom: 12px; }
    .attach-tab {
        padding: 5px 12px; border-radius: 5px; font-size: 12.5px; font-weight: 600;
        border: 1px solid #e5e7eb; background: #fff; color: #6b7280; cursor: pointer;
        transition: background 0.12s, color 0.12s;
    }
    .attach-tab.active { background: #ede9fe; color: #4f28d9; border-color: #c4b5fd; }
    .attach-pane { display: none; }
    .attach-pane.active { display: block; }

    .form-actions {
        display: flex; justify-content: flex-end; gap: 10px;
        margin-top: 24px; padding-top: 20px; border-top: 1px solid #f0f0f5;
    }
    .btn-batal {
        padding: 9px 22px; border: 1px solid #e5e7eb; border-radius: 6px;
        font-size: 13px; font-weight: 600; font-family: 'Lato', sans-serif;
        color: #374151; background: #fff; cursor: pointer; text-decoration: none;
        display: inline-flex; align-items: center;
        transition: border-color 0.12s, background 0.12s, color 0.12s;
    }
    .btn-batal:hover { border-color: #c4b5fd; background: #faf8ff; color: #4f28d9; }
    .btn-draft {
        padding: 9px 22px; border: 1px solid #e5e7eb; border-radius: 6px;
        font-size: 13px; font-weight: 700; font-family: 'Lato', sans-serif;
        color: #6b7280; background: #fff; cursor: pointer;
        display: inline-flex; align-items: center; gap: 6px;
        transition: border-color 0.12s, background 0.12s, color 0.12s;
    }
    .btn-draft:hover { border-color: #c4b5fd; background: #faf8ff; color: #4f28d9; }
    .btn-publish-submit {
        padding: 9px 22px; background: linear-gradient(135deg, #9025FB, #4617D3);
        color: #fff; font-size: 13px; font-weight: 700;
        font-family: 'Lato', sans-serif; border: none; border-radius: 6px;
        cursor: pointer; display: inline-flex; align-items: center; gap: 6px;
        box-shadow: 0 2px 8px rgba(109,40,217,0.2);
        transition: opacity 0.15s, transform 0.15s;
    }
    .btn-publish-submit:hover { opacity: 0.88; transform: translateY(-1px); }

    /* ══════════════════════════════════════════════
       BREAKPOINTS
       ▸ Tablet  768–1023px : reduce card padding
       ▸ Mobile  < 768px   : full-width action buttons
       ▸ XS      < 480px   : minimal padding
    ══════════════════════════════════════════════ */

    /* ── Tablet ── */
    @media (max-width: 1023px) {
        .page-body  { padding: 20px !important; }
        .form-card  { padding: 22px 20px 24px; }
    }

    /* ── Mobile ── */
    @media (max-width: 767px) {
        .page-body   { padding: 14px 16px !important; }
        .form-card   { padding: 18px 16px 20px; }
        .form-actions { justify-content: stretch; flex-wrap: wrap; }
        .form-actions .btn-cancel,
        .form-actions .btn-save { flex: 1; justify-content: center; }
    }

    /* ── Small mobile ── */
    @media (max-width: 479px) {
        .page-body  { padding: 12px !important; }
        .form-card  { padding: 14px 12px 16px; border-radius: 8px; }
    }
</style>
@endpush

@section('content')
<div class="form-card">
    <div class="form-card-title">Buat Pengumuman Baru</div>

    <div class="pub-notice">
        <svg width="15" height="15" fill="none" stroke="#1d4ed8" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div class="pub-notice-text">
            <div class="pub-notice-title">Pengumuman akan tampil di beranda siswa</div>
            Pilih "Publish" untuk langsung menampilkan, atau "Simpan Draft" untuk menyimpan tanpa mempublish.
        </div>
    </div>

    {{-- Tampilkan error validasi --}}
    @if ($errors->any())
        <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:7px;padding:12px 14px;margin-bottom:18px;">
            <p style="font-size:13px;font-weight:700;color:#dc2626;margin-bottom:6px;">Terjadi kesalahan:</p>
            <ul style="margin:0;padding-left:18px;">
                @foreach ($errors->all() as $error)
                    <li style="font-size:12.5px;color:#dc2626;">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Success message --}}
    @if (session('success'))
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:7px;padding:12px 14px;margin-bottom:18px;">
            <p style="font-size:13px;color:#16a34a;">{{ session('success') }}</p>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.pengumuman.store') }}" enctype="multipart/form-data" id="buatForm">
        @csrf

        {{-- Foto / Banner --}}
        <div class="form-group">
            <label class="form-label">Foto / Banner <span style="font-weight:400;color:#9ca3af;font-size:12px;">(opsional)</span></label>
            <label class="upload-zone" id="bannerZone" for="bannerInput"
                   ondragover="upOver(event)" ondragleave="upLeave(event)" ondrop="upDrop(event)">
                <input type="file" name="photo" id="bannerInput" accept="image/png,image/jpeg,image/webp"
                       onchange="upHandle(this.files)">
                <div id="bannerPh">
                    <div class="up-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:100%;height:100%">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                    </div>
                    <div class="up-text">Unggah foto atau banner pengumuman, atau</div>
                    <div class="up-btn">
                        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        Cari di komputer
                    </div>
                </div>
                <div class="up-preview" id="bannerPreview">
                    <img id="bannerPreviewImg" src="" alt="preview">
                    <button type="button" class="up-preview-remove"
                            onclick="event.preventDefault(); event.stopPropagation(); removePreview()">
                        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        Hapus foto
                    </button>
                </div>
            </label>
            <div class="up-hint">PNG, JPG, WEBP — maks. 5MB</div>
            @error('photo')
                <p style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</p>
            @enderror
        </div>

        {{-- Judul --}}
        <div class="form-group">
            <label class="form-label" for="pub-title">Judul Pengumuman <span style="color:#dc2626;">*</span></label>
            <input type="text" id="pub-title" name="title" class="form-input"
                   placeholder="Masukkan judul pengumuman..." value="{{ old('title') }}" required>
            @error('title')
                <p style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</p>
            @enderror
        </div>

        {{-- Isi / Konten --}}
        <div class="form-group">
            <label class="form-label">Isi Pengumuman <span style="color:#dc2626;">*</span></label>
            <div class="editor-wrap">
                <div class="editor-toolbar">
                    <button type="button" class="toolbar-btn" data-cmd="bold"><b>B</b></button>
                    <button type="button" class="toolbar-btn" data-cmd="italic"><i>I</i></button>
                    <button type="button" class="toolbar-btn" data-cmd="underline"><u>U</u></button>
                    <div class="tb-div"></div>
                    <button type="button" class="toolbar-btn" data-cmd="justifyLeft">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6" stroke-width="2" stroke-linecap="round"/><line x1="3" y1="12" x2="15" y2="12" stroke-width="2" stroke-linecap="round"/><line x1="3" y1="18" x2="18" y2="18" stroke-width="2" stroke-linecap="round"/></svg>
                    </button>
                    <button type="button" class="toolbar-btn" data-cmd="justifyCenter">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6" stroke-width="2" stroke-linecap="round"/><line x1="6" y1="12" x2="18" y2="12" stroke-width="2" stroke-linecap="round"/><line x1="4" y1="18" x2="20" y2="18" stroke-width="2" stroke-linecap="round"/></svg>
                    </button>
                    <button type="button" class="toolbar-btn" data-cmd="insertOrderedList">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><line x1="10" y1="6" x2="21" y2="6" stroke-width="2" stroke-linecap="round"/><line x1="10" y1="12" x2="21" y2="12" stroke-width="2" stroke-linecap="round"/><line x1="10" y1="18" x2="21" y2="18" stroke-width="2" stroke-linecap="round"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h1v4M4 10h2"/></svg>
                    </button>
                    <button type="button" class="toolbar-btn" data-cmd="insertUnorderedList">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><line x1="9" y1="6" x2="20" y2="6" stroke-width="2" stroke-linecap="round"/><line x1="9" y1="12" x2="20" y2="12" stroke-width="2" stroke-linecap="round"/><line x1="9" y1="18" x2="20" y2="18" stroke-width="2" stroke-linecap="round"/><circle cx="4" cy="6" r="1.5" fill="currentColor"/><circle cx="4" cy="12" r="1.5" fill="currentColor"/><circle cx="4" cy="18" r="1.5" fill="currentColor"/></svg>
                    </button>
                </div>
                <div class="editor-body" id="pub-editor" contenteditable="true"
                     data-target="content-hidden"
                     data-placeholder="Tulis isi pengumuman di sini..."></div>
            </div>
            <textarea name="content" id="content-hidden">{{ old('content') }}</textarea>
            @error('content')
                <p style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</p>
            @enderror
        </div>

        {{-- Lampiran (opsional) --}}
        <div class="form-group">
            <label class="form-label">Lampiran <span style="font-weight:400;color:#9ca3af;font-size:12px;">(opsional)</span></label>
            <div class="attach-tabs">
                <button type="button" class="attach-tab active" onclick="switchTab('file')">📎 File</button>
                <button type="button" class="attach-tab"        onclick="switchTab('link')">🔗 Link</button>
            </div>
            <div class="attach-pane active" id="pane-file">
                <input type="file" name="attachment_file" class="form-input"
                       style="padding:7px;" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx">
                <p style="font-size:11.5px;color:#9ca3af;margin-top:4px;">Maks. 5MB. Format: PDF, Word, Excel, PowerPoint.</p>
                <input type="text" name="attachment_label" class="form-input" style="margin-top:8px;"
                       placeholder="Label lampiran (opsional)">
            </div>
            <div class="attach-pane" id="pane-link">
                <input type="url" name="link_url" class="form-input" placeholder="https://drive.google.com/...">
                <input type="text" name="link_label" class="form-input" style="margin-top:8px;"
                       placeholder="Label link (contoh: Lihat di Google Drive)">
            </div>
        </div>

        <div class="form-actions">
            <a href="{{ route('admin.pengumuman.index') }}" class="btn-batal">Batal</a>
            <button type="submit" name="is_published" value="0" class="btn-draft">
                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1" stroke-width="2"/></svg>
                Simpan Draft
            </button>
            <button type="submit" name="is_published" value="1" class="btn-publish-submit">
                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Unggah
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    /* ── Rich text editor ── */
    document.querySelectorAll('.toolbar-btn[data-cmd]').forEach(btn => {
        btn.addEventListener('mousedown', e => e.preventDefault());
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

    // sync content sebelum submit
    document.getElementById('buatForm').addEventListener('submit', syncEditors);

    /* ── Banner upload ── */
    function upHandle(files) {
        const file = files[0];
        if (!file) return;
        if (!['image/png','image/jpeg','image/webp'].includes(file.type)) {
            alert('Format tidak didukung. Gunakan PNG, JPG, atau WEBP.');
            document.getElementById('bannerInput').value = '';
            return;
        }
        if (file.size > 5 * 1024 * 1024) {
            alert('Ukuran file melebihi 5MB.');
            document.getElementById('bannerInput').value = '';
            return;
        }
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('bannerPh').style.display = 'none';
            document.getElementById('bannerPreviewImg').src = e.target.result;
            document.getElementById('bannerPreview').style.display = 'block';
        };
        reader.readAsDataURL(file);
    }

    function removePreview() {
        document.getElementById('bannerInput').value = '';
        document.getElementById('bannerPreview').style.display = 'none';
        document.getElementById('bannerPh').style.display = '';
    }

    function upOver(e)  { e.preventDefault(); document.getElementById('bannerZone').classList.add('drag-over'); }
    function upLeave(e) { e.preventDefault(); document.getElementById('bannerZone').classList.remove('drag-over'); }
    function upDrop(e)  {
        e.preventDefault();
        document.getElementById('bannerZone').classList.remove('drag-over');
        const dt = new DataTransfer();
        dt.items.add(e.dataTransfer.files[0]);
        document.getElementById('bannerInput').files = dt.files;
        upHandle(e.dataTransfer.files);
    }

    /* ── Attachment tabs ── */
    function switchTab(tab) {
        document.querySelectorAll('.attach-tab').forEach((t,i) => t.classList.toggle('active', ['file','link'][i] === tab));
        document.querySelectorAll('.attach-pane').forEach(p => p.classList.remove('active'));
        document.getElementById('pane-' + tab).classList.add('active');
    }
</script>
@endpush