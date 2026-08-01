@extends('layouts.pegawai')

@section('content')
    <div class="section-header">
        <h1>Riwayat Absensi</h1>
    </div>

    <div class="card mb-4 border-0">
        <div class="card-body d-flex flex-wrap justify-content-between align-items-center" style="gap: 12px;">
            <div>
                <div class="text-muted small text-uppercase font-weight-bold mb-2">Rekap Absensi Saya</div>
                <h4 class="mb-1">Pantau statistik kehadiran & riwayat absensi</h4>
                <p class="text-muted mb-0">Lihat total kehadiran, jumlah terlambat, dan status absen harianmu di sini.</p>
            </div>

            <a href="{{ route('pegawai.absensi') }}" class="btn btn-primary font-weight-bold">
                <i class="fas fa-camera mr-1"></i> Absen Sekarang
            </a>
        </div>
    </div>

    <!-- KARTU STATISTIK RINGKASAN PRESENSI PEGAWAI -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-3 mb-lg-0">
            <div class="card card-statistic-1 shadow-sm mb-0">
                <div class="card-icon bg-primary">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header"><h4>Total Hadir</h4></div>
                    <div class="card-body">{{ $stats['total_hadir'] ?? 0 }} Hari</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-3 mb-lg-0">
            <div class="card card-statistic-1 shadow-sm mb-0">
                <div class="card-icon bg-success">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header"><h4>Tepat Waktu</h4></div>
                    <div class="card-body">{{ $stats['tepat_waktu'] ?? 0 }} Hari</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-3 mb-lg-0">
            <div class="card card-statistic-1 shadow-sm mb-0">
                <div class="card-icon bg-warning">
                    <i class="fas fa-user-clock"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header"><h4>Terlambat</h4></div>
                    <div class="card-body">{{ $stats['terlambat'] ?? 0 }} Hari</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-3 mb-lg-0">
            <div class="card card-statistic-1 shadow-sm mb-0">
                <div class="card-icon bg-danger">
                    <i class="fas fa-running"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header"><h4>Pulang Cepat</h4></div>
                    <div class="card-body">{{ $stats['pulang_cepat'] ?? 0 }} Hari</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <h4 class="mb-0">Data Absensi Saya</h4>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Jam Masuk</th>
                            <th>Foto Masuk</th>
                            <th>Jam Pulang</th>
                            <th>Foto Pulang</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayatAbsensi as $item)
                            <tr>
                                <td>{{ $riwayatAbsensi->firstItem() + $loop->index }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}</td>
                                <td>{{ $item->jam_masuk ?? '-' }}</td>
                                <td>
                                    @if($item->foto_masuk)
                                        <a href="javascript:void(0)" class="show-photo-modal" data-image-url="{{ asset('storage/' . $item->foto_masuk) }}">
                                            <img
                                                src="{{ asset('storage/' . $item->foto_masuk) }}"
                                                alt="Foto Masuk"
                                                style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;"
                                            >
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $item->jam_pulang ?? '-' }}</td>
                                <td>
                                    @if($item->foto_pulang)
                                        <a href="javascript:void(0)" class="show-photo-modal" data-image-url="{{ asset('storage/' . $item->foto_pulang) }}">
                                            <img
                                                src="{{ asset('storage/' . $item->foto_pulang) }}"
                                                alt="Foto Pulang"
                                                style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;"
                                            >
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->status === 'terlambat')
                                        <span class="badge badge-warning">Terlambat</span>
                                    @elseif($item->jam_pulang)
                                        <span class="badge badge-success">Lengkap</span>
                                    @elseif($item->jam_masuk)
                                        <span class="badge badge-warning">Belum Pulang</span>
                                    @else
                                        <span class="badge badge-secondary">Tidak Ada Data</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    Belum ada riwayat absensi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($riwayatAbsensi->hasPages())
            <div class="card-footer">
                {{ $riwayatAbsensi->links() }}
            </div>
        @endif
    </div>
    </div>

    <!-- Image Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Pratinjau Foto Absensi</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body text-center">
            <img id="modalImagePreview" src="" alt="Foto" style="max-width: 100%; height: auto; border-radius: 8px;">
          </div>
        </div>
      </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        $('#imageModal').appendTo('body');
        const photoTriggers = document.querySelectorAll('.show-photo-modal');
        const modalImage = document.getElementById('modalImagePreview');

        photoTriggers.forEach(trigger => {
            trigger.addEventListener('click', function(e) {
                e.preventDefault();
                const imageUrl = this.getAttribute('data-image-url');
                modalImage.src = imageUrl;
                $('#imageModal').modal('show');
            });
        });
    });
    </script>
@endsection
