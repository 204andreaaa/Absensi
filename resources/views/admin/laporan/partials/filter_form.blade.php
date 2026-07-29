<div class="card-body bg-light border-bottom p-3">
    <form method="GET" action="{{ url()->current() }}" class="row align-items-end">
        <div class="col-md-3 form-group mb-md-0">
            <label class="font-weight-bold text-dark mb-1">Dari Tanggal</label>
            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
        </div>
        <div class="col-md-3 form-group mb-md-0">
            <label class="font-weight-bold text-dark mb-1">Sampai Tanggal</label>
            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
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
