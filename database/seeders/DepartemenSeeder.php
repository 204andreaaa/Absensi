<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Departemen;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DepartemenSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('departemens')->truncate();
        Schema::enableForeignKeyConstraints();

        $departemens = [
            ['nama_departemen' => 'Procrument', 'keterangan' => 'Pengadaan Product'],
            ['nama_departemen' => 'IT & Software', 'keterangan' => 'Divisi Teknologi Informasi'],
            ['nama_departemen' => 'Operational', 'keterangan' => 'Divisi Operasional'],
            ['nama_departemen' => 'HRD', 'keterangan' => 'Human Resource Department'],
            ['nama_departemen' => 'Finance', 'keterangan' => 'Keuangan Perusahaan'],
            ['nama_departemen' => 'Marketing', 'keterangan' => 'Pengiklanan Product'],
        ];

        foreach ($departemens as $dept) {
            Departemen::create($dept);
        }
    }
}