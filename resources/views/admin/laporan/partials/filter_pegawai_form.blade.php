<div class="card-body bg-light border-bottom p-3">
    <form method="GET" action="{{ url()->current() }}" class="row align-items-end">
        <div class="col-md-4 form-group mb-md-0">
            <label class="font-weight-bold text-dark mb-1">Departemen</label>
            <select name="departemen_id" class="form-control">
                <option value="">-- Semua Departemen --</option>
                @foreach($listDepartemen ?? [] as $d)
                    <option value="{{ $d->id }}" {{ request('departemen_id') == $d->id ? 'selected' : '' }}>
                        {{ $d->nama_departemen }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4 form-group mb-md-0 d-flex">
            <button type="submit" class="btn btn-primary mr-2">
                <i class="fas fa-filter mr-1"></i> Filter
            </button>
            <a href="{{ url()->current() }}" class="btn btn-secondary">
                <i class="fas fa-undo mr-1"></i> Reset
            </a>
        </div>
    </form>
</div>
