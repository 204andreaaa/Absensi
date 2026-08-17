@extends('layouts.admin')

@section('content')
    <div class="section-header">
        <h1>{{ $pageTitle }}</h1>
    </div>

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <h4 class="mb-0">{{ $cardTitle }}</h4>
            <div class="card-header-action">
                <a href="{{ route($exportRoute, request()->query()) }}" class="btn btn-success">
                    <i class="fas fa-file-excel mr-1"></i> Export Excel
                </a>
                <a href="{{ route($exportPdfRoute, request()->query()) }}" class="btn btn-danger ml-2">
                    <i class="fas fa-file-pdf mr-1"></i> Cetak PDF
                </a>
            </div>
        </div>

        @include('admin.laporan.partials.filter_monthly_form')

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-bordered mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th width="50" class="text-center">No</th>
                            <th>Nama Pegawai</th>
                            <th>Bulan</th>
                            <th>Shift / Jadwal</th>
                            <th class="text-center">Total Hadir</th>
                            <th class="text-center">Tepat Waktu</th>
                            <th class="text-center">Terlambat</th>
                            <th class="text-center">Pulang Cepat</th>
                            <th class="text-center">Sesuai Jadwal</th>
                            <th class="text-center">Belum Pulang</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rekapBulanan as $item)
                            <tr>
                                <td class="text-center">{{ $rekapBulanan->firstItem() + $loop->index }}</td>
                                <td class="font-weight-bold text-dark">{{ $item->pegawai_nama }}</td>
                                <td>{{ $item->bulan_label }}</td>
                                <td>{{ $item->shift_label }}</td>
                                <td class="text-center font-weight-bold">{{ $item->total_hadir }}</td>
                                <td class="text-center"><span class="badge badge-success">{{ $item->total_tepat_waktu }}</span></td>
                                <td class="text-center"><span class="badge badge-warning">{{ $item->total_terlambat }}</span></td>
                                <td class="text-center"><span class="badge badge-danger">{{ $item->total_pulang_cepat }}</span></td>
                                <td class="text-center"><span class="badge badge-primary">{{ $item->total_sesuai_jadwal }}</span></td>
                                <td class="text-center"><span class="badge badge-secondary">{{ $item->total_belum_pulang }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">
                                    Belum ada data rekapitulasi bulanan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($rekapBulanan->hasPages())
            <div class="card-footer bg-white">
                {{ $rekapBulanan->links() }}
            </div>
        @endif
    </div>
@endsection
