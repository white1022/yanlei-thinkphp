<?php
declare (strict_types = 1);

namespace app\admin\controller;

use app\common\service\Role as RoleService;
use app\common\validate\Role as RoleValidate;
use think\response\Json;

class Role extends Base
{
    /*
     * 列表
     */
    public function lists() :Json
    {
        list($data, $count) = RoleService::getRoleList();
        return returnResponse(200, '成功', $data, $count);
    }

    /*
     * 添加
     */
    public function add() :Json
    {
        (new RoleValidate())->goCheck('add');
        RoleService::addEditRoleInfo();
        return returnResponse(200, '成功', []);
    }

    /*
     * 修改
     */
    public function edit() :Json
    {
        (new RoleValidate())->goCheck('edit');
        RoleService::addEditRoleInfo();
        return returnResponse(200, '成功', []);
    }

    /*
     * 删除
     */
    public function delete() :Json
    {
        (new RoleValidate())->goCheck('delete');
        RoleService::deleteRoleInfo();
        return returnResponse(200, '成功', []);
    }

    /*
     * 获取规则
     */
    public function rule() :Json
    {
        $list = RoleService::getRuleList();
        return returnResponse(200, '成功', $list);
    }

}
