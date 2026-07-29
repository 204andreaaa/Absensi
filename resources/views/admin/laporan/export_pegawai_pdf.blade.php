<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $reportTitle }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header img {
            max-height: 80px;
            position: absolute;
            left: 0;
            top: 0;
        }
        .header h2 {
            margin: 0 0 5px 0;
            font-size: 16pt;
        }
        .header p {
            margin: 0;
            font-size: 10pt;
            color: #666;
        }
        .report-title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            margin: 20px 0;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            vertical-align: middle;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-align: center;
        }
        td.text-center {
            text-align: center;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10pt;
        }
        .signature {
            margin-top: 50px;
            width: 200px;
            float: right;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 60px;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        @php
            $logoPath = public_path('images/logo-mandau.png');
            $logoBase64 = '';
            if (file_exists($logoPath)) {
                $logoData = base64_encode(file_get_contents($logoPath));
                $logoBase64 = 'data:image/png;base64,' . $logoData;
            }
        @endphp

        @if($logoBase64)
            <img src="{{ $logoBase64 }}" alt="Logo">
        @endif

        <h2>YAYASAN MANDAU BERKARYA</h2>
        <p>Jl. Pendidikan No. 123, Kota Mandau<br>
        Telp: (0765) 123456 | Email: info@mandauberkarya.id</p>
    </div>

    <div class="report-title">
        {{ $reportTitle }}
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Foto</th>
                <th>NIK</th>
                <th>Nama Pegawai</th>
                <th>Departemen</th>
                <th>Jadwal / Shift</th>
                <th>Status</th>
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
                            <img src="{{ $fotoBase64 }}" alt="Foto" style="max-height: 50px; border-radius: 4px;">
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-center">{{ $item->nik }}</td>
                    <td>{{ $item->nama }}</td>
                    <td>{{ optional($item->departemen)->nama_departemen ?? '-' }}</td>
                    <td class="text-center">{{ optional($item->jadwal)->nama_shift ?? '-' }}</td>
                    <td class="text-center">{{ $item->status ? 'Aktif' : 'Non Aktif' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}</p>

        <div class="signature">
            <p>Mengetahui,</p>
            <div class="signature-line">
                <strong>Admin HRD</strong>
            </div>
        </div>
    </div>
</body>
</html>
