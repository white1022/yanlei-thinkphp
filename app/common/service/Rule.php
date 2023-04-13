<?php
declare (strict_types = 1);

namespace app\common\service;

use app\common\exception\BadRequest as BadRequestException;
use app\common\model\Rule as RuleModel;
use app\common\service\Redis as RedisService;

class Rule
{
    /*
     * 获取规则列表
     */
    public static function getRuleList() :array
    {
        list($page, $limit) = get_page_limit();
        $title = input('get.title', '');
        $jump = input('get.jump', '');
        $spread = input('get.spread', '');

        $condition = [];
        if(!empty($title)){
            array_push($condition, ['title','like','%'.$title.'%']);
        }
        if(!empty($jump)){
            array_push($condition, ['jump','like','%'.$jump.'%']);
        }
        if(!empty($spread)){
            array_push($condition, ['spread','=',$spread]);
        }

        $list = RuleModel::where($condition)
            ->order([
                'sort' => 'asc',
                'id' => 'desc',
            ])->limit($limit)
            ->page($page)
            ->select();
        $total = RuleModel::where($condition)
            ->count();

        return [$list, $total];
    }

    /*
     * 添加/修改规则信息
     */
    public static function addEditRuleInfo() :void
    {
        if(input('post.id')){
            $info = RuleModel::findOrEmpty(input('post.id'));
            if($info->isEmpty()) throw new BadRequestException(['errorMessage' => '数据不存在或已删除']);
            if($info->id == input('post.pid')) throw new BadRequestException(['errorMessage' => '父级不能为自己']);
            $res = $info->save(input('post.'));
        }else{
            $res = (new RuleModel())->save(input('post.'));
        }
        if(!$res) throw new BadRequestException(['errorMessage' => '失败']);
    }

    /*
     * 删除规则信息
     */
    public static function deleteRuleInfo() :void
    {
        $res = RuleModel::where([
            ['id', 'in', explode(',', input('post.id'))],
        ])->delete();
        if(!$res) throw new BadRequestException(['errorMessage' => '失败']);
    }

    /*
     * 获取父级列表
     */
    public static function getPidList() :array
    {
        return RuleModel::order(['sort'=>'asc','id'=>'desc'])->column('title', 'id');
    }

    /*
     * 通过缓存获取图标
     */
    public static function getIconByCache() :array
    {
        $redis = RedisService::getInstance();
        $cacheInfo = $redis->get('icon');
        if (!$cacheInfo) {
            $icon = get_icon();
            if(empty($icon)) throw new BadRequestException(['errorMessage' => '图标数据不存在']);
            $redis->setex('icon', 3600, json_encode($icon)); //缓存3600秒
        } else {
            $icon = json_decode($cacheInfo, true);
        }
        return $icon;
    }
}