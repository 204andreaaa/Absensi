<table>
    <thead>
        <tr>
            <th colspan="9" style="font-size: 16px; font-weight: bold; text-align: center;">PT. MANDIRI DAYA UTAMA NUSANTARA</th>
        </tr>
        <tr>
            <th colspan="9" style="text-align: center;">Golden Fatmawati, Jl. RS. Fatmawati Raya No.17 C17, RT.10/RW.6, Gandaria Sel.,</th>
        </tr>
        <tr>
            <th colspan="9" style="text-align: center;">Kec. Cilandak, Kota Jakarta Selatan, Daerah Khusus Ibukota Jakarta 12420</th>
        </tr>
        <tr>
            <th colspan="9" style="text-align: center;">Telepon: (021) 7697070</th>
        </tr>
        <tr>
            <th colspan="9"></th>
        </tr>
        <tr>
            <th colspan="9" style="font-size: 14px; font-weight: bold; text-align: center;">{{ $reportTitle ?? 'Laporan Pulang Cepat' }}</th>
        </tr>
        <tr>
            <th colspan="9"></th>
        </tr>
        <tr>
            <th>No</th>
            <th>Pegawai</th>
            <th>Tanggal</th>
            <th>Shift</th>
            <th>Jadwal Pulang</th>
            <th>Jam Pulang Aktual</th>
            <th>Selisih Pulang Awal</th>
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
                <td>{{ optional(optional($item->pegawai)->jadwal)->jam_pulang ?? '-' }}</td>
                <td>{{ $item->jam_pulang ?? '-' }}</td>
                <td>{{ $item->selisih_pulang_cepat_label }}</td>
                <td>{{ $item->alasan_pulang_awal ?? '-' }}</td>
                <td>{{ $item->foto_pulang ? 'Terlampir' : '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
