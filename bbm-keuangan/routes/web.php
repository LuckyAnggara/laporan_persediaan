<?php

use App\Http\Controllers\LabaRugiController;
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


Route::get('/persediaan', [PersediaanController::class, 'index'])->name('persediaan');
Route::get('/laba-rugi', [LabaRugiController::class, 'index'])->name('labarugi');
Route::get('/laba-rugi-bulanan', [LabaRugiController::class, 'indexBulanan'])->name('labarugibulanan');
Route::get('/laporan', [PersediaanController::class, 'laporan'])->name('laporan');
Route::get('/', [PersediaanController::class, 'home'])->name('home');

Route::get('/run', [LabaRugiController::class, 'run'])->name('run');
Route::get('/test', [LabaRugiController::class, 'testMonth'])->name('testMonth');
