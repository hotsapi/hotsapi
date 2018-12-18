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

Route::get ('replays', 'ReplayController@index')->middleware('throttle:30,1'); //start_date end_date map game_type player min_id
Route::get ('replays/paged', 'ReplayController@paged')->middleware('throttle:30,1'); //start_date end_date map game_type player min_id page
Route::post('upload', 'ReplayController@store');
Route::post('replays', 'ReplayController@store');
Route::get ('replays/fingerprints/v3/{fingerprint}', 'ReplayController@checkV3');
Route::get ('replays/fingerprints/v2/{fingerprint}', 'ReplayController@checkV2');
Route::post('replays/fingerprints', 'ReplayController@massCheck');
Route::get ('replays/min-build', 'ReplayController@minimumBuild');
Route::get ('replays/{replay}', 'ReplayController@show')->middleware('throttle:60,1');


Route::get ('heroes', 'HeroController@index');
Route::get ('heroes/translations', 'HeroController@index'); // left for compatibility
Route::get ('heroes/{id}', 'HeroController@show');
Route::get ('heroes/{id}/abilities/{ability}', 'HeroController@showAbility');

Route::get ('maps', 'MapController@index');
Route::get ('maps/translations', 'MapController@index'); // left for compatibility
Route::get ('maps/{id}', 'MapController@show');

Route::get ('talents', 'TalentController@index');
Route::get ('talents/{id}', 'TalentController@show');
