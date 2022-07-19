<?php
declare (strict_types = 1);

namespace app\admin\controller;

use app\common\service\User as UserService;
use app\common\validate\User as UserValidate;
use think\response\Json;

class User extends Base
{
    /*
     * 列表
     */
    public function lists() :Json
    {
        list($data, $count) = UserService::getUserList();
        return returnResponse(200, '成功', $data, $count);
    }

    /*
     * 添加
     */
    public function add() :Json
    {
        (new UserValidate())->goCheck('add');
        UserService::addUserInfo();
        return returnResponse(200, '成功', []);
    }

    /*
     * 修改
     */
    public function edit() :Json
    {
        (new UserValidate())->goCheck('edit');
        UserService::editUserInfo();
        return returnResponse(200, '成功', []);
    }

    /*
     * 删除
     */
    public function delete() :Json
    {
        (new UserValidate())->goCheck('delete');
        UserService::deleteUserInfo();
        return returnResponse(200, '成功', []);
    }

    /*
     * 修改密码
     */
    public function password() :Json
    {
        (new UserValidate())->goCheck('password');
        UserService::editUserPassword();
        return returnResponse(200, '成功', []);
    }
}
