@extends('layouts.app')

@section('title', 'Kelola Regu')

@section('content')
@php
    $hariOptions = [
        0 => 'Minggu',
        1 => 'Senin',
        2 => 'Selasa',
        3 => 'Rabu',
        4 => 'Kamis',
        5 => 'Jumat',
        6 => 'Sabtu',
    ];
@endphp
<style>
    .regu-wrap { display: grid; gap: 16px; }
    .regu-card { background: var(--panel-bg); border: 1px solid var(--border-color); border-radius: 14px; overflow: hidden; }
    .regu-head { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:14px 16px; border-bottom:1px solid var(--border-color); }
    .regu-toggle { width:100%; background:transparent; color:var(--text-color); border:0; border-bottom:1px solid var(--border-color); border-radius:0; padding:14px 16px; display:flex; align-items:center; justify-content:space-between; gap:12px; font:inherit; cursor:pointer; text-align:left; }
    .regu-toggle:hover { background:var(--bg-color); }
    .regu-toggle-title { display:flex; align-items:center; gap:10px; min-width:0; }
    .regu-chevron { width:28px; height:28px; border-radius:8px; display:grid; place-items:center; background:var(--bg-color); border:1px solid var(--border-color); color:var(--primary); transition:transform .18s ease; flex:0 0 auto; }
    .regu-chevron svg { width:14px; height:14px; }
    .regu-card.is-open .regu-chevron { transform:rotate(180deg); }
    .regu-content { display:none; }
    .regu-card.is-open .regu-content { display:block; }
    .regu-head h2 { margin:0; }
    .regu-count { color: var(--muted); font-size: 12px; }
    .regu-table-wrap { overflow-x:auto; }
    .regu-table { width:100%; min-width:760px; border-collapse:collapse; }
    .regu-table th, .regu-table td { padding:12px 14px; border-bottom:1px solid var(--border-color); text-align:left; vertical-align:middle; }
    .regu-table th { background:var(--bg-color); color:var(--muted); font-size:11px; text-transform:uppercase; letter-spacing:.04em; }
    .regu-table tr:last-child td { border-bottom:0; }
    .regu-person { font-weight:700; color:var(--text-color); }
    .regu-muted { color:var(--muted); font-size:12px; margin-top:2px; }
    .regu-actions { display:flex; gap:8px; align-items:center; flex-wrap:wrap; }
    .regu-form { display:grid; gap:14px; padding:16px; }
    .regu-form-grid { display:grid; grid-template-columns: minmax(180px, .8fr) minmax(220px, 1fr); gap:12px; align-items:end; }
    .regu-member-grid { display:grid; grid-template-columns: repeat(5, minmax(150px, 1fr)); gap:10px; }
    .regu-ops { display:grid; grid-template-columns: minmax(220px, 1fr) auto; gap:12px; align-items:end; padding:14px 16px; border-bottom:1px solid var(--border-color); background:var(--bg-color); }
    .regu-ops-buttons { display:flex; gap:8px; align-items:center; flex-wrap:wrap; justify-content:flex-end; }
    .regu-shift-select { min-width:130px; }
    @media (max-width: 1000px) { .regu-member-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } .regu-form-grid { grid-template-columns:1fr; } }
    @media (max-width: 560px) { .regu-member-grid, .regu-ops { grid-template-columns:1fr; } }
</style>

