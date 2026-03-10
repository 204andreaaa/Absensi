@extends('layouts.admin')

@section('content')
    <div class="section-header">
        <h1>Dataset Wajah</h1>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <h4 class="mb-0">Daftar Dataset Pegawai</h4>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Pegawai</th>
                            <th>ID Pegawai</th>
                            <th>Jumlah Dataset</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($datasetPegawai as $pegawai)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $pegawai->nama }}</td>
                                <td>{{ $pegawai->id }}</td>
                                <td>{{ $pegawai->dataset_wajahs_count }}</td>
                                <td>
                                    @if($pegawai->dataset_wajahs_count >= $minDataset)
                                        <span class="badge badge-success">Lengkap</span>
                                    @else
                                        <span class="badge badge-warning">Belum Lengkap</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <form
                                        action="{{ route('admin.dataset.delete', $pegawai->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Hapus semua dataset wajah pegawai ini?')"
                                        class="d-inline"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            Hapus Dataset
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">
                                    Belum ada dataset pegawai yang terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
