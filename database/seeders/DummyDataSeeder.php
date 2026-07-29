<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pegawai;
use App\Models\Absensi;
use App\Models\DatasetWajah;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $pegawais = Pegawai::all();
        
        $status_list = ['tepat_waktu', 'terlambat']; // From controller it uses 'tepat_waktu' and 'terlambat'
        
        // Setup descriptor string dummy (array of 128 floats)
        $dummy_descriptor = json_encode(array_fill(0, 128, 0.5));

        // Create a dummy image in storage
        $dummyImagePath = 'attendance/dummy.jpg';
        if (!Storage::disk('public')->exists($dummyImagePath)) {
            // A simple 1x1 black pixel jpeg in base64
            $base64 = '/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=';
            Storage::disk('public')->put($dummyImagePath, base64_decode($base64));
        }

        foreach ($pegawais as $pegawai) {
            // 1. Create Dataset Wajah if not exist (creating 15 datasets per employee to pass minDataset=15)
            if ($pegawai->dataset_wajahs()->count() < 15) {
                for ($i = 0; $i < 15; $i++) {
                    DatasetWajah::create([
                        'pegawai_id' => $pegawai->id,
                        'descriptor' => $dummy_descriptor
                    ]);
                }
            }

            // 2. Create Absensi for the last 7 days
            for ($i = 7; $i >= 1; $i--) {
                $tanggal = Carbon::now()->subDays($i)->format('Y-m-d');
                
                // Skip if weekend (Saturday and Sunday)
                $isWeekend = Carbon::now()->subDays($i)->isWeekend();
                if ($isWeekend) {
                    continue; // Skip weekend
                }

                $status = $status_list[array_rand($status_list)];
                
                $jam_masuk = '08:00:00';
                $alasan_telat = null;
                if ($status == 'terlambat') {
                    $jam_masuk = '08:'.str_pad(rand(15, 59), 2, '0', STR_PAD_LEFT).':00';
                    $alasan_telat = 'Macet di jalan';
                } else {
                    $jam_masuk = '07:'.str_pad(rand(30, 59), 2, '0', STR_PAD_LEFT).':00';
                }

                $jam_pulang = '17:'.str_pad(rand(0, 30), 2, '0', STR_PAD_LEFT).':00';
                
                // Cek apakah sudah absen hari ini
                $exists = Absensi::where('pegawai_id', $pegawai->id)
                                 ->where('tanggal', $tanggal)
                                 ->exists();

                if (!$exists) {
                    Absensi::create([
                        'pegawai_id' => $pegawai->id,
                        'tanggal' => $tanggal,
                        'jam_masuk' => $jam_masuk,
                        'jam_pulang' => $jam_pulang,
                        'status' => $status,
                        'alasan_telat' => $alasan_telat,
                        'alasan_pulang_awal' => null,
                        'foto_masuk' => $dummyImagePath,
                        'foto_pulang' => $dummyImagePath,
                    ]);
                }
            }
        }
    }
}
