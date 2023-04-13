<?php
declare (strict_types = 1);

namespace app\common\service;

use app\common\exception\BadRequest as BadRequestException;
use app\common\model\Express as ExpressModel;

class Express
{
    /*
     * 获取快递列表
     */
    public static function getExpressList() :array
    {
        list($page, $limit) = get_page_limit();
        $name = input('get.name', '');

        $condition = [];
        if(!empty($name)){
            array_push($condition, ['name','like','%'.$name.'%']);
        }

        $list = ExpressModel::where($condition)
            ->order('create_time', 'desc')
            ->limit($limit)
            ->page($page)
            ->select();
        $total = ExpressModel::where($condition)
            ->count();

        return [$list, $total];
    }

    /*
     * 添加/修改快递信息
     */
    public static function addEditExpressInfo() :void
    {
        if(input('post.id')){
            $info = ExpressModel::findOrEmpty(input('post.id'));
            if($info->isEmpty()) throw new BadRequestException(['errorMessage' => '数据不存在或已删除']);
            $res = $info->save(input('post.'));
        }else{
            $res = (new ExpressModel())->save(input('post.'));
        }
        if(!$res) throw new BadRequestException(['errorMessage' => '失败']);
    }

    /*
     * 删除快递信息
     */
    public static function deleteExpressInfo() :void
    {
        $res = ExpressModel::where([
            ['id', 'in', explode(',', input('post.id'))],
        ])->delete();
        if(!$res) throw new BadRequestException(['errorMessage' => '失败']);
    }
}