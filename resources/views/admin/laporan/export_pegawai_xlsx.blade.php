<table>
    <thead>
        <tr>
            <th colspan="7" style="text-align: center; font-size: 14pt; font-weight: bold;">
                YAYASAN MANDAU BERKARYA
            </th>
        </tr>
        <tr>
            <th colspan="7" style="text-align: center; font-size: 12pt;">
                Jl. Pendidikan No. 123, Kota Mandau
            </th>
        </tr>
        <tr>
            <th colspan="7" style="text-align: center; font-size: 12pt;">
                Telp: (0765) 123456 | Email: info@mandauberkarya.id
            </th>
        </tr>
        <tr>
            <th colspan="7" style="text-align: center; font-weight: bold;">
                _________________________________________________________________________________________________________
            </th>
        </tr>
        <tr>
            <th colspan="7" style="text-align: center; font-size: 13pt; font-weight: bold;">
                {{ mb_strtoupper($reportTitle) }}
            </th>
        </tr>
        <tr>
            <th colspan="7" style="text-align: center;">
                Tanggal Cetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}
            </th>
        </tr>
        <tr>
            <th colspan="7"></th>
        </tr>

        <!-- Table Header -->
        <tr>
            <th style="font-weight: bold; background-color: #f3f4f6; border: 1px solid #000000; text-align: center; width: 5px;">No</th>
            <th style="font-weight: bold; background-color: #f3f4f6; border: 1px solid #000000; text-align: center; width: 15px;">NIK</th>
            <th style="font-weight: bold; background-color: #f3f4f6; border: 1px solid #000000; text-align: center; width: 18px;">Foto (Terakhir)</th>
            <th style="font-weight: bold; background-color: #f3f4f6; border: 1px solid #000000; text-align: center; width: 25px;">Nama Pegawai</th>
            <th style="font-weight: bold; background-color: #f3f4f6; border: 1px solid #000000; text-align: center; width: 20px;">Departemen</th>
            <th style="font-weight: bold; background-color: #f3f4f6; border: 1px solid #000000; text-align: center; width: 15px;">Jadwal / Shift</th>
            <th style="font-weight: bold; background-color: #f3f4f6; border: 1px solid #000000; text-align: center; width: 15px;">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($pegawai as $index => $item)
            <tr>
                <td style="border: 1px solid #000000; text-align: center; vertical-align: center;">
                    {{ $index + 1 }}
                </td>
                <td style="border: 1px solid #000000; text-align: center; vertical-align: center;">
                    {{ $item->nik }}
                </td>
                <td style="border: 1px solid #000000; text-align: center; vertical-align: center;">
                    <!-- Gambar akan dirender oleh withDrawings di Export class -->
                </td>
                <td style="border: 1px solid #000000; vertical-align: center;">
                    {{ $item->nama }}
                </td>
                <td style="border: 1px solid #000000; vertical-align: center;">
                    {{ optional($item->departemen)->nama_departemen ?? '-' }}
                </td>
                <td style="border: 1px solid #000000; text-align: center; vertical-align: center;">
                    {{ optional($item->jadwal)->nama_shift ?? '-' }}
                </td>
                <td style="border: 1px solid #000000; text-align: center; vertical-align: center;">
                    {{ $item->status ? 'Aktif' : 'Non Aktif' }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
