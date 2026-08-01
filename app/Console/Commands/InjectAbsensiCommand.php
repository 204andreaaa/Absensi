<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pegawai;
use App\Models\Departemen;
use App\Models\JadwalKerja;
use App\Models\DatasetWajah;
use App\Models\HariLibur;
use App\Models\Absensi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;

class InjectAbsensiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'absensi:inject {--bulan=7} {--tahun=2026} {--reset-attendance}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Otomatis scan folder public/images/injekFace/ untuk mendaftarkan pegawai, dataset wajah, dan presensi foto.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $bulan = (int) $this->option('bulan');
        $tahun = (int) $this->option('tahun');
        $resetAttendance = $this->option('reset-attendance');

        $injekBaseDir = public_path('images/injekFace');

        if (!File::isDirectory($injekBaseDir)) {
            $this->error("❌ Folder public/images/injekFace tidak ditemukan!");
            return 1;
        }

        $subfolders = File::directories($injekBaseDir);
        if (empty($subfolders)) {
            $this->warn("⚠️ Tidak ada folder pegawai di public/images/injekFace/");
            return 0;
        }

        $this->info("🚀 Scanning " . count($subfolders) . " folder pegawai di public/images/injekFace/...");

        if ($resetAttendance) {
            Absensi::truncate();
            $this->info("🧹 Data absensi lama telah dibersihkan.");
        }

        // Shift & Departemen Default
        $defaultDept = Departemen::firstOrCreate(['nama_departemen' => 'IT & Software'], ['keterangan' => 'Divisi Teknologi Informasi']);
        $defaultJadwal = JadwalKerja::firstOrCreate(['nama_shift' => 'Shift Pagi'], ['jam_masuk' => '08:00:00', 'jam_pulang' => '17:00:00', 'toleransi_telat' => 10]);

        $startDate = Carbon::createFromDate($tahun, $bulan, 1);
        $hariLiburMap = HariLibur::whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan)->pluck('nama_libur', 'tanggal')->toArray();
        $attendanceBaseDir = public_path('storage/attendance');
        if (!File::isDirectory($attendanceBaseDir)) {
            File::makeDirectory($attendanceBaseDir, 0755, true, true);
        }

        // Pre-fetch base descriptor template
        $baseDescriptor = null;
        $existingDataset = DatasetWajah::first();
        if ($existingDataset && !empty($existingDataset->descriptor)) {
            $baseDescriptor = json_decode($existingDataset->descriptor, true);
        }
        if (!$baseDescriptor || count($baseDescriptor) < 128) {
            $baseDescriptor = [];
            for ($i = 0; $i < 128; $i++) {
                $baseDescriptor[] = (float) sprintf('%.6f', (mt_rand(-200, 200) / 1000));
            }
        }

        foreach ($subfolders as $folderPath) {
            $folderName = basename($folderPath);
            $namaClean = ucfirst(strtolower($folderName));
            $username = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $folderName));

            $this->newLine();
            $this->info("📂 Memproses folder: {$folderName} (Pegawai: {$namaClean})");

            // Scan foto-foto di folder
            $files = File::files($folderPath);
            $masukFiles = [];
            $pulangFiles = [];

            foreach ($files as $file) {
                $fname = strtolower($file->getFilename());
                if (str_contains($fname, 'masuk')) {
                    $masukFiles[] = $file->getPathname();
                } elseif (str_contains($fname, 'pulang')) {
                    $pulangFiles[] = $file->getPathname();
                }
            }

            sort($masukFiles);
            sort($pulangFiles);

            $profileSrc = !empty($masukFiles) ? $masukFiles[0] : (!empty($files) ? $files[0]->getPathname() : null);
            $relProfilePath = "pegawai/{$username}.jpg";

            if ($profileSrc && File::exists($profileSrc)) {
                $targetProfileDir = public_path('storage/pegawai');
                if (!File::isDirectory($targetProfileDir)) {
                    File::makeDirectory($targetProfileDir, 0755, true, true);
                }
                File::copy($profileSrc, public_path('storage/' . $relProfilePath));
            }

            // Cari atau Buat Pegawai
            $pegawai = Pegawai::where('username', $username)->orWhere('nama', 'like', $namaClean)->first();
            if (!$pegawai) {
                $nikSeq = date('Ym') . sprintf('%04d', Pegawai::count() + 1);
                $pegawai = Pegawai::create([
                    'nik' => $nikSeq,
                    'nama' => $namaClean,
                    'departemen_id' => $defaultDept->id,
                    'jadwal_kerja_id' => $defaultJadwal->id,
                    'jabatan' => 'Staff ' . $namaClean,
                    'username' => $username,
                    'password' => Hash::make('123456'),
                    'role' => 'pegawai',
                    'status' => 1,
                    'foto' => $relProfilePath
                ]);
                $this->info("  ➕ Akun baru dibuat: {$pegawai->nama} (Username: {$username})");
            } else {
                $pegawai->update(['foto' => $relProfilePath]);
                $this->info("  ℹ️ Akun terdaftar ditemukan: {$pegawai->nama}");
            }

            // Buat Dataset Wajah jika < 15
            if ($pegawai->dataset_wajahs()->count() < 15) {
                DatasetWajah::where('pegawai_id', $pegawai->id)->delete();
                for ($k = 1; $k <= 16; $k++) {
                    $varied = [];
                    foreach ($baseDescriptor as $val) {
                        $varied[] = (float) sprintf('%.6f', $val + (mt_rand(-15, 15) / 10000));
                    }
                    DatasetWajah::create(['pegawai_id' => $pegawai->id, 'descriptor' => json_encode($varied)]);
                }
                $this->info("  ✨ 16 Dataset Wajah otomatis disiapkan untuk {$pegawai->nama}.");
            }

            // Pasangkan Foto Masuk & Pulang
            $pairCount = max(count($masukFiles), count($pulangFiles));
            if ($pairCount === 0) {
                $this->warn("  ⚠️ Tidak ada foto masuk/pulang di folder {$folderName}");
                continue;
            }

            $currentDate = $startDate->copy();
            $injectedAbsensi = 0;

            for ($p = 0; $p < $pairCount; $p++) {
                // Cari hari kerja KOSONG (skip weekend, libur, & tanggal yang sudah terisi presensi)
                while (
                    $currentDate->isWeekend() ||
                    isset($hariLiburMap[$currentDate->toDateString()]) ||
                    Absensi::where('pegawai_id', $pegawai->id)->whereDate('tanggal', $currentDate->toDateString())->exists()
                ) {
                    $currentDate->addDay();
                    if ($currentDate->month != $bulan) {
                        break;
                    }
                }
                if ($currentDate->month != $bulan) {
                    break;
                }

                $dateStr = $currentDate->toDateString();
                $dayDir = $attendanceBaseDir . '/' . $dateStr;
                if (!File::isDirectory($dayDir)) {
                    File::makeDirectory($dayDir, 0755, true, true);
                }

                $fotoMasukRel = "attendance/{$dateStr}/masuk_{$pegawai->id}.jpg";
                $fotoPulangRel = "attendance/{$dateStr}/pulang_{$pegawai->id}.jpg";

                $srcMasuk = $masukFiles[$p] ?? ($masukFiles[0] ?? $profileSrc);
                $srcPulang = $pulangFiles[$p] ?? ($pulangFiles[0] ?? $profileSrc);

                if ($srcMasuk && File::exists($srcMasuk)) {
                    File::copy($srcMasuk, public_path('storage/' . $fotoMasukRel));
                }
                if ($srcPulang && File::exists($srcPulang)) {
                    File::copy($srcPulang, public_path('storage/' . $fotoPulangRel));
                }

                $alasanTelatList = [
                    'Macet parah di jalan utama',
                    'Ban kendaraan bocor saat berangkat',
                    'Kondisi cuaca hujan deras & banjir',
                    'Kendaraan mogok di jalan',
                    'Antrean panjang di perlintasan kereta'
                ];

                $alasanPulangAwalList = [
                    'Keperluan keluarga mendesak',
                    'Kondisi badan kurang fit / sakit',
                    'Izin mengurus dokumen ke dinas',
                    'Menjemput anak sekolah emergency',
                    'Ada perbaikan instalasi listrik di rumah'
                ];

                $status = 'tepat_waktu';
                $alasanTelat = null;
                $alasanPulangAwal = null;
                $jamMasuk = sprintf('07:%02d:%02d', rand(45, 58), rand(10, 59));
                $jamPulang = sprintf('17:%02d:%02d', rand(2, 20), rand(10, 59));

                $chance = rand(1, 100);
                if ($chance <= 12) { // ~12% Peluang Terlambat
                    $jamMasuk = sprintf('08:%02d:%02d', rand(16, 35), rand(10, 59));
                    $status = 'terlambat';
                    $alasanTelat = $alasanTelatList[array_rand($alasanTelatList)];
                } elseif ($chance >= 88) { // ~12% Peluang Pulang Cepat
                    $jamPulang = sprintf('15:%02d:%02d', rand(30, 55), rand(10, 59));
                    $alasanPulangAwal = $alasanPulangAwalList[array_rand($alasanPulangAwalList)];
                }

                Absensi::updateOrCreate(
                    ['pegawai_id' => $pegawai->id, 'tanggal' => $dateStr],
                    [
                        'jam_masuk' => $jamMasuk,
                        'jam_pulang' => $jamPulang,
                        'status' => $status,
                        'alasan_telat' => $alasanTelat,
                        'alasan_pulang_awal' => $alasanPulangAwal,
                        'foto_masuk' => $fotoMasukRel,
                        'foto_pulang' => $fotoPulangRel
                    ]
                );

                $injectedAbsensi++;
                $currentDate->addDay();
            }

            $this->info("  ✅ Berhasil menginjek {$injectedAbsensi} hari presensi untuk {$pegawai->nama}!");
        }

        $this->newLine();
        $this->info("🎉 AUTO INJECT SELESAI 100%! Semua folder di injekFace telah terproses otomatis.");
        return 0;
    }
}
