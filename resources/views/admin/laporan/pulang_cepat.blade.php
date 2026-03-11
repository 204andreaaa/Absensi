@extends('layouts.admin')

@section('content')
    <div class="section-header">
        <h1>{{ $pageTitle }}</h1>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <h4 class="mb-0">{{ $cardTitle }}</h4>
            <div class="card-header-action">
                <a href="{{ route($exportRoute) }}" class="btn btn-success">
                    Export Excel
                </a>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Pegawai</th>
                            <th>Tanggal</th>
                            <th>Shift</th>
                            <th>Jadwal Pulang</th>
                            <th>Jam Pulang Aktual</th>
                            <th>Selisih Pulang Awal</th>
                            <th>Alasan Pulang Awal</th>
                            <th>Foto Pulang</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($laporanAbsensi as $item)
                            <tr>
                                <td>{{ $laporanAbsensi->firstItem() + $loop->index }}</td>
                                <td>{{ optional($item->pegawai)->nama ?? 'Pegawai Tidak Diketahui' }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}</td>
                                <td>{{ $item->shift_label }}</td>
                                <td>{{ optional(optional($item->pegawai)->jadwal)->jam_pulang ?? '-' }}</td>
                                <td>{{ $item->jam_pulang ?? '-' }}</td>
                                <td><span class="badge badge-danger">{{ $item->selisih_pulang_cepat_label }}</span></td>
                                <td>{{ $item->alasan_pulang_awal ?? '-' }}</td>
                                <td>
                                    @if($item->foto_pulang)
                                        <a href="{{ asset('storage/' . $item->foto_pulang) }}" target="_blank">
                                            <img
                                                src="{{ asset('storage/' . $item->foto_pulang) }}"
                                                alt="Foto Pulang"
                                                style="width: 72px; height: 72px; object-fit: cover; border-radius: 8px;"
                                            >
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    Belum ada data pulang cepat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($laporanAbsensi->hasPages())
            <div class="card-footer">
                {{ $laporanAbsensi->links() }}
            </div>
        @endif
    </div>
@endsection
