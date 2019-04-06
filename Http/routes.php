<?php

Route::group(['middleware' => 'web', 'namespace' => 'Modules\Core\Http\Controllers'],function(){
	Route::get('/home','CoreController@index');
	Route::post('/home','CoreController@index2');
	Route::get('/admin','CoreAdminController@index');
	Route::get('/test','CoreController@test');
});