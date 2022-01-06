<?php
// +----------------------------------------------------------------------
// | 权限设置
// +----------------------------------------------------------------------

return [
    //admin模块允许访问的规则
    'admin_except_rule' => [
//        'login/lang',//多语言选择
//        'login/captcha',//验证码输出
//        'login/login',//执行登录
    ],

    //index模块允许访问的规则
    'index_except_rule' => [
//        'login/lang',//多语言选择
//        'login/sendEmailCaptcha',//获取邮箱验证码
//        'login/sendMobileCaptcha',//获取手机验证码
//        'user/login',//登录
//        'user/register',//注册
//        'user/retrievePassword',//找回密码
    ],
];
