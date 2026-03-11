<table>
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
        @foreach($laporanAbsensi as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ optional($item->pegawai)->nama ?? 'Pegawai Tidak Diketahui' }}</td>
                <td>{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}</td>
                <td>{{ $item->shift_label }}</td>
                <td>{{ optional(optional($item->pegawai)->jadwal)->jam_masuk ?? '-' }}</td>
                <td>{{ $item->toleransi_telat_label }}</td>
                <td>{{ $item->jam_masuk ?? '-' }}</td>
                <td>{{ $item->selisih_telat_label }}</td>
                <td>{{ $item->alasan_telat ?? '-' }}</td>
                <td>{{ $item->foto_masuk ? 'Terlampir' : '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
