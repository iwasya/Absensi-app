<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register | {{ config('app.name') }}</title>
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, sans-serif;
            background: #f6f7fb;
            color: #1f2937;
            display: grid;
            place-items: center;
        }
        .panel {
            width: min(440px, calc(100% - 32px));
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 28px;
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.08);
        }
        h1 {
            margin: 0 0 8px;
            font-size: 26px;
        }
        p {
            margin: 0 0 24px;
            color: #6b7280;
        }
        .notice {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1d4ed8;
            border-radius: 6px;
            padding: 10px 12px;
            margin-bottom: 18px;
            font-size: 14px;
        }
        label {
            display: block;
            margin: 16px 0 6px;
            font-weight: 700;
            font-size: 14px;
        }
        input,
        select {
            width: 100%;
            box-sizing: border-box;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 11px 12px;
            font-size: 15px;
        }
        button {
            width: 100%;
            margin-top: 20px;
            border: 0;
            border-radius: 6px;
            background: #2563eb;
            color: #ffffff;
            padding: 12px 16px;
            font-weight: 700;
            font-size: 15px;
        }
        a {
            color: #2563eb;
            text-decoration: none;
            font-weight: 700;
        }
        .foot {
            margin-top: 18px;
            text-align: center;
            font-size: 14px;
            color: #6b7280;
        }
        .error {
            margin-top: 6px;
            color: #b91c1c;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <main class="panel">
        <h1>Register</h1>
        <p>Buat akun baru untuk sistem absensi PPSU lokal.</p>

        @if($isFirstUser)
            <div class="notice">Akun pertama otomatis dibuat sebagai Admin Absensi untuk setup awal.</div>
        @endif

        <form method="POST" action="{{ route('register', [], false) }}">
            @csrf

            <label for="nama">Nama</label>
            <input id="nama" type="text" name="nama" value="{{ old('nama') }}" required autofocus autocomplete="name">
            @error('nama')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="username">Username</label>
            <input id="username" type="text" name="username" value="{{ old('username') }}" required autocomplete="username">
            @error('username')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email">
            @error('email')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="id_tempat">Tempat Tugas</label>
            <select id="id_tempat" name="id_tempat">
                <option value="">Belum dipilih</option>
                @foreach($tempatTugas as $tempat)
                    <option value="{{ $tempat->id_tempat }}" @selected(old('id_tempat') == $tempat->id_tempat)>{{ $tempat->nama_tempat }}</option>
                @endforeach
            </select>
            @error('id_tempat')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="password">Password</label>
            <input id="password" type="password" name="password" required autocomplete="new-password">
            @error('password')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="password_confirmation">Konfirmasi Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">

            <button type="submit">Daftar</button>
        </form>

        <div class="foot">
            Sudah punya akun? <a href="{{ route('login', [], false) }}">Login</a>
        </div>
    </main>
</body>
</html>
