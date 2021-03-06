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

Route::get('/', function () {
    return view('welcome');
});

Route::get('reports', 'App\Http\Controllers\ReportsController@index');
Route::get('profiles', 'App\Http\Controllers\ProfilesController@index');
Route::get('bookings', 'App\Http\Controllers\BookingsController@index');
Route::get('stays', 'App\Http\Controllers\StaysController@index');
