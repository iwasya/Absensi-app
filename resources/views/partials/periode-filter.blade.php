<div class="panel">
    <form method="GET" action="{{ url()->current() }}" class="filter-bar">
        <div class="filter-control">
            <label for="id_periode">Periode Data</label>
            <select id="id_periode" name="id_periode">
                <option value="">Semua tahun</option>

                @foreach($periodes ?? [] as $periode)
                    <option value="{{ $periode->id_periode }}"
                        @selected(optional($selectedPeriode ?? null)->id_periode === $periode->id_periode)>
                        
                        {{ \Carbon\Carbon::parse($periode->tanggal_mulai)->format('Y') }}
                    </option>
                @endforeach

            </select>
        </div>

        <button type="submit">Tampilkan</button>

        @if(isset($exportUrl))
            <button type="submit" formaction="{{ $exportUrl }}" formmethod="GET">
                {{ $exportLabel ?? 'Download CSV' }}
            </button>
        @endif

        @if(isset($selectedPeriode) && $selectedPeriode)
            <a href="{{ url()->current() }}">Reset</a>
        @endif

    </form>

    <p class="muted" style="margin-bottom:0">

        @if(isset($selectedPeriode) && $selectedPeriode)
            Menampilkan arsip tahun 
            {{ \Carbon\Carbon::parse($selectedPeriode->tanggal_mulai)->format('Y') }}.
        @else
            Menampilkan semua data. Pilih tahun untuk melihat arsip tahun sebelumnya.
        @endif

    </p>
</div>
