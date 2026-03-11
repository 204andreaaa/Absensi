<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->string('foto_masuk')->nullable()->after('alasan_pulang_awal');
            $table->string('foto_pulang')->nullable()->after('foto_masuk');
        });
    }

    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropColumn(['foto_masuk', 'foto_pulang']);
        });
    }
};
