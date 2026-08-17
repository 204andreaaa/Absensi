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
                <a href="{{ route($exportPdfRoute, request()->query()) }}" class="btn btn-danger ml-2">
                    <i class="fas fa-file-pdf mr-1"></i> Cetak PDF
                </a>
            </div>
        </div>

        @include('admin.laporan.partials.filter_form')

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
