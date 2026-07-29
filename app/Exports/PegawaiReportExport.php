<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class PegawaiReportExport implements FromView, ShouldAutoSize, WithDrawings, WithEvents
{
    public function __construct(
        private readonly Collection $pegawai,
        private readonly string $reportTitle = 'Laporan Pegawai'
    ) {
    }

    public function view(): View
    {
        return view('admin.laporan.export_pegawai_xlsx', [
            'reportTitle' => $this->reportTitle,
            'pegawai' => $this->pegawai
        ]);
    }

    public function drawings(): array
    {
        $drawings = [];

        // Add Logo at A1
        $logoPath = public_path('images/logo-mandau.png');
        if (file_exists($logoPath)) {
            $logoDrawing = new Drawing();
            $logoDrawing->setName('Logo Mandau');
            $logoDrawing->setDescription('Logo Perusahaan');
            $logoDrawing->setPath($logoPath);
            $logoDrawing->setHeight(90);
            $logoDrawing->setCoordinates('A1');
            $logoDrawing->setOffsetX(10);
            $logoDrawing->setOffsetY(10);
            $drawings[] = $logoDrawing;
        }

        foreach ($this->pegawai as $index => $item) {
            $row = $index + 9; // Offset by 8 rows due to letterhead

            if ($item->foto) {
                $pathFoto = public_path('storage/' . $item->foto);

                if (file_exists($pathFoto)) {
                    $drawing = new Drawing();
                    $drawing->setName('Foto Pegawai');
                    $drawing->setDescription('Foto Pegawai');
                    $drawing->setPath($pathFoto);
                    $drawing->setHeight(70);
                    $drawing->setCoordinates('C' . $row);
                    $drawing->setOffsetX(8);
                    $drawing->setOffsetY(5);
                    $drawings[] = $drawing;
                }
            }
        }

        return $drawings;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // C column is Foto
                $sheet->getColumnDimension('C')->setWidth(18);

                // Adjust row heights for data (starting at row 9)
                foreach (range(9, $this->pegawai->count() + 8) as $row) {
                    $sheet->getRowDimension($row)->setRowHeight(60);
                }
            },
        ];
    }
}
