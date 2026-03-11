@extends('layouts.admin')

@section('content')
    <div class="section-header">
        <h1>Hari Libur</h1>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h4 class="mb-0">Info Hari Libur</h4>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="alert alert-primary mb-0">
                        <div class="font-weight-bold mb-2">Skema Hari Libur Aktif</div>
                        <div>Sabtu dan Minggu otomatis dianggap libur oleh sistem.</div>
                        <div class="mt-2">Gunakan form di bawah untuk menambahkan libur khusus lain seperti libur nasional atau libur kantor.</div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header">
                    <h4 class="mb-0">Tambah Hari Libur</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.hari-libur.store') }}">
                        @csrf

                        <div class="form-group">
                            <label for="tanggal">Tanggal</label>
                            <input type="date" name="tanggal" id="tanggal" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="nama_libur">Nama Libur</label>
                            <input type="text" name="nama_libur" id="nama_libur" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="keterangan">Keterangan</label>
                            <textarea name="keterangan" id="keterangan" class="form-control" rows="3"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">
                            Simpan Hari Libur
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h4 class="mb-0">Daftar Hari Libur Manual</h4>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Nama Libur</th>
                                    <th>Keterangan</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($hariLiburs as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}</td>
                                        <td>{{ $item->nama_libur }}</td>
                                        <td>{{ $item->keterangan ?? '-' }}</td>
                                        <td class="text-center">
                                            <form
                                                method="POST"
                                                action="{{ route('admin.hari-libur.delete', $item->id) }}"
                                                onsubmit="return confirm('Hapus hari libur ini?')"
                                                class="d-inline"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            Belum ada hari libur.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
