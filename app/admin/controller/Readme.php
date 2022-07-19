<?php
declare (strict_types = 1);

namespace app\admin\controller;

use app\common\service\Readme as ReadmeService;
use app\common\validate\Readme as ReadmeValidate;
use think\Request;
use think\response\Json;

class Readme extends Base
{
    /*
     * 列表
     */
    public function lists() :Json
    {
        list($data, $count) = ReadmeService::getReadmeList();
        return returnResponse(200, '成功', $data, $count);
    }

    /*
     * 添加
     */
    public function add() :Json
    {
        (new ReadmeValidate())->goCheck('add');
        ReadmeService::addEditReadmeInfo();
        return returnResponse(200, '成功', []);
    }

    /*
     * 修改
     */
    public function edit() :Json
    {
        (new ReadmeValidate())->goCheck('edit');
        ReadmeService::addEditReadmeInfo();
        return returnResponse(200, '成功', []);
    }

    /*
     * 删除
     */
    public function delete() :Json
    {
        (new ReadmeValidate())->goCheck('delete');
        ReadmeService::deleteReadmeInfo();
        return returnResponse(200, '成功', []);
    }

    /*
     * 获取文章分类
     */
    public function getReadmeCategory() :Json
    {
        $list = ReadmeService::getReadmeCategoryList();
        return returnResponse(200, '成功', $list);
    }

}
