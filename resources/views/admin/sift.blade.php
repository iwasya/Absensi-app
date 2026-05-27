@extends('layouts.app')

@section('title', 'SIFT - Shift & Timetable')

@section('content')
    <h1>SIFT - Shift & Timetable</h1>

    {{-- Tab Navigation --}}
    <div class="panel" style="padding: 0; margin-bottom: 16px;">
        <div style="display: flex; border-bottom: 1px solid var(--border-color);">
            <a href="{{ route('admin.sift.index', ['tab' => 'shifts']) }}"
               class="tab-btn {{ $activeTab === 'shifts' ? 'active' : '' }}">
                <svg fill="none" viewBox="0 0 16 16" style="width:14px;height:14px;"><circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.3"/><path d="M8 5v3l2 2" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                Master Shift
            </a>
            <a href="{{ route('admin.sift.index', ['tab' => 'petugas']) }}"
               class="tab-btn {{ $activeTab === 'petugas' ? 'active' : '' }}">
                <svg fill="none" viewBox="0 0 16 16" style="width:14px;height:14px;"><circle cx="6" cy="5" r="3" stroke="currentColor" stroke-width="1.3"/><path d="M1 14c0-3 2.2-5 5-5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                Assign Petugas
            </a>
        </div>
    </div>

    {{-- SUCCESS/ERROR MESSAGES --}}
    @if(session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="error">{{ session('error') }}</div>
    @endif

    {{-- ======================== TAB 1: MASTER SHIFT ======================== --}}
    @if($activeTab === 'shifts')
    <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 20px;">

        {{-- Form Tambah Shift Baru --}}
        <div class="panel">
            <h2 style="margin-bottom: 16px;">+ Tambah Shift Baru</h2>
            <form method="POST" action="{{ route('admin.sift.store-shift') }}">
                @csrf
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <div>
                        <label>Nama Shift</label>
                        <input type="text" name="nama_shift" placeholder="Contoh: Shift 1, Shift Pagi, Shift Malam" required>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <div>
                            <label>Jam Masuk</label>
                            <input type="time" name="jam_masuk" value="07:00" required>
                        </div>
                        <div>
                            <label>Durasi (Jam)</label>
                            <input type="number" name="durasi_jam" value="8" min="1" max="24" required>
                        </div>
                    </div>
                    <div>
                        <label>Warna (Hex)</label>
                        <div style="display: flex; gap: 8px; align-items: center;">
                            <input type="color" name="warna" value="#3B82F6" style="width: 40px; height: 40px; padding: 2px; cursor: pointer;">
                            <span style="color: var(--muted); font-size: 12px;">Pilih warna untuk membedakan shift di kalender</span>
                        </div>
                    </div>
                    <button type="submit" style="width: 100%;">Simpan Shift</button>
                </div>
            </form>
        </div>

        {{-- Daftar Shift --}}
        <div class="panel">
            <h2 style="margin-bottom: 16px;">
                Daftar Shift
                <a href="{{ route('admin.sift.export') }}" style="float: right; font-size: 12px; font-weight: normal;">
                    Download CSV
                </a>
            </h2>

            @if($shifts->isEmpty())
                <div style="text-align: center; padding: 30px; color: var(--muted);">
                    <svg fill="none" viewBox="0 0 24 24" style="width:48px;height:48px;margin-bottom:12px;opacity:0.5;"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/><path d="M12 8v4M12 16h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    <p>Belum ada shift. Tambahkan shift baru di form sebelah.</p>
                </div>
            @else
                <table style="font-size: 12px;">
                    <thead>
                        <tr>
                            <th>Warna</th>
                            <th>Nama Shift</th>
                            <th>Jam Kerja</th>
                            <th>Durasi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($shifts as $shift)
                        <tr>
                            <td>
                                <div style="width: 20px; height: 20px; border-radius: 50%; background: {{ $shift->warna }}; border: 2px solid rgba(0,0,0,0.1);"></div>
                            </td>
                            <td>
                                <strong>{{ $shift->nama_shift }}</strong>
                            </td>
                            <td>
                                @if($shift->jam_masuk && $shift->jam_pulang)
                                    {{ \Carbon\Carbon::parse($shift->jam_masuk)->format('H:i') }}
                                    -
                                    {{ \Carbon\Carbon::parse($shift->jam_pulang)->format('H:i') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $shift->durasi_jam }} jam</td>
                            <td>
                                <span class="badge {{ $shift->status ? 'approve' : 'reject' }}">
                                    {{ $shift->status ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td>
                                <div class="sift-action-group">
                                    {{-- Edit --}}
                                    <button type="button"
                                        class="sift-btn sift-btn-edit"
                                        onclick="editShift({{ $shift->id }}, '{{ $shift->nama_shift }}', '{{ $shift->jam_masuk ? \Carbon\Carbon::parse($shift->jam_masuk)->format('H:i') : '' }}', {{ $shift->durasi_jam }}, '{{ $shift->warna }}')">
                                        <svg fill="none" viewBox="0 0 14 14" style="width:12px;height:12px;flex-shrink:0"><path d="M9.5 1.5l3 3L5 12l-3 .5.5-3L9.5 1.5z" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        Edit
                                    </button>

                                    {{-- Toggle Aktif / Nonaktif --}}
                                    <form method="POST" action="{{ route('admin.sift.toggle-shift', $shift->id) }}" style="margin:0;">
                                        @csrf
                                        @if($shift->status)
                                            <button type="submit" class="sift-btn sift-btn-deactivate">
                                                <svg fill="none" viewBox="0 0 14 14" style="width:12px;height:12px;flex-shrink:0"><rect x="3" y="2" width="3" height="10" rx="1" fill="currentColor"/><rect x="8" y="2" width="3" height="10" rx="1" fill="currentColor"/></svg>
                                                Nonaktifkan
                                            </button>
                                        @else
                                            <button type="submit" class="sift-btn sift-btn-activate">
                                                <svg fill="none" viewBox="0 0 14 14" style="width:12px;height:12px;flex-shrink:0"><path d="M2 7l4 4 6-6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                Aktifkan
                                            </button>
                                        @endif
                                    </form>

                                    {{-- Hapus --}}
                                    <form method="POST" action="{{ route('admin.sift.destroy-shift', $shift->id) }}" style="margin:0;"
                                          onsubmit="return confirm('Hapus shift {{ $shift->nama_shift }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="sift-btn sift-btn-delete">
                                            <svg fill="none" viewBox="0 0 14 14" style="width:12px;height:12px;flex-shrink:0"><path d="M2 4h10M9 4V3H5v1M6 7v4M8 7v4M3 4l.7 8h6.6L11 4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    {{-- Modal Edit Shift --}}
    <div id="editShiftModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; align-items: center; justify-content: center;">
        <div style="background: var(--panel-bg); border-radius: 12px; padding: 24px; width: 400px; max-width: 90%;">
            <h2 style="margin-bottom: 16px;">Edit Shift</h2>
            <form method="POST" id="editShiftForm">
                @csrf
                @method('PUT')
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <div>
                        <label>Nama Shift</label>
                        <input type="text" name="nama_shift" id="edit_nama_shift" required>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <div>
                            <label>Jam Masuk</label>
                            <input type="time" name="jam_masuk" id="edit_jam_masuk" required>
                        </div>
                        <div>
                            <label>Durasi (Jam)</label>
                            <input type="number" name="durasi_jam" id="edit_durasi_jam" min="1" max="24" required>
                        </div>
                    </div>
                    <div>
                        <label>Warna</label>
                        <input type="color" name="warna" id="edit_warna" style="width: 60px; height: 36px;">
                    </div>
                    <div style="display: flex; gap: 8px; margin-top: 8px;">
                        <button type="submit" class="sift-btn sift-btn-save">
                            <svg fill="none" viewBox="0 0 14 14" style="width:13px;height:13px;flex-shrink:0"><path d="M2 7l4 4 6-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            Simpan
                        </button>
                        <button type="button" onclick="closeEditModal()" class="sift-btn sift-btn-cancel">
                            <svg fill="none" viewBox="0 0 14 14" style="width:13px;height:13px;flex-shrink:0"><path d="M3 3l8 8M11 3L3 11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                            Batal
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- ======================== TAB 2: ASSIGN PETUGAS ======================== --}}
    @if($activeTab === 'petugas')
    <div class="panel" style="margin-bottom: 16px;">
        <h2 style="margin-bottom: 16px;">
            Assign Shift ke Petugas
            <a href="{{ route('admin.sift.export') }}" style="float: right; font-size: 12px; font-weight: normal;">Download CSV</a>
        </h2>

        {{-- Bulk Assign Form --}}
        <div style="background: var(--bg-color); border-radius: 9px; padding: 16px; margin-bottom: 16px;">
            <h3 style="font-size: 13px; margin-bottom: 12px;">Bulk Assign (Pilih beberapa petugas)</h3>
            <form method="POST" action="{{ route('admin.sift.bulk-assign') }}">
                @csrf
                <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
                    <label style="font-size: 12px;">Pilih Petugas:</label>
                    <select name="shift" style="padding: 8px; min-width: 150px;">
                        <option value="">-- Tidak Ada Shift --</option>
                        @foreach($shifts as $shift)
                            <option value="{{ $shift->nama_shift }}">{{ $shift->nama_shift }} ({{ $shift->jam_masuk ? \Carbon\Carbon::parse($shift->jam_masuk)->format('H:i') : '' }} - {{ $shift->jam_pulang ? \Carbon\Carbon::parse($shift->jam_pulang)->format('H:i') : '' }})</option>
                        @endforeach
                    </select>
                    <button type="submit" class="sift-btn sift-btn-bulk"
                            onclick="return confirm('Yakin assign shift ke petugas yang dipilih?')">
                        <svg fill="none" viewBox="0 0 14 14" style="width:13px;height:13px;flex-shrink:0"><path d="M2 7l4 4 6-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Update Shift Terpilih
                    </button>
                </div>
            </form>
        </div>

        {{-- Petugas List with Shift Assignment --}}
        <form id="assignForm" method="POST" action="{{ route('admin.sift.bulk-assign') }}">
            @csrf
            <input type="hidden" name="shift" id="bulk_shift_value" value="">

            <table>
                <thead>
                    <tr>
                        <th style="width: 40px;"><input type="checkbox" id="selectAll"></th>
                        <th>Petugas</th>
                        <th>Regu</th>
                        <th>Tempat</th>
                        <th>Shift Saat Ini</th>
                        <th>Jam Kerja</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td><input type="checkbox" name="user_ids[]" value="{{ $user->id_user }}" class="row-checkbox"></td>
                        <td>
                            <strong>{{ $user->nama }}</strong><br>
                            <small class="muted">{{ $user->username }}</small>
                        </td>
                        <td>{{ $user->regu ?? '-' }}</td>
                        <td>{{ $user->tempatTugas->nama_tempat ?? '-' }}</td>
                        <td>
                            @if($user->shift)
                                <span class="badge approve">{{ $user->shift }}</span>
                            @else
                                <span class="badge pending">Belum ada</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $userShift = $shifts->firstWhere('nama_shift', $user->shift);
                            @endphp
                            @if($userShift && $userShift->jam_masuk && $userShift->jam_pulang)
                                {{ \Carbon\Carbon::parse($userShift->jam_masuk)->format('H:i') }}
                                -
                                {{ \Carbon\Carbon::parse($userShift->jam_pulang)->format('H:i') }}
                            @else
                                <span class="muted">-</span>
                            @endif
                        </td>
                        <td>
                            <form method="POST" action="{{ route('admin.sift.assign') }}" style="display: inline;">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $user->id_user }}">
                                <select name="shift" style="padding: 5px; border: 1px solid var(--border2); border-radius: 6px; font-size: 11px; min-width: 120px;">
                                    <option value="">--</option>
                                    @foreach($shifts as $shift)
                                        <option value="{{ $shift->nama_shift }}" {{ $user->shift === $shift->nama_shift ? 'selected' : '' }}>
                                            {{ $shift->nama_shift }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit" class="sift-btn sift-btn-update">
                                    <svg fill="none" viewBox="0 0 14 14" style="width:11px;height:11px;flex-shrink:0"><path d="M2 7l4 4 6-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    Simpan
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="muted" style="text-align: center;">Belum ada petugas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </form>
    </div>
    @endif

    <style>
        .tab-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 12px 20px;
            font-size: 13px;
            font-weight: 500;
            color: var(--muted);
            border-bottom: 2px solid transparent;
            transition: color .15s, border-color .15s;
            text-decoration: none;
        }
        .tab-btn:hover { color: var(--text-color); }
        .tab-btn.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
        }

        /* ── Sift Action Buttons ── */
        .sift-action-group {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
            align-items: center;
        }

        .sift-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            padding: 6px 11px;
            cursor: pointer;
            transition: all .18s ease;
            white-space: nowrap;
            text-decoration: none;
            line-height: 1;
        }
        .sift-btn:hover { transform: translateY(-1px); box-shadow: 0 4px 10px rgba(0,0,0,.12); }
        .sift-btn:active { transform: translateY(0); box-shadow: none; }

        /* Edit – amber */
        .sift-btn-edit {
            background: rgba(245,158,11,.10);
            color: #b45309;
            border: 1px solid rgba(245,158,11,.30);
        }
        .sift-btn-edit:hover { background: rgba(245,158,11,.20); border-color: rgba(245,158,11,.5); }

        /* Aktifkan – green */
        .sift-btn-activate {
            background: rgba(34,197,94,.10);
            color: #15803d;
            border: 1px solid rgba(34,197,94,.28);
        }
        .sift-btn-activate:hover { background: rgba(34,197,94,.20); border-color: rgba(34,197,94,.5); }

        /* Nonaktifkan – blue */
        .sift-btn-deactivate {
            background: rgba(59,130,246,.10);
            color: var(--primary2, #1d4ed8);
            border: 1px solid rgba(59,130,246,.25);
        }
        .sift-btn-deactivate:hover { background: rgba(59,130,246,.18); }

        /* Hapus – red */
        .sift-btn-delete {
            background: rgba(239,68,68,.10);
            color: #dc2626;
            border: 1px solid rgba(239,68,68,.25);
        }
        .sift-btn-delete:hover { background: rgba(239,68,68,.18); border-color: rgba(239,68,68,.4); }

        /* Update per-row – primary solid */
        .sift-btn-update {
            background: var(--primary);
            color: #fff;
            border: 1px solid transparent;
            padding: 6px 13px;
        }
        .sift-btn-update:hover { background: #2563eb; }

        /* Bulk assign */
        .sift-btn-bulk {
            background: var(--primary);
            color: #fff;
            border: 1px solid transparent;
            padding: 9px 16px;
            font-size: 13px;
        }
        .sift-btn-bulk:hover { background: #2563eb; }

        /* Modal – save */
        .sift-btn-save {
            flex: 1;
            background: var(--primary);
            color: #fff;
            border: 1px solid transparent;
            padding: 10px 16px;
            font-size: 13px;
        }
        .sift-btn-save:hover { background: #2563eb; }

        /* Modal – cancel */
        .sift-btn-cancel {
            flex: 1;
            background: var(--bg-color);
            color: var(--text-color);
            border: 1px solid var(--border-color);
            padding: 10px 16px;
            font-size: 13px;
        }
        .sift-btn-cancel:hover { background: var(--border-color); }
    </style>
@endsection

@section('scripts')
<script>
    // Select All Checkbox
    document.getElementById('selectAll')?.addEventListener('change', function() {
        document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = this.checked);
    });

    // Edit Shift Modal
    function editShift(id, nama, jamMasuk, durasi, warna) {
        document.getElementById('editShiftForm').action = '/admin/sift/shift/' + id;
        document.getElementById('edit_nama_shift').value = nama;
        document.getElementById('edit_jam_masuk').value = jamMasuk;
        document.getElementById('edit_durasi_jam').value = durasi;
        document.getElementById('edit_warna').value = warna;
        document.getElementById('editShiftModal').style.display = 'flex';
    }

    function closeEditModal() {
        document.getElementById('editShiftModal').style.display = 'none';
    }

    // Close modal on outside click
    document.getElementById('editShiftModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeEditModal();
    });
</script>
@endsection