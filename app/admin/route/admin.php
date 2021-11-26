<?php

use app\common\exception\BadRequest as BadRequestException;
use app\middleware\Auth as AuthMiddleware;
use think\facade\Route;

// 虚拟分组
Route::group(function () {
    // login分组
    Route::group('login', function () {
        Route::rule('captcha', 'captcha', 'GET');
        Route::rule('login', 'login', 'POST');
    })->prefix('Login/');
    // admin分组
    Route::group('admin', function () {
        Route::rule('index', 'index', 'GET');
        Route::rule('info', 'info', 'GET');
        Route::rule('upload', 'upload', 'POST');
    })->prefix('Admin/');

    // index分组
    Route::group('index', function () {
        Route::rule('upload', 'upload', 'POST');
        Route::rule('create', 'create', 'POST');
        Route::rule('update', 'update', 'POST');
        Route::rule('get', 'get', 'GET');
    })->prefix('Index/');

})->middleware(AuthMiddleware::class);

// 全局MISS路由
Route::miss(function() {
    throw new BadRequestException(['errorMessage' => '404 Not Found!']);
});
