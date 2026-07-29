<table>
    <thead>
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
            <th colspan="10" style="font-size: 14px; font-weight: bold; text-align: center;">{{ $reportTitle ?? 'Laporan Keterlambatan' }}</th>
        </tr>
        <tr>
            <th colspan="10"></th>
        </tr>
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
