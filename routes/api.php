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
    Route::any('/invite-user', 'ActivityController@inviteUser');
});

Route::prefix('/group')->group(function () {
//    //立即开团  --- 支付成功之后放在回调里
//    Route::any('/create', 'GroupController@create');
//    //参加别人的团 --- 支付成功之后放在回调里
//    Route::any('/add-other', 'GroupController@addOther');
    //所有的团列表
    Route::any('/lists', 'GroupController@lists');
    //某一个团里面成员列表
    Route::any('/user-lists/{id}', 'GroupController@userList');
});

Route::prefix('/award')->group(function () {
    Route::any('/lists', 'AwardController@lists');
    Route::any('/create', 'AwardController@create');
    Route::any('/my-lists', 'AwardController@myLists');
});

Route::prefix('/order')->group(function () {
    Route::any('/lists', 'OrderController@lists');
});

Route::prefix('/course')->group(function () {
    Route::any('/detail/{id}', 'CourseController@detail');
    Route::any('/type-lists', 'CourseController@typeLists');
    Route::any('/lists', 'CourseController@lists');
    Route::any('/company-child-lists/{id}', 'CourseController@companyChildList');
});

Route::prefix('/pay')->group(function () {
    Route::any('/pay', 'PayController@pay');
    Route::any('/notify', 'PayController@notify');
});

//});
