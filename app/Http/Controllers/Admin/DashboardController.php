<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pegawai;
use App\Models\Departemen;
use App\Models\Absensi;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $bulan = (int) $request->input('bulan', Carbon::now()->month);
        $tahun = (int) $request->input('tahun', Carbon::now()->year);

        $totalPegawai = Pegawai::where('status', 1)->count();
        $totalDepartemen = Departemen::count();

        // Data Absensi Bulan Terpilih
        $monthlyAttendances = Absensi::whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->get();

        $totalAbsensiBulanIni = $monthlyAttendances->count();
        $tepatWaktuBulanIni = $monthlyAttendances->where('status', '!=', 'terlambat')->count();
        $terlambatBulanIni = $monthlyAttendances->where('status', 'terlambat')->count();

        // Data Absensi Hari Ini / Tanggal Terakhir di Bulan Terpilih
        $todayStr = Carbon::today()->toDateString();
        $todayAttendances = Absensi::whereDate('tanggal', $todayStr)->get();
        if ($todayAttendances->isEmpty() && $bulan != Carbon::now()->month) {
            $latestDate = Absensi::whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan)->max('tanggal');
            if ($latestDate) {
                $todayAttendances = Absensi::whereDate('tanggal', $latestDate)->get();
            }
        }

        $hadirHariIni = $todayAttendances->count();
        $tepatWaktuHariIni = $todayAttendances->where('status', '!=', 'terlambat')->count();
        $terlambatHariIni = $todayAttendances->where('status', 'terlambat')->count();
        $belumAbsenHariIni = max(0, $totalPegawai - $hadirHariIni);

        // Data Absensi Terbaru di Bulan Terpilih
        $latestAbsensi = Absensi::whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->with(['pegawai.departemen', 'pegawai.jadwal'])
            ->orderByDesc('tanggal')
            ->orderByDesc('created_at')
            ->take(7)
            ->get();

        if ($latestAbsensi->isEmpty()) {
            $latestAbsensi = Absensi::with(['pegawai.departemen', 'pegawai.jadwal'])
                ->orderByDesc('tanggal')
                ->orderByDesc('created_at')
                ->take(7)
                ->get();
        }

        // Data Tren Kehadiran Harian di Bulan Terpilih (untuk Chart.js)
        $daysInMonth = Carbon::createFromDate($tahun, $bulan, 1)->daysInMonth;
        $chartLabels = [];
        $chartTepatWaktu = [];
        $chartTerlambat = [];

        for ($d = 1; $d <= $daysInMonth; $d++) {
            $date = Carbon::createFromDate($tahun, $bulan, $d);
            if ($date->isWeekend()) continue; // Tampilkan hari kerja saja di chart
            
            $dateStr = $date->toDateString();
            $chartLabels[] = $date->format('d M');

            $dailyAbsensi = $monthlyAttendances->filter(function($item) use ($dateStr) {
                return $item->tanggal === $dateStr;
            });

            $chartTepatWaktu[] = $dailyAbsensi->where('status', '!=', 'terlambat')->count();
            $chartTerlambat[] = $dailyAbsensi->where('status', 'terlambat')->count();
        }

        return view('admin.index', [
            'bulan' => $bulan,
            'tahun' => $tahun,
            'totalPegawai' => $totalPegawai,
            'totalDepartemen' => $totalDepartemen,
            'totalAbsensiBulanIni' => $totalAbsensiBulanIni,
            'tepatWaktuBulanIni' => $tepatWaktuBulanIni,
            'terlambatBulanIni' => $terlambatBulanIni,
            'hadirHariIni' => $hadirHariIni,
            'tepatWaktuHariIni' => $tepatWaktuHariIni,
            'terlambatHariIni' => $terlambatHariIni,
            'belumAbsenHariIni' => $belumAbsenHariIni,
            'latestAbsensi' => $latestAbsensi,
            'chartLabels' => $chartLabels,
            'chartTepatWaktu' => $chartTepatWaktu,
            'chartTerlambat' => $chartTerlambat,
        ]);
    }
}
