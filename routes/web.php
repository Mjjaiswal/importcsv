<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImportDataController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::middleware(['auth', 'role:exporter'])->group(function () {
    Route::get('export', [ImportDataController::class, 'export'])->name('data.export');
});

Route::middleware(['auth', 'role:importer'])->group(function () {
    Route::post('import', [ImportDataController::class, 'import'])->name('data.import');
});

Route::get('/csv-import-logs', [ImportDataController::class, 'showLogs'])->middleware('auth')->name('csv.import.logs');