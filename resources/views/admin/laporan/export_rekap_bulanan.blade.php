<table>
    <tr>
        <th colspan="10" style="font-size: 16px; font-weight: bold; text-align: center;">PT. MANDIRI DAYA UTAMA NUSANTARA</th>
    </tr>
    <tr>
        <th colspan="10" style="text-align: center;">Golden Fatmawati, Jl. RS. Fatmawati Raya No.17 C17, RT.10/RW.6, Gandaria Sel.,</th>
    </tr>
    <tr>
        <th colspan="10" style="text-align: center;">Kec. Cilandak, Kota Jakarta Selatan, Daerah Khusus Ibukota Jakarta 12420</th>
    </tr>
    <tr>
        <th colspan="10" style="text-align: center;">Telepon: (021) 7697070</th>
    </tr>
    <tr>
        <th colspan="10"></th>
    </tr>
    <tr>
        <th colspan="10" style="font-size: 14px; font-weight: bold; text-align: center;">{{ $reportTitle ?? 'Laporan Rekap Bulanan' }}</th>
    </tr>
    <tr>
        <th colspan="10"></th>
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
