<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Absensi extends Model
{
    use HasFactory;

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

    protected static function booted()
    {
        static::deleting(function ($absensi) {
            if ($absensi->foto_masuk && Storage::disk('public')->exists($absensi->foto_masuk)) {
                Storage::disk('public')->delete($absensi->foto_masuk);
            }
            if ($absensi->foto_pulang && Storage::disk('public')->exists($absensi->foto_pulang)) {
                Storage::disk('public')->delete($absensi->foto_pulang);
            }
        });
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }
}
