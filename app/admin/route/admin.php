<?php

use app\common\exception\BadRequest as BadRequestException;
use app\middleware\Auth as AuthMiddleware;
use think\facade\Route;

// 不需要登录的路由
Route::group(function () {
    // Storage分组
    Route::group('storage', function () {
        Route::rule('upload', 'upload', 'POST');
        Route::rule('uploadByLayEdit', 'uploadByLayEdit', 'POST');
        Route::rule('download', 'download', 'GET');
    })->prefix('Storage/');

    // Oss分组
    Route::group('oss', function () {
        Route::rule('upload', 'upload', 'POST');
        Route::rule('uploadByLayEdit', 'uploadByLayEdit', 'POST');
        Route::rule('download', 'download', 'GET');
    })->prefix('Oss/');

    // login分组
    Route::group('login', function () {
        Route::rule('lang', 'lang', 'GET');
        Route::rule('captcha', 'captcha', 'GET');
        Route::rule('login', 'login', 'POST');
    })->prefix('Login/');

    // h5分组
    Route::group('h5', function () {
        Route::rule('goodsInfo', 'goodsInfo', 'GET');
        Route::rule('noticeInfo', 'noticeInfo', 'GET');
        Route::rule('readmeInfo', 'readmeInfo', 'GET');
        Route::rule('systemSetupInfo', 'systemSetupInfo', 'GET');
        Route::rule('shareDownloadInfo', 'shareDownloadInfo', 'GET');
    })->prefix('H5/');
});

