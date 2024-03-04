<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::get('/', function () {
    return redirect('/home');
})->middleware('auth');

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::post('/home/data', [App\Http\Controllers\HomeController::class, 'data'])->name('home.data');

    Route::get('/absensi', [App\Http\Controllers\AbsensiController::class, 'index'])->name('absensi');
    Route::get('/absensi/report/{periode?}', [App\Http\Controllers\AbsensiController::class, 'report'])->name('absensi.report');
    Route::post('/absensi/save', [App\Http\Controllers\AbsensiController::class, 'store'])->name('absensi.save');
    Route::get('/absensi/{tgl}/file/{filename}', [App\Http\Controllers\AbsensiController::class, 'file'])->name('absensi.file');

    Route::get('/unit_absensi', [App\Http\Controllers\UnitAbsensiController::class, 'index'])
        ->name('unit_absensi');
    Route::get('/unit_absensi/data', [App\Http\Controllers\UnitAbsensiController::class, 'data'])
        ->name('unit_absensi.data');
    Route::get('/unit_absensi/edit/{id}', [App\Http\Controllers\UnitAbsensiController::class, 'edit'])
        ->name('unit_absensi.edit');
    Route::post('/unit_absensi/save', [App\Http\Controllers\UnitAbsensiController::class, 'store'])
        ->name('unit_absensi.save');
    Route::get('/unit_absensi/delete/{id}', [App\Http\Controllers\UnitAbsensiController::class, 'destroy'])
        ->name('unit_absensi.delete');
    Route::get('/unit_absensi/set_status/{id}', [App\Http\Controllers\UnitAbsensiController::class, 'set_status'])
        ->name('unit_absensi.set_status');
});
