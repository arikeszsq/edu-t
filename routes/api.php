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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::any('/login', 'LoginController@login');

//Route::middleware('auth:user')->group(function () {


Route::prefix('/activity')->group(function () {
    Route::get('/lists', 'ActivityController@lists');
    Route::get('/type/{id}', 'ActivityController@type');
    Route::get('/detail/{id}', 'ActivityController@detail');
});

Route::prefix('/group')->group(function () {
    Route::get('/lists', 'GroupController@lists');
});


    Route::prefix('/pay')->group(function () {
        Route::get('/pay', 'PayController@pay');
        Route::get('/notify', 'PayController@notify');
    });




    Route::prefix('/my')->group(function () {

        Route::any('/info', function () {
            return 1111;
        });


    });
//});
