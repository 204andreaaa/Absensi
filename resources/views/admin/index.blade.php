@extends('layouts.admin')

@section('content')
<div class="section-header">
  <h1>Dashboard Overview</h1>
  <div class="section-header-breadcrumb">
    <div class="breadcrumb-item active">Dashboard</div>
  </div>
</div>

{{-- WELCOME HERO BANNER WITH EMBEDDED MONTH & YEAR FILTER --}}
<div class="row mb-4">
  <div class="col-12">
    <div class="card bg-gradient-primary text-white shadow-sm" style="background: linear-gradient(135deg, #6777ef 0%, #3547d7 100%); border-radius: 12px;">
      <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap">
        <div class="my-1">
          <h3 class="font-weight-bold text-white mb-2">Selamat Datang, Admin! 👋</h3>
          <p class="mb-0 text-white-50">Sistem Presensi Pegawai berbasis Liveness Detection & Face Recognition</p>
        </div>
        <div class="my-1">
          <form method="GET" action="{{ route('admin.dashboard') }}" class="form-inline bg-white px-3 py-2 shadow-sm" style="border-radius: 30px;">
            <i class="far fa-calendar-alt text-primary mr-2" style="font-size: 1.1rem;"></i>
            <select name="bulan" class="form-control form-control-sm border-0 font-weight-bold text-primary mr-2" onchange="this.form.submit()" style="background-color: #eef2ff; border-radius: 20px; cursor: pointer; font-size: 0.9rem; outline: none; height: auto; padding: 6px 14px 6px 14px; line-height: 1.4;">
              @foreach(range(1, 12) as $m)
                @php $mName = \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F'); @endphp
                <option value="{{ $m }}" {{ $m == $bulan ? 'selected' : '' }}>{{ $mName }}</option>
              @endforeach
            </select>
            <select name="tahun" class="form-control form-control-sm border-0 font-weight-bold text-primary" onchange="this.form.submit()" style="background-color: #eef2ff; border-radius: 20px; cursor: pointer; font-size: 0.9rem; outline: none; height: auto; padding: 6px 14px 6px 14px; line-height: 1.4;">
              @foreach([2025, 2026, 2027] as $y)
                <option value="{{ $y }}" {{ $y == $tahun ? 'selected' : '' }}>{{ $y }}</option>
              @endforeach
            </select>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- STAT CARDS --}}
<div class="row">
  <div class="col-lg-4 col-md-6 col-sm-12">
    <div class="card card-statistic-2 shadow-sm">
      <div class="card-icon shadow-warning bg-warning">
        <i class="fas fa-clock"></i>
      </div>
      <div class="card-wrap">
        <div class="card-header">
          <h4>Terlambat ({{ \Carbon\Carbon::createFromDate(null, $bulan, 1)->translatedFormat('F') }})</h4>
        </div>
        <div class="card-body">
          {{ $terlambatBulanIni }} <span class="text-muted font-weight-normal style-sm">Presensi</span>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4 col-md-6 col-sm-12">
    <div class="card card-statistic-2 shadow-sm">
      <div class="card-icon shadow-success bg-success">
        <i class="fas fa-user-check"></i>
      </div>
      <div class="card-wrap">
        <div class="card-header">
          <h4>Total Hadir ({{ \Carbon\Carbon::createFromDate(null, $bulan, 1)->translatedFormat('F') }})</h4>
        </div>
        <div class="card-body">
          {{ $totalAbsensiBulanIni }} <span class="text-muted font-weight-normal style-sm">Presensi</span>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4 col-md-6 col-sm-12">
    <div class="card card-statistic-2 shadow-sm">
      <div class="card-icon shadow-info bg-info">
        <i class="fas fa-check-circle"></i>
      </div>
      <div class="card-wrap">
        <div class="card-header">
          <h4>Tepat Waktu ({{ \Carbon\Carbon::createFromDate(null, $bulan, 1)->translatedFormat('F') }})</h4>
        </div>
        <div class="card-body">
          {{ $tepatWaktuBulanIni }} <span class="text-muted font-weight-normal style-sm">Presensi</span>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4 col-md-6 col-sm-12">
    <div class="card card-statistic-2 shadow-sm">
      <div class="card-icon shadow-primary bg-primary">
        <i class="fas fa-users"></i>
      </div>
      <div class="card-wrap">
        <div class="card-header">
          <h4>Total Pegawai</h4>
        </div>
        <div class="card-body">
          {{ $totalPegawai }} <span class="text-muted font-weight-normal style-sm">Orang</span>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4 col-md-6 col-sm-12">
    <div class="card card-statistic-2 shadow-sm">
      <div class="card-icon shadow-danger bg-danger">
        <i class="fas fa-user-times"></i>
      </div>
      <div class="card-wrap">
        <div class="card-header">
          <h4>Belum Absen (Hari Ini)</h4>
        </div>
        <div class="card-body">
          {{ $belumAbsenHariIni }} <span class="text-muted font-weight-normal style-sm">Orang</span>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4 col-md-6 col-sm-12">
    <div class="card card-statistic-2 shadow-sm">
      <div class="card-icon shadow-dark bg-dark">
        <i class="fas fa-building"></i>
      </div>
      <div class="card-wrap">
        <div class="card-header">
          <h4>Total Departemen</h4>
        </div>
        <div class="card-body">
          {{ $totalDepartemen }} <span class="text-muted font-weight-normal style-sm">Divisi</span>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- GRAFIK & AKTIVITAS TERBARU --}}
