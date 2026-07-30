@php
    $logoPath = public_path('images/logo-mandau.png');
    $logoSrc = asset('images/logo-mandau.png');
    if (file_exists($logoPath)) {
        $logoData = base64_encode(file_get_contents($logoPath));
        $logoSrc = 'data:image/png;base64,' . $logoData;
    }
@endphp

<table style="width: 100%; border-collapse: collapse; border: none; margin-bottom: 5px;">
    <tr style="border: none;">
        <td style="width: 140px; border: none; vertical-align: middle; text-align: center; padding: 0;">
            <img src="{{ $logoSrc }}" alt="Logo" style="max-width: 130px; max-height: 75px; object-fit: contain;">
        </td>
        <td style="border: none; vertical-align: middle; padding-left: 15px;">
            <h2 style="margin: 0; font-size: 15pt; font-weight: 800; text-transform: uppercase; color: #000; letter-spacing: 0.5px;">PT. MANDIRI DAYA UTAMA NUSANTARA</h2>
            <p style="margin: 4px 0 2px 0; font-size: 9pt; color: #333; line-height: 1.3;">
                Golden Fatmawati, Jl. RS. Fatmawati Raya No.17 C17, RT.10/RW.6, Gandaria Sel.,
            </p>
            <p style="margin: 0 0 2px 0; font-size: 9pt; color: #333; line-height: 1.3;">
                Kec. Cilandak, Kota Jakarta Selatan, Daerah Khusus Ibukota Jakarta 12420
            </p>
            <p style="margin: 0; font-size: 9pt; color: #333; line-height: 1.3;">
                Telepon: (021) 7697070
            </p>
        </td>
    </tr>
</table>
<div style="border-bottom: 3px double #000; margin-top: 8px; margin-bottom: 20px;"></div>
