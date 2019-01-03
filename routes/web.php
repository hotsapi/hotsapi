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

Route::get('/', 'WebController@home');
Route::get('/upload', 'WebController@upload');
Route::get('/docs', 'WebController@docs');
Route::get('/swagger', 'WebController@swagger');
Route::get('/faq', 'WebController@faq');
Route::get('/bnet-auth', 'WebController@bnetAuth');
