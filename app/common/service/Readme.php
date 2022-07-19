<?php
declare (strict_types = 1);

namespace app\common\service;

use app\common\exception\BadRequest as BadRequestException;
use app\common\model\Readme as ReadmeModel;
use app\common\model\ReadmeCategory as ReadmeCategoryModel;

class Readme
{
    /*
     * 获取文章列表
     */
    public static function getReadmeList() :array
    {
        list($page, $limit) = get_page_limit();
        $readme_category_id = input('get.readme_category_id', '');

        $condition = [];
        if(!empty($readme_category_id)){
            array_push($condition, ['readme_category_id','=',$readme_category_id]);
        }

        $list = ReadmeModel::with(['readmeCategory'])
            ->where($condition)
            ->order('create_time', 'desc')
            ->limit($limit)
            ->page($page)
            ->select();
        $total = ReadmeModel::where($condition)
            ->count();

        return [$list, $total];
    }

    /*
     * 添加/修改文章信息
     */
    public static function addEditReadmeInfo() :void
    {
        if(input('post.id')){
            $info = ReadmeModel::findOrEmpty(input('post.id'));
            if($info->isEmpty()) throw new BadRequestException(['errorMessage' => '数据不存在或已删除']);
            $res = $info->save(input('post.'));
        }else{
            $res = (new ReadmeModel())->save(input('post.'));
        }
        if(!$res) throw new BadRequestException(['errorMessage' => '失败']);
    }

    /*
     * 删除文章信息
     */
    public static function deleteReadmeInfo() :void
    {
        $res = ReadmeModel::where([
            ['id', 'in', explode(',', input('post.id'))],
        ])->delete();
        if(!$res) throw new BadRequestException(['errorMessage' => '失败']);
    }

    /*
     * 获取文章分类列表
     */
    public static function getReadmeCategoryList() :array
    {
        $readmeCategory = ReadmeCategoryModel::order(['sort'=>'asc','id'=>'desc'])->select()->toArray();
        return generateTreeMap($readmeCategory);
    }

}