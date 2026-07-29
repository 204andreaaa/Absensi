<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $reportTitle ?? 'Laporan Rekap Bulanan' }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; font-size: 12px; }
        th { background-color: #f2f2f2; }
        h3 { text-align: center; margin: 10px 0 20px 0; }
        .text-center { text-align: center; }
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

    <h3>{{ $reportTitle ?? 'Laporan Rekap Bulanan' }}</h3>

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
                    <td class="text-center">{{ $item->total_hadir }}</td>
                    <td class="text-center">{{ $item->total_tepat_waktu }}</td>
                    <td class="text-center">{{ $item->total_terlambat }}</td>
                    <td class="text-center">{{ $item->total_pulang_cepat }}</td>
                    <td class="text-center">{{ $item->total_sesuai_jadwal }}</td>
                    <td class="text-center">{{ $item->total_belum_pulang }}</td>
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
