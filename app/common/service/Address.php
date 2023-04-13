<?php
declare (strict_types = 1);

namespace app\common\service;

use app\common\exception\BadRequest as BadRequestException;
use app\common\model\Address as AddressModel;
use app\common\model\Region as RegionModel;

class Address
{
    /*
     * 获取地址列表
     */
    public static function getAddressList() :array
    {
        list($page, $limit) = get_page_limit();
        $receiver = input('get.receiver', '');
        $mobile = input('get.mobile', '');
        $is_default = input('get.is_default', '');

        $condition = [
            ['user_id','=',input('get.user_id', 0)]
        ];
        if(!empty($receiver)){
            array_push($condition, ['receiver','like','%'.$receiver.'%']);
        }
        if(!empty($mobile)){
            array_push($condition, ['mobile','like','%'.$mobile.'%']);
        }
        if(!empty($is_default)){
            array_push($condition, ['is_default','=',$is_default]);
        }

        $list = AddressModel::with(['user'])
            ->where($condition)
            ->order('create_time', 'desc')
            ->limit($limit)
            ->page($page)
            ->select();
        $total = AddressModel::where($condition)
            ->count();

        return [$list, $total];
    }

    /*
     * 添加/修改地址信息
     */
    public static function addEditAddressInfo() :void
    {
        if(input('post.id')){
            $info = AddressModel::findOrEmpty(input('post.id'));
            if($info->isEmpty()) throw new BadRequestException(['errorMessage' => '数据不存在或已删除']);
            $res = $info->save(input('post.'));
        }else{
            $res = (new AddressModel())->save(input('post.'));
        }
        if(!$res) throw new BadRequestException(['errorMessage' => '失败']);
    }

    /*
     * 删除地址信息
     */
    public static function deleteAddressInfo() :void
    {
        $res = AddressModel::where([
            ['id', 'in', explode(',', input('post.id'))],
        ])->delete();
        if(!$res) throw new BadRequestException(['errorMessage' => '失败']);
    }
}