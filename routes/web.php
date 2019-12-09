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

Route::get('/report', 'ReportController@index');
Route::get('/report/{report}', 'ReportController@show');
Route::post('/report', 'ReportController@create');
Route::put('/report', 'ReportController@update');
Route::delete('/report', 'ReportController@delete');
