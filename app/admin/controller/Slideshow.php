<?php
declare (strict_types = 1);

namespace app\admin\controller;

use app\common\service\Slideshow as SlideshowService;
use app\common\validate\Slideshow as SlideshowValidate;
use think\Request;
use think\response\Json;

class Slideshow extends Base
{
    /*
     * 列表
     */
    public function lists() :Json
    {
        list($data, $count) = SlideshowService::getSlideshowList();
        return returnResponse(200, '成功', $data, $count);
    }

    /*
     * 添加
     */
    public function add() :Json
    {
        (new SlideshowValidate())->goCheck('add');
        SlideshowService::addEditSlideshowInfo();
        return returnResponse(200, '成功', []);
    }

    /*
     * 修改
     */
    public function edit() :Json
    {
        (new SlideshowValidate())->goCheck('edit');
        SlideshowService::addEditSlideshowInfo();
        return returnResponse(200, '成功', []);
    }

    /*
     * 删除
     */
    public function delete() :Json
    {
        (new SlideshowValidate())->goCheck('delete');
        SlideshowService::deleteSlideshowInfo();
        return returnResponse(200, '成功', []);
    }
}
