<?php
declare (strict_types = 1);

namespace app\common\service;

use app\common\exception\BadRequest as BadRequestException;
use app\common\model\Readme as ReadmeModel;
use app\common\model\ReadmeCategory as ReadmeCategoryModel;

class ReadmeCategory
{
    /*
     * 获取文章分类列表
     */
    public static function getReadmeCategoryList() :array
    {
        list($page, $limit) = get_page_limit();
        $pid = input('get.pid', '');
        $name = input('get.name', '');

        $condition = [];
        if(!empty($pid)){
            array_push($condition, ['pid','=',$pid]);
        }
        if(!empty($name)){
            array_push($condition, ['name','like','%'.$name.'%']);
        }

        $list = ReadmeCategoryModel::where($condition)
            ->order('create_time', 'desc')
            ->limit($limit)
            ->page($page)
            ->select();
        $total = ReadmeCategoryModel::where($condition)
            ->count();

        return [$list, $total];
    }

    /*
     * 添加/修改文章分类信息
     */
    public static function addEditReadmeCategoryInfo() :void
    {
        if(input('post.id')){
            $info = ReadmeCategoryModel::findOrEmpty(input('post.id'));
            if($info->isEmpty()) throw new BadRequestException(['errorMessage' => '数据不存在或已删除']);
            if($info->id == input('post.pid')) throw new BadRequestException(['errorMessage' => '父级不能为自己']);
            $res = $info->save(input('post.'));
        }else{
            $res = (new ReadmeCategoryModel())->save(input('post.'));
        }
        if(!$res) throw new BadRequestException(['errorMessage' => '失败']);
    }

    /*
     * 删除文章分类信息
     */
    public static function deleteReadmeCategoryInfo() :void
    {
        $ids = explode(',', input('post.id'));
        foreach ($ids as $id){
            $info = ReadmeCategoryModel::where('id', '=', $id)
                ->findOrEmpty();
            if($info->isEmpty()) throw new BadRequestException(['errorMessage' => '数据不存在或已删除']);
            $count = ReadmeCategoryModel::where('pid', '=', $info->id)
                ->count();
            if($count) throw new BadRequestException(['errorMessage' => '存在下级文章分类']);
            $count = ReadmeModel::where('readme_category_id', '=', $info->id)
                ->count();
            if($count) throw new BadRequestException(['errorMessage' => '存在文章正在使用该分类']);
            $res = $info->delete();
            if(!$res) throw new BadRequestException(['errorMessage' => '失败']);
        }
    }

    /*
     * 获取父级列表
     */
    public static function getPidList() :array
    {
        $readmeCategory = ReadmeCategoryModel::order(['sort'=>'asc','id'=>'desc'])->select()->toArray();
        return generateTreeMap($readmeCategory);
    }
}