@extends('layouts.app')

@section('title', 'Import Users dari Excel')

@section('content')
<style>
    main {
        max-width: 100% !important;
        padding: 20px 28px !important;
        background: var(--bg-color);
    }

    .users-page {
        display: flex;
        flex-direction: column;
        gap: 24px;
        max-width: 1200px;
    }

    .users-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .users-header > div {
        flex: 1;
        min-width: 0;
    }

    .users-header h1 {
        margin: 0;
        font-size: 28px;
        font-weight: 700;
        color: var(--text-color);
        letter-spacing: -0.5px;
    }

    .users-header p {
        margin: 8px 0 0 0 !important;
        font-size: 14px;
        color: var(--muted);
    }

    .users-card {
        background: var(--panel-bg);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        transition: box-shadow 0.3s ease;
    }

    .users-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .users-card-body {
        padding: 32px;
    }

    .import-container {
        display: grid;
        gap: 18px;
    }

    .import-template-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 14px 28px;
        border-radius: 12px;
        border: 2px solid var(--border-color);
        background: transparent;
        color: var(--text-color);
        text-decoration: none;
        font-weight: 700;
        font-size: 15px;
        width: fit-content;
        transition: all 0.3s ease;
        letter-spacing: 0.3px;
    }

    .import-template-btn:hover {
        border-color: var(--primary);
        background: var(--primary);
        color: #fff;
        transform: translateY(-2px);
    }

    .import-drop-zone {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 240px;
        padding: 20px;
        border: 2px dashed var(--border-color);
        border-radius: 18px;
        background: var(--bg-color);
        color: var(--muted);
        text-align: center;
        cursor: pointer;
        gap: 12px;
    }

    .import-drop-zone.dragover {
        background: rgba(59, 130, 246, 0.08);
        border-color: var(--primary-border);
    }

    .import-drop-icon {
        font-size: 40px;
    }

    .import-drop-title {
        font-weight: 700;
        color: var(--text-color);
    }

    .import-drop-hint {
        font-size: 13px;
    }

    .import-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        align-items: center;
    }

    .import-btn-upload,
    .import-btn-cancel,
    .users-btn-secondary {
        padding: 14px 28px;
        border-radius: 12px;
        border: none;
        cursor: pointer;
        font-weight: 700;
        font-size: 15px;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
        letter-spacing: 0.3px;
    }

    .import-btn-upload {
        background: var(--primary);
        color: #fff;
        box-shadow: 0 4px 14px rgba(var(--primary-rgb), 0.3);
        min-width: 140px;
    }

    .import-btn-upload:hover {
        opacity: 0.95;
        box-shadow: 0 6px 20px rgba(var(--primary-rgb), 0.4);
        transform: translateY(-2px);
    }

    .import-btn-upload:active {
        opacity: 0.9;
        transform: translateY(0);
    }

    .import-btn-cancel,
    .users-btn-secondary {
        background: transparent;
        border: 2px solid var(--border-color);
        color: var(--text-color);
        min-width: 120px;
    }

    .import-btn-cancel:hover,
    .users-btn-secondary:hover {
        border-color: var(--primary);
        background: var(--primary);
        color: #fff;
        transform: translateY(-2px);
    }

    .import-filename {
        display: none;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 12px 14px;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        background: var(--bg-color);
        color: var(--text-color);
    }

    .import-filename button {
        border: none;
        background: transparent;
        cursor: pointer;
        font-size: 16px;
        color: var(--muted);
    }

    .import-note {
        padding: 14px 16px;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        background: var(--bg-color);
        color: var(--muted);
        font-size: 13px;
        line-height: 1.6;
    }

    .import-note strong {
        color: var(--text-color);
    }

    .import-file-input {
        display: none;
    }

    @media (max-width: 760px) {
        main {
            padding: 16px 16px !important;
        }

        .users-header {
            flex-direction: column;
            align-items: stretch;
        }

        .users-header a {
            width: 100%;
        }

        .users-card-body {
            padding: 20px;
        }

        .users-header h1 {
            font-size: 24px;
        }

        .import-btn-upload,
        .import-btn-cancel,
        .import-template-btn,
        .users-btn-secondary {
            width: 100%;
        }

        .import-actions {
            flex-direction: column;
            gap: 10px;
        }
    }
