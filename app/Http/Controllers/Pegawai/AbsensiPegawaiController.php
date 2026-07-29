<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Absensi;
use App\Models\HariLibur;
use App\Models\Pegawai;
use Carbon\Carbon;

class AbsensiPegawaiController extends Controller
{
    private function getHolidayMessage(Carbon $date): ?string
    {
        if ($date->isWeekend()) {
            return 'Hari ini libur akhir pekan';
        }

        $hariLibur = HariLibur::whereDate('tanggal', $date->toDateString())->first();

        if ($hariLibur) {
            return 'Hari ini libur: ' . $hariLibur->nama_libur;
        }

        return null;
    }

    private function saveAttendancePhoto(?string $imageData, int $pegawaiId, string $mode): ?string
    {
        if (!$imageData || !str_starts_with($imageData, 'data:image/')) {
            return null;
        }

        [$meta, $content] = explode(',', $imageData, 2);
        $extension = str_contains($meta, 'image/png') ? 'png' : 'jpg';
        $binary = base64_decode($content);

        if ($binary === false) {
            return null;
        }

        $filename = sprintf(
            'attendance/%s/%s_%s_%s.%s',
            Carbon::today()->format('Y-m-d'),
            $pegawaiId,
            $mode,
            now()->format('His'),
            $extension
        );

        Storage::disk('public')->put($filename, $binary);

        return $filename;
    }


    public function dashboard()
    {
        $pegawaiId = Auth::id();
        $today = Carbon::today();
        $holidayMessage = $this->getHolidayMessage($today);
        $todayString = $today->toDateString();
        $startOfMonth = $today->copy()->startOfMonth()->toDateString();

        $todayAttendance = Absensi::where('pegawai_id', $pegawaiId)
            ->whereDate('tanggal', $todayString)
            ->first();

        $monthlyAttendances = Absensi::where('pegawai_id', $pegawaiId)
            ->whereBetween('tanggal', [$startOfMonth, $todayString])
            ->orderByDesc('tanggal')
            ->limit(5)
            ->get();

        $stats = [
            'hadir_bulan_ini' => Absensi::where('pegawai_id', $pegawaiId)
                ->whereBetween('tanggal', [$startOfMonth, $todayString])
                ->count(),
            'sudah_masuk_hari_ini' => (bool) optional($todayAttendance)->jam_masuk,
            'sudah_pulang_hari_ini' => (bool) optional($todayAttendance)->jam_pulang,
            'dataset_count' => Auth::user()->dataset_wajahs()->count()
        ];

        return view('pegawai.dashboard', [
            'todayAttendance' => $todayAttendance,
            'monthlyAttendances' => $monthlyAttendances,
            'stats' => $stats,
            'holidayMessage' => $holidayMessage
        ]);
    }

    public function index()
    {
        $pegawai = Auth::user()->load('jadwal');
        $holidayMessage = $this->getHolidayMessage(Carbon::today());

        return view('pegawai.absensi', [
            'datasetCount' => $pegawai->dataset_wajahs()->count(),
            'minDataset' => 15,
            'jadwalPegawai' => $pegawai->jadwal,
            'holidayMessage' => $holidayMessage
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
        $today = Carbon::today();
        $todayString = $today->toDateString();
        $holidayMessage = $this->getHolidayMessage($today);

        $absen = Absensi::where('pegawai_id', $pegawaiId)
            ->whereDate('tanggal', $todayString)
            ->first();

        if ($holidayMessage) {
            return response()->json([
                'status' => false,
                'message' => $holidayMessage
            ], 422);
        }

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
            $jadwalMasuk = Carbon::parse($todayString . ' ' . $pegawai->jadwal->jam_masuk);
            $batasToleransi = $jadwalMasuk->copy()->addMinutes((int) $pegawai->jadwal->toleransi_telat);
            $statusMasuk = $now->gt($batasToleransi) ? 'terlambat' : 'tepat_waktu';

            if ($statusMasuk === 'terlambat' && blank($request->alasan_telat)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Alasan keterlambatan wajib diisi'
                ], 422);
            }

            Absensi::create([
                'pegawai_id' => $pegawaiId,
                'tanggal' => $todayString,
                'jam_masuk' => $now->format('H:i:s'),
                'status' => $statusMasuk,
                'alasan_telat' => $statusMasuk === 'terlambat' ? $request->alasan_telat : null,
                'foto_masuk' => $this->saveAttendancePhoto($request->foto_bukti, $pegawaiId, 'masuk')
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
            $jadwalPulang = Carbon::parse($todayString . ' ' . $pegawai->jadwal->jam_pulang);

            if ($jadwalPulang->lessThanOrEqualTo(Carbon::parse($todayString . ' ' . $pegawai->jadwal->jam_masuk))) {
                $jadwalPulang->addDay();
            }

            $statusPulang = $now->lt($jadwalPulang) ? 'pulang_cepat' : 'sesuai_jadwal';

            if ($statusPulang === 'pulang_cepat' && blank($request->alasan_pulang_awal)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Alasan pulang awal wajib diisi'
                ], 422);
            }

            $absen->update([
                'jam_pulang' => $now->format('H:i:s'),
                'alasan_pulang_awal' => $statusPulang === 'pulang_cepat' ? $request->alasan_pulang_awal : null,
                'foto_pulang' => $this->saveAttendancePhoto($request->foto_bukti, $pegawaiId, 'pulang')
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
