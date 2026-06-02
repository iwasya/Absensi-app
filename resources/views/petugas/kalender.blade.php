@extends('layouts.app')

@section('title', 'Kalender Tugas')

@section('content')
<style>
    main { max-width: 100% !important; margin: 0 !important; padding: 24px 28px !important; }

    .cal-page { display: grid; gap: 16px; }
    .cal-header { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
    .cal-header h1 { font-size: 20px; font-weight: 600; color: var(--text-color); margin: 0; }
    .cal-sub { font-size: 12px; color: var(--muted); margin-top: 3px; }
    .cal-card { background: var(--panel-bg); border: 1px solid var(--border-color); border-radius: 14px; overflow: hidden; }
    .cal-nav { display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; border-bottom: 1px solid var(--border-color); gap: 12px; }
    .cal-month { font-size: 14px; font-weight: 600; color: var(--text-color); text-align: center; }
    .cal-arrow { width: 32px; height: 32px; border-radius: 7px; border: 1px solid var(--border2); background: var(--panel-bg); color: var(--muted); font-size: 18px; line-height: 1; cursor: pointer; display: flex; align-items: center; justify-content: center; text-decoration: none; font-family: inherit; transition: background .15s; }
    .cal-arrow:hover { background: var(--bg-color); color: var(--text-color); }
    .cal-body { padding: 12px 16px 14px; }
    .cal-day-labels { display: grid; grid-template-columns: repeat(7,1fr); gap: 2px; margin-bottom: 4px; }
    .cal-day-lbl { text-align: center; font-size: 10px; font-weight: 600; color: var(--muted); text-transform: uppercase; padding: 4px 0; }
    .cal-grid { display: grid; grid-template-columns: repeat(7,1fr); gap: 2px; }
    .cal-cell { aspect-ratio: 1; display: flex; align-items: center; justify-content: center; font-size: 12px; border-radius: 7px; color: var(--text-color); position: relative; cursor: pointer; border: 1px solid transparent; background: var(--bg-color); font-family: inherit; }
    .cal-cell.other { color: var(--border2); opacity: .65; }
    .cal-cell.today { background: var(--primary-soft); color: var(--primary); font-weight: 700; border: 1px solid var(--primary-border); }
    .cal-cell.selected { border-color: var(--primary); box-shadow: inset 0 0 0 1px var(--primary); }
    .cal-dot-stack { position: absolute; bottom: 4px; left: 50%; transform: translateX(-50%); display: flex; align-items: center; gap: 2px; }
    .cal-dot { width: 5px; height: 5px; border-radius: 50%; }
    .cal-detail { margin: 10px 16px 14px; padding: 12px 14px; border-radius: 10px; background: var(--bg-color); border: 1px solid var(--border-color); }
    .cal-detail-title { font-size: 12px; font-weight: 600; color: var(--text-color); margin-bottom: 8px; }
    .cal-detail-list { display: flex; flex-direction: column; gap: 8px; }
    .cal-detail-row { display: flex; align-items: flex-start; gap: 8px; min-width: 0; }
    .cal-detail-dot { width: 7px; height: 7px; border-radius: 50%; margin-top: 6px; flex: 0 0 auto; }
    .cal-detail-text { min-width: 0; flex: 1; }
    .cal-detail-main { font-size: 12px; color: var(--text-color); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .cal-detail-sub { font-size: 11px; color: var(--muted); margin-top: 2px; line-height: 1.4; }
    .cal-legend { display: flex; flex-wrap: wrap; gap: 12px; padding: 10px 16px 14px; border-top: 1px solid var(--border-color); }
    .cal-leg-item { display: flex; align-items: center; gap: 5px; font-size: 11px; color: var(--muted); }
    .cal-leg-dot { width: 7px; height: 7px; border-radius: 50%; }
    .today-card { padding: 16px 18px; display: grid; gap: 10px; }
    .today-title { font-size: 14px; font-weight: 600; color: var(--text-color); }
    .today-list { display: grid; gap: 8px; }
    .today-row { display: flex; align-items: flex-start; gap: 8px; color: var(--text-color); font-size: 13px; }
    .today-row .muted { color: var(--muted); font-size: 12px; }
    .empty-note { color: var(--muted); font-size: 12px; }

    @media (max-width: 600px) {
        main { padding: 16px !important; }
        .cal-body { padding: 10px; }
        .cal-detail, .cal-legend { margin-left: 10px; margin-right: 10px; }
    }
</style>

<div class="cal-page">
    <div class="cal-header">
        <div>
            <h1>Kalender</h1>
            <div class="cal-sub">{{ $currentMonth->translatedFormat('F Y') }}</div>
        </div>
    </div>

    <div class="cal-card">
        <div class="cal-nav">
            <a class="cal-arrow" href="{{ route('petugas.tugas.kalender', ['month' => $previousMonth->month, 'year' => $previousMonth->year]) }}">&#8249;</a>
            <div class="cal-month">{{ $currentMonth->translatedFormat('F Y') }}</div>
            <a class="cal-arrow" href="{{ route('petugas.tugas.kalender', ['month' => $nextMonth->month, 'year' => $nextMonth->year]) }}">&#8250;</a>
        </div>
        <div class="cal-body">
            <div class="cal-day-labels">
                <div class="cal-day-lbl">Min</div><div class="cal-day-lbl">Sen</div><div class="cal-day-lbl">Sel</div>
                <div class="cal-day-lbl">Rab</div><div class="cal-day-lbl">Kam</div><div class="cal-day-lbl">Jum</div>
                <div class="cal-day-lbl">Sab</div>
            </div>
            <div class="cal-grid">
                @foreach($days as $day)
                    @php
                        $dateKey = $day->format('Y-m-d');
                        $dayNumber = (int) $day->format('d');
                        $colors = [];
                        if ($day->month === $currentMonth->month) {
                            if (in_array($dayNumber, $kalenderHadir ?? [], true)) $colors[] = 'var(--green)';
                            if (in_array($dayNumber, $kalenderTelat ?? [], true)) $colors[] = 'var(--amber)';
                            if (in_array($dayNumber, $kalenderAbsen ?? [], true)) $colors[] = 'var(--red)';
                            if (in_array($dayNumber, $kalenderCuti ?? [], true)) $colors[] = '#EC4899';
                            if (isset($tugasCalendarDetails[$dateKey]) && $tugasCalendarDetails[$dateKey]->isNotEmpty()) $colors[] = '#8B5CF6';
                            if (isset($eventCalendarDetails[$dateKey]) && $eventCalendarDetails[$dateKey]->isNotEmpty()) $colors[] = '#0EA5E9';
                            if (in_array($dayNumber, $kalenderLiburMingguan ?? [], true)) $colors[] = '#14B8A6';
                            if (isset($replacementCalendarDetails[$dateKey]) && $replacementCalendarDetails[$dateKey]->isNotEmpty()) $colors[] = '#F59E0B';
                        }
                    @endphp
                    <button type="button"
                        class="cal-cell {{ $day->month !== $currentMonth->month ? 'other' : '' }} {{ $day->isToday() ? 'today' : '' }}"
                        data-date="{{ $dateKey }}">
                        {{ $dayNumber }}
                        @if($colors)
                            <span class="cal-dot-stack">
                                @foreach(array_slice($colors, 0, 5) as $color)
                                    <span class="cal-dot" style="background:{{ $color }}"></span>
                                @endforeach
                            </span>
                        @endif
                    </button>
                @endforeach
            </div>
        </div>
        <div class="cal-detail" id="cal-detail"></div>
        <div class="cal-legend">
            <div class="cal-leg-item"><div class="cal-leg-dot" style="background:var(--green)"></div>Hadir</div>
            <div class="cal-leg-item"><div class="cal-leg-dot" style="background:var(--amber)"></div>Telat</div>
            <div class="cal-leg-item"><div class="cal-leg-dot" style="background:var(--red)"></div>Tidak Absen</div>
            <div class="cal-leg-item"><div class="cal-leg-dot" style="background:#EC4899"></div>Cuti</div>
            <div class="cal-leg-item"><div class="cal-leg-dot" style="background:#8B5CF6"></div>Tugas</div>
            <div class="cal-leg-item"><div class="cal-leg-dot" style="background:#0EA5E9"></div>Event</div>
            <div class="cal-leg-item"><div class="cal-leg-dot" style="background:#14B8A6"></div>Libur Mingguan</div>
            <div class="cal-leg-item"><div class="cal-leg-dot" style="background:#F59E0B"></div>Pengganti Cuti</div>
        </div>
    </div>

    <div class="cal-card today-card">
        <div class="today-title">Hari Ini</div>
        <div class="today-list">
            @if($todayWeeklyOff)
                <div class="today-row">
                    <span class="badge approve">libur</span>
                    <div>{{ $todayWeeklyOff['title'] }} <span class="muted">{{ $todayWeeklyOff['description'] }}</span></div>
                </div>
            @endif

            @foreach($todayReplacementCuti as $cuti)
                <div class="today-row">
                    <span class="badge pending">pengganti</span>
                    <div>Menggantikan {{ $cuti->user->nama ?? '-' }} <span class="muted">cuti {{ $cuti->jenis_cuti }}</span></div>
                </div>
            @endforeach

            @foreach($todayItems as $item)
                <div class="today-row">
                    <span class="badge {{ $item->jenis_event }}">{{ $item->jenis_event }}</span>
                    <div>{{ $item->nama_event ?? '-' }} <span class="muted">{{ $item->keterangan }}</span></div>
                </div>
            @endforeach

            @if(isset($todayTugas) && $todayTugas->isNotEmpty())
                @foreach($todayTugas as $tugas)
                    <div class="today-row">
                        <span class="badge {{ $tugas->status == 'approved' ? 'approve' : ($tugas->status == 'rejected' ? 'reject' : 'pending') }}">{{ ucfirst($tugas->status) }}</span>
                        <div>{{ $tugas->uraian }}</div>
                    </div>
                @endforeach
            @endif

            @if(! $todayWeeklyOff && $todayItems->isEmpty() && (! isset($todayTugas) || $todayTugas->isEmpty()) && $todayReplacementCuti->isEmpty())
                <div class="empty-note">Tidak ada libur atau kegiatan khusus hari ini.</div>
            @endif
        </div>

        @if(($liburKompensasi ?? collect())->isNotEmpty())
            <div class="today-title" style="margin-top:6px;">Libur Kompensasi Tersedia</div>
            <div class="today-list">
                @foreach($liburKompensasi->take(3) as $kompensasi)
                    <div class="today-row">
                        <span class="badge approve">kompensasi</span>
                        <div>{{ $kompensasi->tanggal_kerja->translatedFormat('d F Y') }} <span class="muted">{{ $kompensasi->keterangan }}</span></div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<script>
(function() {
    var months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    var absensiDetails = @json($absensiCalendarDetails ?? []);
    var tugasDetails = @json($tugasCalendarDetails ?? []);
    var eventDetails = @json($eventCalendarDetails ?? []);
    var weeklyOffDetails = @json($weeklyOffCalendarDetails ?? []);
    var replacementDetails = @json($replacementCalendarDetails ?? []);
    var todayKey = '{{ today()->format('Y-m-d') }}';
    var selectedKey = document.querySelector('.cal-cell.today')?.dataset.date
        || document.querySelector('.cal-cell:not(.other)')?.dataset.date
        || todayKey;

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, function (char) {
            return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[char];
        });
    }

    function absensiColor(status) {
        var normalized = String(status || '').toLowerCase();
        if (normalized.indexOf('telat') !== -1 || normalized.indexOf('terlambat') !== -1) return 'var(--amber)';
        if (normalized.indexOf('tidak') !== -1 || normalized.indexOf('absen') !== -1) return 'var(--red)';
        if (normalized.indexOf('cuti') !== -1) return '#EC4899';
        return 'var(--green)';
    }

    function detailRows(key) {
        var rows = [];
        (absensiDetails[key] || []).forEach(function(item) {
            rows.push({ color: absensiColor(item.nama), title: item.nama, meta: item.waktu + ' - ' + item.status });
        });
        (tugasDetails[key] || []).forEach(function(item) {
            rows.push({ color: '#8B5CF6', title: item.nama, meta: item.waktu + ' - ' + item.status });
        });
        (eventDetails[key] || []).forEach(function(item) {
            rows.push({ color: '#0EA5E9', title: item.nama, meta: item.status });
        });
        (weeklyOffDetails[key] || []).forEach(function(item) {
            rows.push({ color: '#14B8A6', title: item.nama, meta: item.status + ' - ' + item.waktu });
        });
        (replacementDetails[key] || []).forEach(function(item) {
            rows.push({ color: '#F59E0B', title: item.nama, meta: item.status + ' - ' + item.waktu });
        });
        return rows;
    }

    function formatTitle(key) {
        var date = new Date(key + 'T00:00:00');
        return date.getDate() + ' ' + months[date.getMonth()] + ' ' + date.getFullYear();
    }

    function renderDetail(key) {
        var box = document.getElementById('cal-detail');
        if (!box) return;
        var rows = detailRows(key);

        if (!rows.length) {
            box.innerHTML = '<div class="cal-detail-title">' + escapeHtml(formatTitle(key)) + '</div>'
                + '<div class="cal-detail-sub">Belum ada keterangan pada tanggal ini.</div>';
            return;
        }

        box.innerHTML = '<div class="cal-detail-title">' + escapeHtml(formatTitle(key)) + '</div>'
            + '<div class="cal-detail-list">'
            + rows.map(function(row) {
                return '<div class="cal-detail-row">'
                    + '<span class="cal-detail-dot" style="background:' + row.color + '"></span>'
                    + '<div class="cal-detail-text">'
                    + '<div class="cal-detail-main">' + escapeHtml(row.title) + '</div>'
                    + '<div class="cal-detail-sub">' + escapeHtml(row.meta) + '</div>'
                    + '</div>'
                    + '</div>';
            }).join('')
            + '</div>';
    }

    function markSelected() {
        document.querySelectorAll('.cal-cell').forEach(function(cell) {
            cell.classList.toggle('selected', cell.dataset.date === selectedKey);
        });
    }

    document.querySelectorAll('.cal-cell').forEach(function(cell) {
        cell.addEventListener('click', function() {
            selectedKey = cell.dataset.date;
            renderDetail(selectedKey);
            markSelected();
        });
    });

    renderDetail(selectedKey);
    markSelected();
})();
</script>
@endsection
