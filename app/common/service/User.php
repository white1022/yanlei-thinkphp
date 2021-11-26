<?php
declare (strict_types = 1);

namespace app\common\service;


use app\common\exception\NotFound as NotFoundException;
use app\common\model\User as UserModel;
use app\common\service\Redis as RedisService;

class User
{
    /*
     * 通过缓存获取管理员信息
     */
    public static function getUserInfoByCache(int $id = 0) :UserModel
    {
        $redis = RedisService::getInstance();
        $cacheInfo = $redis->get('user:'.$id);
        if (!$cacheInfo) {
            $user = UserModel::findOrEmpty($id);
            if($user->isEmpty()) throw new NotFoundException();
            $redis->setex('user:'.$id, 3600, json_encode($user)); //缓存3600秒
        } else {
            $user = json_decode($cacheInfo); //返回对象
        }
        return $user;
    }
}
