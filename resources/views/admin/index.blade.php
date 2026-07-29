@extends('layouts.admin')

@section('content')
<div class="section-header">
  <h1>Dashboard Overview</h1>
  <div class="section-header-breadcrumb">
    <div class="breadcrumb-item active">Dashboard</div>
  </div>
</div>

{{-- WELCOME HERO BANNER --}}
<div class="row mb-4">
  <div class="col-12">
    <div class="card bg-gradient-primary text-white shadow-sm" style="background: linear-gradient(135deg, #6777ef 0%, #3547d7 100%); border-radius: 12px;">
      <div class="card-body p-4 d-flex justify-content-between align-items-center">
        <div>
          <h3 class="font-weight-bold text-white mb-2">Selamat Datang, Admin! 👋</h3>
          <p class="mb-0 text-white-50">Sistem Presensi Pegawai berbasis Liveness Detection & Face Recognition</p>
        </div>
        <div class="text-right d-none d-md-block">
          <span class="badge badge-light px-3 py-2 text-primary font-weight-bold" style="font-size: 0.9rem; border-radius: 20px;">
            <i class="far fa-calendar-alt mr-1"></i> {{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY') }}
          </span>
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
          <h4>Terlambat Hari Ini</h4>
        </div>
        <div class="card-body">
          {{ $terlambatHariIni }} <span class="text-muted font-weight-normal style-sm">Orang</span>
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
          <h4>Hadir Hari Ini</h4>
        </div>
        <div class="card-body">
          {{ $hadirHariIni }} <span class="text-muted font-weight-normal style-sm">Orang</span>
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
          <h4>Tepat Waktu</h4>
        </div>
        <div class="card-body">
          {{ $tepatWaktuHariIni }} <span class="text-muted font-weight-normal style-sm">Orang</span>
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
          <h4>Belum Absen Masuk</h4>
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

  {{-- GRAFIK TREN KEHADIRAN 7 HARI --}}
  <div class="col-lg-7">
    <div class="card shadow-sm">
      <div class="card-header">
        <h4><i class="fas fa-chart-line mr-2 text-primary"></i> Tren Kehadiran (7 Hari Terakhir)</h4>
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