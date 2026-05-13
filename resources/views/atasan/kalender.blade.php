@extends('layouts.app')

@section('title', 'Kalender Monitoring')

@section('content')
    <style>
        .calendar-event.absensi-count {
            background: var(--info-soft-bg);
            color: var(--info-soft-text);
            font-weight: 700;
        }

        .calendar-event.tugas-count {
            background: var(--warning-soft-bg);
            color: var(--warning-soft-text);
            font-weight: 700;
        }
    </style>

    <h1>Kalender Monitoring</h1>

    <div class="panel">
        <div class="calendar-head">
            <a href="{{ route('atasan.kalender.index', ['month' => $previousMonth->month, 'year' => $previousMonth->year]) }}">Sebelumnya</a>
            <h2>{{ $currentMonth->translatedFormat('F Y') }}</h2>
            <a href="{{ route('atasan.kalender.index', ['month' => $nextMonth->month, 'year' => $nextMonth->year]) }}">Berikutnya</a>
        </div>

        <div class="calendar-grid">
            @foreach(['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'] as $dayName)
                <div class="calendar-day-name">{{ $dayName }}</div>
            @endforeach

            @foreach($days as $day)
                @php
                    $dateKey = $day->format('Y-m-d');
                    $dayEvents = $events->get($dateKey, collect());
                    $absensiCount = $absensiByDate->get($dateKey, 0);
                    $tugasCount = $tugasByDate->get($dateKey, 0);
                @endphp
                <div class="calendar-cell {{ $day->month !== $currentMonth->month ? 'muted-day' : '' }}">
                    <div class="calendar-date">{{ $day->format('d') }}</div>

                    @if($absensiCount > 0)
                        <div class="calendar-event absensi-count">{{ $absensiCount }} absen</div>
                    @endif

                    @if($tugasCount > 0)
                        <div class="calendar-event tugas-count">{{ $tugasCount }} tugas</div>
                    @endif

                    @foreach($dayEvents as $event)
                        <div class="calendar-event {{ $event->jenis_event }}">
                            <strong>{{ $event->nama_event ?? ucfirst($event->jenis_event) }}</strong><br>
                            <span>{{ $event->keterangan }}</span>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>

    <div class="panel">
        <h3>Keterangan</h3>
        <div style="display: flex; gap: 16px; flex-wrap: wrap; font-size: 14px;">
            <div style="display: flex; align-items: center; gap: 6px;">
                <span style="display: inline-block; width: 12px; height: 12px; background: #fee2e2; border-radius: 2px;"></span> Libur
            </div>
            <div style="display: flex; align-items: center; gap: 6px;">
                <span style="display: inline-block; width: 12px; height: 12px; background: #dcfce7; border-radius: 2px;"></span> Kegiatan
            </div>
            <div style="display: flex; align-items: center; gap: 6px;">
                <span style="display: inline-block; width: 12px; height: 12px; background: #eef2ff; border-radius: 2px;"></span> Jumlah Absensi
            </div>
            <div style="display: flex; align-items: center; gap: 6px;">
                <span style="display: inline-block; width: 12px; height: 12px; background: #fff7ed; border-radius: 2px;"></span> Jumlah Tugas
            </div>
        </div>
    </div>
@endsection
