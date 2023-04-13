<?php
declare (strict_types = 1);

namespace app\admin\controller;

use app\common\exception\BadRequest as BadRequestException;
use app\common\model\Goods as GoodsModel;
use app\common\model\Notice as NoticeModel;
use app\common\model\Readme as ReadmeModel;
use app\common\model\SystemSetup as SystemSetupModel;
use think\response\Json;

class H5 extends Base
{
    /*
     * 获取商品信息
     */
    public function goodsInfo() :Json
    {
        $id = input('get.id', 0);
        $info = GoodsModel::where('id', '=', $id)
            ->field(['detail'])
            ->findOrEmpty();
        if($info->isEmpty()) throw new BadRequestException(['errorMessage' => '数据不存在或已删除']);
        return returnResponse(200, '成功', $info);
    }

    /*
     * 获取公告信息
     */
    public function noticeInfo() :Json
    {
        $id = input('get.id', 0);
        $info = NoticeModel::where('id', '=', $id)
            ->field(['content'])
            ->findOrEmpty();
        if($info->isEmpty()) throw new BadRequestException(['errorMessage' => '数据不存在或已删除']);
        return returnResponse(200, '成功', $info);
    }

    /*
     * 获取文章信息
     */
    public function readmeInfo() :Json
    {
        $id = input('get.id', 0);
        $info = ReadmeModel::where('id', '=', $id)
            ->field(['content'])
            ->findOrEmpty();
        if($info->isEmpty()) throw new BadRequestException(['errorMessage' => '数据不存在或已删除']);
        return returnResponse(200, '成功', $info);
    }

    /*
     * 获取系统设置信息
     */
    public function systemSetupInfo() :Json
    {
        $field = input('get.field', '');
        $array = ['use_help', 'about_platform', 'platform_user_agreement', 'platform_privacy_policy'];
        if(!in_array($field, $array)) throw new BadRequestException(['errorMessage' => '字段不存在']);
        $info = SystemSetupModel::field([$field => 'field_name'])
            ->findOrEmpty(1);
        if($info->isEmpty()) throw new BadRequestException(['errorMessage' => '数据不存在或已删除']);
        return returnResponse(200, '成功', $info);
    }

    /*
     * 获取分享下载信息
     */
    public function shareDownloadInfo() :Json
    {
        $info = SystemSetupModel::field(['site_name', 'site_logo', 'platform_application'])
            ->findOrEmpty(1);
        if($info->isEmpty()) throw new BadRequestException(['errorMessage' => '数据不存在或已删除']);
        return returnResponse(200, '成功', $info);
    }

}
