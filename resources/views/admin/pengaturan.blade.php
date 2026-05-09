@extends('layouts.app')

@section('title', 'Pengaturan Aplikasi')

@section('content')
<div style="max-width: 600px; margin: 0 auto;">
    <h1 style="margin-bottom: 24px; font-size: 24px; font-weight: bold;">Pengaturan Aplikasi</h1>

    @if(session('success'))
        <div style="background: #ecfdf5; color: #065f46; padding: 12px 16px; border-radius: 6px; margin-bottom: 24px; font-weight: bold;">
            {{ session('success') }}
        </div>
    @endif

    <div class="panel" style="background: #fff; padding: 24px; border-radius: 8px; border: 1px solid #e5e7eb; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        <form method="POST" action="{{ route('admin.pengaturan.store') }}" enctype="multipart/form-data">
            @csrf

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-weight: bold; margin-bottom: 8px;">Logo Aplikasi</label>
                @if($app_logo)
                    <div style="margin-bottom: 12px; background: #f3f4f6; padding: 12px; border-radius: 8px; display: inline-block;">
                        <img src="{{ Storage::url($app_logo) }}" alt="Logo Aplikasi" style="max-height: 80px; max-width: 100%;">
                    </div>
                @else
                    <div style="margin-bottom: 12px; color: #6b7280; font-style: italic;">Belum ada logo yang diatur.</div>
                @endif
                <input type="file" name="app_logo" accept="image/*" style="display: block; width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 6px;">
                <p style="font-size: 12px; color: #6b7280; margin-top: 4px;">Kosongkan jika tidak ingin mengubah logo. Format: JPG, PNG. Maks: 2MB.</p>
                @error('app_logo')
                    <div style="color: #ef4444; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-weight: bold; margin-bottom: 8px;">Tema Tampilan</label>
                <div style="display: flex; gap: 16px;">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; padding: 12px 16px; border: 1px solid #e5e7eb; border-radius: 8px; background: #f9fafb;">
                        <input type="radio" name="app_theme" value="light" {{ $app_theme === 'light' ? 'checked' : '' }}>
                        Terang (Light)
                    </label>
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; padding: 12px 16px; border: 1px solid #374151; border-radius: 8px; background: #111827; color: #fff;">
                        <input type="radio" name="app_theme" value="dark" {{ $app_theme === 'dark' ? 'checked' : '' }}>
                        Gelap (Dark)
                    </label>
                </div>
                @error('app_theme')
                    <div style="color: #ef4444; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" style="background: #2563eb; color: white; border: none; padding: 12px 24px; border-radius: 6px; font-weight: bold; cursor: pointer; width: 100%; font-size: 16px;">Simpan Pengaturan</button>
        </form>
    </div>
</div>
@endsection
