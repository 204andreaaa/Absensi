@extends('layouts.pegawai')

@section('content')
    <div class="section-header">
        <h1>Riwayat Absensi</h1>
    </div>

    <div class="card mb-4 border-0">
        <div class="card-body d-flex flex-wrap justify-content-between align-items-center" style="gap: 12px;">
            <div>
                <div class="text-muted small text-uppercase font-weight-bold mb-2">Rekap Saya</div>
                <h4 class="mb-1">Pantau semua absensi dalam satu layar</h4>
                <p class="text-muted mb-0">Data masuk dan pulang ditampilkan otomatis berdasarkan riwayat tersimpan.</p>
            </div>

            <a href="{{ route('pegawai.absensi') }}" class="btn btn-primary">
                Absen Sekarang
            </a>
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
