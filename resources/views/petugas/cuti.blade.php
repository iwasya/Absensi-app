@extends('layouts.app')

@section('title', 'Pengajuan Cuti')

@section('content')
<style>
    .cuti-page {
        max-width: 1120px;
        margin: 0 auto;
    }

    .cuti-hero {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 16px;
        align-items: center;
        margin-bottom: 16px;
        padding: 18px;
        background: var(--panel-bg);
        border: 1px solid var(--border-color);
        border-radius: 14px;
    }

    .cuti-hero h1 {
        margin: 0;
    }

    .cuti-hero p {
        margin: 6px 0 0;
        color: var(--muted);
        font-size: 13px;
        line-height: 1.6;
    }

    .cuti-quota-pill {
        display: inline-flex;
        align-items: center;
        min-height: 36px;
        padding: 7px 14px;
        border-radius: 99px;
        background: var(--primary-soft);
        border: 1px solid var(--primary-border);
        color: var(--primary2);
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
    }

    .cuti-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 300px;
        gap: 16px;
        align-items: start;
    }

    .cuti-panel {
        background: var(--panel-bg);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        padding: 18px;
        margin-bottom: 16px;
    }

    .cuti-panel-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        padding-bottom: 14px;
        margin-bottom: 16px;
        border-bottom: 1px solid var(--border-color);
    }

    .cuti-panel-head h2 {
        margin: 0;
    }

    .cuti-panel-head p {
        margin: 5px 0 0;
        color: var(--muted);
        font-size: 12px;
        line-height: 1.5;
    }

    .cuti-panel-badge {
        display: inline-flex;
        align-items: center;
        min-height: 28px;
        padding: 5px 10px;
        border-radius: 99px;
        background: var(--primary-soft);
        border: 1px solid var(--primary-border);
        color: var(--primary2);
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .cuti-form {
        display: grid;
        gap: 14px;
    }

    .cuti-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .cuti-full {
        grid-column: 1 / -1;
    }

    .cuti-textarea {
        min-height: 120px;
        line-height: 1.6;
    }

    .cuti-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding-top: 2px;
    }

    .cuti-actions .muted {
        font-size: 12px;
        line-height: 1.5;
    }

    .cuti-submit {
        min-height: 40px;
        padding-inline: 18px;
    }

    .cuti-side {
        position: sticky;
        top: 88px;
    }

    .cuti-summary {
        display: grid;
        gap: 10px;
    }

    .cuti-summary-item {
        padding: 12px;
        border: 1px solid var(--border-color);
        border-radius: 11px;
        background: var(--bg-color);
    }

    .cuti-summary-label {
        margin-bottom: 4px;
        color: var(--muted);
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .cuti-summary-value {
        color: var(--text-color);
        font-size: 18px;
        font-weight: 700;
        line-height: 1.2;
    }

    .cuti-summary-note {
        margin-top: 4px;
        color: var(--muted);
        font-size: 12px;
        line-height: 1.5;
    }

    .replacement-list {
        display: grid;
        gap: 10px;
    }

    .replacement-item {
        background: var(--bg-color);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 12px;
        display: grid;
        gap: 10px;
    }

    .replacement-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    .replacement-title {
        color: var(--text-color);
        font-weight: 700;
    }

    .replacement-meta {
        color: var(--muted);
        font-size: 12px;
        margin-top: 3px;
    }

    .cuti-table-wrap {
        overflow-x: auto;
    }

    .cuti-table-wrap table {
        min-width: 860px;
    }

    .cuti-date {
        white-space: nowrap;
        font-weight: 600;
    }

    .cuti-reason {
        min-width: 190px;
        line-height: 1.5;
    }

    .cuti-action-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 28px;
        padding: 5px 10px;
        border-radius: 7px;
        background: var(--green);
        color: #fff;
        font-size: 11px;
        font-weight: 700;
        text-decoration: none;
        white-space: nowrap;
    }

    .cuti-action-link:hover {
        color: #fff;
        background: #059669;
    }

    .field-error {
        margin-top: 5px;
        color: var(--red-dark);
        font-size: 12px;
        line-height: 1.4;
    }

    .cuti-empty {
        text-align: center;
        padding: 22px 14px;
    }

    @media (max-width: 920px) {
        .cuti-hero,
        .cuti-grid {
            grid-template-columns: 1fr;
        }

        .cuti-side {
            position: static;
        }

        .cuti-quota-pill {
            width: fit-content;
        }
    }

    @media (max-width: 640px) {
        .cuti-hero,
        .cuti-panel {
            padding: 16px;
        }

        .cuti-form-grid {
            grid-template-columns: 1fr;
        }

        .cuti-full {
            grid-column: auto;
        }

        .cuti-panel-head,
        .cuti-actions {
            display: grid;
        }

        .cuti-submit {
            width: 100%;
        }
    }
