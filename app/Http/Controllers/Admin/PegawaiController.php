<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\Departemen;
use App\Models\JadwalKerja;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Hash;

class PegawaiController extends Controller
{

    public function index()
    {
        $departemen = Departemen::all();
        $jadwal = JadwalKerja::all();

        return view('admin.pegawai.index', compact('departemen','jadwal'));
    }

    public function data()
    {
        $data = Pegawai::with('departemen','jadwal')->latest()->get();

        return DataTables::of($data)

        ->addIndexColumn()

        ->addColumn('departemen', function($d){
            return $d->departemen->nama_departemen ?? '-';
        })

        ->addColumn('jadwal', function($d){
            return $d->jadwal->nama_shift ?? '-';
        })

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

        Pegawai::updateOrCreate(

            ['id'=>$request->id],

            [
                'nik'=>$request->nik,
                'nama'=>$request->nama,
                'departemen_id'=>$request->departemen_id,
                'jadwal_kerja_id'=>$request->jadwal_kerja_id,
                'jabatan'=>$request->jabatan,
                'username'=>$request->username,
                'password'=>Hash::make($request->password),
                'status'=>$request->status
            ]

        );

        return response()->json([
            'success'=>true
        ]);
    }


    public function edit($id)
    {
        return Pegawai::find($id);
    }


    public function delete($id)
    {
        Pegawai::find($id)->delete();

        return response()->json([
            'success'=>true
        ]);
    }
}