</style>

<div class="users-page">
    <div class="users-header">
        <div>
            <h1>Import Users dari Excel</h1>
            <p style="margin: 8px 0 0; color: var(--muted);">Unggah file Excel untuk menambahkan banyak user sekaligus.</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="users-btn-secondary">← Kembali</a>
    </div>

    <div class="users-card">
        <div class="users-card-body">
            @if(session('success'))
                <div style="margin-bottom: 16px; padding: 14px; border: 1px solid var(--success-border); border-radius: 12px; background: var(--success-soft); color: var(--success);">
                    {{ session('success') }}
                </div>
            @endif

            <div class="import-container">
                <p style="margin: 0; color: var(--muted);">📊 Import data user secara massal menggunakan file Excel. NIK akan di-encrypt otomatis untuk keamanan data.</p>

                <div class="import-note">
                    <strong>Kolom wajib diisi:</strong> Nama, NIK, Username, Email, Password, Role, dan Status Akun.
                    Kolom No tersedia untuk penomoran data. Tempat Tugas, No Telepon, Jabatan, Regu, Shift, dan Alamat boleh dikosongkan jika belum dibutuhkan.
                </div>

                <a href="{{ route('admin.users.template') }}" class="import-template-btn">📥 Download Template Excel</a>

                <form method="POST" action="{{ route('admin.users.import') }}" enctype="multipart/form-data" id="importForm">
                    @csrf
                    <div class="import-drop-zone" id="dropZone" onclick="document.getElementById('fileInput').click()">
                        <input type="file" id="fileInput" name="file" accept=".xlsx,.xls" class="import-file-input" onchange="handleFileSelect(event)">
                        <div class="import-drop-icon">📁</div>
                        <div class="import-drop-title">Drag & Drop file Excel di sini</div>
                        <div class="import-drop-hint">atau klik untuk memilih file (.xlsx, .xls)</div>
                    </div>

                    <div id="selectedFile" class="import-filename">
                        <strong id="fileName"></strong>
                        <button type="button" onclick="clearFile()">✕</button>
                    </div>

                    <div class="import-actions" id="actionButtons" style="display: none;">
                        <button type="submit" class="import-btn-upload">Upload & Import</button>
                        <button type="button" onclick="clearFile()" class="import-btn-cancel">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    const selectedFileDiv = document.getElementById('selectedFile');
    const fileNameSpan = document.getElementById('fileName');
    const actionButtons = document.getElementById('actionButtons');

    if (dropZone) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => dropZone.classList.add('dragover'), false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => dropZone.classList.remove('dragover'), false);
        });

        dropZone.addEventListener('drop', handleDrop, false);
    }

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        if (fileInput) {
            fileInput.files = files;
        }
        handleFileSelect({ target: { files } });
    }

    function handleFileSelect(event) {
        const files = event.target.files;
        if (files && files.length > 0) {
            const file = files[0];
            if (file.name.match(/\.(xlsx|xls)$/i)) {
                fileNameSpan.textContent = file.name;
                selectedFileDiv.style.display = 'flex';
                actionButtons.style.display = 'flex';
            } else {
                alert('Hanya file Excel (.xlsx, .xls) yang diperbolehkan!');
                clearFile();
            }
        }
    }

    function clearFile() {
        if (fileInput) {
            fileInput.value = '';
        }
        selectedFileDiv.style.display = 'none';
        actionButtons.style.display = 'none';
        fileNameSpan.textContent = '';
    }
</script>
@endsection
