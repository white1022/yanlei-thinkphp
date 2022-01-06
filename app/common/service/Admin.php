<?php
declare (strict_types = 1);

namespace app\common\service;

use app\common\model\Role as RoleModel;
use app\common\model\Rule as RuleModel;
use app\common\exception\BadRequest as BadRequestException;
use app\common\model\Admin as AdminModel;
use app\common\service\Jwt as JwtService;
use app\common\service\Log as LogService;
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
            if($admin->isEmpty()) throw new BadRequestException(['errorMessage' => '管理员不存在或已删除']);
            $admin = $admin->toArray();
            $redis->setex('admin:'.$id, 3600, json_encode($admin)); //缓存3600秒
        } else {
            $admin = json_decode($cacheInfo, true);
        }
        return $admin;
    }

    /*
     * 通过登录获取管理员信息并写入缓存
     */
    public static function getAdminInfoByLogin() :AdminModel
    {
        //查询用户信息
        $admin = AdminModel::where('email', input('post.email'))->findOrEmpty();
        //判空抛异常
        if($admin->isEmpty()) throw new BadRequestException(['errorMessage' => '账号或密码错误']);
        //判断账号是否启用
        if($admin->is_use == 2) throw new BadRequestException(['errorMessage' => '账号未启用']);
        //验证密码
        if(!password_verify(input('post.password'), $admin->password)) throw new BadRequestException(['errorMessage' => '密码错误']);
        //登录成功 更新登录时间以及登录IP
        $admin->last_login_time = time();
        if(!$admin->save()) throw new BadRequestException(['errorMessage' => '数据更新失败']);
        //写入日志
        LogService::save(1, $admin->id, '登入');
        //写入缓存
        $redis = RedisService::getInstance();
        $redis->setex('admin:'.$admin->id, 3600, json_encode($admin->toArray())); //缓存3600秒
        //返回数据
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
     * 通过管理员id删除缓存信息
     */
    public static function deleteCacheByAdminId(int $id = 0) :void
    {
        //写入日志
        LogService::save(1, $id, '登出');
        //删除缓存
        $redis = RedisService::getInstance();
        $redis->del('admin:'.$id);
    }

    /*
     * 通过管理员id获取侧边栏菜单
     */
    public static function getMenuByAdminId(int $id = 0) :array
    {
        // 读取用户所属角色
        $roles = AdminModel::findOrEmpty($id)->role->where('is_use', '1');
        $ids = []; // 保存用户所属角色的所有权限规则id
        foreach ($roles as $role) {
            $ids = array_merge($ids, explode(',', trim($role->rules, ',')));
        }
        $ids = array_unique($ids);
        // 读取所属权限规则
        $rules = RuleModel::where('id', 'in', $ids)->order('sort', 'desc')->select()->toArray();
        return generateTree($rules);
    }

    /*
     * 获取管理员列表
     */
    public static function getAdminList() :array
    {
        list($page, $limit) = get_page_limit();
        $nickname = input('get.nickname', '');
        $email = input('get.email', '');
        $mobile = input('get.mobile', '');
        $is_use = input('get.is_use', '');

        $condition = [];
        if(!empty($nickname)){
            array_push($condition, ['nickname','like','%'.$nickname.'%']);
        }
        if(!empty($email)){
            array_push($condition, ['email','like','%'.$email.'%']);
        }
        if(!empty($mobile)){
            array_push($condition, ['mobile','like','%'.$mobile.'%']);
        }
        if(!empty($is_use)){
            array_push($condition, ['is_use','=',$is_use]);
        }

        $list = AdminModel::where($condition)
            ->order('create_time', 'desc')
            ->limit($limit)
            ->page($page)
            //->append(['is_use_text']) //追加其它的字段（该字段必须有定义获取器）
            ->select();
        foreach ($list as $item){
            $item->role; //获取管理员的所有角色
        }
        $total = AdminModel::where($condition)
            ->count();

        return [$list, $total];
    }

    /*
     * 添加/修改管理员信息
     */
    public static function addEditAdminInfo() :void
    {
        if(input('post.id')){
            $info = AdminModel::findOrEmpty(input('post.id'));
            if($info->isEmpty()) throw new BadRequestException(['errorMessage' => '数据不存在或已删除']);
            $res = $info->save(input('post.'));
            if(!$res) throw new BadRequestException(['errorMessage' => '失败']);
            //更新关联的中间表数据
            $info->role()->detach();
            $info->role()->saveAll(input('post.role'));
        }else{
            $admin = new AdminModel();
            $res = $admin->save(input('post.'));
            if(!$res) throw new BadRequestException(['errorMessage' => '失败']);
            //增加关联的中间表数据
            $admin->role()->saveAll(input('post.role'));
        }
    }

    /*
     * 删除管理员信息
     */
    public static function deleteAdminInfo() :void
    {
        $ids = explode(',', input('post.id'));
        foreach ($ids as $id){
            if($id == 1) throw new BadRequestException(['errorMessage' => '总管理员不能删除']);
            $info = AdminModel::where('id', '=', $id)
                ->findOrEmpty();
            if($info->isEmpty()) throw new BadRequestException(['errorMessage' => '数据不存在或已删除']);
            $res = $info->delete();
            if(!$res) throw new BadRequestException(['errorMessage' => '失败']);
            //删除关联的中间表数据
            $info->role()->detach();
        }
    }

    /*
     * 获取角色列表
     */
    public static function getRoleList() :array
    {
        $roles = RoleModel::order('create_time', 'desc')->column('name', 'id');
        return $roles;
    }

    /*
     * 修改管理员密码
     */
    public static function changeAdminPassword() :void
    {
        $info = AdminModel::findOrEmpty(input('post.id'));
        if($info->isEmpty()) throw new BadRequestException(['errorMessage' => '数据不存在或已删除']);
        $res = $info->save(input('post.'));
        if(!$res) throw new BadRequestException(['errorMessage' => '失败']);
    }




}
