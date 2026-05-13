@extends('layouts.app')

@section('title', 'Dashboard Atasan')

@section('content')
    <style>
        .dashboard-calendar-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 14px;
            flex-wrap: wrap;
        }

        .dashboard-calendar-nav {
            display: flex;
            gap: 8px;
        }

        .dashboard-calendar-nav a {
            padding: 8px 10px;
            border-radius: 6px;
            background: var(--soft-bg);
            color: var(--soft-text);
            font-size: 13px;
        }

        .dashboard-calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            border: 1px solid var(--border-color);
            border-radius: 8px;
            overflow: hidden;
            background: var(--panel-bg);
        }

        .dashboard-day-name,
        .dashboard-day {
            min-width: 0;
            border-right: 1px solid var(--border-color);
            border-bottom: 1px solid var(--border-color);
        }

        .dashboard-day-name:nth-child(7n),
        .dashboard-day:nth-child(7n) {
            border-right: 0;
        }

        .dashboard-day-name {
            padding: 9px;
            background: var(--bg-color);
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
            text-align: center;
        }

        .dashboard-day {
            min-height: 112px;
            padding: 8px;
            background: var(--panel-bg);
        }

        .dashboard-day.outside-month {
            background: var(--bg-color);
            color: var(--muted);
        }

        .dashboard-day.today {
            box-shadow: inset 0 0 0 2px var(--primary);
        }

        .dashboard-date {
            margin-bottom: 6px;
            font-weight: 700;
        }

        .dashboard-event {
            display: block;
            margin-top: 5px;
            padding: 5px 6px;
            border-radius: 6px;
            background: var(--soft-bg);
            color: var(--soft-text);
            font-size: 11px;
            line-height: 1.25;
        }

        .dashboard-event.absen {
            background: var(--info-soft-bg);
            color: var(--info-soft-text);
            font-weight: 700;
        }

        .dashboard-event.tugas {
            background: var(--warning-soft-bg);
            color: var(--warning-soft-text);
            font-weight: 700;
        }

        .dashboard-event.libur,
        .dashboard-event.cuti_bersama {
            background: var(--danger-soft-bg);
            color: var(--danger-soft-text);
        }

        .dashboard-event.kegiatan {
            background: var(--success-bg);
            color: var(--success-text);
        }

        .activity-list {
            display: grid;
            gap: 10px;
        }

        .activity-item {
            display: grid;
            gap: 4px;
            padding: 10px 0;
            border-bottom: 1px solid var(--border-color);
        }

        .activity-item:last-child {
            border-bottom: 0;
        }

        .activity-meta {
            color: var(--muted);
            font-size: 12px;
        }

        @media (max-width: 760px) {
            .dashboard-calendar-grid {
                grid-template-columns: 1fr;
            }

            .dashboard-day-name {
                display: none;
            }

            .dashboard-day {
                min-height: auto;
                border-right: 0;
            }
        }
    </style>

    <h1>Dashboard Atasan</h1>
    <div class="grid">
        <div class="stat">Cuti pending<strong>{{ $cutiPending->count() }}</strong></div>
        <div class="stat">Tugas pending<strong>{{ $tugasPending->count() }}</strong></div>
        <div class="stat">Absensi hari ini<strong>{{ $absensiHariIni->count() }}</strong></div>
        <div class="stat">Absensi bulan ini<strong>{{ $absensiBulanIni }}</strong></div>
    </div>

    <div class="panel">
        <h2>Aktivitas Terbaru Petugas</h2>
        <div class="activity-list">
            @forelse($aktivitasTerbaru as $activity)
                <div class="activity-item">
                    <div>
                        <span class="badge">{{ ucfirst(str_replace('_', ' ', $activity->modul)) }}</span>
                        {{ $activity->user->nama ?? '-' }} - {{ $activity->aktivitas }}
                    </div>
                    <div class="activity-meta">
                        {{ $activity->created_at?->format('d/m/Y H:i') ?? '-' }}
                        @if($activity->user?->tempatTugas)
                            - {{ $activity->user->tempatTugas->nama_tempat }}
                        @endif
                    </div>
                </div>
            @empty
                <p class="muted">Belum ada aktivitas terbaru dari petugas.</p>
            @endforelse
        </div>
    </div>

    <div class="panel">
        <div class="dashboard-calendar-head">
            <h2>Kalender {{ $currentMonth->translatedFormat('F Y') }}</h2>
            <div class="dashboard-calendar-nav">
                <a href="{{ route('dashboard', ['month' => $previousMonth->month, 'year' => $previousMonth->year]) }}">Sebelumnya</a>
                <a href="{{ route('dashboard', ['month' => $nextMonth->month, 'year' => $nextMonth->year]) }}">Berikutnya</a>
            </div>
        </div>

        <div class="dashboard-calendar-grid">
            @foreach(['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'] as $dayName)
                <div class="dashboard-day-name">{{ $dayName }}</div>
            @endforeach

            @foreach($days as $day)
                @php
                    $dateKey = $day->format('Y-m-d');
                    $dayEvents = $events->get($dateKey, collect());
                    $absensiCount = $absensiByDate->get($dateKey, 0);
                    $tugasCount = $tugasByDate->get($dateKey, 0);
                @endphp

                <div class="dashboard-day {{ $day->month !== $currentMonth->month ? 'outside-month' : '' }} {{ $day->isToday() ? 'today' : '' }}">
                    <div class="dashboard-date">{{ $day->day }}</div>

                    @if($absensiCount > 0)
                        <span class="dashboard-event absen">{{ $absensiCount }} absen</span>
                    @endif

                    @if($tugasCount > 0)
                        <span class="dashboard-event tugas">{{ $tugasCount }} tugas</span>
                    @endif

                    @foreach($dayEvents as $event)
                        <span class="dashboard-event {{ $event->jenis_event }}">
                            {{ $event->nama_event ?: ucfirst(str_replace('_', ' ', $event->jenis_event)) }}
                        </span>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>

    <div class="panel">
        <h2>Absensi Hari Ini</h2>
        @forelse($absensiHariIni as $item)
            <p>{{ $item->user->nama ?? '-' }} - <span class="badge {{ $item->status }}">{{ $item->status }}</span> {{ $item->jam_masuk }}</p>
        @empty
            <p class="muted">Belum ada absensi hari ini.</p>
        @endforelse
    </div>
@endsection
