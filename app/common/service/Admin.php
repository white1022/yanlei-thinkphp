<?php
declare (strict_types = 1);

namespace app\common\service;


use app\common\model\Rule as RuleModel;
use app\common\exception\NotFound as NotFoundException;
use app\common\exception\BadRequest as BadRequestException;
use app\common\model\Admin as AdminModel;
use app\common\service\Jwt as JwtService;
use app\common\service\Redis as RedisService;

class Admin
{
    /*
     * 通过缓存获取管理员信息
     */
    public static function getAdminInfoByCache(int $id = 0) :array
    {
        $redis = RedisService::getInstance();
        $cacheInfo = $redis->get('admin:'.$id);
        if (!$cacheInfo) {
            $admin = AdminModel::findOrEmpty($id);
            if($admin->isEmpty()) throw new NotFoundException();
            $admin = $admin->toArray();
            $redis->setex('admin:'.$id, 3600, json_encode($admin)); //缓存3600秒
        } else {
            $admin = json_decode($cacheInfo, true);
        }
        return $admin;
    }

    /*
     * 通过登录获取管理员信息
     */
    public static function getAdminInfoByLogin() :AdminModel
    {
        //查询用户信息
        $admin = AdminModel::where('email', input('post.email'))->findOrEmpty();
        //判空抛异常
        if($admin->isEmpty()) throw new NotFoundException();
        //判断账号是否启用
        if($admin->is_use == 0) throw new BadRequestException(['errorMessage' => '账号未启用']);
        //验证密码
        if(!password_verify(input('post.password'), $admin->password)) throw new BadRequestException(['errorMessage' => '密码错误']);
        //登录成功 更新登录时间以及登录IP
        $admin->last_login_time = time();
        if(!$admin->save()) throw new BadRequestException(['errorMessage' => '数据更新失败']);
        return $admin;
    }

    /*
     * 通过id获取令牌，把id编码到token中
     */
    public static function getTokenById(int $id = 0) :string
    {
        $token = JwtService::getInstance()
            ->setTableName('admin')
            ->setTableId($id)
            ->encode()
            ->getToken();
        return $token;
    }

    /*
     * 获取列表数据
     */
    public static function getList() :array
    {
        $limit = input('get.limit/d', 15);
        $page = input('get.page/d', 1);
        $email = input('get.email/s', '');
        $condition = [];
        if(!empty($email)){
            array_push($condition, ['email','like','%'.$email.'%']);
        }
        $list = AdminModel::where($condition)
            ->order([
                'id' => 'desc',
            ])->limit($limit)
            ->page($page)
            ->select();
        $total = AdminModel::where($condition)
            ->count();
        return [
            'list' => $list,
            'total' => $total,
        ];
    }


    public function getRolesById(int $id = 0) :bool
    {
        // todo 没写完
        // 读取用户所属角色
        $roles = AdminModel::findOrEmpty($id)->role->where('is_use', '1');
        $ids = []; // 保存用户所属角色的所有权限规则id
        foreach ($roles as $role) {
            $ids = array_merge($ids, explode(',', trim($role->rules, ',')));
        }
        $ids = array_unique($ids);
        // 读取所属权限规则
        $rules = RuleModel::where(['id'=>$ids])->select();
        $auth = array_map('strtolower', array_column($rules->toArray(), 'name'));
        // 是否有这一权限
        //if(!in_array($rule, $auth)) returnResponse(400, 'permission denied');
    }

}
