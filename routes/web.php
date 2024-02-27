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
});
