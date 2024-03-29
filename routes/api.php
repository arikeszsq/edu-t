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

Route::any('/index/add-content', 'IndexController@addContent');



Route::middleware('auth:api')->any('/user', function (Request $request) {
    return $request->user();
});

//以下接口，不需要用户登录
Route::any('/login', 'LoginController@login');
Route::prefix('/activity')->group(function () {
    Route::any('/lists', 'ActivityController@lists');
    //活动浏览+1
    Route::any('/view', 'ActivityController@view');
});
Route::prefix('/basic')->group(function () {
    Route::any('/settings', 'BasicController@settings');
    Route::any('/kf/{id}', 'BasicController@kflist');
    Route::any('/kf', 'BasicController@kflist');
});

Route::prefix('/pay')->group(function () {
    Route::any('/notify', 'PayController@notify');
    Route::any('/txnotify', 'PayController@txnotify');
});

//以下接口，需要登录


//Route::middleware('auth:user')->group(function () {
Route::prefix('/activity')->group(function () {
    Route::any('/type/{id}', 'ActivityController@type');
    Route::any('/detail/{id}', 'ActivityController@detail');
    Route::any('/invite-user', 'ActivityController@inviteUser');
    Route::any('/web-create', 'ActivityController@webCreate');
    Route::any('/complaint', 'ActivityController@complaint');
    Route::any('/add-share-num', 'ActivityController@addShareNum');
    Route::any('/jointeam/{id}', 'ActivityController@jointeam');
});

//邀请列表的接口
Route::prefix('/invite')->group(function () {
    Route::any('/lists', 'InviteController@lists');
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
    //所有的奖励列表
    Route::any('/only-lists', 'AwardController@onlyLists');
    //奖励列表，包括判断是否可以领取
    Route::any('/lists', 'AwardController@lists');
    //领取奖励
    Route::any('/create', 'AwardController@create');
    //我的奖励
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

    //购买页，获取已经选择的校区，课程和奖励信息
    Route::any('/courseschool/info', 'CourseController@checkInfo');
});

Route::prefix('/pay')->group(function () {
    Route::any('/pay', 'PayController@pay');
});

Route::prefix('/user')->group(function () {
    Route::any('/info', 'UserController@info');
    Route::any('/share-info', 'UserController@shareInfo');
    Route::any('/update', 'UserController@update');
    Route::any('/set-a', 'UserController@setA');
    //获取我的海报
    Route::any('/get-invite-pic', 'UserController@getInvitePic');
    //提现
    Route::any('/apply-cash-out', 'UserController@applyCashOut');
});

Route::prefix('/log')->group(function () {
    Route::any('/list', 'LogController@lists');
});

Route::prefix('/upload')->group(function () {
    Route::any('/file', 'UploadController@file');
});

//机构
Route::prefix('/company')->group(function () {
    Route::any('/detail', 'CompanyController@detail');
});

//});
