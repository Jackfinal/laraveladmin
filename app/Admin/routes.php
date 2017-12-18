<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');
    $router->resource('/category', 'CategoryController');
    $router->resource('/article', 'ArticleController');
    $router->resource('/user', 'UserController');
    $router->resource('/usergroup', 'UserGroupController');
    $router->any('reportImport', 'ReportImportController@import');
    $router->resource('/report', 'ReportController');
    $router->resource('/link', 'LinkController');
    $router->resource('/quickreply', 'QuickReplyController');
    $router->post('report/saveReply', 'HomeController@saveReply');
    //测试 转移数据
    $router->get('dealData', 'HomeController@dealData');
    
    
    $router->post('upload/uploadImg', 'UploadController@postUploadImg');
    $router->resources([
        'china/province'        => China\ProvinceController::class,
        'china/city'            => China\CityController::class,
        'china/district'        => China\DistrictController::class
    ]);

});
