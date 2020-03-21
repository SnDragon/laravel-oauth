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
Route::get('/', 'Index\IndexController@welcome');
Route::get('/index', 'Index\IndexController@index');
Route::get('/login', 'Index\IndexController@login');
Route::group(['namespace' => 'OAuth'], function (){
    // github
   Route::get('/github/login', 'GithubController@login');
   Route::get('/github/callback', 'GithubController@callback');
});
