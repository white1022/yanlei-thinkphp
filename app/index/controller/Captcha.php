<?php
declare (strict_types = 1);

namespace app\index\controller;

use app\common\service\Captcha as CaptchaService;
use think\response\Json;

class Captcha extends Base
{
    /*
     * 验证码发送到手机
     */
    public function sendToMobile() :Json
    {
        CaptchaService::sendMobileCaptcha();
        return returnResponse(200, '成功', []);
    }

    /*
     * 验证码发送到邮箱
     */
    public function sendToEmail() :Json
    {
        CaptchaService::sendEmailCaptcha();
        return returnResponse(200, '成功', []);
    }
}