<div class="regu-wrap">
    <div>
        <h1>Kelola Regu</h1>
        <p class="muted">Atasan membuat regu berisi 5 petugas dan menentukan siapa ketua regunya.</p>
    </div>

    <section class="regu-card">
        <div class="regu-head">
            <h2>Buat Regu</h2>
            <div class="regu-count">Pilih tepat 5 orang</div>
        </div>
        <form method="POST" action="{{ route('atasan.regu.store') }}" class="regu-form" id="reguForm">
            @csrf
            @if($petugasList->count() < 5)
                <div class="error" style="margin:0;">
                    Petugas yang belum punya regu kurang dari 5 orang. Saat ini tersedia {{ $petugasList->count() }} petugas.
                </div>
            @endif
            <div class="regu-form-grid">
                <div>
                    <label for="nama_regu">Nama Regu</label>
                    <input id="nama_regu" name="nama_regu" value="{{ old('nama_regu') }}" placeholder="Contoh: Regu 1" required>
                </div>
                <div>
                    <label for="ketua_id">Ketua Regu</label>
                    <select id="ketua_id" name="ketua_id" required>
                        <option value="">Pilih ketua dari anggota yang dipilih</option>
                        @foreach($petugasList as $petugas)
                            <option value="{{ $petugas->id_user }}" @selected(old('ketua_id') == $petugas->id_user)>
                                {{ $petugas->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label>Anggota Regu</label>
                <div class="regu-member-grid">
                    @for($i = 0; $i < 5; $i++)
                        <select name="anggota_ids[]" class="anggota-select" required>
                            <option value="">Anggota {{ $i + 1 }}</option>
                            @foreach($petugasList as $petugas)
                                <option value="{{ $petugas->id_user }}" @selected((old('anggota_ids.' . $i) ?? null) == $petugas->id_user)>
                                    {{ $petugas->nama }}
                                </option>
                            @endforeach
                        </select>
                    @endfor
                </div>
            </div>
            <div class="regu-actions">
                <button type="submit" @disabled($petugasList->count() < 5)>Simpan Regu</button>
                <span class="muted">Ketua wajib salah satu dari 5 anggota.</span>
            </div>
        </form>
    </section>

    @forelse($petugasByRegu as $regu => $items)
        <section class="regu-card {{ $loop->first ? 'is-open' : '' }}">
            <button type="button" class="regu-toggle" aria-expanded="{{ $loop->first ? 'true' : 'false' }}">
                <span class="regu-toggle-title">
                    <span class="regu-chevron" aria-hidden="true">
                        <svg fill="none" viewBox="0 0 16 16"><path d="M4 6l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <span>
                        <strong>{{ $regu }}</strong>
                        <span class="regu-count" style="display:block;margin-top:2px;">{{ $items->count() }} petugas</span>
                    </span>
                </span>
                @php $ketua = $items->firstWhere('is_ketua_regu', true); @endphp
                <span class="regu-count">Ketua: {{ $ketua->nama ?? '-' }}</span>
            </button>
            <div class="regu-content">
            @php
                $formId = 'reguUpdate' . $loop->iteration;
                $isReguAktif = $regu !== 'Belum Ada Regu';
                $currentTempatIds = $items->pluck('id_tempat')->filter()->unique()->values();
                $currentTempatId = $currentTempatIds->count() === 1 ? $currentTempatIds->first() : null;
            @endphp
            @if($isReguAktif)
            <form id="{{ $formId }}" method="POST" action="{{ route('atasan.regu.update-operasional') }}" style="margin:0;">
                @csrf
                <input type="hidden" name="nama_regu" value="{{ $regu }}">
            </form>
            <div class="regu-ops">
                <div>
                    <label for="tempat_{{ $loop->iteration }}">Tempat Kerja Regu</label>
                    <select id="tempat_{{ $loop->iteration }}" name="id_tempat" form="{{ $formId }}">
                        <option value="">-</option>
                        @foreach($tempatTugas as $tempat)
                            <option value="{{ $tempat->id_tempat }}" @selected((string) $currentTempatId === (string) $tempat->id_tempat)>
                                {{ $tempat->nama_tempat }}
                            </option>
                        @endforeach
                    </select>
                    @if($currentTempatIds->count() > 1)
                        <small class="muted">Anggota regu ini masih tersebar di beberapa tempat kerja.</small>
                    @endif
                </div>
                <div class="regu-ops-buttons">
                    <button type="button" class="random-hari-libur" data-form="{{ $formId }}">Random Hari Libur</button>
                    <button type="submit" form="{{ $formId }}">Simpan Tempat, Shift & Libur</button>
                </div>
            </div>
            @endif
            <div class="regu-table-wrap">
                <table class="regu-table">
                    <thead>
                        <tr>
                            <th>Petugas</th>
                            <th>Tempat</th>
                            <th>Shift Petugas</th>
                            <th>Hari Libur</th>
                            <th>Status Ketua</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                            <tr>
                                <td>
                                    <div class="regu-person">{{ $item->nama }}</div>
                                    <div class="regu-muted">{{ $item->jabatan ?: 'Petugas' }}</div>
                                </td>
                                <td>{{ $item->tempatTugas->nama_tempat ?? '-' }}</td>
                                <td>
                                    @if($isReguAktif)
                                    <select class="regu-shift-select" name="shifts[{{ $item->id_user }}]" form="{{ $formId }}">
                                        <option value="">-</option>
                                        <option value="Shift 1" @selected($item->shift === 'Shift 1')>Shift 1</option>
                                        <option value="Shift 2" @selected($item->shift === 'Shift 2')>Shift 2</option>
                                        <option value="Shift 3" @selected($item->shift === 'Shift 3')>Shift 3</option>
                                    </select>
                                    @else
                                        <span class="muted">{{ $item->shift ?? '-' }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($isReguAktif)
                                    <select class="regu-shift-select hari-libur-select" name="hari_libur[{{ $item->id_user }}]" form="{{ $formId }}">
                                        <option value="">-</option>
                                        @foreach($hariOptions as $dayNumber => $dayName)
                                            <option value="{{ $dayNumber }}" @selected((string) $item->hari_libur === (string) $dayNumber)>{{ $dayName }}</option>
                                        @endforeach
                                    </select>
                                    @else
                                        <span class="muted">{{ $item->hariLiburLabel() }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->is_ketua_regu)
                                        <span class="badge approve">Ketua Regu</span>
                                    @else
                                        <span class="badge pending">Anggota</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="regu-actions">
                                        @if(! $item->is_ketua_regu && $item->regu)
                                            <form method="POST" action="{{ route('atasan.regu.ketua') }}" style="margin:0;">
                                                @csrf
                                                <input type="hidden" name="id_user" value="{{ $item->id_user }}">
                                                <button type="submit">Jadikan Ketua</button>
                                            </form>
                                        @else
                                            <span class="muted">{{ $item->regu ? 'Aktif' : 'Isi regu dulu' }}</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            </div>
        </section>
    @empty
        <div class="panel muted">Belum ada petugas di area tugas kamu.</div>
    @endforelse
</div>

<script>
    document.querySelectorAll('.regu-toggle').forEach(function (button) {
        button.addEventListener('click', function () {
            var card = button.closest('.regu-card');
            var isOpen = card.classList.toggle('is-open');
            button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });
    });

    document.getElementById('reguForm')?.addEventListener('submit', function (event) {
        var anggota = Array.from(document.querySelectorAll('.anggota-select')).map(function (select) { return select.value; }).filter(Boolean);
        var ketua = document.getElementById('ketua_id')?.value;
        var unique = Array.from(new Set(anggota));

        if (anggota.length !== 5 || unique.length !== 5) {
            event.preventDefault();
            alert('Pilih tepat 5 anggota berbeda untuk satu regu.');
            return;
        }

        if (!ketua || !unique.includes(ketua)) {
            event.preventDefault();
            alert('Ketua regu harus salah satu dari 5 anggota yang dipilih.');
        }
    });

    document.querySelectorAll('.random-hari-libur').forEach(function (button) {
        button.addEventListener('click', function () {
            var formId = button.dataset.form;
            var selects = Array.from(document.querySelectorAll('select.hari-libur-select[form="' + formId + '"]'));
            var days = ['0', '1', '2', '3', '4', '5', '6'];

            for (var i = days.length - 1; i > 0; i--) {
                var j = Math.floor(Math.random() * (i + 1));
                var temp = days[i];
                days[i] = days[j];
                days[j] = temp;
            }

            selects.forEach(function (select, index) {
                select.value = days[index % days.length];
            });
        });
    });
</script>
@endsection
