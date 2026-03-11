<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MonthlyAttendanceSummaryExport implements FromView, ShouldAutoSize
{
    public function __construct(
        private readonly Collection $rekapBulanan,
        private readonly string $reportTitle = 'Laporan Rekap Bulanan'
    ) {
    }

    public function view(): View
    {
        return view('admin.laporan.export_rekap_bulanan', [
            'reportTitle' => $this->reportTitle,
            'rekapBulanan' => $this->rekapBulanan,
        ]);
    }
}
