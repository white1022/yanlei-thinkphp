<?php

use app\common\exception\BadRequest as BadRequestException;
use app\middleware\Auth as AuthMiddleware;
use think\facade\Route;

// 不需要登录的路由
Route::group(function () {
    // Storage分组
    Route::group('storage', function () {
        Route::rule('download', 'download', 'GET');
    })->prefix('Storage/');

    // login分组
    Route::group('login', function () {
        Route::rule('lang', 'lang', 'GET');
        Route::rule('captcha', 'captcha', 'GET');
        Route::rule('login', 'login', 'POST');
    })->prefix('Login/');
});

// 需要登录的路由
Route::group(function () {
    // Storage分组
    Route::group('storage', function () {
        Route::rule('upload', 'upload', 'POST');
    })->prefix('Storage/');

    // login分组
    Route::group('login', function () {
        Route::rule('logout', 'logout', 'GET');
    })->prefix('Login/');

    // admin分组
    Route::group('admin', function () {
        Route::rule('lists', 'lists', 'GET');
        Route::rule('add', 'add', 'POST');
        Route::rule('edit', 'edit', 'POST');
        Route::rule('delete', 'delete', 'POST');
        Route::rule('getMenu', 'getMenu', 'GET');
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

    // index分组
    Route::group('index', function () {
        Route::rule('index', 'index', 'GET');
    })->prefix('Index/');

})->middleware(AuthMiddleware::class);

// 全局MISS路由
Route::miss(function() {
    throw new BadRequestException(['errorMessage' => '404 Not Found!']);
});
