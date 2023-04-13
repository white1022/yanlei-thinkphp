<?php
// +----------------------------------------------------------------------
// | 配置设置
// | 用于增加自定义配置，建议先读取env文件中的环境变量配置
// +----------------------------------------------------------------------

return [
    // 阿里云市场的实名认证配置
    'certification' => [
        'AppCode' => env('CERTIFICATION.AppCode', ''),
    ],

    // 阿里云市场的物流查询配置
    'logisticsquery' => [
        'AppCode' => env('LOGISTICSQUERY.AppCode', ''),
    ],


    //阿里大于短信配置
    'alidayu' => [
        'accessKeyId' => env('ALIDAYU.accessKeyId', ''),
        'accessKeySecret' => env('ALIDAYU.accessKeySecret', ''),
    ],

    //阿里云对象存储配置
    'aliyunoss' => [
        'accessKeyId' => env('ALIYUNOSS.accessKeyId', ''),
        'accessKeySecret' => env('ALIYUNOSS.accessKeySecret', ''),
        'endpointDomain' => env('ALIYUNOSS.endpointDomain', ''), //地域节点的域名
        'bucketDomain' => env('ALIYUNOSS.bucketDomain', ''), //存储空间的域名
        'bucketName' => env('ALIYUNOSS.bucketName', ''), //存储空间的名称
        'bucketBindDomain' => env('ALIYUNOSS.bucketBindDomain', ''), //存储空间的绑定的自定义的域名
    ],

    //微信授权一键登录配置
    'wechatoauth' => [
        'appId' => env('WECHATOAUTH.appId', ''),
        'appSecret' => env('WECHATOAUTH.appSecret', ''),
    ],

    //支付宝支付配置
    'alipay' => [
        'appId' => env('ALIPAY.appId', ''), //App ID
        'merchantPrivateKey' => env('ALIPAY.merchantPrivateKey', ''), //应用私钥
        //'alipayCertPath' => base_path() . 'common' . DIRECTORY_SEPARATOR . 'certification' . DIRECTORY_SEPARATOR . 'alipay' . DIRECTORY_SEPARATOR . 'alipayCertPublicKey_RSA2.crt', //支付宝公钥证书文件路径
        //'alipayRootCertPath' => base_path() . 'common' . DIRECTORY_SEPARATOR . 'certification' . DIRECTORY_SEPARATOR . 'alipay' . DIRECTORY_SEPARATOR . 'alipayRootCert.crt', //支付宝根证书文件路径
        //'merchantCertPath' => base_path() . 'common' . DIRECTORY_SEPARATOR . 'certification' . DIRECTORY_SEPARATOR . 'alipay' . DIRECTORY_SEPARATOR . 'appCertPublicKey_2021002107616985.crt', //应用公钥证书文件路径
        'alipayPublicKey' => env('ALIPAY.alipayPublicKey', ''), //支付宝公钥
        'notifyUrl' => env('ALIPAY.notifyUrl', ''), //支付类接口异步通知接收服务地址
        'encryptKey' => env('ALIPAY.encryptKey', ''), //AES密钥
    ],

    //微信支付配置 php版本是7.0或加上libsodium-php扩展
    'wechatpay' => [
        'apiV3key' => env('WECHATPAY.apiV3key', ''), //APIv3密钥
        'appId' => env('WECHATPAY.appId', ''), //App ID
        'merchantId' => env('WECHATPAY.merchantId', ''), //商户号
        'merchantCertificateSerial' => env('WECHATPAY.merchantCertificateSerial', ''), //商户API证书序列号
        'merchantPrivateKey' => base_path() . 'common' . DIRECTORY_SEPARATOR . 'certification' . DIRECTORY_SEPARATOR . 'wechatpay' . DIRECTORY_SEPARATOR . 'apiclient_key.pem', //商户私钥
        'merchantCertificate' => base_path() . 'common' . DIRECTORY_SEPARATOR . 'certification' . DIRECTORY_SEPARATOR . 'wechatpay' . DIRECTORY_SEPARATOR . 'apiclient_cert.pem', //商户API证书
        'platformCertificate' => base_path() . 'common' . DIRECTORY_SEPARATOR . 'certification' . DIRECTORY_SEPARATOR . 'wechatpay' . DIRECTORY_SEPARATOR . 'wechatpay_cert.pem', //微信支付平台证书
        'notifyUrl' => env('WECHATPAY.notifyUrl', ''), //回调地址
    ],











];
