<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Absensi;
use Carbon\Carbon;

class AbsensiPegawaiController extends Controller
{

    public function dashboard()
    {
        $pegawaiId = Auth::id();
        $today = Carbon::today()->toDateString();
        $startOfMonth = Carbon::today()->startOfMonth()->toDateString();

        $todayAttendance = Absensi::where('pegawai_id', $pegawaiId)
            ->whereDate('tanggal', $today)
            ->first();

        $monthlyAttendances = Absensi::where('pegawai_id', $pegawaiId)
            ->whereBetween('tanggal', [$startOfMonth, $today])
            ->orderByDesc('tanggal')
            ->limit(5)
            ->get();

        $stats = [
            'hadir_bulan_ini' => Absensi::where('pegawai_id', $pegawaiId)
                ->whereBetween('tanggal', [$startOfMonth, $today])
                ->count(),
            'sudah_masuk_hari_ini' => (bool) optional($todayAttendance)->jam_masuk,
            'sudah_pulang_hari_ini' => (bool) optional($todayAttendance)->jam_pulang,
            'dataset_count' => Auth::user()->dataset_wajahs()->count()
        ];

        return view('pegawai.dashboard', [
            'todayAttendance' => $todayAttendance,
            'monthlyAttendances' => $monthlyAttendances,
            'stats' => $stats
        ]);
    }

    public function index()
    {
        return view('pegawai.absensi');
    }

    public function riwayat()
    {
        $riwayatAbsensi = Absensi::where('pegawai_id', Auth::id())
            ->orderByDesc('tanggal')
            ->paginate(10);

        return view('pegawai.riwayat', [
            'riwayatAbsensi' => $riwayatAbsensi
        ]);
    }

    public function store(Request $request)
    {
        $pegawaiId = Auth::id();
        $today = date('Y-m-d');

        $absen = Absensi::where('pegawai_id', $pegawaiId)
            ->whereDate('tanggal', $today)
            ->first();

        if ($request->mode == 'masuk') {
            if ($absen) {
                return response()->json([
                    'status' => false,
                    'message' => 'Anda sudah absen masuk'
                ]);
            }

            Absensi::create([
                'pegawai_id' => $pegawaiId,
                'tanggal' => $today,
                'jam_masuk' => now(),
                'status' => 'hadir'
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Absensi masuk berhasil disimpan'
            ]);
        }

        if ($request->mode == 'keluar') {
            if (!$absen) {
                return response()->json([
                    'status' => false,
                    'message' => 'Belum absen masuk'
                ]);
            }

            if ($absen->jam_pulang) {
                return response()->json([
                    'status' => false,
                    'message' => 'Anda sudah absen pulang'
                ]);
            }

            $absen->update([
                'jam_pulang' => now()
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Absensi pulang berhasil disimpan'
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Mode absensi tidak valid'
        ], 422);
    }

}
