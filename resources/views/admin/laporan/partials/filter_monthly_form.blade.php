<div class="card-body bg-light border-bottom p-3">
    <form method="GET" action="{{ url()->current() }}" class="row align-items-end">
        <div class="col-md-3 form-group mb-md-0">
            <label class="font-weight-bold text-dark mb-1">Bulan</label>
            <select name="bulan" class="form-control">
                <option value="">-- Semua Bulan --</option>
                @php
                    $months = [
                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                    ];
                @endphp
                @foreach($months as $num => $name)
                    <option value="{{ sprintf('%02d', $num) }}" {{ request('bulan') == sprintf('%02d', $num) ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 form-group mb-md-0">
            <label class="font-weight-bold text-dark mb-1">Tahun</label>
            <select name="tahun" class="form-control">
                <option value="">-- Semua Tahun --</option>
                @php
                    $currentYear = (int) date('Y');
                    $startYear = $currentYear - 3;
                @endphp
                @for($y = $currentYear; $y >= $startYear; $y--)
                    <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>
                        {{ $y }}
                    </option>
                @endfor
            </select>
        </div>
        <div class="col-md-3 form-group mb-md-0">
            <label class="font-weight-bold text-dark mb-1">Pegawai</label>
            <select name="pegawai_id" class="form-control">
                <option value="">-- Semua Pegawai --</option>
                @foreach($listPegawai ?? [] as $p)
                    <option value="{{ $p->id }}" {{ request('pegawai_id') == $p->id ? 'selected' : '' }}>
                        {{ $p->nama }} ({{ $p->nik }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 form-group mb-md-0 d-flex">
            <button type="submit" class="btn btn-primary mr-2">
                <i class="fas fa-filter mr-1"></i> Filter
            </button>
            <a href="{{ url()->current() }}" class="btn btn-secondary">
                <i class="fas fa-undo mr-1"></i> Reset
            </a>
        </div>
    </form>
</div>
