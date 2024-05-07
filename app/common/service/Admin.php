<?php
declare (strict_types = 1);

namespace app\common\service;

use app\common\exception\BadRequest as BadRequestException;
use app\common\exception\Unauthorized as UnauthorizedException;
use app\common\model\Admin as AdminModel;
use app\common\model\Role as RoleModel;
use app\common\model\Rule as RuleModel;
use app\common\service\Jwt as JwtService;
use app\common\service\Redis as RedisService;
use think\facade\Db;
use think\facade\Log;
use think\Exception;

class Admin
{
    /*
     * 通过缓存获取管理员信息
     */
    public static function getAdminInfoByCache(int $id = 0) :array
    {
        $redis = RedisService::getInstance();
        $key = 'ADMIN:'.$id;
        $cacheInfo = $redis->get($key);
        if (!$cacheInfo) {
            $admin = AdminModel::findOrEmpty($id);
            if($admin->isEmpty()) throw new BadRequestException(['errorMessage' => '管理员不存在或已删除']);
            $admin = $admin->toArray();
            $redis->setex($key, 3600, json_encode($admin)); //缓存3600秒
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
        //写入缓存
        $redis = RedisService::getInstance();
        $redis->setex('ADMIN:'.$admin->id, 3600, json_encode($admin->toArray())); //缓存3600秒
        //返回数据
        return $admin;
    }

    /*
     * 通过id获取管理员令牌
     * 把id编码到token中
     */
    public static function getAdminTokenById(int $id = 0) :string
    {
        $token = JwtService::getInstance()
            ->setTableName('admin')
            ->setTableId($id)
            ->encode()
            ->getToken();
        return $token;
    }

    /*
     * 通过id验证管理员
     */
    public static function verifyAdminById(int $id = 0) :void
    {
        $admin = AdminModel::findOrEmpty($id);
        if($admin->isEmpty()) throw new UnauthorizedException(['errorMessage' => '管理员不存在或已删除']);
        if($admin->is_use == 2) throw new BadRequestException(['errorMessage' => '账号未启用']);
        $redis = RedisService::getInstance();
        $key = 'ADMIN:ID:' . $admin->id;
        $adminId = $redis->get($key);
        if(!$adminId){
            $redis->setex($key, 3600, $admin->id);
        }
    }

    /*
     * 通过管理员id删除缓存信息
     */
    public static function deleteCacheByAdminId(int $id = 0) :void
    {
        $redis = RedisService::getInstance();
        $redis->del('ADMIN:'.$id); //删除缓存
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
        $rules = RuleModel::where('id', 'in', $ids)->order(['sort'=>'asc','id'=>'desc'])->select()->toArray();
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

        $list = AdminModel::with(['role'])
            ->where($condition)
            ->order('create_time', 'desc')
            ->limit($limit)
            ->page($page)
            //->append(['is_use_text']) //追加其它的字段（该字段必须有定义获取器）
            ->select();
        $total = AdminModel::where($condition)
            ->count();

        return [$list, $total];
    }

    /*
     * 添加管理员信息
     */
    public static function addAdminInfo() :void
    {
        $count = AdminModel::where([
            ['email', '=', input('post.email')],
        ])->count();
        if($count) throw new BadRequestException(['errorMessage' => '邮箱已存在']);
        $count = AdminModel::where([
            ['mobile', '=', input('post.mobile')],
        ])->count();
        if($count) throw new BadRequestException(['errorMessage' => '电话已存在']);
        $admin = new AdminModel();
        $res = $admin->save(input('post.'));
        if(!$res) throw new BadRequestException(['errorMessage' => '失败']);
        //增加关联的中间表数据
        $admin->role()->saveAll(input('post.role'));
    }

    /*
     * 修改管理员信息
     */
    public static function editAdminInfo() :void
    {
        //启动事务
        Db::startTrans();
        try {
            //数据库加锁操作
            $info = AdminModel::lock(true)->findOrEmpty(input('post.id'));
            if($info->isEmpty()) throw new BadRequestException(['errorMessage' => '数据不存在或已删除']);
            $count = AdminModel::where([
                ['id', '<>', $info->id],
                ['email', '=', input('post.email')],
            ])->count();
            if($count) throw new BadRequestException(['errorMessage' => '邮箱已存在']);
            $count = AdminModel::where([
                ['id', '<>', $info->id],
                ['mobile', '=', input('post.mobile')],
            ])->count();
            if($count) throw new BadRequestException(['errorMessage' => '电话已存在']);
            $res = $info->save(input('post.'));
            if(!$res) throw new BadRequestException(['errorMessage' => '失败']);
            //更新关联的中间表数据
            $info->role()->detach();
            $info->role()->saveAll(input('post.role'));
            //更新缓存
            $redis = RedisService::getInstance();
            $key = 'ADMIN:'.$info->id;
            $cacheInfo = $redis->get($key);
            if($cacheInfo) $redis->setex($key, 3600, json_encode($info->toArray())); //缓存3600秒
            //sleep(10);
            //提交事务
            Db::commit();
        }catch (Exception $exception) {
            //回滚事务
            Db::rollback();
            Log::record('修改管理员信息--' . $exception->getMessage());
            throw new BadRequestException(['errorMessage' => $exception->getMessage()]);
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
            //删除缓存
            $redis = RedisService::getInstance();
            $key = 'ADMIN:'.$info->id;
            $cacheInfo = $redis->get($key);
            if($cacheInfo) $redis->del($key);
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
    public static function editAdminPassword() :void
    {
        $info = AdminModel::findOrEmpty(input('post.id'));
        if($info->isEmpty()) throw new BadRequestException(['errorMessage' => '数据不存在或已删除']);
        $res = $info->save(input('post.'));
        if(!$res) throw new BadRequestException(['errorMessage' => '失败']);
    }

    /*
     * 修改我的资料
     */
    public static function editMyProfile(int $id = 0) :void
    {
        $info = AdminModel::findOrEmpty($id);
        if($info->isEmpty()) throw new BadRequestException(['errorMessage' => '数据不存在或已删除']);
        $count = AdminModel::where([
            ['id', '<>', $info->id],
            ['email', '=', input('post.email')],
        ])->count();
        if($count) throw new BadRequestException(['errorMessage' => '邮箱已存在']);
        $count = AdminModel::where([
            ['id', '<>', $info->id],
            ['mobile', '=', input('post.mobile')],
        ])->count();
        if($count) throw new BadRequestException(['errorMessage' => '电话已存在']);
        $res = $info->save(input('post.'));
        if(!$res) throw new BadRequestException(['errorMessage' => '失败']);
        //更新缓存
        $redis = RedisService::getInstance();
        $key = 'ADMIN:'.$info->id;
        $cacheInfo = $redis->get($key);
        if ($cacheInfo) {
            $time = $redis->ttl($key); //返回剩余时间
            $data = $info->toArray();
            $redis->setex($key, $time, json_encode($data));
        }

    }

    /*
     * 修改我的密码
     */
    public static function editMyPassword(int $id = 0) :void
    {
        $info = AdminModel::findOrEmpty($id);
        if($info->isEmpty()) throw new BadRequestException(['errorMessage' => '数据不存在或已删除']);
        if(!password_verify(input('post.old_password'), $info->password)) throw new BadRequestException(['errorMessage' => '当前密码错误']);
        if($info->password == password_hash(input('post.password'), PASSWORD_DEFAULT)) throw new BadRequestException(['errorMessage' => '当前密码和新密码相同']);
        $res = $info->save(input('post.'));
        if(!$res) throw new BadRequestException(['errorMessage' => '失败']);
        //删除缓存
        $redis = RedisService::getInstance();
        $redis->del('ADMIN:'.$info->id);
    }




}
