<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Absensi;
use Illuminate\Support\Facades\Storage;

class ResetAbsensiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'absensi:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menghapus total seluruh data presensi di database dan membersihkan seluruh file foto di storage server.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->confirm('⚠️ Apakah Anda yakin ingin MENGHAPUS TOTAL SELURUH DATA PRESENSI dan SELURUH FILE FOTO di storage server?')) {
            $records = Absensi::all();
            $count = $records->count();

            foreach ($records as $record) {
                $record->delete();
            }

            Storage::disk('public')->deleteDirectory('attendance');

            $this->info("🧹 SUKSES! {$count} data presensi dan seluruh file foto di storage telah berhasil dibersihkan total!");
            return 0;
        }

        $this->warn('Batal melakukan reset.');
        return 0;
    }
}
