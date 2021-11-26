<?php
declare (strict_types = 1);

namespace app\admin\controller;

use app\common\service\Admin as AdminService;
use app\common\service\Storage as StorageService;
use think\response\Json;

class Admin extends Base
{
    /*
     * 显示资源列表
     */
    public function index() :Json
    {
        $list = AdminService::getList();
        return json($list);
    }

    public function info() :Json
    {
        $info = AdminService::getAdminInfoByCache($this->adminId);
        $info && $info['roles'] = 'admin';
        return json($info);
    }

    /*
     * 上传
     */
    public function upload() :Json
    {
        $url = StorageService::upload();
        return json(['url' => $url]);
    }


}
