<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pegawai;
use App\Models\Departemen;
use App\Models\JadwalKerja;
use Illuminate\Support\Facades\Hash;

class PegawaiSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan Departemen & Jadwal Kerja Siap
        $deptIT = Departemen::firstOrCreate(['nama_departemen' => 'IT & Software'], ['keterangan' => 'Divisi Teknologi Informasi']);
        $deptHRD = Departemen::firstOrCreate(['nama_departemen' => 'HRD'], ['keterangan' => 'Human Resource Department']);
        $deptFinance = Departemen::firstOrCreate(['nama_departemen' => 'Finance'], ['keterangan' => 'Keuangan Perusahaan']);
        $deptMarketing = Departemen::firstOrCreate(['nama_departemen' => 'Marketing'], ['keterangan' => 'Pengiklanan Product']);
        $deptProcurement = Departemen::firstOrCreate(['nama_departemen' => 'Procrument'], ['keterangan' => 'Pengadaan Product']);
        $deptOps = Departemen::firstOrCreate(['nama_departemen' => 'Operational'], ['keterangan' => 'Divisi Operasional']);

        $jadwalPagi = JadwalKerja::firstOrCreate(
            ['nama_shift' => 'Shift Pagi'],
            ['jam_masuk' => '08:00:00', 'jam_pulang' => '17:00:00', 'toleransi_telat' => 10]
        );

        $jadwalSiang = JadwalKerja::firstOrCreate(
            ['nama_shift' => 'Shift Siang'],
            ['jam_masuk' => '13:00:00', 'jam_pulang' => '21:00:00', 'toleransi_telat' => 10]
        );

        $pegawaiList = [
            [
                'nik' => 'ADM001',
                'nama' => 'Administrator',
                'departemen_id' => $deptIT->id,
                'jadwal_kerja_id' => $jadwalPagi->id,
                'jabatan' => 'System Admin',
                'username' => 'admin',
                'password' => Hash::make('123456'),
                'role' => 'admin',
                'status' => 1,
            ],
            [
                'nik' => '2026080002',
                'nama' => 'Agung Mubarok',
                'departemen_id' => $deptIT->id,
                'jadwal_kerja_id' => $jadwalPagi->id,
                'jabatan' => 'Staff IT',
                'username' => 'agung',
                'password' => Hash::make('123456'),
                'role' => 'pegawai',
                'status' => 1,
            ],
            [
                'nik' => '2026080003',
                'nama' => 'Andrea',
                'departemen_id' => $deptIT->id,
                'jadwal_kerja_id' => $jadwalPagi->id,
                'jabatan' => 'Staff IT',
                'username' => 'andrea',
                'password' => Hash::make('123456'),
                'role' => 'pegawai',
                'status' => 1,
            ],
            [
                'nik' => '2026080004',
                'nama' => 'Chika',
                'departemen_id' => $deptProcurement->id,
                'jadwal_kerja_id' => $jadwalPagi->id,
                'jabatan' => 'Staff Procurement',
                'username' => 'chika',
                'password' => Hash::make('123456'),
                'role' => 'pegawai',
                'status' => 1,
            ],
            [
                'nik' => '2026080005',
                'nama' => 'Intan',
                'departemen_id' => $deptFinance->id,
                'jadwal_kerja_id' => $jadwalPagi->id,
                'jabatan' => 'Staff Finance',
                'username' => 'intan',
                'password' => Hash::make('123456'),
                'role' => 'pegawai',
                'status' => 1,
            ],
            [
                'nik' => '2026080006',
                'nama' => 'Imad',
                'departemen_id' => $deptHRD->id,
                'jadwal_kerja_id' => $jadwalPagi->id,
                'jabatan' => 'Staff HRD',
                'username' => 'imad',
                'password' => Hash::make('123456'),
                'role' => 'pegawai',
                'status' => 1,
            ],
            [
                'nik' => '2026080007',
                'nama' => 'Irham Badruzaman',
                'departemen_id' => $deptIT->id,
                'jadwal_kerja_id' => $jadwalPagi->id,
                'jabatan' => 'Staff IT',
                'username' => 'irham',
                'password' => Hash::make('123456'),
                'role' => 'pegawai',
                'status' => 1,
            ],
            [
                'nik' => '2026080008',
                'nama' => 'Ditiya Pratama',
                'departemen_id' => $deptFinance->id,
                'jadwal_kerja_id' => $jadwalPagi->id,
                'jabatan' => 'Staff Finance',
                'username' => 'ditiya',
                'password' => Hash::make('123456'),
                'role' => 'pegawai',
                'status' => 1,
            ],
            [
                'nik' => '2026080009',
                'nama' => 'Alkhar Fikri',
                'departemen_id' => $deptMarketing->id,
                'jadwal_kerja_id' => $jadwalPagi->id,
                'jabatan' => 'Staff Marketing',
                'username' => 'alkhar',
                'password' => Hash::make('123456'),
                'role' => 'pegawai',
                'status' => 1,
            ],
            [
                'nik' => '202607310001',
                'nama' => 'Dena',
                'departemen_id' => $deptFinance->id,
                'jadwal_kerja_id' => $jadwalPagi->id,
                'jabatan' => 'Staff Finance',
                'username' => 'dena',
                'password' => Hash::make('123456'),
                'role' => 'pegawai',
                'status' => 1,
            ],
        ];

        $baseDescriptor = [
            -0.081234, 0.124567, 0.054321, -0.098765, -0.112233, 0.044556, -0.033445, -0.077889,
            0.133221, -0.066554, 0.144332, -0.022110, -0.155443, -0.088776, -0.011223, 0.099887,
            -0.122334, -0.055667, -0.077889, -0.044556, 0.033445, 0.011223, 0.088776, -0.066554,
            0.022110, -0.133221, -0.099887, -0.033445, 0.055667, -0.088776, -0.022110, -0.077889,
            -0.112233, -0.044556, 0.066554, 0.011223, -0.055667, 0.033445, 0.122334, 0.077889,
            -0.088776, 0.099887, 0.022110, 0.144332, -0.011223, 0.055667, 0.033445, -0.066554,
            -0.133221, -0.077889, 0.088776, 0.112233, 0.044556, 0.066554, -0.022110, -0.055667,
            -0.099887, 0.011223, 0.122334, -0.044556, 0.077889, -0.033445, -0.088776, 0.066554,
            0.055667, -0.122334, -0.066554, 0.099887, 0.011223, -0.088776, 0.033445, -0.077889,
            -0.044556, 0.133221, 0.077889, -0.022110, -0.112233, 0.066554, -0.055667, 0.022110,
            0.088776, -0.099887, -0.011223, 0.122334, 0.044556, -0.077889, 0.033445, 0.066554,
            -0.055667, 0.022110, -0.088776, 0.099887, -0.033445, -0.122334, 0.077889, -0.011223,
            -0.066554, 0.055667, 0.022110, -0.088776, 0.112233, -0.044556, 0.066554, -0.099887,
            0.011223, -0.077889, 0.033445, 0.122334, -0.055667, 0.088776, -0.022110, -0.066554,
            0.044556, 0.077889, -0.099887, 0.011223, -0.122334, 0.055667, -0.033445, 0.088776,
            -0.077889, 0.022110, 0.066554, -0.055667, -0.011223, 0.099887, -0.044556, 0.033445
        ];

        foreach ($pegawaiList as $data) {
            $pegawai = Pegawai::updateOrCreate(
                ['username' => $data['username']],
                $data
            );

            // Buat 16 Dataset Wajah otomatis jika belum ada / < 15
            if ($pegawai->role === 'pegawai' && $pegawai->dataset_wajahs()->count() < 15) {
                \App\Models\DatasetWajah::where('pegawai_id', $pegawai->id)->delete();
                for ($k = 1; $k <= 16; $k++) {
                    $varied = [];
                    foreach ($baseDescriptor as $val) {
                        $varied[] = (float) sprintf('%.6f', $val + (mt_rand(-15, 15) / 10000));
                    }
                    \App\Models\DatasetWajah::create([
                        'pegawai_id' => $pegawai->id,
                        'descriptor' => json_encode($varied)
                    ]);
                }
            }
        }
    }
}