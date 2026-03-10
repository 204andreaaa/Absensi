@extends('layouts.admin')

@section('content')
    <div class="section-header">
        <h1>Laporan Kehadiran</h1>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <h4 class="mb-0">Rekap Absensi Pegawai</h4>
        </div>

        <div class="card-body p-0">
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
                            <th>Jam Pulang</th>
                            <th>Status Pulang</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($laporanAbsensi as $item)
                            @php
                                $jadwal = optional(optional($item->pegawai)->jadwal);
                                $jadwalMasuk = $jadwal->jam_masuk;
                                $jadwalPulang = $jadwal->jam_pulang;
                                $statusMasuk = $item->status === 'terlambat' ? 'Terlambat' : 'Tepat Waktu';
                                $statusPulang = '-';

                                if ($item->jam_pulang && $jadwalMasuk && $jadwalPulang) {
                                    $tanggal = \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d');
                                    $jadwalMasukAt = \Carbon\Carbon::parse($tanggal . ' ' . $jadwalMasuk);
                                    $jadwalPulangAt = \Carbon\Carbon::parse($tanggal . ' ' . $jadwalPulang);
                                    $jamPulangAt = \Carbon\Carbon::parse($tanggal . ' ' . $item->jam_pulang);

                                    if ($jadwalPulangAt->lessThanOrEqualTo($jadwalMasukAt)) {
                                        $jadwalPulangAt->addDay();

                                        if ($jamPulangAt->lessThanOrEqualTo($jadwalMasukAt)) {
                                            $jamPulangAt->addDay();
                                        }
                                    }

                                    $statusPulang = $jamPulangAt->lt($jadwalPulangAt) ? 'Pulang Cepat' : 'Sesuai Jadwal';
                                } elseif ($item->jam_masuk) {
                                    $statusPulang = 'Belum Absen Pulang';
                                }
                            @endphp

                            <tr>
                                <td>{{ $laporanAbsensi->firstItem() + $loop->index }}</td>
                                <td>{{ optional($item->pegawai)->nama ?? 'Pegawai Tidak Diketahui' }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}</td>
                                <td>{{ $jadwal->nama_shift ?? '-' }}</td>
                                <td>
                                    {{ $jadwalMasuk ?? '-' }}
                                    -
                                    {{ $jadwalPulang ?? '-' }}
                                </td>
                                <td>{{ $item->jam_masuk ?? '-' }}</td>
                                <td>
                                    @if($item->status === 'terlambat')
                                        <span class="badge badge-warning">Terlambat</span>
                                    @else
                                        <span class="badge badge-success">Tepat Waktu</span>
                                    @endif
                                </td>
                                <td>{{ $item->jam_pulang ?? '-' }}</td>
                                <td>
                                    @if($statusPulang === 'Pulang Cepat')
                                        <span class="badge badge-danger">Pulang Cepat</span>
                                    @elseif($statusPulang === 'Sesuai Jadwal')
                                        <span class="badge badge-success">Sesuai Jadwal</span>
                                    @elseif($statusPulang === 'Belum Absen Pulang')
                                        <span class="badge badge-secondary">Belum Pulang</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    Belum ada data absensi.
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
@endsection
