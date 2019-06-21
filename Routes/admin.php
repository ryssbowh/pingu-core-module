<?php

/*
|--------------------------------------------------------------------------
| Admin Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group prefixed with admin which
| contains the "web" middleware group and the permission middleware "can:access admin area".
|
*/


Route::get('/','CoreController@home');

/**
 * Settings
 */
Route::get('/settings/general', ['uses' => 'CoreSettingsController@index'])
	->middleware('can:view general settings')
	->name('settings.admin.general');
Route::get('/settings/mailing', ['uses' => 'CoreSettingsController@index'])
	->middleware('can:view mailing settings')
	->name('settings.admin.mailing');

Route::get('/settings/general/edit', ['uses' => 'CoreSettingsController@edit'])
	->middleware('can:edit general settings')
	->name('settings.admin.general.edit');
Route::get('/settings/mailing/edit', ['uses' => 'CoreSettingsController@edit'])
	->middleware('can:edit mailing settings')
	->name('settings.admin.mailing.edit');

Route::post('/settings/general/edit', ['uses' => 'CoreSettingsController@update'])
	->middleware('can:edit general settings');
Route::post('/settings/mailing/edit', ['uses' => 'CoreSettingsController@update'])
	->middleware('can:edit mailing settings');