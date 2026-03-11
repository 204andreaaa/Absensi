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
            @include('admin.laporan.partials.attendance_table')
        </div>

        @if($laporanAbsensi->hasPages())
            <div class="card-footer">
                {{ $laporanAbsensi->links() }}
            </div>
        @endif
    </div>
@endsection
