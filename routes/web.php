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
/***
 * 显示屏
 */
Route::any('/', 'tvController@index');
//Route::any('/tv', 'tvController@index');

//Route::get('/', function () {
//    return view('index', ['name' => 'Samantha', 'logs' => [1, 2, 3]]);
//});
//
//
//Route::get('/news', function () {
//    return view('web.news');
//});
//
//Route::any('/product', 'ProductController@list');
//Route::any('/product/detail/{id}', 'ProductController@detail');
