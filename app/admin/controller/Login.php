<?php
declare (strict_types = 1);

namespace app\admin\controller;

use app\common\service\Admin as AdminService;
use app\common\service\Log as LogService;
use app\common\validate\Admin as AdminValidate;
use think\response\Json;

class Login extends Base
{
    /*
     * 登录页面
     */
    public function index()
    {

    }

    /*
     * 登录
     */
    public function login() :Json
    {
        (new AdminValidate())->goCheck('login');
        $admin = AdminService::getAdminInfoByLogin();
        $token = AdminService::getTokenById($admin->id);
        LogService::save(1, $admin->id, '登录');
        return json(['token' => $token]);
    }

    /*
     * 验证码
     */
    public function captcha()
    {

    }

    /*
     * 多语言选择
     */
    public function lang()
    {

    }

}
