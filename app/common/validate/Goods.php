<?php
declare (strict_types = 1);

namespace app\common\validate;

use think\Validate;

class Goods extends Base
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'id' => ['require'],
        'first_goods_category_id' => ['require', 'number'],
        'second_goods_category_id' => ['require', 'number'],
        'third_goods_category_id' => ['require', 'number'],
        'name' => ['require'],
        'title' => ['require'],
        'surface_plot' => ['require'],
        'price' => ['require'],
        'sales' => ['require', 'number'],
        'stock' => ['require', 'number'],
        'popularity' => ['require', 'number'],
        'image' => ['require'],
        'video' => ['require'],
        'attribute_mean' => ['require'],
        'specification_mean' => ['require'],
        'attribute' => ['require'],
        'freight' => ['require'],
        'detail' => ['require'],
        'is_recommend_index' => ['require', 'in' => '1,2'],
        'is_recommend_hot' => ['require', 'in' => '1,2'],
        'is_recommend_blast' => ['require', 'in' => '1,2'],
        'status' => ['require', 'in' => '1,2'],
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [];

    /**
     * 验证场景
     *
     * @var array
     */
    protected $scene = [
        'add'  =>  ['first_goods_category_id','second_goods_category_id','third_goods_category_id','name','title','surface_plot','price','sales','stock','popularity','image','attribute_mean','specification_mean','attribute','freight','detail','is_recommend_index','is_recommend_hot','is_recommend_blast','status'],
        'edit'  =>  ['id','first_goods_category_id','second_goods_category_id','third_goods_category_id','name','title','surface_plot','price','sales','stock','popularity','image','attribute_mean','specification_mean','attribute','freight','detail','is_recommend_index','is_recommend_hot','is_recommend_blast','status'],
        'delete'  =>  ['id'],
    ];
}
