<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pegawai;
use App\Models\Departemen;
use App\Models\JadwalKerja;
use App\Models\HariLibur;
use App\Models\Absensi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;

class GenerateJulyAttendance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:absensi-juli';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates full July 2026 attendance records with unique clothing variations for Dena (Staff Finance)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("🚀 Memulai proses generasi data absensi sebulan penuh untuk Dena (Staff Finance)...");

        // 1. Path Foto Muka Sumber
        $sourcePhotoPath = "C:\\Users\\itmdu\\.gemini\\antigravity-ide\\brain\\6e031a38-d926-4693-bd1a-6850687157f5\\media__1785480939239.png";
        
        if (!File::exists($sourcePhotoPath)) {
            $this->error("❌ File foto sumber tidak ditemukan di: {$sourcePhotoPath}");
            return 1;
        }

        // 2. Departemen & Jadwal Kerja
        $departemen = Departemen::firstOrCreate(
            ['nama_departemen' => 'Finance'],
            ['keterangan' => 'Divisi Keuangan & Administrasi']
        );

        $jadwal = JadwalKerja::firstOrCreate(
            ['nama_shift' => 'Shift Reguler Finance'],
            [
                'jam_masuk' => '08:00:00',
                'jam_pulang' => '17:00:00',
                'toleransi_telat' => 15
            ]
        );

        // 3. Pegawai Dena
        $profileDir = public_path('storage/pegawai');
        if (!File::isDirectory($profileDir)) {
            File::makeDirectory($profileDir, 0755, true, true);
        }
        $profilePhotoRel = 'pegawai/dena.png';
        File::copy($sourcePhotoPath, public_path('storage/' . $profilePhotoRel));

        $pegawai = Pegawai::updateOrCreate(
            ['nama' => 'Dena'],
            [
                'nik' => '202607310001',
                'departemen_id' => $departemen->id,
                'jadwal_kerja_id' => $jadwal->id,
                'jabatan' => 'Staff Finance',
                'username' => 'dena',
                'password' => Hash::make('password'),
                'role' => 'pegawai',
                'status' => 1,
                'foto' => $profilePhotoRel
            ]
        );

        $this->info("✅ Pegawai: {$pegawai->nama} ({$pegawai->jabatan}) - Departemen {$departemen->nama_departemen} berhasil disiapkan.");

        // 4. Color Outfits Presets (23 Pasang Warna Baju & Hijab Unik)
        $outfitPresets = [
            ['h' => 0,   's' => 20,  'v' => 15],  // Burgundy / Maroon
            ['h' => 210, 's' => 45,  'v' => 10],  // Navy Blue
            ['h' => 160, 's' => 30,  'v' => 12],  // Emerald Green
            ['h' => 350, 's' => 25,  'v' => 20],  // Dusty Rose / Pink
            ['h' => 25,  's' => 40,  'v' => 18],  // Terracotta / Camel
            ['h' => 190, 's' => 35,  'v' => 15],  // Dark Teal
            ['h' => 270, 's' => 25,  'v' => 15],  // Lavender Purple
            ['h' => 40,  's' => 50,  'v' => 22],  // Mustard Yellow
            ['h' => 120, 's' => 20,  'v' => 10],  // Olive / Sage Green
            ['h' => 0,   's' => 0,   'v' => 35],  // Light Grey
            ['h' => 210, 's' => 20,  'v' => -20], // Charcoal Dark Grey
            ['h' => 30,  's' => 30,  'v' => 30],  // Warm Beige / Cream
            ['h' => 220, 's' => 60,  'v' => 15],  // Royal Blue
            ['h' => 340, 's' => 40,  'v' => -10], // Dark Magenta / Plum
            ['h' => 180, 's' => 20,  'v' => 25],  // Soft Cyan / Pastel Blue
            ['h' => 15,  's' => 45,  'v' => 10],  // Rust Orange
            ['h' => 260, 's' => 35,  'v' => -15], // Deep Violet
            ['h' => 80,  's' => 25,  'v' => 15],  // Moss Green
            ['h' => 45,  's' => 25,  'v' => -10], // Muted Khaki
            ['h' => 0,   's' => 0,   'v' => -30], // Deep Black Formal
            ['h' => 200, 's' => 15,  'v' => 30],  // Soft Sky Blue
            ['h' => 330, 's' => 30,  'v' => 25],  // Soft Peach
            ['h' => 280, 's' => 20,  'v' => 10],  // Mauve / Violet Grey
        ];

        // 5. Generate Attendance Records for July 2026 (1 - 31 July)
        $startDate = Carbon::createFromDate(2026, 7, 1);
        $daysInJuly = $startDate->daysInMonth;
        $today = Carbon::today();
        $generatedCount = 0;

        $hariLiburMap = HariLibur::whereYear('tanggal', 2026)
            ->whereMonth('tanggal', 7)
            ->pluck('nama_libur', 'tanggal')
            ->toArray();

        $attendanceBaseDir = public_path('storage/attendance');
        if (!File::isDirectory($attendanceBaseDir)) {
            File::makeDirectory($attendanceBaseDir, 0755, true, true);
        }

        $bar = $this->output->createProgressBar($daysInJuly);
        $bar->start();

        for ($day = 1; $day <= $daysInJuly; $day++) {
            $currentDate = Carbon::createFromDate(2026, 7, $day);
            $dateStr = $currentDate->toDateString();
            $isWeekend = $currentDate->isWeekend();
            $isHoliday = isset($hariLiburMap[$dateStr]);

            // Skip jika libur / weekend atau tanggal di masa depan
            if ($isWeekend || $isHoliday || $currentDate->gt($today)) {
                $bar->advance();
                continue;
            }

            // Folder khusus per tanggal
            $dayDir = $attendanceBaseDir . '/' . $dateStr;
            if (!File::isDirectory($dayDir)) {
                File::makeDirectory($dayDir, 0755, true, true);
            }

            // Preset Warna Baju Hari Ini
            $preset = $outfitPresets[($day - 1) % count($outfitPresets)];

            // Generate Foto Masuk & Pulang Unik
            $fotoMasukRel = "attendance/{$dateStr}/masuk.jpg";
            $fotoPulangRel = "attendance/{$dateStr}/pulang.jpg";

            $this->createVariationImage(
                $sourcePhotoPath,
                public_path('storage/' . $fotoMasukRel),
                $preset,
                $dateStr . ' ' . sprintf('%02d:%02d:%02d', 7, rand(48, 59), rand(10, 59))
            );

            $this->createVariationImage(
                $sourcePhotoPath,
                public_path('storage/' . $fotoPulangRel),
                $preset,
                $dateStr . ' ' . sprintf('%02d:%02d:%02d', 17, rand(2, 20), rand(10, 59)),
                true // Slight angle shift for checkout photo
            );

            // Tentukan Jam Masuk & Status
            $isLate = ($day == 9 || $day == 21); // Misal 2 hari terlambat sebagai contoh realistis
            if ($isLate) {
                $jamMasuk = sprintf('08:%02d:%02d', rand(16, 28), rand(10, 59));
                $status = 'terlambat';
                $alasanTelat = ($day == 9) ? 'Macet parah di jalan tol' : 'Kendaraan mogok saat berangkat';
            } else {
                $jamMasuk = sprintf('07:%02d:%02d', rand(45, 59), rand(10, 59));
                $status = 'tepat_waktu';
                $alasanTelat = null;
            }

            $jamPulang = sprintf('17:%02d:%02d', rand(2, 22), rand(10, 59));

            Absensi::updateOrCreate(
                [
                    'pegawai_id' => $pegawai->id,
                    'tanggal' => $dateStr
                ],
                [
                    'jam_masuk' => $jamMasuk,
                    'jam_pulang' => $jamPulang,
                    'status' => $status,
                    'alasan_telat' => $alasanTelat,
                    'foto_masuk' => $fotoMasukRel,
                    'foto_pulang' => $fotoPulangRel
                ]
            );

            $generatedCount++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("🎉 Selesai! Berhasil meng-generate {$generatedCount} data absensi sebulan penuh bulan Juli 2026 untuk Dena dengan pakaian & warna yang bervariasi setiap hari!");

        return 0;
    }

    /**
     * Memproses gambar dengan variasi warna pakaian, filter brightness, zoom, dan watermark timestamp
     */
    private function createVariationImage(string $sourcePath, string $targetPath, array $preset, string $timestampText, bool $isCheckout = false)
    {
        $data = @file_get_contents($sourcePath);
        if ($data === false) {
            File::copy($sourcePath, $targetPath);
            return;
        }

        $src = @imagecreatefromstring($data);
        if (!$src) {
            File::copy($sourcePath, $targetPath);
            return;
        }

        $origW = imagesx($src);
        $origH = imagesy($src);

        // Dynamic Zoom / Crop Variations (Camera Angle Differences)
        $zoomFactor = 1.0 + (rand(-6, 6) / 100);
        $cropW = (int) ($origW * $zoomFactor);
        $cropH = (int) ($origH * $zoomFactor);
        $offsetX = rand(0, max(1, $origW - $cropW));
        $offsetY = rand(0, max(1, $origH - $cropH));

        $dst = imagecreatetruecolor($origW, $origH);

        // Smooth resampling with zoom & shift
        imagecopyresampled(
            $dst, $src,
            0, 0,
            $offsetX, $offsetY,
            $origW, $origH,
            $cropW, $cropH
        );

        // Apply Color Tint for Clothing & Background Variation
        $redShift = rand(-20, 35);
        $greenShift = rand(-20, 35);
        $blueShift = rand(-20, 35);
        $brightness = rand(-10, 10);
        $contrast = rand(-6, 6);

        imagefilter($dst, IMG_FILTER_COLORIZE, $redShift, $greenShift, $blueShift);
        imagefilter($dst, IMG_FILTER_BRIGHTNESS, $brightness);
        imagefilter($dst, IMG_FILTER_CONTRAST, $contrast);

        // Add Attendance Watermark Badge Overlay at Bottom Left
        $blackTrans = imagecolorallocatealpha($dst, 15, 23, 42, 35);
        $whiteText = imagecolorallocate($dst, 255, 255, 255);
        $greenBadge = imagecolorallocate($dst, 34, 197, 94);

        // Draw watermark box
        imagefilledrectangle($dst, 10, $origH - 48, $origW - 10, $origH - 8, $blackTrans);
        imagefilledrectangle($dst, 14, $origH - 42, 20, $origH - 14, $greenBadge);

        imagestring($dst, 3, 26, $origH - 43, "PRESENSI " . ($isCheckout ? 'PULANG' : 'MASUK') . " VERIFIED", $whiteText);
        imagestring($dst, 2, 26, $origH - 26, $timestampText . " WIB", $whiteText);

        imagejpeg($dst, $targetPath, 92);

        imagedestroy($src);
        imagedestroy($dst);
    }
}
