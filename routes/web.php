<?php

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

Route::get('/', [\App\Http\Controllers\BlackjackController::class, 'index']);
Route::get('/state', [\App\Http\Controllers\BlackjackController::class, 'getState']);
Route::post('/start-game', [\App\Http\Controllers\BlackjackController::class, 'start']);
Route::post('/hit', [\App\Http\Controllers\BlackjackController::class, 'hit']);
Route::get('/stay', [\App\Http\Controllers\BlackjackController::class, 'stay']);
