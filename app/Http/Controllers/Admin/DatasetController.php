<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DatasetWajah;

class DatasetController extends Controller
{

    public function index()
    {
        return view('admin.dataset.index');
    }


    public function store(Request $request)
    {

        try {

            $pegawai_id = $request->input('pegawai_id');
            $descriptor = $request->input('descriptor');

            DatasetWajah::create([
                'pegawai_id' => $pegawai_id,
                'descriptor' => json_encode($descriptor)
            ]);

            return response()->json([
                'status' => true
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ],500);

        }

    }

    public function load()
    {

        $data = DatasetWajah::with('pegawai')->get();

        $result = [];

        foreach($data as $d){

        $result[]=[

        'pegawai_id'=>$d->pegawai_id,
        'pegawai_nama'=>$d->pegawai?->nama ?? 'Pegawai Tidak Diketahui',
        'descriptor'=>json_decode($d->descriptor)

        ];

        }

        return response()->json($result);

    }

}
