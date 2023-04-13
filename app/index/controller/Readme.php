<?php

namespace app\index\controller;

use app\common\model\ReadmeCategory as ReadmeCategoryModel;
use app\common\model\Readme as ReadmeModel;
use think\response\Json;

class Readme extends Base
{
    /*
     * 获取文章列表
     */
    public function getList() :Json
    {
        $readmeCategory = ReadmeCategoryModel::order(['sort'=>'asc','id'=>'desc'])
            ->select()
            ->toArray();
        $readme = ReadmeModel::select()
            ->toArray();

        foreach ($readmeCategory as &$item1){
            foreach ($readme as $item2){
                if($item2['readme_category_id'] == $item1['id']){
                    $item1['readme'] = $item2;
                }
            }
        }
        $list =  generateTree($readmeCategory);

        return returnResponse(200, '成功', $list);
    }
}