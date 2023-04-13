<?php
declare (strict_types = 1);

namespace app\admin\controller;

use app\common\service\ReadmeCategory as ReadmeCategoryService;
use app\common\validate\ReadmeCategory as ReadmeCategoryValidate;
use think\Request;
use think\response\Json;

class ReadmeCategory extends Base
{
    /*
     * 列表
     */
    public function lists() :Json
    {
        list($data, $count) = ReadmeCategoryService::getReadmeCategoryList();
        return returnResponse(200, '成功', $data, $count);
    }

    /*
     * 添加
     */
    public function add() :Json
    {
        (new ReadmeCategoryValidate())->goCheck('add');
        ReadmeCategoryService::addEditReadmeCategoryInfo();
        return returnResponse(200, '成功', []);
    }

    /*
     * 修改
     */
    public function edit() :Json
    {
        (new ReadmeCategoryValidate())->goCheck('edit');
        ReadmeCategoryService::addEditReadmeCategoryInfo();
        return returnResponse(200, '成功', []);
    }

    /*
     * 删除
     */
    public function delete() :Json
    {
        (new ReadmeCategoryValidate())->goCheck('delete');
        ReadmeCategoryService::deleteReadmeCategoryInfo();
        return returnResponse(200, '成功', []);
    }

    /*
     * 获取父级分类
     */
    public function pid() :Json
    {
        $list = ReadmeCategoryService::getPidList();
        return returnResponse(200, '成功', $list);
    }

}
