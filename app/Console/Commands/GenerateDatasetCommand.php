<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pegawai;
use App\Models\DatasetWajah;

class GenerateDatasetCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dataset:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Otomatis membuat 16 sampel dataset wajah untuk seluruh pegawai yang belum memiliki dataset wajah.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
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

        $pegawais = Pegawai::where('role', 'pegawai')->get();
        $generatedCount = 0;

        foreach ($pegawais as $pegawai) {
            if ($pegawai->dataset_wajahs()->count() < 15) {
                DatasetWajah::where('pegawai_id', $pegawai->id)->delete();
                for ($k = 1; $k <= 16; $k++) {
                    $varied = [];
                    foreach ($baseDescriptor as $val) {
                        $varied[] = (float) sprintf('%.6f', $val + (mt_rand(-15, 15) / 10000));
                    }
                    DatasetWajah::create([
                        'pegawai_id' => $pegawai->id,
                        'descriptor' => json_encode($varied)
                    ]);
                }
                $this->info("✨ 16 Dataset Wajah berhasil dibuat untuk: {$pegawai->nama} (NIK: {$pegawai->nik})");
                $generatedCount++;
            }
        }

        $this->info("🎉 Selesai! Total {$generatedCount} pegawai berhasil disiapkan dataset wajahnya (Siap Absensi Kamera).");
        return 0;
    }
}
