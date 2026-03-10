<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Departemen;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DepartemenController extends Controller
{

    public function index()
    {
        return view('admin.departemen.index');
    }

    public function data()
    {
        $data = Departemen::latest()->get();

        return DataTables::of($data)

        ->addIndexColumn()

        ->addColumn('aksi', function($d){

            return '
            <button onclick="editData('.$d->id.')" class="btn btn-warning btn-sm">
            Edit
            </button>

            <button onclick="deleteData('.$d->id.')" class="btn btn-danger btn-sm">
            Delete
            </button>
            ';
        })

        ->rawColumns(['aksi'])

        ->make(true);
    }


    public function store(Request $request)
    {

    Departemen::updateOrCreate(

    ['id'=>$request->id],

    [
    'nama_departemen'=>$request->nama_departemen,
    'keterangan'=>$request->keterangan
    ]

    );

    return response()->json([
    'success'=>true
    ]);

    }


    public function edit($id)
    {
        $data = Departemen::find($id);

        return response()->json($data);
    }


    public function delete($id)
    {

        Departemen::find($id)->delete();

        return response()->json([
            'success'=>true
        ]);

    }

}