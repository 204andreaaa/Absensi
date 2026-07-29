@extends('layouts.admin')

@section('content')
    <div class="section-header">
        <h1>{{ $pageTitle }}</h1>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <h4 class="mb-0">{{ $cardTitle }}</h4>
            <div class="card-header-action">
                <a href="{{ route($exportRoute, request()->query()) }}" class="btn btn-success">
                    <i class="fas fa-file-excel mr-1"></i> Export Excel
                </a>
                <a href="{{ route($exportPdfRoute, request()->query()) }}" target="_blank" class="btn btn-danger ml-2">
                    <i class="fas fa-file-pdf mr-1"></i> Print PDF
                </a>
            </div>
        </div>

        @include('admin.laporan.partials.filter_monthly_form')

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Pegawai</th>
                            <th>Bulan</th>
                            <th>Shift</th>
                            <th>Total Hadir</th>
                            <th>Tepat Waktu</th>
                            <th>Terlambat</th>
                            <th>Pulang Cepat</th>
                            <th>Sesuai Jadwal</th>
                            <th>Belum Pulang</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rekapBulanan as $item)
                            <tr>
                                <td>{{ $rekapBulanan->firstItem() + $loop->index }}</td>
                                <td>{{ $item->pegawai_nama }}</td>
                                <td>{{ $item->bulan_label }}</td>
                                <td>{{ $item->shift_label }}</td>
                                <td>{{ $item->total_hadir }}</td>
                                <td><span class="badge badge-success">{{ $item->total_tepat_waktu }}</span></td>
                                <td><span class="badge badge-warning">{{ $item->total_terlambat }}</span></td>
                                <td><span class="badge badge-danger">{{ $item->total_pulang_cepat }}</span></td>
                                <td><span class="badge badge-primary">{{ $item->total_sesuai_jadwal }}</span></td>
                                <td><span class="badge badge-secondary">{{ $item->total_belum_pulang }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">
                                    Belum ada data rekap bulanan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($rekapBulanan->hasPages())
            <div class="card-footer">
                {{ $rekapBulanan->links() }}
            </div>
        @endif
    </div>
@endsection
