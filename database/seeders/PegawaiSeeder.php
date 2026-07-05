<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PegawaiSeeder extends Seeder
{
    public function run(): void
    {
        $pegawai = [
            [
                'nik' => 'ADM001',
                'nama' => 'Administrator',
                'departemen_id' => 1,
                'jadwal_kerja_id' => 1,
                'jabatan' => 'System Admin',
                'username' => 'admin',
                'password' => Hash::make('123456'),
                'role' => 'admin',
                'status' => 1,
            ],
            [
                'nik' => 'PG001',
                'nama' => 'Andrea',
                'departemen_id' => 1,
                'jadwal_kerja_id' => 1,
                'jabatan' => 'Staff IT',
                'username' => 'andrea',
                'password' => Hash::make('123456'),
                'role' => 'pegawai',
                'status' => 1,
            ],
            [
                'nik' => 'PG002',
                'nama' => 'Andi Pratama',
                'departemen_id' => 2,
                'jadwal_kerja_id' => 1,
                'jabatan' => 'HRD Staff',
                'username' => 'andi',
                'password' => Hash::make('123456'),
                'role' => 'pegawai',
                'status' => 1,
            ],
            [
                'nik' => 'PG003',
                'nama' => 'Siti Rahma',
                'departemen_id' => 3,
                'jadwal_kerja_id' => 2,
                'jabatan' => 'Finance Staff',
                'username' => 'siti',
                'password' => Hash::make('123456'),
                'role' => 'pegawai',
                'status' => 1,
            ],
        ];

        foreach ($pegawai as $data) {
            DB::table('pegawais')->updateOrInsert(
                ['nik' => $data['nik']],
                [
                    ...$data,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}