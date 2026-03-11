<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->text('alasan_telat')->nullable()->after('status');
            $table->text('alasan_pulang_awal')->nullable()->after('alasan_telat');
        });
    }

    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropColumn(['alasan_telat', 'alasan_pulang_awal']);
        });
    }
};
