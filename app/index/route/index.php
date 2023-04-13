<?php

use app\common\exception\BadRequest as BadRequestException;
use app\middleware\Auth as AuthMiddleware;
use think\facade\Route;

// 不需要登录的路由
Route::group(function () {
    // 验证码相关
    Route::group('captcha', function () {
        Route::rule('sendToMobile', 'sendToMobile', 'POST');
    })->prefix('Captcha/');

    // 系统设置相关
    Route::group('systemSetup', function () {
        Route::rule('getInformation', 'getInformation', 'GET');
    })->prefix('SystemSetup/');

    // 首页相关
    Route::group('index', function () {
        Route::rule('pc', 'pc', 'GET');
        Route::rule('app', 'app', 'GET');
    })->prefix('Index/');

    // 地区相关
    Route::group('region', function () {
        Route::rule('getList', 'getList', 'GET');
    })->prefix('Region/');

    // 公告相关
    Route::group('notice', function () {
        Route::rule('getList', 'getList', 'GET');
    })->prefix('Notice/');

    // 文章相关
    Route::group('readme', function () {
        Route::rule('getList', 'getList', 'GET');
    })->prefix('Readme/');

    // 登录相关
    Route::group('login', function () {
        Route::rule('userLogin', 'userLogin', 'POST');
    })->prefix('Login/');

    // 用户相关
    Route::group('user', function () {
        Route::rule('register', 'register', 'POST');
        Route::rule('forgetPassword', 'forgetPassword', 'POST');
        Route::rule('checkTokenExpire', 'checkTokenExpire', 'POST');
        Route::rule('automaticCancelAftermarket', 'automaticCancelAftermarket', 'GET');
        Route::rule('automaticConfirmReceipt', 'automaticConfirmReceipt', 'GET');
        Route::rule('getWeChatOpenIdByCode', 'getWeChatOpenIdByCode', 'GET');
        Route::rule('getWeChatPhoneNumberByCode', 'getWeChatPhoneNumberByCode', 'GET');
        Route::rule('registerByWeChat', 'registerByWeChat', 'POST');
        Route::rule('shareByWeChat', 'shareByWeChat', 'GET');
        Route::rule('getEvaluateList', 'getEvaluateList', 'GET');
        Route::rule('getHotGoods', 'getHotGoods', 'GET');
    })->prefix('User/');

    // 商品相关
    Route::group('goods', function () {
        Route::rule('getGoodsCategoryTree', 'getGoodsCategoryTree', 'GET');
        Route::rule('getGoodsCategoryList', 'getGoodsCategoryList', 'GET');
        Route::rule('getGoodsList', 'getGoodsList', 'GET');
        Route::rule('getGoodsInfo', 'getGoodsInfo', 'GET');
    })->prefix('Goods/');

    // 订单相关
    Route::group('order', function () {
        Route::rule('getOrderStatusByRedis', 'getOrderStatusByRedis', 'GET');
    })->prefix('Order/');

    // 阿里支付相关
    Route::group('aliPay', function () {
        Route::rule('pay', 'pay', 'POST');
        Route::rule('callback', 'callback', 'POST');
    })->prefix('AliPay/');

    // 微信支付相关
    Route::group('weChatPay', function () {
        Route::rule('pay', 'pay', 'POST');
        Route::rule('callback', 'callback', 'POST');
        Route::rule('updatePlatformCertificate', 'updatePlatformCertificate', 'GET');
    })->prefix('WeChatPay/');


});

// 需要登录的路由
Route::group(function () {
    // 存储相关
    Route::group('storage', function () {
        Route::rule('upload', 'upload', 'POST');
    })->prefix('Storage/');

    // 云存储相关
    Route::group('oss', function () {
        Route::rule('upload', 'upload', 'POST');
    })->prefix('Oss/');

    // 用户相关
    Route::group('user', function () {
        Route::rule('realNameAuthentication', 'realNameAuthentication', 'POST');
        Route::rule('logisticsQuery', 'logisticsQuery', 'GET');
        Route::rule('getPersonalInformation', 'getPersonalInformation', 'GET');
        Route::rule('editPersonalInformation', 'editPersonalInformation', 'POST');
        Route::rule('cancelAccount', 'cancelAccount', 'GET');
        Route::rule('changeMobile', 'changeMobile', 'POST');
        Route::rule('changePassword', 'changePassword', 'POST');
        Route::rule('getTransactionList', 'getTransactionList', 'GET');
        Route::rule('getAddressList', 'getAddressList', 'GET');
        Route::rule('addEditAddress', 'addEditAddress', 'POST');
        Route::rule('deleteAddress', 'deleteAddress', 'POST');
        Route::rule('getDefaultAddress', 'getDefaultAddress', 'GET');
        Route::rule('getCollectionList', 'getCollectionList', 'GET');
        Route::rule('isCollection', 'isCollection', 'POST');
        Route::rule('addCollection', 'addCollection', 'POST');
        Route::rule('deleteCollection', 'deleteCollection', 'POST');
        Route::rule('cancelCollection', 'cancelCollection', 'POST');
        Route::rule('addFeedback', 'addFeedback', 'POST');
        Route::rule('getCartList', 'getCartList', 'GET');
        Route::rule('addCart', 'addCart', 'POST');
        Route::rule('deleteCart', 'deleteCart', 'POST');
        Route::rule('getCartListById', 'getCartListById', 'GET');
        Route::rule('getConstructionTemporaryCartData', 'getConstructionTemporaryCartData', 'POST');
        Route::rule('modifyCartQuantity', 'modifyCartQuantity', 'POST');
        Route::rule('getOrderList', 'getOrderList', 'GET');
        Route::rule('remindDelivery', 'remindDelivery', 'POST');
        Route::rule('confirmReceipt', 'confirmReceipt', 'POST');
        Route::rule('cancelOrder', 'cancelOrder', 'POST');
        Route::rule('applyAfterSale', 'applyAfterSale', 'POST');
        Route::rule('editAftermarket', 'editAftermarket', 'POST');
        Route::rule('deleteAftermarket', 'deleteAftermarket', 'POST');
        Route::rule('cancelAftermarket', 'cancelAftermarket', 'POST');
        Route::rule('addEvaluate', 'addEvaluate', 'POST');
        Route::rule('deleteEvaluate', 'deleteEvaluate', 'POST');
        Route::rule('getAftermarketList', 'getAftermarketList', 'GET');
        Route::rule('getAftermarketInfo', 'getAftermarketInfo', 'GET');
        Route::rule('getStatisticsData', 'getStatisticsData', 'GET');
    })->prefix('User/');

    // 订单相关
    Route::group('order', function () {
        Route::rule('createOrder', 'createOrder', 'POST');
        Route::rule('toPay', 'toPay', 'POST');
        Route::rule('getOrderInfo', 'getOrderInfo', 'GET');
        Route::rule('getOrderEvaluate', 'getOrderEvaluate', 'GET');
        Route::rule('deleteOrder', 'deleteOrder', 'POST');
    })->prefix('Order/');



})->middleware(AuthMiddleware::class);

// 全局MISS路由
Route::miss(function() {
    throw new BadRequestException(['errorMessage' => '404 Not Found!']);
});
