@extends('layouts.pegawai')

@section('content')
    <div class="section-header">
        <h1>Dashboard Pegawai</h1>
    </div>

    <div class="card mb-4 border-0 overflow-hidden">
        <div
            class="card-body"
            style="background: linear-gradient(135deg, #1d4ed8, #14b8a6); color: #fff;"
        >
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="text-uppercase font-weight-bold mb-2" style="letter-spacing: 0.12em; opacity: 0.8;">
                        Selamat Datang
                    </div>
                    <h2 class="font-weight-bold mb-2">{{ auth()->user()->nama }}</h2>
                    <p class="mb-4" style="opacity: 0.9;">
                        Cek status absensi hari ini, lanjutkan absen wajah, dan pantau riwayat langsung dari ponsel.
                    </p>
                    <div class="d-flex flex-wrap" style="gap: 12px;">
                        <a href="{{ route('pegawai.absensi') }}" class="btn btn-light text-primary">
                            Mulai Absensi
                        </a>
                        <a href="{{ route('pegawai.riwayat') }}" class="btn btn-outline-light">
                            Lihat Riwayat
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 d-none d-lg-block text-right">
                    <i class="fas fa-mobile-alt" style="font-size: 5rem; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4 col-md-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-primary">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Hadir Bulan Ini</h4>
                    </div>
                    <div class="card-body">{{ $stats['hadir_bulan_ini'] }}</div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-success">
                    <i class="fas fa-sign-in-alt"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Masuk Hari Ini</h4>
                    </div>
                    <div class="card-body">{{ $stats['sudah_masuk_hari_ini'] ? 'Sudah' : 'Belum' }}</div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-warning">
                    <i class="fas fa-database"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Dataset Wajah</h4>
                    </div>
                    <div class="card-body">{{ $stats['dataset_count'] }}/15</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header">
                    <h4>Ringkasan Hari Ini</h4>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <div class="text-muted small">Tanggal</div>
                            <div class="font-weight-bold">
                                {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
                            </div>
                        </div>

                        @if($todayAttendance)
                            <span class="badge badge-success">Sudah Absen</span>
                        @else
                            <span class="badge badge-secondary">Belum Absen</span>
                        @endif
                    </div>

                    <div class="p-3 rounded" style="background: #f6f9fc;">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Jam Masuk</span>
                            <strong>{{ optional($todayAttendance)->jam_masuk ?? '-' }}</strong>
                        </div>

                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Jam Pulang</span>
                            <strong>{{ optional($todayAttendance)->jam_pulang ?? '-' }}</strong>
                        </div>
                    </div>

                    <a href="{{ route('pegawai.absensi') }}" class="btn btn-primary btn-block mt-4">
                        Buka Kamera Absensi
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card">
                <div class="card-header">
                    <h4>5 Riwayat Terbaru</h4>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Masuk</th>
                                    <th>Pulang</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($monthlyAttendances as $item)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}</td>
                                        <td>{{ $item->jam_masuk ?? '-' }}</td>
                                        <td>{{ $item->jam_pulang ?? '-' }}</td>
                                        <td>
                                            @if($item->status === 'terlambat')
                                                <span class="badge badge-warning">Terlambat</span>
                                            @else
                                                <span class="badge badge-success">Tepat Waktu</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">
                                            Belum ada riwayat absensi.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-right bg-white border-0 pt-0">
                    <a href="{{ route('pegawai.riwayat') }}" class="btn btn-outline-primary btn-sm">
                        Lihat Semua
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
