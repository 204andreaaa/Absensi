<table>
    <tr>
        <td colspan="10">{{ $reportTitle }}</td>
    </tr>
</table>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Pegawai</th>
            <th>Bulan</th>
            <th>Shift</th>
            <th>Total Hadir</th>
            <th>Tepat Waktu</th>
            <th>Terlambat</th>
            <th>Pulang Cepat</th>
            <th>Sesuai Jadwal</th>
            <th>Belum Pulang</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rekapBulanan as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->pegawai_nama }}</td>
                <td>{{ $item->bulan_label }}</td>
                <td>{{ $item->shift_label }}</td>
                <td>{{ $item->total_hadir }}</td>
                <td>{{ $item->total_tepat_waktu }}</td>
                <td>{{ $item->total_terlambat }}</td>
                <td>{{ $item->total_pulang_cepat }}</td>
                <td>{{ $item->total_sesuai_jadwal }}</td>
                <td>{{ $item->total_belum_pulang }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
