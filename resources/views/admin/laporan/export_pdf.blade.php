<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $reportTitle ?? 'Laporan' }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; font-size: 12px; }
        th { background-color: #f2f2f2; }
        h3 { text-align: center; margin: 10px 0 20px 0; }
        .text-center { text-align: center; }
        img.foto-absensi { width: 60px; height: auto; }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background-color: #4CAF50; color: white; border: none; cursor: pointer;">Print / Save as PDF</button>
        <button onclick="window.close()" style="padding: 10px 20px; background-color: #f44336; color: white; border: none; cursor: pointer; margin-left: 10px;">Tutup</button>
    </div>

    @include('admin.laporan.partials.kop_surat')

    <h3>{{ $reportTitle ?? 'Laporan Absensi' }}</h3>

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
                    <td class="text-center">
                        @if($item->foto_masuk)
                            <img src="{{ asset('storage/'.$item->foto_masuk) }}" class="foto-absensi" alt="Masuk">
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $item->alasan_telat ?? '-' }}</td>
                    <td>{{ $item->jam_pulang ?? '-' }}</td>
                    <td>{{ $item->status_pulang_label }}</td>
                    <td>{{ $item->alasan_pulang_awal ?? '-' }}</td>
                    <td class="text-center">
                        @if($item->foto_pulang)
                            <img src="{{ asset('storage/'.$item->foto_pulang) }}" class="foto-absensi" alt="Pulang">
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
