<?php

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Dcat\Admin\Admin;

Admin::routes();

Route::group([
    'prefix'     => config('admin.route.prefix'),
    'namespace'  => config('admin.route.namespace'),
    'middleware' => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');

    $router->resource('activity', 'ActivityController');
    $router->resource('activity-sign-com', 'ActivitySignComController');
    $router->resource('activity-sign-user', 'ActivitySignUserController');
    $router->resource('activity-group', 'ActivityGroupController');
    $router->resource('company', 'CompanyController');
    $router->resource('company-child', 'CompanyChildController');
    $router->resource('award', 'AwardController');
    $router->resource('user-award', 'UserAwardController');
    $router->resource('invite-log', 'UserActivityInviteController');


    $router->get('custom/map', 'CompanyChildController@customMap'); // 自定义地图视图

});
