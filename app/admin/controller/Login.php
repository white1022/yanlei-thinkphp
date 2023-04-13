<?php
declare (strict_types = 1);

namespace app\admin\controller;

use app\common\exception\BadRequest as BadRequestException;
use app\common\service\Admin as AdminService;
use app\common\service\Log as LogService;
use app\common\validate\Admin as AdminValidate;
use think\response\Json;

class Login extends Base
{
    /*
     * 登入
     */
    public function login() :Json
    {
        (new AdminValidate())->goCheck('login');
        $admin = AdminService::getAdminInfoByLogin();
        $token = AdminService::getAdminTokenById($admin->id);
        LogService::save(1, $admin->id, '登入');
        //return json(['token' => $token]);
        return returnResponse(200, '成功', ['token' => $token]);
    }

    /*
     * 登出
     */
    public function logout() :Json
    {
        AdminService::deleteCacheByAdminId($this->adminId);
        LogService::save(1, $this->adminId, '登出');
        $this->adminId = 0;
        return returnResponse(200, '成功', []);
    }

    /*
     * 验证码
     */
    public function captcha() :Json
    {
        // TODO 存储到redis
        $captcha = random_string(4, 4);
        return json(['captcha' => $captcha]);
    }

    /*
     * 多语言选择
     */
    public function lang() :Json
    {
        (new AdminValidate())->goCheck('lang');
        $lang = change_lang(input("get.lang"));
        if(empty($lang)) throw new BadRequestException(['errorMessage' => 'language setup failed']);
        return json(['lang' => $lang]);
    }

}
