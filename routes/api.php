<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->any('/user', function (Request $request) {
    return $request->user();
});

Route::any('/login', 'LoginController@login');

//Route::middleware('auth:user')->group(function () {
Route::prefix('/activity')->group(function () {
    Route::any('/lists', 'ActivityController@lists');
    Route::any('/type/{id}', 'ActivityController@type');
    Route::any('/detail/{id}', 'ActivityController@detail');
});

Route::prefix('/group')->group(function () {
    Route::any('/lists', 'GroupController@lists');
});

Route::prefix('/order')->group(function () {
    Route::any('/lists', 'OrderController@lists');
});

Route::prefix('/award')->group(function () {
    Route::any('/lists', 'AwardController@lists');
    Route::any('/my-lists', 'AwardController@myLists');
});

Route::prefix('/pay')->group(function () {
    Route::any('/pay', 'PayController@pay');
    Route::any('/notify', 'PayController@notify');
});

Route::prefix('/my')->group(function () {
    Route::any('/info', function () {
        return 1111;
    });
});

//});
