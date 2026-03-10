<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\Pegawai;
use Carbon\Carbon;

class AbsensiController extends Controller
{

    public function index()
    {
        return view('admin.absensi.index');
    }

    public function laporan()
    {
        $laporanAbsensi = Absensi::with('pegawai.jadwal')
            ->orderByDesc('tanggal')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.laporan.index', [
            'laporanAbsensi' => $laporanAbsensi
        ]);
    }

    public function store(Request $request)
    {
        $pegawai = Pegawai::with('jadwal')->findOrFail($request->pegawai_id);
        $pegawai_id = $pegawai->id;
        $mode = $request->mode;

        $today = date('Y-m-d');

        $absen = Absensi::where('pegawai_id',$pegawai_id)
                ->whereDate('tanggal',$today)
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
        $jadwalMasuk = Carbon::parse($today . ' ' . $pegawai->jadwal->jam_masuk);
        $batasToleransi = $jadwalMasuk->copy()->addMinutes((int) $pegawai->jadwal->toleransi_telat);
        $statusMasuk = $now->gt($batasToleransi) ? 'terlambat' : 'tepat_waktu';

        Absensi::create([
        'pegawai_id'=>$pegawai_id,
        'tanggal'=>$today,
        'jam_masuk'=>$now->format('H:i:s'),
        'status'=>$statusMasuk
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
        $jadwalPulang = Carbon::parse($today . ' ' . $pegawai->jadwal->jam_pulang);

        if($jadwalPulang->lessThanOrEqualTo(Carbon::parse($today . ' ' . $pegawai->jadwal->jam_masuk))){
        $jadwalPulang->addDay();
        }

        $statusPulang = $now->lt($jadwalPulang) ? 'pulang_cepat' : 'sesuai_jadwal';

        $absen->update([
        'jam_pulang'=>$now->format('H:i:s')
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

}
