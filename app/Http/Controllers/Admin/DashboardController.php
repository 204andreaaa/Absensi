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
    public function index()
    {
        $today = Carbon::today()->toDateString();
        $totalPegawai = Pegawai::where('status', 1)->count();
        $totalDepartemen = Departemen::count();

        // Data Absensi Hari Ini
        $todayAttendances = Absensi::whereDate('tanggal', $today)
            ->with(['pegawai.departemen', 'pegawai.jadwal'])
            ->get();

        $hadirHariIni = $todayAttendances->count();
        $tepatWaktuHariIni = $todayAttendances->where('status', '!=', 'terlambat')->count();
        $terlambatHariIni = $todayAttendances->where('status', 'terlambat')->count();
        $belumAbsenHariIni = max(0, $totalPegawai - $hadirHariIni);

        // Data Absensi Terbaru (fallback jika hari ini belum ada data absensi untuk tampilan demo)
        $latestAbsensi = Absensi::whereDate('tanggal', $today)
            ->with(['pegawai.departemen', 'pegawai.jadwal'])
            ->orderByDesc('updated_at')
            ->take(7)
            ->get();

        if ($latestAbsensi->isEmpty()) {
            $latestAbsensi = Absensi::with(['pegawai.departemen', 'pegawai.jadwal'])
                ->orderByDesc('tanggal')
                ->orderByDesc('created_at')
                ->take(7)
                ->get();
        }

        // Data Tren Kehadiran 7 Hari Terakhir untuk Chart.js
        $chartLabels = [];
        $chartTepatWaktu = [];
        $chartTerlambat = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dateString = $date->toDateString();
            $chartLabels[] = $date->format('d M');

            $dailyAbsensi = Absensi::whereDate('tanggal', $dateString)->get();
            $chartTepatWaktu[] = $dailyAbsensi->where('status', '!=', 'terlambat')->count();
            $chartTerlambat[] = $dailyAbsensi->where('status', 'terlambat')->count();
        }

        return view('admin.index', [
            'totalPegawai' => $totalPegawai,
            'totalDepartemen' => $totalDepartemen,
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
