<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HariLibur;
use Illuminate\Http\Request;

class HariLiburController extends Controller
{
    public function index()
    {
        $hariLiburs = HariLibur::orderByDesc('tanggal')->get();

        return view('admin.hari_libur.index', [
            'hariLiburs' => $hariLiburs
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => ['required', 'date', 'unique:hari_liburs,tanggal'],
            'nama_libur' => ['required', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string']
        ]);

        HariLibur::create($request->only([
            'tanggal',
            'nama_libur',
            'keterangan'
        ]));

        return redirect()
            ->route('admin.hari-libur.index')
            ->with('success', 'Hari libur berhasil ditambahkan.');
    }

    public function destroy(int $id)
    {
        HariLibur::findOrFail($id)->delete();

        return redirect()
            ->route('admin.hari-libur.index')
            ->with('success', 'Hari libur berhasil dihapus.');
    }
}
