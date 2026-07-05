<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;

use App\Http\Controllers\Admin\DepartemenController;
use App\Http\Controllers\Admin\JadwalKerjaController;
use App\Http\Controllers\Admin\PegawaiController;
use App\Http\Controllers\Admin\DatasetController;
use App\Http\Controllers\Admin\AbsensiController;
use App\Http\Controllers\Admin\HariLiburController;

use App\Http\Controllers\Pegawai\DatasetPegawaiController;
use App\Http\Controllers\Pegawai\AbsensiPegawaiController;


/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/login',[AuthController::class,'loginForm'])->name('login');
Route::post('/login',[AuthController::class,'login'])->name('login.process');
Route::post('/logout',[AuthController::class,'logout'])->name('logout');


/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
->middleware(['auth','role:admin'])
->name('admin.')
->group(function(){

    Route::get('/dashboard', function () {
        return view('admin.index');
    })->name('dashboard');


    /* DEPARTEMEN */

    Route::get('departemen',[DepartemenController::class,'index'])->name('departemen.index');
    Route::get('departemen/data',[DepartemenController::class,'data'])->name('departemen.data');
    Route::post('departemen/store',[DepartemenController::class,'store'])->name('departemen.store');
    Route::get('departemen/edit/{id}',[DepartemenController::class,'edit']);
    Route::delete('departemen/delete/{id}',[DepartemenController::class,'delete']);


    /* JADWAL KERJA */

    Route::get('jadwal',[JadwalKerjaController::class,'index'])->name('jadwal.index');
    Route::get('jadwal/data',[JadwalKerjaController::class,'data'])->name('jadwal.data');
    Route::post('jadwal/store',[JadwalKerjaController::class,'store'])->name('jadwal.store');
    Route::get('jadwal/edit/{id}',[JadwalKerjaController::class,'edit']);
    Route::delete('jadwal/delete/{id}',[JadwalKerjaController::class,'delete']);

    Route::get('hari-libur',[HariLiburController::class,'index'])->name('hari-libur.index');
    Route::post('hari-libur/store',[HariLiburController::class,'store'])->name('hari-libur.store');
    Route::delete('hari-libur/delete/{id}',[HariLiburController::class,'destroy'])->name('hari-libur.delete');


    /* PEGAWAI */

    Route::get('pegawai',[PegawaiController::class,'index'])->name('pegawai.index');
    Route::get('pegawai/data',[PegawaiController::class,'data'])->name('pegawai.data');
    Route::post('pegawai/store',[PegawaiController::class,'store'])->name('pegawai.store');
    Route::get('pegawai/edit/{id}',[PegawaiController::class,'edit']);
    Route::delete('pegawai/delete/{id}',[PegawaiController::class,'delete']);


    /* DATASET WAJAH */

    Route::get('dataset',[DatasetController::class,'index'])->name('dataset.index');
    Route::post('dataset/store',[DatasetController::class,'store'])->name('dataset.store');
    Route::get('dataset/load',[DatasetController::class,'load'])->name('dataset.load');
    Route::delete('dataset/delete/{pegawaiId}',[DatasetController::class,'destroy'])->name('dataset.delete');


    /* ABSENSI */

    Route::get('absensi',[AbsensiController::class,'index'])->name('absensi.index');
    Route::post('absensi/store',[AbsensiController::class,'store'])->name('absensi.store');


    /* LAPORAN */

    Route::get('laporan', [AbsensiController::class,'laporan'])->name('laporan.index');
    Route::get('laporan/export-excel', [AbsensiController::class,'exportExcel'])->name('laporan.export-excel');
    Route::get('laporan/tepat-waktu', [AbsensiController::class,'laporanTepatWaktu'])->name('laporan.tepat-waktu');
    Route::get('laporan/tepat-waktu/export-excel', [AbsensiController::class,'exportExcelTepatWaktu'])->name('laporan.tepat-waktu.export-excel');
    Route::get('laporan/terlambat', [AbsensiController::class,'laporanTerlambat'])->name('laporan.terlambat');
    Route::get('laporan/terlambat/export-excel', [AbsensiController::class,'exportExcelTerlambat'])->name('laporan.terlambat.export-excel');
    Route::get('laporan/pulang-cepat', [AbsensiController::class,'laporanPulangCepat'])->name('laporan.pulang-cepat');
    Route::get('laporan/pulang-cepat/export-excel', [AbsensiController::class,'exportExcelPulangCepat'])->name('laporan.pulang-cepat.export-excel');
    Route::get('laporan/rekap-bulanan', [AbsensiController::class,'laporanRekapBulanan'])->name('laporan.rekap-bulanan');
    Route::get('laporan/rekap-bulanan/export-excel', [AbsensiController::class,'exportExcelRekapBulanan'])->name('laporan.rekap-bulanan.export-excel');

});


/*
|--------------------------------------------------------------------------
| PEGAWAI ROUTES
|--------------------------------------------------------------------------
*/

Route::prefix('pegawai')
    ->middleware(['auth','role:pegawai'])
    ->name('pegawai.')
    ->group(function(){

    Route::get('/dataset',[DatasetPegawaiController::class,'index'])->name('dataset');
    Route::post('/dataset/store',[DatasetPegawaiController::class,'store'])->name('dataset.store');
    Route::get('/dataset/load',[DatasetPegawaiController::class,'load'])->name('dataset.load');

    Route::middleware('pegawai.dataset.completed')->group(function () {
        Route::get('/dashboard',[AbsensiPegawaiController::class,'dashboard'])->name('dashboard');
        Route::get('/absensi',[AbsensiPegawaiController::class,'index'])->name('absensi');
        Route::post('/absensi/store',[AbsensiPegawaiController::class,'store'])->name('absensi.store');
        Route::get('/riwayat-absensi',[AbsensiPegawaiController::class,'riwayat'])->name('riwayat');
    });

});
