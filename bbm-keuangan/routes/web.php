<?php

use App\Http\Controllers\InputController;
use App\Http\Controllers\LabaRugiController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PersediaanController;
use Illuminate\Support\Facades\Route;

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


Route::get('/input', [InputController::class, 'index'])->name('input');
Route::get('/persediaan', [PersediaanController::class, 'index'])->name('persediaan');
Route::get('/persediaan/excel', [PersediaanController::class, 'excelExport'])->name('export-excel');
Route::get('/laba-rugi', [LabaRugiController::class, 'index'])->name('labarugi');
Route::get('/laba-rugi-dua', [LabaRugiController::class, 'generateLabaRugi2'])->name('labarugi2');
Route::get('/laba-rugi-bulanan', [LabaRugiController::class, 'indexBulanan'])->name('labarugibulanan');
Route::get('/laba-rugi-tahunan', [LabaRugiController::class, 'indexTahunan'])->name('labarugitahunan');
Route::get('/laporan', [PersediaanController::class, 'laporan'])->name('laporan');
Route::get('/laporan-pelanggan', [PelangganController::class, 'index'])->name('laporan-pelanggan');
Route::get('/laporan-pelanggan/excel', [PelangganController::class, 'excelExport'])->name('export-excel-pelanggan');

Route::get('/', [PersediaanController::class, 'home'])->name('home');

Route::get('/run', [LabaRugiController::class, 'run'])->name('run');
Route::get('/test', [LabaRugiController::class, 'testMonth'])->name('testMonth');
Route::get('/input/bulan', [LabaRugiController::class, 'generateLabaRugiBulanan'])->name('input-bulanan');
