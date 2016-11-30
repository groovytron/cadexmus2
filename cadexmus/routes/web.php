<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', function () {

    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->middleware('auth');
Route::resource('sample', 'SampleController');
Route::resource('projet', 'ProjetController');
Route::get('projet/{projet}/chat','ProjetController@getChat')->name("projet.getChat");

Route::get('projet/{projet}/{version}', 'ProjetController@getUpdate')->name('projet.getUpdates');

