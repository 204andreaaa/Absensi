<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $reportTitle }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 8mm 10mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8pt;
            color: #0f172a;
            margin: 0;
            padding: 0;
            background: #ffffff;
        }

        /* NO PRINT ACTION BAR */
        .no-print-bar {
            background: #1e293b;
            padding: 10px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            border-radius: 6px;
        }

        .no-print-bar span {
            color: #ffffff;
            font-weight: bold;
            font-size: 13px;
        }

        .btn-action {
            padding: 6px 14px;
            border-radius: 4px;
            border: none;
            font-weight: bold;
            font-size: 12px;
            cursor: pointer;
        }

        .btn-print {
            background: #4f46e5;
            color: #ffffff;
        }

        .btn-close {
            background: #64748b;
            color: #ffffff;
            margin-left: 6px;
        }

        @media print {
            .no-print-bar {
                display: none !important;
            }
        }

        /* REPORT TITLE */
        .report-title-box {
            text-align: center;
            margin-bottom: 14px;
        }

        .report-title-box h3 {
            margin: 0;
            font-size: 12pt;
            font-weight: 800;
            text-transform: uppercase;
            color: #0f172a;
            letter-spacing: 0.5px;
        }

        /* MATRIX TABLE */
        table.matrix-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        table.matrix-table th {
            border: 1px solid #64748b;
            padding: 5px 2px;
            text-align: center;
            font-size: 7.5pt;
            font-weight: bold;
            background-color: #f1f5f9;
        }

        table.matrix-table th.bg-holiday {
            background-color: #fecaca !important;
            color: #991b1b;
        }

        table.matrix-table td {
            border: 1px solid #cbd5e1;
            padding: 4px 2px;
            text-align: center;
            font-size: 8pt;
            vertical-align: middle;
        }

        .bg-off-cell {
            background-color: #f8fafc !important;
        }

        .text-hadir {
            color: #16a34a;
            font-weight: bold;
            font-size: 9.5pt;
        }

        .text-alpa {
            color: #dc2626;
            font-weight: bold;
            font-size: 9.5pt;
        }

        .text-off {
            color: #94a3b8;
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
            float: right;
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

    <!-- ACTION BAR UNTUK BROWSER -->
    <div class="no-print-bar">
        <span>Preview Print Laporan Kehadiran Bulanan (Grid Matrix)</span>
        <div>
            <button onclick="window.print()" class="btn-action btn-print">🖨️ Cetak / Simpan PDF</button>
            <button onclick="window.close()" class="btn-action btn-close">✖ Tutup</button>
        </div>
    </div>

    <!-- KOP SURAT STANDAR -->
    @include('admin.laporan.partials.kop_surat')

    <!-- JUDUL LAPORAN -->
    <div class="report-title-box">
        <h3>REKAPITULASI KEHADIRAN PEGAWAI - BULAN {{ strtoupper($bulan_label) }}</h3>
    </div>

    <!-- TABEL MATRIX GRID -->
    <table class="matrix-table">
        <thead>
            <tr>
                <th style="width: 25px;">NO</th>
                <th style="width: 70px;">NIK</th>
                <th style="width: 130px; text-align: left;">NAMA PEGAWAI</th>
                <th style="width: 90px; text-align: left;">DEPARTEMEN</th>
                
                @for($d = 1; $d <= $daysInMonth; $d++)
                    @php $dayHead = $daysHeader[$d]; @endphp
                    <th style="width: 20px;" class="{{ $dayHead['is_off'] ? 'bg-holiday' : '' }}">
                        {{ $d }}
                    </th>
                @endfor

                <th style="width: 45px; background-color: #e2e8f0;">HADIR</th>
            </tr>
        </thead>
        <tbody>
            @forelse($matrix as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="color: #475569;">{{ $row->pegawai->nik }}</td>
                    <td style="text-align: left; font-weight: bold;">{{ $row->pegawai->nama }}</td>
                    <td style="text-align: left;">{{ optional($row->pegawai->departemen)->nama_departemen ?? '-' }}</td>

                    @for($d = 1; $d <= $daysInMonth; $d++)
                        @php 
                            $cell = $row->days[$d]; 
                            $dayHead = $daysHeader[$d];
                        @endphp

                        <td class="{{ $dayHead['is_off'] ? 'bg-off-cell' : '' }}">
                            @if($cell['status'] === 'hadir')
                                <span class="text-hadir">✓</span>
                            @elseif($cell['status'] === 'alpa')
                                <span class="text-alpa">✕</span>
                            @else
                                <span class="text-off">-</span>
                            @endif
                        </td>
                    @endfor

                    <td style="font-weight: bold; background-color: #f1f5f9;">
                        {{ $row->total_hadir }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $daysInMonth + 5 }}" style="text-align: center; color: #94a3b8; padding: 20px;">
                        Belum ada data rekapitulasi kehadiran.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- TANDA TANGAN HEAD IT -->
    <div class="footer-section">
        <table class="signature-table">
            <tr>
                <td style="width: 70%;">
                    <div style="font-size: 7.5pt; color: #64748b;">
                        <strong>Keterangan Simbol:</strong> &nbsp;
                        <span class="text-hadir">✓</span> = Hadir / Absen &nbsp;&bull;&nbsp;
                        <span class="text-alpa">✕</span> = Tidak Hadir / Alpa &nbsp;&bull;&nbsp;
                        <span style="color: #991b1b;">Tanggal Red</span> = Libur / Akhir Pekan
                    </div>
                </td>
                <td style="width: 30%;">
                    <div class="signature-box">
                        <div class="signature-date">Dicetak pada: <span class="print-timestamp"></span></div>
                        <div class="signature-title">Mengetahui,</div>
                        <div class="signature-name">Sadrakh Simorangkir</div>
                        <div class="signature-position">Head IT</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <script>
        function updatePrintTimestamp() {
            const formattedTime = new Intl.DateTimeFormat('id-ID', {
                day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit'
            }).format(new Date()).replace('.', ':');

            document.querySelectorAll('.print-timestamp').forEach(function (element) {
                element.textContent = formattedTime;
            });
        }

        window.addEventListener('beforeprint', updatePrintTimestamp);
        updatePrintTimestamp();
    </script>
</body>
</html>
