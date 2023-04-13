<?php
declare (strict_types = 1);

namespace app\index\controller;

use app\common\service\Log as LogService;
use app\common\service\User as UserService;
use think\response\Json;

class Login extends Base
{
    /*
     * 用户登录
     */
    public function userLogin() :Json
    {
        $user = UserService::getUserInfoByLogin();
        $token = UserService::getUserTokenById($user->id);
        LogService::save(2, $user->id, '登入');
        return returnResponse(200, '成功', ['token' => $token]);
    }



}
