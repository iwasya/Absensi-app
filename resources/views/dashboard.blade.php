<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard | {{ config('app.name') }}</title>
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, sans-serif;
            background: #f6f7fb;
            color: #1f2937;
        }
        header {
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            padding: 16px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        main {
            max-width: 960px;
            margin: 32px auto;
            padding: 0 24px;
        }
        h1 {
            margin: 0;
            font-size: 24px;
        }
        .box {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 24px;
        }
        button {
            border: 0;
            border-radius: 6px;
            background: #1f2937;
            color: #ffffff;
            padding: 10px 14px;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <header>
        <strong>{{ config('app.name') }}</strong>
        <form method="POST" action="{{ route('logout', [], false) }}">
            @csrf
            <button type="submit">Logout</button>
        </form>
    </header>

    <main>
        <div class="box">
            <h1>Dashboard</h1>
            <p>Halo, {{ auth()->user()->nama }}. Login berhasil dan session aktif.</p>
        </div>
    </main>
</body>
</html>
