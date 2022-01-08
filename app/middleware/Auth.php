<?php
declare (strict_types = 1);

namespace app\middleware;

use app\common\exception\BadRequest as BadRequestException;
use app\common\exception\Base as BaseException;
use app\common\exception\Unauthorized as UnauthorizedException;
use app\common\service\Jwt as JwtService;

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
        if(empty($token)) throw new BadRequestException(['errorMessage' => 'token不存在']);
        // 验证token有效性
        $jwt = JwtService::getInstance();
        $jwt->setToken($token);
        if(!$jwt->validate() || !$jwt->verify()) throw new UnauthorizedException(['errorMessage' => 'token已失效']);

        return $next($request);
    }
}
