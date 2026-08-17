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

        @include('admin.laporan.partials.filter_pegawai_form')

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Foto (Terakhir)</th>
                            <th>NIK</th>
                            <th>Nama</th>
                            <th>Departemen</th>
                            <th>Jadwal / Shift</th>
                            <th>Jabatan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pegawai as $item)
                            <tr>
                                <td>{{ $pegawai->firstItem() + $loop->index }}</td>
                                <td>
                                    @if($item->foto)
                                        <a href="javascript:void(0)" class="show-photo-modal" data-image-url="{{ asset('storage/' . $item->foto) }}">
                                            <img
                                                src="{{ asset('storage/' . $item->foto) }}"
                                                alt="Foto {{ $item->nama }}"
                                                style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;"
                                            >
                                        </a>
                                    @else
                                        <span class="text-muted text-sm">Tidak ada foto</span>
                                    @endif
                                </td>
                                <td>{{ $item->nik }}</td>
                                <td>{{ $item->nama }}</td>
                                <td>{{ optional($item->departemen)->nama_departemen ?? '-' }}</td>
                                <td>{{ optional($item->jadwal)->nama_shift ?? '-' }}</td>
                                <td>{{ $item->jabatan ?? '-' }}</td>
                                <td>
                                    @if($item->status)
                                        <span class="badge badge-success">Aktif</span>
                                    @else
                                        <span class="badge badge-danger">Non Aktif</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    Belum ada data pegawai.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($pegawai->hasPages())
            <div class="card-footer">
                {{ $pegawai->links() }}
            </div>
        @endif
    </div>

    <!-- Image Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Pratinjau Foto</h5>
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
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    $('#imageModal').appendTo('body');

    const photoTriggers = document.querySelectorAll('.show-photo-modal');
    const modalImage = document.getElementById('modalImagePreview');

    photoTriggers.forEach(trigger => {
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            modalImage.src = this.getAttribute('data-image-url');
            $('#imageModal').modal('show');
        });
    });
});
</script>
@endpush
