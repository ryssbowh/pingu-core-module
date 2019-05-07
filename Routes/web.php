<?php

Route::get('/','Controller@home')->middleware('home');
Route::get('/home','Controller@home');