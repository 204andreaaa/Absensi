<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Absensi;
use App\Models\Pegawai;
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
        return view('pegawai.absensi', [
            'datasetCount' => Auth::user()->dataset_wajahs()->count(),
            'minDataset' => 15
        ]);
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
        $pegawai = Pegawai::with('jadwal')->findOrFail(Auth::id());
        $pegawaiId = $pegawai->id;
        $today = date('Y-m-d');

        $absen = Absensi::where('pegawai_id', $pegawaiId)
            ->whereDate('tanggal', $today)
            ->first();

        if (!$pegawai->jadwal) {
            return response()->json([
                'status' => false,
                'message' => 'Jadwal kerja pegawai belum diatur'
            ], 422);
        }

        if ($request->mode == 'masuk') {
            if ($absen) {
                return response()->json([
                    'status' => false,
                    'message' => 'Anda sudah absen masuk'
                ]);
            }

            $now = Carbon::now();
            $jadwalMasuk = Carbon::parse($today . ' ' . $pegawai->jadwal->jam_masuk);
            $batasToleransi = $jadwalMasuk->copy()->addMinutes((int) $pegawai->jadwal->toleransi_telat);
            $statusMasuk = $now->gt($batasToleransi) ? 'terlambat' : 'tepat_waktu';

            Absensi::create([
                'pegawai_id' => $pegawaiId,
                'tanggal' => $today,
                'jam_masuk' => $now->format('H:i:s'),
                'status' => $statusMasuk
            ]);

            return response()->json([
                'status' => true,
                'message' => $statusMasuk === 'terlambat'
                    ? 'Absensi masuk berhasil disimpan. Status: terlambat'
                    : 'Absensi masuk berhasil disimpan. Status: tepat waktu'
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

            $now = Carbon::now();
            $jadwalPulang = Carbon::parse($today . ' ' . $pegawai->jadwal->jam_pulang);

            if ($jadwalPulang->lessThanOrEqualTo(Carbon::parse($today . ' ' . $pegawai->jadwal->jam_masuk))) {
                $jadwalPulang->addDay();
            }

            $statusPulang = $now->lt($jadwalPulang) ? 'pulang_cepat' : 'sesuai_jadwal';

            $absen->update([
                'jam_pulang' => $now->format('H:i:s')
            ]);

            return response()->json([
                'status' => true,
                'message' => $statusPulang === 'pulang_cepat'
                    ? 'Absensi pulang berhasil disimpan. Status: pulang cepat'
                    : 'Absensi pulang berhasil disimpan. Status: sesuai jadwal'
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Mode absensi tidak valid'
        ], 422);
    }

}
