<?php
declare (strict_types = 1);

namespace app\middleware;

use app\common\exception\Base as BaseException;
use app\common\exception\Unauthorized as UnauthorizedException;
use app\common\service\Admin as AdminService;
use app\common\service\Jwt as JwtService;
use app\common\service\User as UserService;

class Auth
{
    /*
     * 处理请求
     */
    public function handle($request, \Closure $next)
    {
        if($request->isOptions()) throw new BaseException(['httpCode' => 200, 'errorMessage' => '放行OPTIONS请求']);
        // $rule = $request->controller(true) . '/' . $request->action(true);
        // 获取token
        $authorization = $request->header('authorization') ?? '';
        $token = substr_count($authorization,'Bearer ') == 1 ? substr($authorization,7) : '';
        if(empty($token)) throw new UnauthorizedException(['errorMessage' => 'token不存在']);
        // 验证token有效性
        $jwt = JwtService::getInstance();
        $jwt->setToken($token);
        if(!$jwt->validate() || !$jwt->verify()) throw new UnauthorizedException(['errorMessage' => 'token已失效']);
        //验证token中的id有效性
        switch ($jwt->getTableName())
        {
            case 'admin':
                AdminService::verifyAdminById($jwt->getTableId());
                break;
            case 'user':
                UserService::verifyUserById($jwt->getTableId());
                break;
            default:
                throw new UnauthorizedException(['errorMessage' => 'token异常']);
        }

        return $next($request);
    }
}
