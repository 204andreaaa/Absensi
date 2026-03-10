<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JadwalKerja;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class JadwalKerjaController extends Controller
{

    public function index()
    {
        return view('admin.jadwal.index');
    }


    public function data()
    {

        $data = JadwalKerja::latest()->get();

        return DataTables::of($data)

        ->addIndexColumn()

        ->addColumn('aksi', function($d){

            return '
            <button onclick="editData('.$d->id.')" class="btn btn-warning btn-sm">Edit</button>

            <button onclick="deleteData('.$d->id.')" class="btn btn-danger btn-sm">Delete</button>
            ';
        })

        ->rawColumns(['aksi'])

        ->make(true);

    }



    public function store(Request $request)
    {

        JadwalKerja::updateOrCreate(

            ['id'=>$request->id],

            [

                'nama_shift'=>$request->nama_shift,
                'jam_masuk'=>$request->jam_masuk,
                'jam_pulang'=>$request->jam_pulang,
                'toleransi_telat'=>$request->toleransi_telat

            ]

        );

        return response()->json([
            'success'=>true
        ]);

    }



    public function edit($id)
    {
        $data = JadwalKerja::find($id);

        return response()->json($data);
    }



    public function delete($id)
    {

        JadwalKerja::find($id)->delete();

        return response()->json([
            'success'=>true
        ]);

    }

}