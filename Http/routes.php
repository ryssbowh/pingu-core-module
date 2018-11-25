<?php

Route::group(['middleware' => 'web', 'namespace' => 'Modules\Core\Http\Controllers'],function(){
	Route::get('/home','CoreController@index');
	Route::get('/','CoreController@index');

	Route::get('/admin','CoreAdminController@index');
});

