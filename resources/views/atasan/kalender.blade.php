@extends('layouts.app')

@section('title', 'Kalender Monitoring')

@section('content')
    <h1>Kalender Monitoring Tugas</h1>

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
                    $dayTugas = isset($tugasByDate[$dateKey]) ? collect($tugasByDate[$dateKey]) : collect();
                @endphp
                <div class="calendar-cell {{ $day->month !== $currentMonth->month ? 'muted-day' : '' }}">
                    <div class="calendar-date">{{ $day->format('d') }}</div>
                    @foreach($dayEvents as $event)
                        <div class="calendar-event {{ $event->jenis_event }}">
                            <strong>{{ $event->nama_event ?? ucfirst($event->jenis_event) }}</strong><br>
                            <span>{{ $event->keterangan }}</span>
                        </div>
                    @endforeach
                    @foreach($dayTugas as $tugas)
                        <div class="calendar-event" style="background: #eef2ff; color: #3730a3; border-left: 3px solid #6366f1;">
                            <strong style="color: #4338ca;">{{ $tugas->user->nama }}:</strong><br>
                            {{ \Illuminate\Support\Str::limit($tugas->uraian, 30) }}<br>
                            <span style="font-size: 10px; font-weight: bold; text-transform: uppercase;">{{ $tugas->status }}</span>
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
                <span style="display: inline-block; width: 12px; height: 12px; background: #eef2ff; border-left: 3px solid #6366f1;"></span> Tugas Petugas
            </div>
        </div>
    </div>
@endsection
