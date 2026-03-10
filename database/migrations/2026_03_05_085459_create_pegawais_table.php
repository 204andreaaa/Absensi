<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pegawais', function (Blueprint $table) {

            $table->id();

            $table->string('nik')->unique();
            $table->string('nama');

            $table->unsignedBigInteger('departemen_id');
            $table->unsignedBigInteger('jadwal_kerja_id');

            $table->string('jabatan')->nullable();

            $table->string('username')->unique();
            $table->string('password');

            $table->enum('role',['admin','pegawai'])->default('pegawai');

            $table->boolean('status')->default(true);

            $table->timestamps();

            // Foreign Key Departemen
            $table->foreign('departemen_id')
                ->references('id')
                ->on('departemens')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            // Foreign Key Jadwal Kerja
            $table->foreign('jadwal_kerja_id')
                ->references('id')
                ->on('jadwal_kerjas')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawais');
    }
};