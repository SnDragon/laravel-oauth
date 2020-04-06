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
Route::group(['namespace' => 'Index'], function () {
    Route::get('/welcome', 'IndexController@welcome');
    Route::get('/', 'IndexController@index');
    Route::get('/login', 'IndexController@login');
    Route::get('/logout', 'IndexController@logout');
});
Route::group(['namespace' => 'OAuth'], function () {
    // github
    Route::get('/github/login', 'GithubController@login');
    Route::get('/github/callback', 'GithubController@callback');
    // 微博
    Route::get('/weibo/login', 'WeiboController@login');
    Route::get('/weibo/callback', 'WeiboController@callback');
    // QQ
    Route::get('/qq/login', 'QQController@login');
    Route::get('/qq/callback', 'QQController@callback');
    // 微信
    Route::get('/wechat/login', 'WechatController@login');
    Route::get('/wechat/callback', 'WechatController@callback');
});
