<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Pegawai extends Authenticatable
{

    use HasFactory;

    protected $table = 'pegawais';

    protected $fillable = [

        'nik',
        'nama',
        'foto',
        'departemen_id',
        'jadwal_kerja_id',
        'jabatan',
        'username',
        'password',
        'role',
        'status'

    ];

    protected $hidden = [

        'password'

    ];

    public function departemen()
    {
        return $this->belongsTo(Departemen::class);
    }

    public function jadwal()
    {
        return $this->belongsTo(JadwalKerja::class,'jadwal_kerja_id');
    }

    // RELASI DATASET WAJAH
    public function dataset_wajahs()
    {
        return $this->hasMany(DatasetWajah::class,'pegawai_id');
    }

    public function absensis()
    {
        return $this->hasMany(Absensi::class, 'pegawai_id');
    }

}
