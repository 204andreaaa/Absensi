@extends('layouts.admin')

@section('content')
<div class="section-header">
  <h1>Dashboard</h1>
  <div class="section-header-breadcrumb">
    <div class="breadcrumb-item active">Dashboard</div>
  </div>
</div>

{{-- STAT CARDS --}}
<div class="row">
  <div class="col-lg-4 col-md-4 col-sm-12">
    <div class="card card-statistic-2">
      <div class="card-icon shadow-primary bg-primary">
        <i class="fas fa-box"></i>
      </div>
      <div class="card-wrap">
        <div class="card-header">
          <h4>Total Produk Genset</h4>
        </div>
        <div class="card-body">
          0
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4 col-md-4 col-sm-12">
    <div class="card card-statistic-2">
      <div class="card-icon shadow-primary bg-info">
        <i class="fas fa-newspaper"></i>
      </div>
      <div class="card-wrap">
        <div class="card-header">
          <h4>Total Berita / Blog</h4>
        </div>
        <div class="card-body">
          0
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4 col-md-4 col-sm-12">
    <div class="card card-statistic-2">
      <div class="card-icon shadow-primary bg-success">
        <i class="fas fa-envelope"></i>
      </div>
      <div class="card-wrap">
        <div class="card-header">
          <h4>Total Pesan Kontak</h4>
        </div>
        <div class="card-body">
          0
        </div>
      </div>
    </div>
  </div>
</div>

{{-- LATEST POSTS & MESSAGES --}}
<div class="row">

  {{-- BERITA TERBARU --}}
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header">
        <h4>Berita Terbaru</h4>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-striped mb-0">
            <thead>
              <tr>
                <th>Judul</th>
                <th>Slug</th>
                <th>Status</th>
                <th>Tanggal Publish</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td colspan="4" class="text-center">
                  Belum ada data berita.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  {{-- PESAN KONTAK TERBARU --}}
  <div class="col-lg-4">
    <div class="card card-hero">
      <div class="card-header">
        <div class="card-icon">
          <i class="far fa-envelope"></i>
        </div>
        <h4>0</h4>
        <div class="card-description">Pesan belum dibaca</div>
      </div>

      <div class="card-body p-0">
        <div class="tickets-list">

          <div class="p-3 text-center">
            Belum ada pesan masuk.
          </div>

          <a href="#" class="ticket-item ticket-more">
            Lihat semua pesan <i class="fas fa-chevron-right"></i>
          </a>

        </div>
      </div>
    </div>
  </div>

</div>
@endsection