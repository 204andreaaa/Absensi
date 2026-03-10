<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JadwalSeeder extends Seeder
{
    public function run(): void
    {

        DB::table('jadwal_kerjas')->insert([

            [
                'nama_shift' => 'Shift Pagi',
                'jam_masuk' => '08:00:00',
                'jam_pulang' => '17:00:00',
                'toleransi_telat' => 10,
                'created_at' => now(),
                'updated_at' => now()
            ],

            [
                'nama_shift' => 'Shift Siang',
                'jam_masuk' => '13:00:00',
                'jam_pulang' => '21:00:00',
                'toleransi_telat' => 10,
                'created_at' => now(),
                'updated_at' => now()
            ],

            [
                'nama_shift' => 'Shift Malam',
                'jam_masuk' => '21:00:00',
                'jam_pulang' => '05:00:00',
                'toleransi_telat' => 15,
                'created_at' => now(),
                'updated_at' => now()
            ]

        ]);

    }
}