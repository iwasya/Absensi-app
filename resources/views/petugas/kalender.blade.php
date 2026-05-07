@extends('layouts.app')

@section('title', 'Kalender Tugas')

@section('content')
    <h1>Kalender</h1>

    <div class="panel">
        <h2>Hari Ini</h2>
        @forelse($todayItems as $item)
            <p>
                <span class="badge {{ $item->jenis_event }}">{{ $item->jenis_event }}</span>
                {{ $item->nama_event ?? '-' }}
                <span class="muted">{{ $item->keterangan }}</span>
            </p>
        @empty
            <p class="muted">Tidak ada libur atau kegiatan khusus hari ini.</p>
        @endforelse
    </div>

    <div class="panel">
        <div class="calendar-head">
            <a href="{{ route('petugas.tugas.kalender', ['month' => $previousMonth->month, 'year' => $previousMonth->year]) }}">Sebelumnya</a>
            <h2>{{ $currentMonth->translatedFormat('F Y') }}</h2>
            <a href="{{ route('petugas.tugas.kalender', ['month' => $nextMonth->month, 'year' => $nextMonth->year]) }}">Berikutnya</a>
        </div>

        <div class="calendar-grid">
            @foreach(['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'] as $dayName)
                <div class="calendar-day-name">{{ $dayName }}</div>
            @endforeach

            @foreach($days as $day)
                @php
                    $dateKey = $day->format('Y-m-d');
                    $dayEvents = $events->get($dateKey, collect());
                @endphp
                <div class="calendar-cell {{ $day->month !== $currentMonth->month ? 'muted-day' : '' }}">
                    <div class="calendar-date">{{ $day->format('d') }}</div>
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
@endsection
