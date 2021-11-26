<?php
// +----------------------------------------------------------------------
// | 权限设置
// +----------------------------------------------------------------------

return [
    //admin模块允许访问的规则
    'admin_except_rule' => [
        'login/index',//登录页面
        'login/captcha',//验证码输出
        'login/lang',//多语言选择
        'login/login',//执行登录
    ],

    //index模块允许访问的规则
    'index_except_rule' => [
        'login/lang',//多语言选择
        'login/sendEmailCaptcha',//获取邮箱验证码
        'login/sendMobileCaptcha',//获取手机验证码
        'user/login',//登录
        'user/register',//注册
        'user/retrievePassword',//找回密码
        'equipment/doEquipmentRepairForCentral',
        //'equipment/getEquipmentInfo',
        'api/getUpgradePackage',//获取最新升级包
        'equipment/getEquipmentRepairInfo',
        'equipment/editEquipmentRepairStatus',
        'login/send_captcha',//验证码
        //'user/logout',//退出
        'index/index',//首页
        'index/search',//搜索
        'equipment/getEquipmentIdByScreenImei',//获取设备id通过屏幕的imei编号
        'equipment/getRealtimeDataByEquipmentId',//获取设备实时数据通过设备id
        'api/getParamSetup',//获取参数设置
        'api/getSocketUrl',//获取socket地址
        'login/sendEmailCaptcha',//获取socket地址
    ],
];
