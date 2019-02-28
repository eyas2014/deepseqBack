<?php

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

Route::get('/api/chart/{zoom?}/{start?}', "ApiController@chart");

Route::get('/api/shift/{zoom?}/{start?}', "ApiController@shift");


Route::get('/', function () {
    return view('welcome');
});

