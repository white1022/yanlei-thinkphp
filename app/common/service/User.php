<?php
declare (strict_types = 1);

namespace app\common\service;


use app\common\exception\BadRequest as BadRequestException;
use app\common\model\User as UserModel;
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
        $is_use = input('get.is_use', '');

        $condition = [];
        if(!empty($nickname)){
            array_push($condition, ['nickname','like','%'.$nickname.'%']);
        }
        if(!empty($is_use)){
            array_push($condition, ['is_use','=',$is_use]);
        }

        $list = UserModel::where($condition)
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
            $res = $info->delete();
            if(!$res) throw new BadRequestException(['errorMessage' => '失败']);
            // TODO 其他数据的清理
        }
    }

    /*
     * 通过缓存获取用户信息
     */
    public static function getUserInfoByCache(int $id = 0) :array
    {
        $redis = RedisService::getInstance();
        $key = 'user:'.$id;
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
}
