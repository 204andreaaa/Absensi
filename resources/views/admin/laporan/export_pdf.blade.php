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
        .footer-section { width: 100%; margin-top: 30px; page-break-inside: avoid; }
        .signature-table { width: 100%; border-collapse: collapse; border: none; margin-top: 0; }
        .signature-table td { border: none !important; vertical-align: top; padding: 0; }
        .signature-box { width: 220px; text-align: center; display: inline-block; }
        .signature-date { font-size: 9pt; color: #64748b; margin-bottom: 8px; }
        .signature-title { font-size: 9.5pt; font-weight: 600; margin-bottom: 60px; }
        .signature-name { font-size: 9.5pt; font-weight: 800; border-top: 1.5px solid #0f172a; padding-top: 4px; display: inline-block; min-width: 160px; }
        .signature-position { font-size: 9pt; margin-top: 4px; }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
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

    <div class="footer-section">
        <div style="text-align: right;">
            <div class="signature-box">
                <div class="signature-date">Jakarta, {{ now()->locale('id')->translatedFormat('l, d F Y') }}</div>
                <div class="signature-title">Mengetahui,</div>
                <div class="signature-name">Sadrakh Simorangkir</div>
                <div class="signature-position">Head IT</div>
            </div>
        </div>
    </div>
</body>
</html>
