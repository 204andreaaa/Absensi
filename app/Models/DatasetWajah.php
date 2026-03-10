<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Pegawai;

class DatasetWajah extends Model
{

    protected $table = 'dataset_wajahs';

    protected $fillable = [
        'pegawai_id',
        'descriptor'
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

}
