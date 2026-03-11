<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Pegawai</th>
            <th>Tanggal</th>
            <th>Shift</th>
            <th>Jadwal</th>
            <th>Jam Masuk</th>
            <th>Status Masuk</th>
            <th>Foto Masuk</th>
            <th>Alasan Telat</th>
            <th>Jam Pulang</th>
            <th>Status Pulang</th>
            <th>Alasan Pulang Awal</th>
            <th>Foto Pulang</th>
        </tr>
    </thead>
    <tbody>
        @foreach($laporanAbsensi as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ optional($item->pegawai)->nama ?? 'Pegawai Tidak Diketahui' }}</td>
                <td>{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}</td>
                <td>{{ $item->shift_label }}</td>
                <td>{{ $item->jadwal_label }}</td>
                <td>{{ $item->jam_masuk ?? '-' }}</td>
                <td>{{ $item->status_masuk_label }}</td>
                <td>{{ $item->foto_masuk ? 'Terlampir' : '-' }}</td>
                <td>{{ $item->alasan_telat ?? '-' }}</td>
                <td>{{ $item->jam_pulang ?? '-' }}</td>
                <td>{{ $item->status_pulang_label }}</td>
                <td>{{ $item->alasan_pulang_awal ?? '-' }}</td>
                <td>{{ $item->foto_pulang ? 'Terlampir' : '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
