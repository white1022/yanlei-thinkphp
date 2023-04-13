<?php
declare (strict_types = 1);

namespace app\common\service;

use app\common\exception\BadRequest as BadRequestException;
use app\common\model\Captcha as CaptchaModel;
use app\common\service\Sms as SmsService;

class Captcha
{
    /*
     * 发送手机验证码
     */
    public static function sendMobileCaptcha() :void
    {
        $mobile = input('post.mobile','');
        $code = input('post.code','');

        if(empty($mobile)) throw new BadRequestException(['errorMessage' => '手机号不能为空']);
        if(empty($code)) throw new BadRequestException(['errorMessage' => '验证码模板标识不能为空']);
        if(!preg_match("/^1[345678]{1}\d{9}$/",$mobile)) throw new BadRequestException(['errorMessage' => '请填写正确的手机号']);

        $captcha = mt_rand(1000, 9999);

        $res = SmsService::send($mobile, (string)$captcha, '小站优农', $code);
        if($res){
            $captcha = CaptchaModel::create([
                'mobile' => $mobile,
                'captcha' => $captcha,
                'expire_time' => time() + 3*60, //有效期3分钟,
            ]);
            if($captcha->isEmpty()) throw new BadRequestException(['errorMessage' => '失败']);
        }else{
            throw new BadRequestException(['errorMessage' => '短信发送失败']);
        }
    }

    /*
     * 发送邮箱证码
     */
    public static function sendEmailCaptcha() :void
    {
        // todo 需要的时候在写
    }
}