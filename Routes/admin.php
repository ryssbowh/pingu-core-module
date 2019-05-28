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


Route::get('/','CoreController@adminHome');
Route::get('/settings/core', ['uses' => 'CoreSettingsController@index'])
	->middleware('can:view core settings')
	->name('settings.admin.core');

Route::get('/settings/core/edit', ['uses' => 'CoreSettingsController@edit'])
	->middleware('can:edit core settings')
	->name('settings.admin.core.edit');

Route::post('/settings/core/edit', ['uses' => 'CoreSettingsController@update'])
	->middleware('can:edit core settings');