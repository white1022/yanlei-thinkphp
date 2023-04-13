<?php
declare (strict_types = 1);

namespace app\admin\controller;

use app\common\service\Admin as AdminService;
use app\common\validate\Admin as AdminValidate;
use think\response\Json;

class Admin extends Base
{
    /*
     * 列表
     */
    public function lists() :Json
    {
        list($data, $count) = AdminService::getAdminList();
        return returnResponse(200, '成功', $data, $count);
    }

    /*
     * 添加
     */
    public function add() :Json
    {
        (new AdminValidate())->goCheck('add');
        AdminService::addAdminInfo();
        return returnResponse(200, '成功', []);
    }

    /*
     * 修改
     */
    public function edit() :Json
    {
        (new AdminValidate())->goCheck('edit');
        AdminService::editAdminInfo();
        return returnResponse(200, '成功', []);
    }

    /*
     * 删除
     */
    public function delete() :Json
    {
        (new AdminValidate())->goCheck('delete');
        AdminService::deleteAdminInfo();
        return returnResponse(200, '成功', []);
    }

    /*
     * 获取侧边栏菜单
     */
    public function getMenu() :Json
    {
        $list = AdminService::getMenuByAdminId($this->adminId);
        return returnResponse(200, '成功', $list);
    }

    /*
     * 获取管理员信息
     */
    public function info() :Json
    {
        $info = AdminService::getAdminInfoByCache($this->adminId);
        return returnResponse(200, '成功', $info);
    }

    /*
     * 获取角色
     */
    public function role() :Json
    {
        $list = AdminService::getRoleList();
        return returnResponse(200, '成功', $list);
    }

    /*
     * 修改密码
     */
    public function password() :Json
    {
        (new AdminValidate())->goCheck('password');
        AdminService::editAdminPassword();
        return returnResponse(200, '成功', []);
    }

    /*
     * 修改我的资料
     */
    public function editMyProfile() :Json
    {
        (new AdminValidate())->goCheck('my_profile');
        AdminService::editMyProfile($this->adminId);
        return returnResponse(200, '成功', []);
    }

    /*
     * 修改我的密码
     */
    public function editMyPassword() :Json
    {
        (new AdminValidate())->goCheck('my_password');
        AdminService::editMyPassword($this->adminId);
        return returnResponse(200, '成功', []);
    }






}
