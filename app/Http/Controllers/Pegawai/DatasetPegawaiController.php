<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DatasetWajah;
use Illuminate\Support\Facades\Auth;

class DatasetPegawaiController extends Controller
{

public function index()
{
    $pegawaiId = Auth::id();

    $datasetCount = DatasetWajah::where('pegawai_id',$pegawaiId)->count();

    return view('pegawai.dataset',[
        'datasetCount' => $datasetCount
    ]);
}

public function store(Request $request)
{

    DatasetWajah::create([

    'pegawai_id' => Auth::id(),

    'descriptor' => json_encode($request->descriptor)

    ]);

    return response()->json([
    'status' => true
    ]);

}

public function load()
{

    $datasets = \App\Models\DatasetWajah::with('pegawai')->get();

    $data = [];

    foreach($datasets as $item){

    $data[] = [

    'label'=>$item->pegawai->nama." | ID: ".$item->pegawai_id,
    'descriptor'=>json_decode($item->descriptor)

    ];

    }

    return response()->json($data);

}

}