<div class="row">

  {{-- GRAFIK TREN KEHADIRAN --}}
  <div class="col-lg-7">
    <div class="card shadow-sm">
      <div class="card-header">
        <h4><i class="fas fa-chart-line mr-2 text-primary"></i> Tren Kehadiran ({{ \Carbon\Carbon::createFromDate(null, $bulan, 1)->translatedFormat('F') }} {{ $tahun }})</h4>
      </div>
      <div class="card-body">
        <canvas id="attendanceTrendChart" height="180"></canvas>
      </div>
    </div>
  </div>

  {{-- AKTIVITAS ABSENSI TERBARU --}}
  <div class="col-lg-5">
    <div class="card shadow-sm">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0"><i class="fas fa-history mr-2 text-success"></i> Aktivitas Absensi Terbaru</h4>
        <a href="{{ route('admin.laporan.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-striped table-sm mb-0">
            <thead>
              <tr>
                <th>Pegawai</th>
                <th>Masuk</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              @forelse($latestAbsensi as $item)
                <tr>
                  <td>
                    <div class="font-weight-bold text-dark">{{ optional($item->pegawai)->nama ?? '-' }}</div>
                    <div class="text-muted text-small">{{ optional(optional($item->pegawai)->departemen)->nama_departemen ?? '-' }}</div>
                  </td>
                  <td>
                    <div>{{ $item->jam_masuk ? \Carbon\Carbon::parse($item->jam_masuk)->format('H:i') : '-' }}</div>
                    <div class="text-muted text-small">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</div>
                  </td>
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
                  <td colspan="3" class="text-center text-muted py-3">
                    Belum ada aktivitas absensi.
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    var ctx = document.getElementById("attendanceTrendChart").getContext('2d');
    var attendanceChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: {!! json_encode($chartLabels) !!},
        datasets: [
          {
            label: 'Tepat Waktu',
            data: {!! json_encode($chartTepatWaktu) !!},
            borderColor: '#6777ef',
            backgroundColor: 'rgba(103, 119, 239, 0.15)',
            borderWidth: 3,
            pointBackgroundColor: '#ffffff',
            pointBorderColor: '#6777ef',
            pointRadius: 4,
            fill: true,
            tension: 0.3
          },
          {
            label: 'Terlambat',
            data: {!! json_encode($chartTerlambat) !!},
            borderColor: '#ffa426',
            backgroundColor: 'rgba(255, 164, 38, 0.15)',
            borderWidth: 3,
            pointBackgroundColor: '#ffffff',
            pointBorderColor: '#ffa426',
            pointRadius: 4,
            fill: true,
            tension: 0.3
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: true,
            position: 'top'
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              precision: 0
            }
          }
        }
      }
    });
  });
</script>
@endpush