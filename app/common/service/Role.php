<?php
declare (strict_types = 1);

namespace app\common\service;

use app\common\exception\BadRequest as BadRequestException;
use app\common\model\Role as RoleModel;
use app\common\model\Rule as RuleModel;

class Role
{
    /*
     * 获取角色列表
     */
    public static function getRoleList() :array
    {
        list($page, $limit) = get_page_limit();
        $name = input('get.name', '');
        $is_use = input('get.is_use', '');

        $condition = [];
        if(!empty($name)){
            array_push($condition, ['name','like','%'.$name.'%']);
        }
        if(!empty($is_use)){
            array_push($condition, ['is_use','=',$is_use]);
        }

        $list = RoleModel::where($condition)
            ->order('create_time', 'desc')
            ->limit($limit)
            ->page($page)
            ->select();
        $total = RoleModel::where($condition)
            ->count();

        return [$list, $total];
    }

    /*
     * 添加/修改角色信息
     */
    public static function addEditRoleInfo() :void
    {
        if(input('post.id')){
            $info = RoleModel::findOrEmpty(input('post.id'));
            if($info->isEmpty()) throw new BadRequestException(['errorMessage' => '数据不存在或已删除']);
            $res = $info->save(input('post.'));
        }else{
            $res = (new RoleModel())->save(input('post.'));
        }
        if(!$res) throw new BadRequestException(['errorMessage' => '失败']);
    }

    /*
     * 删除角色信息
     */
    public static function deleteRoleInfo() :void
    {
        $ids = explode(',', input('post.id'));
        foreach ($ids as $id){
            $info = RoleModel::where('id', '=', $id)
                ->findOrEmpty();
            if($info->isEmpty()) throw new BadRequestException(['errorMessage' => '数据不存在或已删除']);
            $res = $info->delete();
            if(!$res) throw new BadRequestException(['errorMessage' => '失败']);
            //删除关联的中间表数据
            $info->admin()->detach();
        }
    }

    /*
     * 获取规则列表
     */
    public static function getRuleList() :array
    {
        $rules = RuleModel::order(['sort'=>'asc','id'=>'desc'])->select()->toArray();
        return generateTree($rules);
    }
}