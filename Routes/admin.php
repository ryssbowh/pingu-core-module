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
    ->middleware('permission:view modules')
    ->name('modules');
Route::post('/modules/install/{module}', ['uses' => 'ModuleController@install'])
    ->middleware('permission:activate modules');
Route::post('/modules/uninstall/{module}', ['uses' => 'ModuleController@uninstall'])
    ->middleware('permission:activate modules');

/**
 * Settings
 */
if (pingu_installed()) {
    foreach (\Settings::allRepositories() as $name => $repository) {
        Route::get('/settings/'.$name, ['uses' => 'SettingsController@index'])
            ->name(adminPrefix().'.settings.'.$name)
            ->middleware('indexSettings:'.$name);
        Route::get('/settings/'.$name.'/edit', ['uses' => 'SettingsController@edit'])
            ->name(adminPrefix().'.settings.'.$name.'.edit')
            ->middleware('editSettings:'.$name);
        Route::put('/settings/'.$name, ['uses' => 'SettingsController@update'])
            ->middleware('editSettings:'.$name);
    }
}

Route::get('settings/cache', ['uses' => 'CacheController@index'])
    ->name(adminPrefix().'.settings.cache')
    ->middleware('permission:manage cache');
Route::post('settings/cache', ['uses' => 'CacheController@empty'])
    ->middleware('permission:manage cache');
Route::post('settings/route_cache', ['uses' => 'CacheController@routeCache'])
    ->middleware('permission:manage cache');
Route::post('settings/rebuild_route_cache', ['uses' => 'CacheController@rebuildRouteCache'])
    ->middleware('permission:manage cache');