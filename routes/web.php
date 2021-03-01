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

Route::get('profiles', 'App\Http\Controllers\ProfilesController@check_profiles_xml_type');
Route::get('reservation', 'App\Http\Controllers\ReservationsController@data_insert');
Route::get('stay', 'App\Http\Controllers\StaysController@data_insert');
