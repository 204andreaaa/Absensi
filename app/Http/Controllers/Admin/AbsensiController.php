<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AttendanceReportExport;
use App\Exports\MonthlyAttendanceSummaryExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use App\Models\Absensi;
use App\Models\HariLibur;
use App\Models\Pegawai;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class AbsensiController extends Controller
{
    private const REPORT_PER_PAGE = 15;

    private function getHolidayMessage(Carbon $date): ?string
    {
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


    public function index()
    {
        return view('admin.absensi.index');
    }

    private function baseAttendanceQuery()
    {
        $query = Absensi::with('pegawai.jadwal')
            ->orderByDesc('tanggal')
            ->orderByDesc('created_at');

        if (request('start_date')) {
            $query->whereDate('tanggal', '>=', request('start_date'));
        }
        if (request('end_date')) {
            $query->whereDate('tanggal', '<=', request('end_date'));
        }
        if (request('bulan')) {
            $query->whereMonth('tanggal', request('bulan'));
        }
        if (request('tahun')) {
            $query->whereYear('tanggal', request('tahun'));
        }
        if (request('pegawai_id')) {
            $query->where('pegawai_id', request('pegawai_id'));
        }

        return $query;
    }

    private function enrichAttendanceRecord(Absensi $item): Absensi
    {
        $jadwal = optional(optional($item->pegawai)->jadwal);
        $jadwalMasuk = $jadwal->jam_masuk;
        $jadwalPulang = $jadwal->jam_pulang;
        $statusPulang = '-';
        $menitTelat = 0;
        $menitPulangCepat = 0;

        if ($item->jam_masuk && $jadwalMasuk) {
            $tanggal = Carbon::parse($item->tanggal)->format('Y-m-d');
            $jamMasukAt = Carbon::parse($tanggal . ' ' . $item->jam_masuk);
            $jadwalMasukAt = Carbon::parse($tanggal . ' ' . $jadwalMasuk);
            $batasToleransi = $jadwalMasukAt->copy()->addMinutes((int) ($jadwal->toleransi_telat ?? 0));

            if ($jamMasukAt->gt($batasToleransi)) {
                $menitTelat = $jamMasukAt->diffInMinutes($batasToleransi);
            }
        }

        if ($item->jam_pulang && $jadwalMasuk && $jadwalPulang) {
            $tanggal = Carbon::parse($item->tanggal)->format('Y-m-d');
            $jadwalMasukAt = Carbon::parse($tanggal . ' ' . $jadwalMasuk);
            $jadwalPulangAt = Carbon::parse($tanggal . ' ' . $jadwalPulang);
            $jamPulangAt = Carbon::parse($tanggal . ' ' . $item->jam_pulang);

            if ($jadwalPulangAt->lessThanOrEqualTo($jadwalMasukAt)) {
                $jadwalPulangAt->addDay();

                if ($jamPulangAt->lessThanOrEqualTo($jadwalMasukAt)) {
                    $jamPulangAt->addDay();
                }
            }

            $statusPulang = $jamPulangAt->lt($jadwalPulangAt) ? 'Pulang Cepat' : 'Sesuai Jadwal';

            if ($statusPulang === 'Pulang Cepat') {
                $menitPulangCepat = $jamPulangAt->diffInMinutes($jadwalPulangAt);
            }
        } elseif ($item->jam_masuk) {
            $statusPulang = 'Belum Absen Pulang';
        }

        $item->setAttribute('jadwal_label', ($jadwalMasuk ?? '-') . ' - ' . ($jadwalPulang ?? '-'));
        $item->setAttribute('shift_label', $jadwal->nama_shift ?? '-');
        $item->setAttribute('toleransi_telat_label', ($jadwal->toleransi_telat ?? 0) . ' menit');
        $item->setAttribute('status_masuk_label', $item->status === 'terlambat' ? 'Terlambat' : 'Tepat Waktu');
        $item->setAttribute('status_pulang_label', $statusPulang);
        $item->setAttribute('menit_telat', $menitTelat);
        $item->setAttribute('selisih_telat_label', $menitTelat > 0 ? $menitTelat . ' menit' : '-');
        $item->setAttribute('menit_pulang_cepat', $menitPulangCepat);
        $item->setAttribute('selisih_pulang_cepat_label', $menitPulangCepat > 0 ? $menitPulangCepat . ' menit' : '-');

        return $item;
    }

    private function attendanceCollection(?callable $filter = null): Collection
    {
        $collection = $this->baseAttendanceQuery()
            ->get()
            ->map(fn (Absensi $item) => $this->enrichAttendanceRecord($item));

        if ($filter) {
            $collection = $collection->filter($filter)->values();
        }

        return $collection;
    }

    private function paginateCollection(Collection $items, int $perPage = self::REPORT_PER_PAGE): LengthAwarePaginator
    {
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $items->slice(($currentPage - 1) * $perPage, $perPage)->values();

        return new LengthAwarePaginator(
            $currentItems,
            $items->count(),
            $perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );
    }

    private function monthlySummaryCollection(): Collection
    {
        return $this->attendanceCollection()
            ->groupBy(function (Absensi $item) {
                return $item->pegawai_id . '-' . Carbon::parse($item->tanggal)->format('Y-m');
            })
            ->map(function (Collection $items) {
                $first = $items->first();
                $bulan = Carbon::parse($first->tanggal)->startOfMonth();

                return (object) [
                    'pegawai_nama' => optional($first->pegawai)->nama ?? 'Pegawai Tidak Diketahui',
                    'shift_label' => $first->shift_label,
                    'bulan_label' => $bulan->translatedFormat('F Y'),
                    'bulan_sort' => $bulan->format('Y-m'),
                    'total_hadir' => $items->count(),
                    'total_tepat_waktu' => $items->where('status', '!=', 'terlambat')->count(),
                    'total_terlambat' => $items->where('status', 'terlambat')->count(),
                    'total_pulang_cepat' => $items->where('status_pulang_label', 'Pulang Cepat')->count(),
                    'total_sesuai_jadwal' => $items->where('status_pulang_label', 'Sesuai Jadwal')->count(),
                    'total_belum_pulang' => $items->where('status_pulang_label', 'Belum Absen Pulang')->count(),
                ];
            })
            ->sortByDesc('bulan_sort')
            ->values();
    }

    private function attendanceReportResponse(
        string $view,
        string $title,
        string $heading,
        string $exportRoute,
        ?callable $filter = null
    ) {
        $laporanAbsensi = $this->paginateCollection($this->attendanceCollection($filter));
        $listPegawai = Pegawai::orderBy('nama')->get();

        return view($view, [
            'pageTitle' => $title,
            'cardTitle' => $heading,
            'exportRoute' => $exportRoute,
            'exportPdfRoute' => str_replace('export-excel', 'export-pdf', $exportRoute),
            'laporanAbsensi' => $laporanAbsensi,
            'listPegawai' => $listPegawai,
        ]);
    }

    public function laporan()
    {
        return $this->attendanceReportResponse(
            'admin.laporan.index',
            'Laporan Kehadiran',
            'Rekap Absensi Pegawai',
            'admin.laporan.export-excel'
        );
    }

    public function exportExcel()
    {
        $laporanAbsensi = $this->attendanceCollection();

        return Excel::download(
            new AttendanceReportExport($laporanAbsensi, 'Laporan Kehadiran'),
            'laporan-absensi-' . now()->format('Ymd-His') . '.xlsx'
        );
    }

    public function exportPdf()
    {
        $laporanAbsensi = $this->attendanceCollection();

        return view('admin.laporan.export_pdf', [
            'reportTitle' => 'Laporan Kehadiran',
            'laporanAbsensi' => $laporanAbsensi
        ]);
    }

    public function laporanTepatWaktu()
    {
        $query = Pegawai::with(['departemen', 'jadwal']);
        if (request('departemen_id')) {
            $query->where('departemen_id', request('departemen_id'));
        }
        $pegawai = $query->paginate(self::REPORT_PER_PAGE);
        $listDepartemen = \App\Models\Departemen::orderBy('nama_departemen')->get();

        return view('admin.laporan.pegawai', [
            'pageTitle' => 'Laporan Pegawai',
            'cardTitle' => 'Data Laporan Pegawai',
            'pegawai' => $pegawai,
            'listDepartemen' => $listDepartemen,
            'exportRoute' => 'admin.laporan.tepat-waktu.export-excel',
            'exportPdfRoute' => 'admin.laporan.tepat-waktu.export-pdf',
        ]);
    }

    public function exportExcelTepatWaktu()
    {
        $query = Pegawai::with(['departemen', 'jadwal']);
        if (request('departemen_id')) {
            $query->where('departemen_id', request('departemen_id'));
        }
        $pegawai = $query->get();

        return Excel::download(
            new \App\Exports\PegawaiReportExport($pegawai),
            'laporan-pegawai-' . now()->format('Ymd-His') . '.xlsx'
        );
    }

    public function exportPdfTepatWaktu()
    {
        $query = Pegawai::with(['departemen', 'jadwal']);
        if (request('departemen_id')) {
            $query->where('departemen_id', request('departemen_id'));
        }
        $pegawai = $query->get();

        return view('admin.laporan.export_pegawai_pdf', [
            'reportTitle' => 'Laporan Pegawai',
            'pegawai' => $pegawai
        ]);
    }

    public function laporanTerlambat()
    {
        return $this->attendanceReportResponse(
            'admin.laporan.terlambat',
            'Laporan Keterlambatan',
            'Daftar Pegawai Terlambat',
            'admin.laporan.terlambat.export-excel',
            fn (Absensi $item) => $item->status === 'terlambat'
        );
    }

    public function exportExcelTerlambat()
    {
        $laporanAbsensi = $this->attendanceCollection(
            fn (Absensi $item) => $item->status === 'terlambat'
        );

        return Excel::download(
            new AttendanceReportExport(
                $laporanAbsensi,
                'Laporan Keterlambatan',
                'admin.laporan.export_terlambat',
                ['masuk' => 'I']
            ),
            'laporan-keterlambatan-' . now()->format('Ymd-His') . '.xlsx'
        );
    }

    public function exportPdfTerlambat()
    {
        $laporanAbsensi = $this->attendanceCollection(
            fn (Absensi $item) => $item->status === 'terlambat'
        );

        return view('admin.laporan.export_pdf', [
            'reportTitle' => 'Laporan Keterlambatan',
            'laporanAbsensi' => $laporanAbsensi
        ]);
    }

    public function laporanPulangCepat()
    {
        return $this->attendanceReportResponse(
            'admin.laporan.pulang_cepat',
            'Laporan Pulang Cepat',
            'Daftar Pegawai Pulang Cepat',
            'admin.laporan.pulang-cepat.export-excel',
            fn (Absensi $item) => $item->status_pulang_label === 'Pulang Cepat'
        );
    }

    public function exportExcelPulangCepat()
    {
        $laporanAbsensi = $this->attendanceCollection(
            fn (Absensi $item) => $item->status_pulang_label === 'Pulang Cepat'
        );

        return Excel::download(
            new AttendanceReportExport(
                $laporanAbsensi,
                'Laporan Pulang Cepat',
                'admin.laporan.export_pulang_cepat',
                ['pulang' => 'I']
            ),
            'laporan-pulang-cepat-' . now()->format('Ymd-His') . '.xlsx'
        );
    }

    public function exportPdfPulangCepat()
    {
        $laporanAbsensi = $this->attendanceCollection(
            fn (Absensi $item) => $item->status_pulang_label === 'Pulang Cepat'
        );

        return view('admin.laporan.export_pdf', [
            'reportTitle' => 'Laporan Pulang Cepat',
            'laporanAbsensi' => $laporanAbsensi
        ]);
    }

    public function laporanRekapBulanan()
    {
        $rekapBulanan = $this->paginateCollection($this->monthlySummaryCollection());
        $listPegawai = Pegawai::orderBy('nama')->get();

        return view('admin.laporan.rekap_bulanan', [
            'pageTitle' => 'Laporan Rekap Bulanan',
            'cardTitle' => 'Rekap Absensi Bulanan',
            'exportRoute' => 'admin.laporan.rekap-bulanan.export-excel',
            'exportPdfRoute' => 'admin.laporan.rekap-bulanan.export-pdf',
            'rekapBulanan' => $rekapBulanan,
            'listPegawai' => $listPegawai,
        ]);
    }

    public function exportExcelRekapBulanan()
    {
        return Excel::download(
            new MonthlyAttendanceSummaryExport($this->monthlySummaryCollection(), 'Laporan Rekap Bulanan'),
            'laporan-rekap-bulanan-' . now()->format('Ymd-His') . '.xlsx'
        );
    }

    public function exportPdfRekapBulanan()
    {
        return view('admin.laporan.export_pdf_rekap_bulanan', [
            'reportTitle' => 'Laporan Rekap Bulanan',
            'rekapBulanan' => $this->monthlySummaryCollection()
        ]);
    }

    public function store(Request $request)
    {
        $pegawai = Pegawai::with('jadwal')->findOrFail($request->pegawai_id);
        $pegawai_id = $pegawai->id;
        $mode = $request->mode;

        $today = Carbon::today();
        $todayString = $today->toDateString();
        $holidayMessage = $this->getHolidayMessage($today);

        if ($holidayMessage) {
            return response()->json([
                'status' => false,
                'message' => $holidayMessage
            ], 422);
        }

        $absen = Absensi::where('pegawai_id',$pegawai_id)
                ->whereDate('tanggal',$todayString)
                ->first();

        if (!$pegawai->jadwal) {
            return response()->json([
                'status' => false,
                'message' => 'Jadwal kerja pegawai belum diatur'
            ], 422);
        }

        if($mode == "masuk"){

        if($absen){
        return response()->json([
        'status'=>false,
        'message'=>'Anda sudah absen masuk'
        ]);
        }

        $now = Carbon::now();
        $jadwalMasuk = Carbon::parse($todayString . ' ' . $pegawai->jadwal->jam_masuk);
        $batasToleransi = $jadwalMasuk->copy()->addMinutes((int) $pegawai->jadwal->toleransi_telat);
        $statusMasuk = $now->gt($batasToleransi) ? 'terlambat' : 'tepat_waktu';

        if($statusMasuk === 'terlambat' && blank($request->alasan_telat)){
        return response()->json([
        'status'=>false,
        'message'=>'Alasan keterlambatan wajib diisi'
        ],422);
        }

        Absensi::create([
        'pegawai_id'=>$pegawai_id,
        'tanggal'=>$todayString,
        'jam_masuk'=>$now->format('H:i:s'),
        'status'=>$statusMasuk,
        'alasan_telat'=>$statusMasuk === 'terlambat' ? $request->alasan_telat : null,
        'foto_masuk'=>$this->saveAttendancePhoto($request->foto_bukti, $pegawai_id, 'masuk')
        ]);

        return response()->json([
        'status'=>true,
        'message'=>$statusMasuk === 'terlambat'
            ? 'Absensi masuk berhasil disimpan. Status: terlambat'
            : 'Absensi masuk berhasil disimpan. Status: tepat waktu'
        ]);

        }

        if($mode == "keluar"){

        if(!$absen){
        return response()->json([
        'status'=>false,
        'message'=>'Anda belum absen masuk'
        ]);
        }

        if($absen->jam_pulang){
        return response()->json([
        'status'=>false,
        'message'=>'Anda sudah absen pulang'
        ]);
        }

        $now = Carbon::now();
        $jadwalPulang = Carbon::parse($todayString . ' ' . $pegawai->jadwal->jam_pulang);

        if($jadwalPulang->lessThanOrEqualTo(Carbon::parse($todayString . ' ' . $pegawai->jadwal->jam_masuk))){
        $jadwalPulang->addDay();
        }

        $statusPulang = $now->lt($jadwalPulang) ? 'pulang_cepat' : 'sesuai_jadwal';

        if($statusPulang === 'pulang_cepat' && blank($request->alasan_pulang_awal)){
        return response()->json([
        'status'=>false,
        'message'=>'Alasan pulang awal wajib diisi'
        ],422);
        }

        $absen->update([
        'jam_pulang'=>$now->format('H:i:s'),
        'alasan_pulang_awal'=>$statusPulang === 'pulang_cepat' ? $request->alasan_pulang_awal : null,
        'foto_pulang'=>$this->saveAttendancePhoto($request->foto_bukti, $pegawai_id, 'pulang')
        ]);

        return response()->json([
        'status'=>true,
        'message'=>$statusPulang === 'pulang_cepat'
            ? 'Absensi pulang berhasil disimpan. Status: pulang cepat'
            : 'Absensi pulang berhasil disimpan. Status: sesuai jadwal'
        ]);

        }

        return response()->json([
        'status'=>false,
        'message'=>'Mode absensi tidak valid'
        ],422);

    }

    public function resetHariIni()
    {
        $todayString = Carbon::today()->toDateString();
        Absensi::whereDate('tanggal', $todayString)->delete();
        
        return response()->json([
            'status' => true,
            'message' => 'Seluruh absensi hari ini berhasil dihapus (Mode Testing)'
        ]);
    }

    public function cameraTesting()
    {
        return view('admin.absensi.camera_testing');
    }

    public function livenessTesting()
    {
        return view('admin.absensi.liveness_testing');
    }

}
