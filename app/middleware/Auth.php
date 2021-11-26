<?php
declare (strict_types = 1);

namespace app\middleware;

use app\common\exception\BadRequest as BadRequestException;
use app\common\exception\Unauthorized as UnauthorizedException;
use app\common\exception\Base as BaseException;
use app\common\service\Jwt as JwtService;
use think\facade\Config;

class Auth
{
    /*
     * 处理请求
     */
    public function handle($request, \Closure $next)
    {
        if($request->isOptions()) throw new BaseException(['httpCode' => 200, 'errorMessage' => '放行OPTIONS请求']);
        switch (app('http')->getName())
        {
            case 'admin':
                $except_rule = Config::get('auth.admin_except_rule');
            break;
            case 'user':
                $except_rule = Config::get('auth.user_except_rule');
            break;
            default:
                $except_rule = [];
        }
        $rule = $request->controller(true) . '/' . $request->action(true);
        $noAuth = array_map('strtolower', $except_rule);
        if(!in_array($rule, $noAuth)){
            $authorization = $request->header('authorization');
            $token = substr_count($authorization,'Bearer ') == 1 ? substr($authorization,7) : '';
            if($token){
                $jwt = JwtService::getInstance();
                $jwt->setToken($token);
                if($jwt->validate() && $jwt->verify()){
                    return $next($request);
                }else{
                    throw new UnauthorizedException([
                        'errorMessage' => 'token已失效'
                    ]);
                }
            }else{
                throw new BadRequestException([
                    'errorMessage' => 'token不存在'
                ]);
            }
        }

        return $next($request);
    }
}
