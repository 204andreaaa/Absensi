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

        @include('admin.laporan.partials.filter_form')

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Pegawai</th>
                            <th>Tanggal</th>
                            <th>Shift</th>
                            <th>Jadwal Masuk</th>
                            <th>Toleransi</th>
                            <th>Jam Masuk Aktual</th>
                            <th>Selisih Telat</th>
                            <th>Alasan Telat</th>
                            <th>Foto Masuk</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($laporanAbsensi as $item)
                            <tr>
                                <td>{{ $laporanAbsensi->firstItem() + $loop->index }}</td>
                                <td>{{ optional($item->pegawai)->nama ?? 'Pegawai Tidak Diketahui' }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}</td>
                                <td>{{ $item->shift_label }}</td>
                                <td>{{ optional(optional($item->pegawai)->jadwal)->jam_masuk ?? '-' }}</td>
                                <td>{{ $item->toleransi_telat_label }}</td>
                                <td>{{ $item->jam_masuk ?? '-' }}</td>
                                <td><span class="badge badge-warning">{{ $item->selisih_telat_label }}</span></td>
                                <td>{{ $item->alasan_telat ?? '-' }}</td>
                                <td>
                                    @if($item->foto_masuk)
                                        <a href="javascript:void(0)" class="show-photo-modal" data-image-url="{{ asset('storage/' . $item->foto_masuk) }}">
                                            <img
                                                src="{{ asset('storage/' . $item->foto_masuk) }}"
                                                alt="Foto Masuk"
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
                                <td colspan="10" class="text-center text-muted py-4">
                                    Belum ada data keterlambatan.
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
