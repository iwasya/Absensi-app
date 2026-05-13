@extends('layouts.app')

@section('title', 'Pengajuan Cuti')

@section('content')
    <h1>Pengajuan Cuti</h1>
    <div class="panel" style="max-width: 100%;">
        <h2>Ajukan Cuti</h2>
        <p class="muted">
            Kuota cuti tahun ini: {{ $cutiTerpakaiTahunIni }} dari {{ $batasCutiTahunan }} kali terpakai.
            Sisa {{ max($batasCutiTahunan - $cutiTerpakaiTahunIni, 0) }} kali.
        </p>
        <form method="POST" action="{{ route('petugas.cuti.store') }}">
            @csrf
            <div class="form-grid">
                <div><label>Tanggal Mulai</label><input type="date" name="tanggal_mulai" required></div>
                <div><label>Tanggal Selesai</label><input type="date" name="tanggal_selesai" required></div>
                <div>
                    <label>Jenis Cuti</label>
                    <select name="jenis_cuti" required>
                        <option value="Tahunan">Tahunan</option>
                        <option value="Besar">Besar</option>
                    </select>
                </div>
                <div>
                    <label>Alasan</label>
                    <select name="alasan" id="alasan_select" required>
                        <option value="Sakit">Sakit</option>
                        <option value="Urusan Keluarga">Urusan Keluarga</option>
                        <option value="Lamaran/Menikah">Lamaran/Menikah</option>
                        <option value="Anggota Keluarga Meninggal">Anggota Keluarga Meninggal</option>
                        <option value="Anggota Keluarga Sakit">Anggota Keluarga Sakit</option>
                        <option value="Anggota Keluarga Menikah">Anggota Keluarga Menikah</option>
                        <option value="Kegiatan Agama atau Budaya">Kegiatan Agama atau Budaya</option>
                        <option value="Musibah/Bencana">Musibah/Bencana</option>
                        <option value="Alasan Lainnya">Alasan Lainnya</option>
                    </select>
                </div>
                <div id="alasan_lainnya_wrapper" style="display: none;">
                    <label>Sebutkan Alasan Lainnya</label>
                    <input type="text" name="alasan_lainnya">
                </div>
                <div>
                    <label>Pendamping Pengganti</label>
                    <select name="id_pengganti" required>
                        <option value="">-- Pilih Petugas Pengganti --</option>
                        @foreach($petugasList as $p)
                            <option value="{{ $p->id_user }}">{{ $p->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <label style="margin-top:12px">Alamat Selama Cuti</label>
            <textarea name="alamat_cuti" required placeholder="Tuliskan alamat lengkap selama masa cuti..."></textarea>
            <button type="submit" style="margin-top:12px">Kirim Pengajuan</button>
        </form>
    </div>

    <table>
        <thead><tr><th>Mulai</th><th>Selesai</th><th>Jenis</th><th>Alasan</th><th>Pengganti</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
            @forelse($items as $item)
                <tr>
                    <td>{{ $item->tanggal_mulai->format('d/m/Y') }}</td>
                    <td>{{ $item->tanggal_selesai->format('d/m/Y') }}</td>
                    <td>{{ $item->jenis_cuti }}</td>
                    <td>
                        {{ $item->alasan }}
                        @if($item->alasan == 'Alasan Lainnya')
                            <br><small class="muted">({{ $item->alasan_lainnya }})</small>
                        @endif
                    </td>
                    <td>{{ $item->pengganti->nama ?? '-' }}</td>
                    <td><span class="badge {{ $item->status }}">{{ $item->status }}</span></td>
                    <td>
                        @if($item->status === 'approve')
                            <a href="{{ route('petugas.cuti.print', $item->id_cuti) }}" target="_blank" class="button" style="padding: 4px 8px; font-size: 11px; background: #059669; color: white; border-radius: 4px; text-decoration: none;">Cetak Surat</a>
                        @else
                            <span class="muted" style="font-size: 11px;">Menunggu Approval</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="muted">Belum ada pengajuan cuti.</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $items->links('pagination.simple') }}

    <script>
        document.getElementById('alasan_select').addEventListener('change', function() {
            var wrapper = document.getElementById('alasan_lainnya_wrapper');
            if (this.value === 'Alasan Lainnya') {
                wrapper.style.display = 'block';
                wrapper.querySelector('input').required = true;
            } else {
                wrapper.style.display = 'none';
                wrapper.querySelector('input').required = false;
            }
        });
    </script>
@endsection
