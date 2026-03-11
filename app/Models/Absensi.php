<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{

    protected $fillable = [

        'pegawai_id',
        'tanggal',
        'jam_masuk',
        'jam_pulang',
        'status',
        'alasan_telat',
        'alasan_pulang_awal',
        'foto_masuk',
        'foto_pulang'

    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

}