</style>

@php
    $sisaCuti = max($batasCutiTahunan - $cutiTerpakaiTahunIni, 0);
@endphp

<div class="cuti-page">
    <section class="cuti-hero">
        <div>
            <h1>Pengajuan Cuti</h1>
            <p>Ajukan cuti dengan data yang lengkap agar proses persetujuan oleh atasan lebih cepat.</p>
        </div>
        <div class="cuti-quota-pill">Sisa {{ $sisaCuti }} dari {{ $batasCutiTahunan }} kali</div>
    </section>

    <div class="cuti-grid">
        <section class="cuti-panel">
            <div class="cuti-panel-head">
                <div>
                    <h2>Ajukan Cuti</h2>
                    <p>Isi periode cuti, alasan, petugas pengganti, dan alamat selama cuti.</p>
                </div>
                <span class="cuti-panel-badge">Form Baru</span>
            </div>

            <form method="POST" action="{{ route('petugas.cuti.store') }}" class="cuti-form" enctype="multipart/form-data">
                @csrf
                <div class="cuti-form-grid">
                    <div>
                        <label for="tanggal_mulai">Tanggal Mulai</label>
                        <input id="tanggal_mulai" type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}" required>
                        @error('tanggal_mulai')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label for="tanggal_selesai">Tanggal Selesai</label>
                        <input id="tanggal_selesai" type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}" required>
                        @error('tanggal_selesai')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label for="jenis_cuti">Jenis Cuti</label>
                        <select id="jenis_cuti" name="jenis_cuti" required>
                            <option value="Tahunan" @selected(old('jenis_cuti') === 'Tahunan')>Tahunan</option>
                            <option value="Besar" @selected(old('jenis_cuti') === 'Besar')>Besar</option>
                            <option value="Sakit" @selected(old('jenis_cuti') === 'Sakit')>Sakit</option>
                            <option value="Kompensasi" @selected(old('jenis_cuti') === 'Kompensasi')>Kompensasi</option>
                        </select>
                        @error('jenis_cuti')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label for="alasan_select">Alasan</label>
                        <select name="alasan" id="alasan_select" required>
                            <option value="Sakit" @selected(old('alasan') === 'Sakit')>Sakit</option>
                            <option value="Urusan Keluarga" @selected(old('alasan') === 'Urusan Keluarga')>Urusan Keluarga</option>
                            <option value="Lamaran/Menikah" @selected(old('alasan') === 'Lamaran/Menikah')>Lamaran/Menikah</option>
                            <option value="Anggota Keluarga Meninggal" @selected(old('alasan') === 'Anggota Keluarga Meninggal')>Anggota Keluarga Meninggal</option>
                            <option value="Anggota Keluarga Sakit" @selected(old('alasan') === 'Anggota Keluarga Sakit')>Anggota Keluarga Sakit</option>
                            <option value="Anggota Keluarga Menikah" @selected(old('alasan') === 'Anggota Keluarga Menikah')>Anggota Keluarga Menikah</option>
                            <option value="Kegiatan Agama atau Budaya" @selected(old('alasan') === 'Kegiatan Agama atau Budaya')>Kegiatan Agama atau Budaya</option>
                            <option value="Musibah/Bencana" @selected(old('alasan') === 'Musibah/Bencana')>Musibah/Bencana</option>
                            <option value="Alasan Lainnya" @selected(old('alasan') === 'Alasan Lainnya')>Alasan Lainnya</option>
                        </select>
                        @error('alasan')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div id="alasan_lainnya_wrapper" style="display: none;">
                        <label for="alasan_lainnya">Sebutkan Alasan Lainnya</label>
                        <input id="alasan_lainnya" type="text" name="alasan_lainnya" value="{{ old('alasan_lainnya') }}" placeholder="Tuliskan alasan cuti">
                        @error('alasan_lainnya')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label for="id_pengganti">Pendamping Pengganti <small style="color: var(--muted);">(dari regu sama)</small></label>
                        <select id="id_pengganti" name="id_pengganti" required>
                            <option value="">-- Pilih Petugas Pengganti --</option>
                            @php
                                $groupedPetugas = $petugasList->groupBy(fn($p) => $p->regu ?: '(Tanpa Regu)');
                            @endphp
                            @foreach($groupedPetugas as $reguName => $anggota)
                                <optgroup label="{{ $reguName }}">
                                    @foreach($anggota as $p)
                                        <option value="{{ $p->id_user }}" @selected(old('id_pengganti') == $p->id_user)>{{ $p->nama }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        @error('id_pengganti')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="cuti-full">
                        <label for="alamat_cuti">Alamat Selama Cuti</label>
                        <textarea id="alamat_cuti" name="alamat_cuti" class="cuti-textarea" required placeholder="Tuliskan alamat lengkap selama masa cuti...">{{ old('alamat_cuti') }}</textarea>
                        @error('alamat_cuti')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="cuti-full">
                        <label for="dokumen">Bukti Dokumen</label>
                        <input id="dokumen" type="file" name="dokumen" accept=".pdf,.jpg,.jpeg,.png,.webp">
                        <small class="muted">Wajib untuk cuti sakit. Format PDF/JPG/PNG/WebP maksimal 4 MB.</small>
                        @error('dokumen')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="cuti-actions">
                    <div class="muted">Pastikan tanggal dan petugas pengganti sudah benar sebelum dikirim.</div>
                    <button type="submit" class="cuti-submit">Kirim Pengajuan</button>
                </div>
            </form>
        </section>

        <aside class="cuti-side">
            <section class="cuti-panel">
                <div class="cuti-panel-head">
                    <div>
                        <h2>Kuota Cuti</h2>
                        <p>Ringkasan pemakaian cuti tahun ini.</p>
                    </div>
                </div>

                <div class="cuti-summary">
                    <div class="cuti-summary-item">
                        <div class="cuti-summary-label">Terpakai</div>
                        <div class="cuti-summary-value">{{ $cutiTerpakaiTahunIni }}</div>
                        <div class="cuti-summary-note">Dari batas {{ $batasCutiTahunan }} kali cuti.</div>
                    </div>
                    <div class="cuti-summary-item">
                        <div class="cuti-summary-label">Sisa</div>
                        <div class="cuti-summary-value">{{ $sisaCuti }}</div>
                        <div class="cuti-summary-note">Sisa kesempatan cuti tahun ini.</div>
                    </div>
                    <div class="cuti-summary-item">
                        <div class="cuti-summary-label">Libur Kompensasi</div>
                        <div class="cuti-summary-value">{{ $liburKompensasiTersedia ?? 0 }}</div>
                        <div class="cuti-summary-note">Hak libur pengganti tersedia.</div>
                    </div>
                </div>
            </section>
        </aside>
    </div>

    @if(($replacementRequests ?? collect())->isNotEmpty())
        <section class="cuti-panel">
            <div class="cuti-panel-head">
                <div>
                    <h2>Permintaan Pengganti Cuti</h2>
                    <p>Terima jika kamu siap menggantikan jadwal petugas yang cuti.</p>
                </div>
                <span class="cuti-panel-badge">{{ $replacementRequests->count() }} Pending</span>
            </div>
            <div style="padding:16px;">
                <div class="replacement-list">
                    @foreach($replacementRequests as $requestCuti)
                        <div class="replacement-item">
                            <div class="replacement-top">
                                <div>
                                    <div class="replacement-title">{{ $requestCuti->user->nama ?? '-' }}</div>
                                    <div class="replacement-meta">
                                        {{ $requestCuti->tanggal_mulai->format('d/m/Y') }} - {{ $requestCuti->tanggal_selesai->format('d/m/Y') }}
                                        · {{ $requestCuti->jenis_cuti }}
                                    </div>
                                </div>
                                <span class="badge pending">Menunggu jawabanmu</span>
                            </div>
                            <div class="cuti-actions">
                                <form method="POST" action="{{ route('petugas.cuti.pengganti.terima', $requestCuti->id_cuti) }}" style="margin:0;">
                                    @csrf
                                    <button type="submit" class="cuti-submit">Terima</button>
                                </form>
                                <form method="POST" action="{{ route('petugas.cuti.pengganti.tolak', $requestCuti->id_cuti) }}" style="margin:0;">
                                    @csrf
                                    <button type="submit" class="cuti-action-link" style="border:0;background:var(--red);cursor:pointer;">Tolak</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="cuti-panel">
        <div class="cuti-panel-head">
            <div>
                <h2>Riwayat Pengajuan</h2>
                <p>Pantau status pengajuan cuti dan cetak surat jika sudah disetujui.</p>
            </div>
            <span class="cuti-panel-badge">{{ $items->total() ?? $items->count() }} Data</span>
        </div>

        <div class="cuti-table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Mulai</th>
                        <th>Selesai</th>
                        <th>Jenis</th>
                        <th>Alasan</th>
                        <th>Pengganti</th>
                        <th>Konfirmasi</th>
                        <th>Dokumen</th>
                        <th>Admin</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td class="cuti-date">{{ $item->tanggal_mulai->format('d/m/Y') }}</td>
                            <td class="cuti-date">{{ $item->tanggal_selesai->format('d/m/Y') }}</td>
                            <td>{{ $item->jenis_cuti }}</td>
                            <td class="cuti-reason">
                                {{ $item->alasan }}
                                @if($item->alasan == 'Alasan Lainnya')
                                    <br><small class="muted">({{ $item->alasan_lainnya }})</small>
                                @endif
                            </td>
                            <td>{{ $item->pengganti->nama ?? '-' }}</td>
                            <td>
                                <span class="badge {{ $item->replacement_status ?? 'pending' }}">
                                    {{ $item->replacement_status === 'accepted' ? 'Diterima' : ($item->replacement_status === 'rejected' ? 'Ditolak' : 'Pending') }}
                                </span>
                            </td>
                            <td>
                                @if($item->dokumen_path)
                                    <a href="{{ asset('storage/' . $item->dokumen_path) }}" target="_blank" class="cuti-action-link">Lihat</a>
                                @else
                                    <span class="muted">-</span>
                                @endif
                            </td>
                            <td><span class="badge {{ $item->admin_status }}">{{ $item->admin_status ?? 'pending' }}</span></td>
                            <td><span class="badge {{ $item->status }}">{{ $item->status }}</span></td>
                            <td>
                                @if($item->status === 'approve')
                                    <a href="{{ route('petugas.cuti.print', $item->id_cuti) }}" target="_blank" class="cuti-action-link">Cetak Surat</a>
                                @else
                                    <span class="muted" style="font-size: 11px;">Menunggu Approval</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="muted cuti-empty">Belum ada pengajuan cuti.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $items->links('pagination.simple') }}
    </section>
</div>

<script>
    var alasanSelect = document.getElementById('alasan_select');
    var wrapper = document.getElementById('alasan_lainnya_wrapper');

    function syncAlasanLainnya() {
        if (!alasanSelect || !wrapper) return;

        var input = wrapper.querySelector('input');
        var isLainnya = alasanSelect.value === 'Alasan Lainnya';
        wrapper.style.display = isLainnya ? 'block' : 'none';
        if (input) input.required = isLainnya;
    }

    if (alasanSelect) {
        alasanSelect.addEventListener('change', syncAlasanLainnya);
        syncAlasanLainnya();
    }
</script>
@endsection
