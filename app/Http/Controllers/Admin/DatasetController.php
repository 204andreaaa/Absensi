<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DatasetWajah;
use App\Models\Pegawai;

class DatasetController extends Controller
{
    private const MIN_DATASET = 15;

    public function index()
    {
        $datasetPegawai = Pegawai::withCount('dataset_wajahs')
            ->having('dataset_wajahs_count', '>', 0)
            ->orderByDesc('dataset_wajahs_count')
            ->orderBy('nama')
            ->get();

        return view('admin.dataset.index', [
            'datasetPegawai' => $datasetPegawai,
            'minDataset' => self::MIN_DATASET
        ]);
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

    public function destroy($pegawaiId)
    {
        DatasetWajah::where('pegawai_id', $pegawaiId)->delete();

        return redirect()
            ->route('admin.dataset.index')
            ->with('success', 'Dataset pegawai berhasil dihapus.');
    }
}
