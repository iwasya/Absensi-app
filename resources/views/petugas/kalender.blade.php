@extends('layouts.app')

@section('title', 'Kalender Tugas')

@section('content')
    <style>
        .calendar-event.weekly-off {
            background: #ccfbf1;
            color: #0f766e;
            border-left: 3px solid #14b8a6;
        }
        .calendar-event.replacement {
            background: #fffbeb;
            color: #92400e;
            border-left: 3px solid #f59e0b;
        }
    </style>

    <h1>Kalender</h1>

    <div class="panel">
        <h2>Hari Ini</h2>
        @if($todayWeeklyOff)
            <p>
                <span class="badge approve">libur</span>
                {{ $todayWeeklyOff['title'] }}
                <span class="muted">{{ $todayWeeklyOff['description'] }}</span>
            </p>
        @endif

        @foreach($todayReplacementCuti as $cuti)
            <p>
                <span class="badge pending">pengganti</span>
                Menggantikan {{ $cuti->user->nama ?? '-' }}
                <span class="muted">cuti {{ $cuti->jenis_cuti }}</span>
            </p>
        @endforeach

        @if(($liburKompensasi ?? collect())->isNotEmpty())
            <h3 style="margin-top: 16px; font-size: 16px;">Libur Kompensasi Tersedia</h3>
            @foreach($liburKompensasi->take(3) as $kompensasi)
                <p>
                    <span class="badge approve">kompensasi</span>
                    {{ $kompensasi->tanggal_kerja->translatedFormat('d F Y') }}
                    <span class="muted">{{ $kompensasi->keterangan }}</span>
                </p>
            @endforeach
        @endif

        @foreach($todayItems as $item)
            <p>
                <span class="badge {{ $item->jenis_event }}">{{ $item->jenis_event }}</span>
                {{ $item->nama_event ?? '-' }}
                <span class="muted">{{ $item->keterangan }}</span>
            </p>
        @endforeach

        @if(! $todayWeeklyOff && $todayItems->isEmpty())
            <p class="muted">Tidak ada libur atau kegiatan khusus hari ini.</p>
        @endif

        @if(isset($todayTugas) && $todayTugas->isNotEmpty())
            <h3 style="margin-top: 16px; font-size: 16px;">Tugas Hari Ini</h3>
            @foreach($todayTugas as $tugas)
                <p>
                    <span class="badge {{ $tugas->status == 'approved' ? 'approve' : ($tugas->status == 'rejected' ? 'reject' : 'pending') }}">{{ ucfirst($tugas->status) }}</span>
                    <strong>{{ $tugas->uraian }}</strong>
                </p>
            @endforeach
        @endif
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
                    $dayTugas = isset($tugasByDate[$dateKey]) ? collect($tugasByDate[$dateKey]) : collect();
                    $dayReplacementCuti = isset($replacementCutiByDate[$dateKey]) ? collect($replacementCutiByDate[$dateKey]) : collect();
                    $weeklyOff = $weeklyOffByDate->get($dateKey);
                @endphp
                <div class="calendar-cell {{ $day->month !== $currentMonth->month ? 'muted-day' : '' }}">
                    <div class="calendar-date">{{ $day->format('d') }}</div>
                    @if($weeklyOff)
                        <div class="calendar-event weekly-off">
                            <strong>{{ $weeklyOff['title'] }}</strong><br>
                            <span>{{ $weeklyOff['description'] }}</span>
                        </div>
                    @endif
                    @foreach($dayReplacementCuti as $cuti)
                        <div class="calendar-event replacement">
                            <strong>Pengganti Cuti</strong><br>
                            <span>{{ $cuti->user->nama ?? '-' }} - {{ $cuti->jenis_cuti }}</span>
                        </div>
                    @endforeach
                    @foreach($dayEvents as $event)
                        <div class="calendar-event {{ $event->jenis_event }}">
                            <strong>{{ $event->nama_event ?? ucfirst($event->jenis_event) }}</strong><br>
                            <span>{{ $event->keterangan }}</span>
                        </div>
                    @endforeach
                    @foreach($dayTugas as $tugas)
                        <div class="calendar-event" style="background: #eef2ff; color: #3730a3; border-left: 3px solid #6366f1;">
                            <strong>Tugas:</strong> {{ \Illuminate\Support\Str::limit($tugas->uraian, 30) }}<br>
                            <span style="font-size: 10px; font-weight: bold; text-transform: uppercase;">{{ $tugas->status }}</span>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
@endsection
