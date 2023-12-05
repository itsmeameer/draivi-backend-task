<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PriceListController;

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

Route::get('/', [PriceListController::class, 'index'])->name('price-list.index');
Route::get('/price-list/data', [PriceListController::class, 'data'])->name('price-list.data');
