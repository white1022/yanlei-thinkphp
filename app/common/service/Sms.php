<?php
declare(strict_types=1);

namespace app\common\service;

use Flc\Dysms\Client;
use Flc\Dysms\Request\SendSms;

/*
 * 短信
 */
class Sms
{
    //sms 配置
    private static $config = [
        'accessKeyId'    => 'LTAI4G7zrqzX3JKtNsoFCzBf', // AccessKeyID
        'accessKeySecret' => 'Lel9LK1tTNA3ErBcONwHuLMmfbuJXW', // AccessKeySecret
    ];

    /*
     * 发送短信
     * @param $mobile 手机号
     * @param $captcha 验证码
     * @param $sms_free_sign_name 短信签名
     * @param $sms_template_code 短信模板ID
     */
    public static function send(string $mobile, string $captcha, string $sms_free_sign_name, string $sms_template_code) :bool
    {
        $client  = new Client(self::$config);
        $sendSms = new SendSms;
        $sendSms->setPhoneNumbers($mobile);
        $sendSms->setSignName($sms_free_sign_name);
        $sendSms->setTemplateCode($sms_template_code);
        $sendSms->setTemplateParam(['code' => $captcha]);
        //$sendSms->setOutId('yourOutId'); //可选，设置流水号

        $res = $client->execute($sendSms); //返回的是个对象
        //$res = json_decode(json_encode($res),true);
        return $res->Code == 'OK' ? true : false;
    }
}
