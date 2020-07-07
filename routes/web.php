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

Route::get('/login', function () {
    return response()->json(['error' => 'Forbidden', 'message' => 'wrong credentials'],401);
})->name('login');

//Auth::routes();
//Route::get('/home', 'HomeController@index')->name('home');
//Route::get('/login', 'HomeController@index');
