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


Route::get('/', 'CoreController@home');

/**
 * Modules
 */
Route::get('/modules', ['uses' => 'ModuleController@index'])
    ->middleware('can:view modules')
    ->name('core.admin.modules');
Route::post('/modules/install/{module}', ['uses' => 'ModuleController@install'])
    ->middleware('can:activate modules');
Route::post('/modules/uninstall/{module}', ['uses' => 'ModuleController@uninstall'])
    ->middleware('can:activate modules');

/**
 * Settings
 */
Route::get('/settings/{setting_section}', ['uses' => 'SettingsController@index'])
    ->middleware('indexSettings:setting_section');
Route::get('/settings/{setting_section}/edit', ['uses' => 'SettingsController@edit'])
    ->middleware('editSettings:setting_section');
Route::put('/settings/{setting_section}', ['uses' => 'SettingsController@update'])
    ->middleware('editSettings:setting_section');