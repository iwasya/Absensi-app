@extends('layouts.app')

@section('title', 'Pengaturan Aplikasi')

@section('content')
<style>
    .settings-page {
        max-width: 760px;
        margin: 0 auto;
    }

    .settings-panel {
        display: grid;
        gap: 24px;
    }

    .settings-section {
        display: grid;
        gap: 10px;
        padding-bottom: 22px;
        border-bottom: 1px solid var(--border-color);
    }

    .settings-section:last-child {
        padding-bottom: 0;
        border-bottom: 0;
    }

    .settings-title {
        margin-bottom: 4px;
    }

    .settings-help {
        margin: 0;
        color: var(--muted);
        font-size: 13px;
        line-height: 1.45;
    }

    .asset-preview {
        width: fit-content;
        max-width: 100%;
        min-width: 160px;
        min-height: 96px;
        display: grid;
        place-items: center;
        padding: 14px;
        background: var(--soft-bg);
        color: var(--soft-text);
        border: 1px solid var(--border-color);
        border-radius: 8px;
    }

    .asset-preview img {
        max-height: 82px;
        max-width: 100%;
        object-fit: contain;
    }

    .brand-preview {
        display: flex;
        align-items: center;
        gap: 12px;
        text-align: left;
    }

    .brand-preview.logo_only,
    .brand-preview.name_only {
        justify-content: center;
        text-align: center;
    }

    .brand-preview img {
        max-height: 58px;
        max-width: 180px;
        object-fit: contain;
    }

    .brand-preview strong {
        display: block;
        font-size: 18px;
        line-height: 1.25;
    }

    .icon-preview {
        min-width: 76px;
        min-height: 76px;
        padding: 12px;
    }

    .icon-preview img {
        width: 48px;
        height: 48px;
        object-fit: contain;
    }

    .manual-icon-preview {
        width: 54px;
        height: 54px;
        display: grid;
        place-items: center;
        border-radius: 14px;
        font-size: 24px;
        font-weight: 700;
    }

    .icon-manual-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
    }

    .theme-options {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .brand-options {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .theme-option {
        display: flex;
        align-items: center;
        gap: 10px;
        min-height: 58px;
        padding: 12px 14px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        background: var(--soft-bg);
        color: var(--soft-text);
        cursor: pointer;
        margin: 0;
    }

    .theme-option input {
        width: auto;
        margin: 0;
    }

    .theme-option:has(input:checked) {
        border-color: var(--primary);
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.16);
    }

    .theme-swatch {
        width: 28px;
        height: 28px;
        border-radius: 999px;
        border: 1px solid var(--border-color);
        flex: 0 0 auto;
    }

    .theme-swatch.light {
        background: linear-gradient(135deg, #ffffff 0 50%, #dbeafe 50% 100%);
    }

    .theme-swatch.dark {
        background: linear-gradient(135deg, #020617 0 50%, #334155 50% 100%);
    }

    .settings-submit {
        width: 100%;
        padding: 12px 18px;
        font-size: 16px;
    }

    @media (max-width: 640px) {
        .theme-options {
            grid-template-columns: 1fr;
        }

        .brand-options {
            grid-template-columns: 1fr;
        }

        .icon-manual-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="settings-page">
    <h1>Pengaturan Aplikasi</h1>

    <div class="panel settings-panel">
        <form method="POST" action="{{ route('admin.pengaturan.store') }}" enctype="multipart/form-data" class="settings-panel">
            @csrf

            @php
                $brandName = old('app_name', $app_name ?: 'Absensi PPSU');
                $brandDisplay = old('app_brand_display', $app_brand_display);
                $brandDisplay = in_array($brandDisplay, ['logo_name', 'logo_only', 'name_only'], true) ? $brandDisplay : 'logo_name';
            @endphp

            <div class="settings-section">
                <div>
                    <h2 class="settings-title">Brand Aplikasi</h2>
                    <p class="settings-help">Atur nama dan logo yang tampil di sidebar. Kosongkan input file jika tidak ingin mengubah logo.</p>
                </div>

                <div class="asset-preview">
                    <div class="brand-preview {{ $brandDisplay }}">
                        @if($app_logo && $brandDisplay !== 'name_only')
                            <img src="{{ Storage::url($app_logo) }}" alt="Logo Aplikasi">
                        @endif

                        @if($brandDisplay !== 'logo_only' || ! $app_logo)
                            <strong>{{ $brandName }}</strong>
                        @endif
                    </div>
                </div>

                <div>
                    <label for="app_name">Nama Aplikasi</label>
                    <input id="app_name" type="text" name="app_name" maxlength="80" value="{{ old('app_name', $brandName) }}" required>
                </div>

                <div class="theme-options brand-options">
                    <label class="theme-option">
                        <input type="radio" name="app_brand_display" value="logo_name" {{ $brandDisplay === 'logo_name' ? 'checked' : '' }}>
                        <span>Logo + Nama</span>
                    </label>

                    <label class="theme-option">
                        <input type="radio" name="app_brand_display" value="logo_only" {{ $brandDisplay === 'logo_only' ? 'checked' : '' }}>
                        <span>Logo Saja</span>
                    </label>

                    <label class="theme-option">
                        <input type="radio" name="app_brand_display" value="name_only" {{ $brandDisplay === 'name_only' ? 'checked' : '' }}>
                        <span>Nama Saja</span>
                    </label>
                </div>

                <div>
                    <label for="app_logo">Upload Logo Baru</label>
                    <input id="app_logo" type="file" name="app_logo" accept="image/*">
                    <p class="settings-help">Format: JPG atau PNG. Maksimal 2MB.</p>
                </div>

                @error('app_name')
                    <div class="error">{{ $message }}</div>
                @enderror
                @error('app_logo')
                    <div class="error">{{ $message }}</div>
                @enderror
                @error('app_brand_display')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="settings-section">
                <div>
                    <h2 class="settings-title">Ikon Web</h2>
                    <p class="settings-help">Ikon ini akan tampil di tab browser dan shortcut perangkat. Pilih upload gambar atau buat ikon manual dari teks dan warna.</p>
                </div>

                @if($app_icon_mode === 'manual')
                    <div class="asset-preview icon-preview">
                        <div class="manual-icon-preview" style="background: {{ $app_icon_bg }}; color: {{ $app_icon_color }};">
                            {{ strtoupper(substr($app_icon_text ?: 'A', 0, 2)) }}
                        </div>
                    </div>
                @elseif($app_icon)
                    <div class="asset-preview icon-preview">
                        <img src="{{ Storage::url($app_icon) }}" alt="Ikon Web">
                    </div>
                @else
                    <div class="asset-preview icon-preview muted">Belum ada ikon</div>
                @endif

                <div class="theme-options">
                    <label class="theme-option">
                        <input type="radio" name="app_icon_mode" value="upload" {{ $app_icon_mode === 'upload' ? 'checked' : '' }}>
                        <span>Upload Gambar</span>
                    </label>

                    <label class="theme-option">
                        <input type="radio" name="app_icon_mode" value="manual" {{ $app_icon_mode === 'manual' ? 'checked' : '' }}>
                        <span>Manual</span>
                    </label>
                </div>

                <div>
                    <label for="app_icon">Upload Ikon Baru</label>
                    <input id="app_icon" type="file" name="app_icon" accept="image/*">
                    <p class="settings-help">Rekomendasi: PNG persegi 512x512. Maksimal 1MB.</p>
                </div>

                <div class="icon-manual-grid">
                    <div>
                        <label for="app_icon_text">Teks Ikon</label>
                        <input id="app_icon_text" type="text" name="app_icon_text" maxlength="2" value="{{ $app_icon_text }}">
                    </div>
                    <div>
                        <label for="app_icon_bg">Warna Background</label>
                        <input id="app_icon_bg" type="color" name="app_icon_bg" value="{{ $app_icon_bg }}">
                    </div>
                    <div>
                        <label for="app_icon_color">Warna Teks</label>
                        <input id="app_icon_color" type="color" name="app_icon_color" value="{{ $app_icon_color }}">
                    </div>
                </div>

                @error('app_icon')
                    <div class="error">{{ $message }}</div>
                @enderror
                @error('app_icon_mode')
                    <div class="error">{{ $message }}</div>
                @enderror
                @error('app_icon_text')
                    <div class="error">{{ $message }}</div>
                @enderror
                @error('app_icon_bg')
                    <div class="error">{{ $message }}</div>
                @enderror
                @error('app_icon_color')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="settings-section">
                <div>
                    <h2 class="settings-title">Tema Tampilan</h2>
                    <p class="settings-help">Pilih tema yang akan digunakan di seluruh aplikasi.</p>
                </div>

                <div class="theme-options">
                    <label class="theme-option">
                        <input type="radio" name="app_theme" value="light" {{ $app_theme === 'light' ? 'checked' : '' }}>
                        <span class="theme-swatch light" aria-hidden="true"></span>
                        <span>Terang</span>
                    </label>

                    <label class="theme-option">
                        <input type="radio" name="app_theme" value="dark" {{ $app_theme === 'dark' ? 'checked' : '' }}>
                        <span class="theme-swatch dark" aria-hidden="true"></span>
                        <span>Gelap</span>
                    </label>
                </div>

                @error('app_theme')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="settings-submit">Simpan Pengaturan</button>
        </form>
    </div>
</div>
@endsection
