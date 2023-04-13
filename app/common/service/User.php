<?php
declare (strict_types = 1);

namespace app\common\service;

use app\common\exception\BadRequest as BadRequestException;
use app\common\exception\Unauthorized as UnauthorizedException;
use app\common\model\Captcha as CaptchaModel;
use app\common\model\User as UserModel;
use app\common\model\Address as AddressModel;
use app\common\service\Jwt as JwtService;
use app\common\service\Redis as RedisService;

class User
{
    /*
     * 获取用户列表
     */
    public static function getUserList() :array
    {
        list($page, $limit) = get_page_limit();
        $nickname = input('get.nickname', '');
        $mobile = input('get.mobile', '');
        $is_use = input('get.is_use', '');

        $condition = [];
        if(!empty($nickname)){
            array_push($condition, ['nickname','like','%'.$nickname.'%']);
        }
        if(!empty($mobile)){
            array_push($condition, ['mobile','like','%'.$mobile.'%']);
        }
        if(!empty($is_use)){
            array_push($condition, ['is_use','=',$is_use]);
        }

        $list = UserModel::with(['address'])->where($condition)
            ->order('create_time', 'desc')
            ->limit($limit)
            ->page($page)
            ->select();
        $total = UserModel::where($condition)
            ->count();

        return [$list, $total];
    }

    /*
     * 添加用户信息
     */
    public static function addUserInfo() :void
    {
        $count = UserModel::where([
            ['email', '=', input('post.email')],
        ])->count();
        if($count) throw new BadRequestException(['errorMessage' => '邮箱已存在']);
        $count = UserModel::where([
            ['mobile', '=', input('post.mobile')],
        ])->count();
        if($count) throw new BadRequestException(['errorMessage' => '电话已存在']);
        $res = (new UserModel())->save(input('post.'));
        if(!$res) throw new BadRequestException(['errorMessage' => '失败']);
    }

    /*
     * 修改用户信息
     */
    public static function editUserInfo() :void
    {
        $info = UserModel::findOrEmpty(input('post.id'));
        if($info->isEmpty()) throw new BadRequestException(['errorMessage' => '数据不存在或已删除']);
        $count = UserModel::where([
            ['id', '<>', $info->id],
            ['email', '=', input('post.email')],
        ])->count();
        if($count) throw new BadRequestException(['errorMessage' => '邮箱已存在']);
        $count = UserModel::where([
            ['id', '<>', $info->id],
            ['mobile', '=', input('post.mobile')],
        ])->count();
        if($count) throw new BadRequestException(['errorMessage' => '电话已存在']);
        $res = $info->save(input('post.'));
        if(!$res) throw new BadRequestException(['errorMessage' => '失败']);
    }

    /*
     * 删除用户信息
     */
    public static function deleteUserInfo() :void
    {
        $ids = explode(',', input('post.id'));
        foreach ($ids as $id){
            $info = UserModel::where('id', '=', $id)
                ->findOrEmpty();
            if($info->isEmpty()) throw new BadRequestException(['errorMessage' => '数据不存在或已删除']);

            $count = AddressModel::where('user_id', '=', $info->id)
                ->count();
            if($count) throw new BadRequestException(['errorMessage' => '存在地址']);

            $res = $info->delete();
            if(!$res) throw new BadRequestException(['errorMessage' => '失败']);
            // TODO 其他数据的清理
        }
    }

    /*
     * 修改用户密码
     */
    public static function editUserPassword() :void
    {
        $info = UserModel::findOrEmpty(input('post.id'));
        if($info->isEmpty()) throw new BadRequestException(['errorMessage' => '数据不存在或已删除']);
        $res = $info->save(input('post.'));
        if(!$res) throw new BadRequestException(['errorMessage' => '失败']);
    }

    /*
     * 通过缓存获取用户信息
     */
    public static function getUserInfoByCache(int $id = 0) :array
    {
        $redis = RedisService::getInstance();
        $key = 'USER:'.$id;
        $cacheInfo = $redis->get($key);
        if (!$cacheInfo) {
            $user = UserModel::findOrEmpty($id);
            if($user->isEmpty()) throw new BadRequestException(['errorMessage' => '用户不存在或已删除']);
            $user = $user->toArray();
            $redis->setex($key, 3600, json_encode($user)); //缓存3600秒
        } else {
            $user = json_decode($cacheInfo, true);
        }
        return $user;
    }

    /*
     * 通过登录获取用户信息并写入缓存
     */
    public static function getUserInfoByLogin() :UserModel
    {
        $login_type = input('post.login_type', '');
        $mobile = input('post.mobile', '');

        //判断手机号格式
        if(!preg_match("/^1[345678]{1}\d{9}$/",$mobile)) throw new BadRequestException(['errorMessage' => '请填写正确的手机号']);
        //查询用户信息
        $user = UserModel::where('mobile', $mobile)->findOrEmpty();
        //判空抛异常
        if($user->isEmpty()) throw new BadRequestException(['errorMessage' => '账号错误']);
        //判断账号是否启用
        if($user->is_use == 2) throw new BadRequestException(['errorMessage' => '账号未启用']);

        if($login_type == 'password'){ //密码登录
            //验证密码
            if(!password_verify(input('post.password', ''), $user->password)) throw new BadRequestException(['errorMessage' => '密码错误']);
        }elseif($login_type == 'captcha'){ //验证码登录
            //验证验证码
            $captcha = CaptchaModel::where([
                ['mobile', '=', $mobile],
                ['expire_time', '>', time()],
            ])->order(['id' => 'desc'])
                ->findOrEmpty();
            if($captcha->isEmpty()) throw new BadRequestException(['errorMessage' => '验证码不存在或已过期']);
            if(input('post.captcha', '') != $captcha->captcha) throw new BadRequestException(['errorMessage' => '验证码不正确']);
            //更新验证码
            $captcha->expire_time = time();
            $captcha->save();
        }elseif($login_type == 'wx'){ //微信登录
            $openid = input('post.openid', '');
            if(empty($openid)) throw new BadRequestException(['errorMessage' => '微信用户唯一标识不存在']);
            if($openid != $user->openid) throw new BadRequestException(['errorMessage' => '微信用户唯一标识不正确']);
        }else{
            throw new BadRequestException(['errorMessage' => '登录方式不存在']);
        }

        //登录成功 更新登录时间以及登录IP
        $user->last_login_time = time();
        if(!$user->save()) throw new BadRequestException(['errorMessage' => '数据更新失败']);
        //写入缓存
        $redis = RedisService::getInstance();
        $redis->setex('USER:'.$user->id, 3600, json_encode($user->toArray())); //缓存3600秒
        //返回数据
        return $user;
    }


    /*
     * 通过id获取用户令牌
     * 把id编码到token中
     */
    public static function getUserTokenById(int $id = 0) :string
    {
        $token = JwtService::getInstance()
            ->setTableName('user')
            ->setTableId($id)
            ->encode()
            ->getToken();
        return $token;
    }

    /*
     * 通过id验证用户
     */
    public static function verifyUserById(int $id = 0) :void
    {
        $user = UserModel::findOrEmpty($id);
        if($user->isEmpty()) throw new UnauthorizedException(['errorMessage' => '用户不存在或已删除']);
        if($user->is_use == 2) throw new BadRequestException(['errorMessage' => '账号未启用']);
        $redis = RedisService::getInstance();
        $key = 'USER:ID:' . $user->id;
        $userId = $redis->get($key);
        if(!$userId){
            $redis->setex($key, 3600, $user->id);
        }
    }
}
