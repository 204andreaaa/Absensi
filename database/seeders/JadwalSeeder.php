<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JadwalKerja;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class JadwalSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('jadwal_kerjas')->truncate();
        Schema::enableForeignKeyConstraints();

        $shifts = [
            [
                'nama_shift' => 'Shift Pagi',
                'jam_masuk' => '08:00:00',
                'jam_pulang' => '17:00:00',
                'toleransi_telat' => 10,
            ],
            [
                'nama_shift' => 'Shift Siang',
                'jam_masuk' => '13:00:00',
                'jam_pulang' => '21:00:00',
                'toleransi_telat' => 10,
            ]
        ];

        foreach ($shifts as $shift) {
            JadwalKerja::create($shift);
        }
    }
}