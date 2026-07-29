<div class="table-responsive">
    <table class="table table-striped mb-0">
        <thead>
            <tr>
                <th>No</th>
                <th>Pegawai</th>
                <th>Tanggal</th>
                <th>Shift</th>
                <th>Jadwal</th>
                <th>Jam Masuk</th>
                <th>Status Masuk</th>
                <th>Alasan Telat</th>
                <th>Foto Masuk</th>
                <th>Jam Pulang</th>
                <th>Status Pulang</th>
                <th>Alasan Pulang Awal</th>
                <th>Foto Pulang</th>
            </tr>
        </thead>
        <tbody>
            @forelse($laporanAbsensi as $item)
                <tr>
                    <td>{{ $laporanAbsensi->firstItem() + $loop->index }}</td>
                    <td>{{ optional($item->pegawai)->nama ?? 'Pegawai Tidak Diketahui' }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}</td>
                    <td>{{ $item->shift_label }}</td>
                    <td>{{ $item->jadwal_label }}</td>
                    <td>{{ $item->jam_masuk ?? '-' }}</td>
                    <td>
                        @if($item->status === 'terlambat')
                            <span class="badge badge-warning">Terlambat</span>
                        @else
                            <span class="badge badge-success">Tepat Waktu</span>
                        @endif
                    </td>
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
                    <td>{{ $item->jam_pulang ?? '-' }}</td>
                    <td>
                        @if($item->status_pulang_label === 'Pulang Cepat')
                            <span class="badge badge-danger">Pulang Cepat</span>
                        @elseif($item->status_pulang_label === 'Sesuai Jadwal')
                            <span class="badge badge-success">Sesuai Jadwal</span>
                        @elseif($item->status_pulang_label === 'Belum Absen Pulang')
                            <span class="badge badge-secondary">Belum Pulang</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>{{ $item->alasan_pulang_awal ?? '-' }}</td>
                    <td>
                        @if($item->foto_pulang)
                            <a href="javascript:void(0)" class="show-photo-modal" data-image-url="{{ asset('storage/' . $item->foto_pulang) }}">
                                <img
                                    src="{{ asset('storage/' . $item->foto_pulang) }}"
                                    alt="Foto Pulang"
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
                    <td colspan="13" class="text-center text-muted py-4">
                        Belum ada data absensi.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
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
    // Append modal to body to prevent z-index/backdrop issues in Bootstrap
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
