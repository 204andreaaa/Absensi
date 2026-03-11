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

class AttendanceReportExport implements FromView, ShouldAutoSize, WithDrawings, WithEvents
{
    public function __construct(
        private readonly Collection $laporanAbsensi,
        private readonly string $reportTitle = 'Laporan Kehadiran',
        private readonly string $viewName = 'admin.laporan.export_xlsx',
        private readonly array $imageColumns = ['masuk' => 'H', 'pulang' => 'M']
    ) {
    }

    public function view(): View
    {
        return view($this->viewName, [
            'reportTitle' => $this->reportTitle,
            'laporanAbsensi' => $this->laporanAbsensi
        ]);
    }

    public function drawings(): array
    {
        $drawings = [];

        foreach ($this->laporanAbsensi as $index => $item) {
            $row = $index + 2;

            if ($item->foto_masuk && isset($this->imageColumns['masuk'])) {
                $pathMasuk = public_path('storage/' . $item->foto_masuk);

                if (file_exists($pathMasuk)) {
                    $drawing = new Drawing();
                    $drawing->setName('Foto Masuk');
                    $drawing->setDescription('Foto Masuk');
                    $drawing->setPath($pathMasuk);
                    $drawing->setHeight(70);
                    $drawing->setCoordinates($this->imageColumns['masuk'] . $row);
                    $drawing->setOffsetX(8);
                    $drawing->setOffsetY(5);
                    $drawings[] = $drawing;
                }
            }

            if ($item->foto_pulang && isset($this->imageColumns['pulang'])) {
                $pathPulang = public_path('storage/' . $item->foto_pulang);

                if (file_exists($pathPulang)) {
                    $drawing = new Drawing();
                    $drawing->setName('Foto Pulang');
                    $drawing->setDescription('Foto Pulang');
                    $drawing->setPath($pathPulang);
                    $drawing->setHeight(70);
                    $drawing->setCoordinates($this->imageColumns['pulang'] . $row);
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

                foreach ($this->imageColumns as $column) {
                    $sheet->getColumnDimension($column)->setWidth(18);
                }

                foreach (range(2, $this->laporanAbsensi->count() + 1) as $row) {
                    $sheet->getRowDimension($row)->setRowHeight(60);
                }
            },
        ];
    }
}