// 需要登录的路由
Route::group(function () {
    // login分组
    Route::group('login', function () {
        Route::rule('logout', 'logout', 'GET');
    })->prefix('Login/');

    // index分组
    Route::group('index', function () {
        Route::rule('console', 'console', 'GET');
    })->prefix('Index/');

    // admin分组
    Route::group('admin', function () {
        Route::rule('lists', 'lists', 'GET');
        Route::rule('add', 'add', 'POST');
        Route::rule('edit', 'edit', 'POST');
        Route::rule('delete', 'delete', 'POST');
        Route::rule('getMenu', 'getMenu', 'GET');
        Route::rule('getMenuForVue', 'getMenuForVue', 'GET');
        Route::rule('info', 'info', 'GET');
        Route::rule('password', 'password', 'POST');
        Route::rule('role', 'role', 'GET');
        Route::rule('editMyProfile', 'editMyProfile', 'POST');
        Route::rule('editMyPassword', 'editMyPassword', 'POST');
    })->prefix('Admin/');

    // rule分组
    Route::group('rule', function () {
        Route::rule('lists', 'lists', 'GET');
        Route::rule('add', 'add', 'POST');
        Route::rule('edit', 'edit', 'POST');
        Route::rule('delete', 'delete', 'POST');
        Route::rule('pid', 'pid', 'GET');
        Route::rule('icon', 'icon', 'GET');
    })->prefix('Rule/');

    // role分组
    Route::group('role', function () {
        Route::rule('lists', 'lists', 'GET');
        Route::rule('add', 'add', 'POST');
        Route::rule('edit', 'edit', 'POST');
        Route::rule('delete', 'delete', 'POST');
        Route::rule('rule', 'rule', 'GET');
    })->prefix('Role/');

    // region分组
    Route::group('region', function () {
        Route::rule('lists', 'lists', 'GET');
        Route::rule('add', 'add', 'POST');
        Route::rule('edit', 'edit', 'POST');
        Route::rule('delete', 'delete', 'POST');
        Route::rule('pid', 'pid', 'GET');
    })->prefix('Region/');

    // systemSetup分组
    Route::group('systemSetup', function () {
        Route::rule('edit', 'edit', 'POST');
        Route::rule('info', 'info', 'GET');
    })->prefix('SystemSetup/');

    // log分组
    Route::group('log', function () {
        Route::rule('lists', 'lists', 'GET');
        Route::rule('delete', 'delete', 'POST');
    })->prefix('Log/');

    // user分组
    Route::group('user', function () {
        Route::rule('lists', 'lists', 'GET');
        Route::rule('add', 'add', 'POST');
        Route::rule('edit', 'edit', 'POST');
        Route::rule('delete', 'delete', 'POST');
        Route::rule('password', 'password', 'POST');
    })->prefix('User/');

    // express分组
    Route::group('express', function () {
        Route::rule('lists', 'lists', 'GET');
        Route::rule('add', 'add', 'POST');
        Route::rule('edit', 'edit', 'POST');
        Route::rule('delete', 'delete', 'POST');
    })->prefix('Express/');

    // feedback分组
    Route::group('feedback', function () {
        Route::rule('lists', 'lists', 'GET');
        Route::rule('delete', 'delete', 'POST');
        Route::rule('process', 'process', 'POST');
    })->prefix('Feedback/');

    // slideshow分组
    Route::group('slideshow', function () {
        Route::rule('lists', 'lists', 'GET');
        Route::rule('add', 'add', 'POST');
        Route::rule('edit', 'edit', 'POST');
        Route::rule('delete', 'delete', 'POST');
    })->prefix('Slideshow/');

    // address分组
    Route::group('address', function () {
        Route::rule('lists', 'lists', 'GET');
        Route::rule('delete', 'delete', 'POST');
    })->prefix('Address/');

    // goodsCategory分组
    Route::group('goodsCategory', function () {
        Route::rule('lists', 'lists', 'GET');
        Route::rule('add', 'add', 'POST');
        Route::rule('edit', 'edit', 'POST');
        Route::rule('delete', 'delete', 'POST');
        Route::rule('pid', 'pid', 'GET');
    })->prefix('GoodsCategory/');

    // goods分组
    Route::group('goods', function () {
        Route::rule('lists', 'lists', 'GET');
        Route::rule('add', 'add', 'POST');
        Route::rule('edit', 'edit', 'POST');
        Route::rule('delete', 'delete', 'POST');
        Route::rule('info', 'info', 'GET');
        Route::rule('getGoodsCategory', 'getGoodsCategory', 'GET');
    })->prefix('Goods/');

    // order分组
    Route::group('order', function () {
        Route::rule('lists', 'lists', 'GET');
        Route::rule('delete', 'delete', 'POST');
        Route::rule('info', 'info', 'GET');
        Route::rule('delivery', 'delivery', 'POST');
        Route::rule('getExpress', 'getExpress', 'GET');
        Route::rule('printPreview', 'printPreview', 'GET');
    })->prefix('Order/');

    // aftermarket分组
    Route::group('aftermarket', function () {
        Route::rule('edit', 'edit', 'POST');
    })->prefix('Aftermarket/');

    // storey分组
    Route::group('storey', function () {
        Route::rule('lists', 'lists', 'GET');
        Route::rule('add', 'add', 'POST');
        Route::rule('edit', 'edit', 'POST');
        Route::rule('delete', 'delete', 'POST');
        Route::rule('goodsCategory', 'goodsCategory', 'GET');
    })->prefix('Storey/');

    // notice分组
    Route::group('notice', function () {
        Route::rule('lists', 'lists', 'GET');
        Route::rule('add', 'add', 'POST');
        Route::rule('edit', 'edit', 'POST');
        Route::rule('delete', 'delete', 'POST');
    })->prefix('Notice/');

    // readmeCategory分组
    Route::group('readmeCategory', function () {
        Route::rule('lists', 'lists', 'GET');
        Route::rule('add', 'add', 'POST');
        Route::rule('edit', 'edit', 'POST');
        Route::rule('delete', 'delete', 'POST');
        Route::rule('pid', 'pid', 'GET');
    })->prefix('ReadmeCategory/');

    // readme分组
    Route::group('readme', function () {
        Route::rule('lists', 'lists', 'GET');
        Route::rule('add', 'add', 'POST');
        Route::rule('edit', 'edit', 'POST');
        Route::rule('delete', 'delete', 'POST');
        Route::rule('getReadmeCategory', 'getReadmeCategory', 'GET');
    })->prefix('Readme/');



})->middleware(AuthMiddleware::class);

// 全局MISS路由
Route::miss(function() {
    throw new BadRequestException(['errorMessage' => '404 Not Found!']);
});
