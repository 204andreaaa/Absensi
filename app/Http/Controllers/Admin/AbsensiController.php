<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Absensi;

class AbsensiController extends Controller
{

    public function index()
    {
        return view('admin.absensi.index');
    }

    public function store(Request $request)
    {

        $pegawai_id = $request->pegawai_id;
        $mode = $request->mode;

        $today = date('Y-m-d');

        $absen = Absensi::where('pegawai_id',$pegawai_id)
                ->whereDate('tanggal',$today)
                ->first();

        if($mode == "masuk"){

        if($absen){
        return response()->json([
        'status'=>false,
        'message'=>'Anda sudah absen masuk'
        ]);
        }

        Absensi::create([
        'pegawai_id'=>$pegawai_id,
        'tanggal'=>$today,
        'jam_masuk'=>now(),
        'status'=>'hadir'
        ]);

        }

        if($mode == "keluar"){

        if(!$absen){
        return response()->json([
        'status'=>false,
        'message'=>'Anda belum absen masuk'
        ]);
        }

        $absen->update([
        'jam_pulang'=>now()
        ]);

        }

        return response()->json([
        'status'=>true
        ]);

    }

}