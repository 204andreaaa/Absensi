<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $reportTitle }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 15mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
            color: #1e293b;
            margin: 0;
            padding: 0;
            background: #ffffff;
        }

        /* REPORT TITLE */
        .report-title-box {
            text-align: center;
            margin-bottom: 22px;
        }

        .report-title-box h3 {
            margin: 0;
            font-size: 13pt;
            font-weight: 800;
            text-transform: uppercase;
            color: #0f172a;
            letter-spacing: 0.8px;
        }

        /* DATA TABLE */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        table.data-table th {
            background-color: #f1f5f9 !important;
            color: #0f172a;
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
            border: 1px solid #cbd5e1;
            padding: 9px 6px;
            text-align: center;
        }

        table.data-table td {
            border: 1px solid #cbd5e1;
            padding: 8px;
            font-size: 9.5pt;
            color: #334155;
            vertical-align: middle;
        }

        table.data-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        .text-center {
            text-align: center;
        }

        .foto-thumb {
            width: 42px;
            height: 42px;
            object-fit: cover;
            border-radius: 50%;
            border: 1.5px solid #cbd5e1;
            display: inline-block;
        }

        .no-photo-badge {
            color: #94a3b8;
            font-size: 8.5pt;
            font-style: italic;
        }

        .status-badge-active {
            color: #16a34a;
            font-weight: bold;
        }

        .status-badge-inactive {
            color: #dc2626;
            font-weight: bold;
        }

        /* SIGNATURE FOOTER */
        .footer-section {
            width: 100%;
            margin-top: 30px;
            page-break-inside: avoid;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
        }

        .signature-table td {
            border: none !important;
            vertical-align: top;
        }

        .signature-box {
            width: 220px;
            text-align: center;
            display: inline-block;
        }

        .signature-date {
            font-size: 9pt;
            color: #64748b;
            margin-bottom: 8px;
        }

        .signature-title {
            font-size: 9.5pt;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 60px;
        }

        .signature-name {
            font-size: 9.5pt;
            font-weight: 800;
            color: #0f172a;
            border-top: 1.5px solid #0f172a;
            padding-top: 4px;
            display: inline-block;
            min-width: 160px;
        }

        .signature-position {
            font-size: 9pt;
            margin-top: 4px;
        }
    </style>
</head>
<body>

    <!-- KOP SURAT STANDAR LAPORAN -->
    @include('admin.laporan.partials.kop_surat')

    <!-- JUDUL LAPORAN -->
    <div class="report-title-box">
        <h3>{{ $reportTitle }}</h3>
    </div>

    <!-- TABEL DATA PEGAWAI -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 12%;">Foto</th>
                <th style="width: 13%;">NIK</th>
                <th style="width: 25%;">Nama Pegawai</th>
                <th style="width: 18%;">Departemen</th>
                <th style="width: 15%;">Jadwal / Shift</th>
                <th style="width: 12%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pegawai as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">
                        @php
                            $fotoBase64 = '';
                            if ($item->foto) {
                                $fotoPath = public_path('storage/' . $item->foto);
                                if (file_exists($fotoPath)) {
                                    $fotoData = base64_encode(file_get_contents($fotoPath));
                                    $fotoExtension = pathinfo($fotoPath, PATHINFO_EXTENSION);
                                    $fotoBase64 = 'data:image/' . $fotoExtension . ';base64,' . $fotoData;
                                }
                            }
                        @endphp

                        @if($fotoBase64)
                            <img src="{{ $fotoBase64 }}" alt="Foto" class="foto-thumb">
                        @else
                            <span class="no-photo-badge">-</span>
                        @endif
                    </td>
                    <td class="text-center" style="font-weight: 600;">{{ $item->nik }}</td>
                    <td style="font-weight: 600; color: #0f172a;">{{ $item->nama }}</td>
                    <td>{{ optional($item->departemen)->nama_departemen ?? '-' }}</td>
                    <td class="text-center">{{ optional($item->jadwal)->nama_shift ?? '-' }}</td>
                    <td class="text-center">
                        @if($item->status)
                            <span class="status-badge-active">Aktif</span>
                        @else
                            <span class="status-badge-inactive">Non Aktif</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- TANDA TANGAN HEAD IT -->
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
