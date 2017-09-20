<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get ('replays', 'ReplayController@index'); //start_date end_date map game_type player min_id
Route::post('upload', 'ReplayController@store');
Route::post('replays', 'ReplayController@store');
Route::get ('replays/fingerprints/v3/{fingerprint}', 'ReplayController@checkV3');
Route::get ('replays/fingerprints/v2/{fingerprint}', 'ReplayController@checkV2');
Route::get ('replays/fingerprints/{fingerprint}', 'ReplayController@checkV1');
Route::post('replays/fingerprints', 'ReplayController@massCheck');
Route::get ('replays/min-build', 'ReplayController@minimumBuild');
Route::get ('replays/{replay}', 'ReplayController@show');

// todo move to a separate controllers
Route::get ('heroes/translations', 'ReplayController@heroTranslations');
Route::get ('maps/translations', 'ReplayController@mapTranslations');
