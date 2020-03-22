<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::group(['namespace' => 'Index'], function(){
//    Route::get('/', 'IndexController@welcome');
    Route::get('/', 'IndexController@index');
    Route::get('/login', 'IndexController@login');
    Route::get('/logout', 'IndexController@logout');
});

Route::group(['namespace' => 'OAuth'], function (){
    // github
   Route::get('/github/login', 'GithubController@login');
   Route::get('/github/callback', 'GithubController@callback');
});
