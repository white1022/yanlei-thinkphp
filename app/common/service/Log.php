<?php
declare(strict_types=1);

namespace app\common\service;

use app\common\exception\BadRequest as BadRequestException;
use app\common\model\Log as LogModel;

/*
 * 日志
 */
class Log
{
    /*
     * 获取日志列表
     */
    public static function getLogList() :array
    {
        list($page, $limit) = get_page_limit();
        $type = input('get.type', '');
        $content = input('get.content', '');

        $condition = [];
        if(!empty($type)){
            array_push($condition, ['type','=',$type]);
        }
        if(!empty($content)){
            array_push($condition, ['content','like','%'.$content.'%']);
        }

        $list = LogModel::where($condition)
            ->order('create_time', 'desc')
            ->limit($limit)
            ->page($page)
            ->select();
        $total = LogModel::where($condition)
            ->count();

        return [$list, $total];
    }

    /*
     * 删除日志信息
     */
    public static function deleteLogInfo() :void
    {
        $res = LogModel::where([
            ['id', 'in', explode(',', input('post.id'))],
        ])->delete();
        if(!$res) throw new BadRequestException(['errorMessage' => '失败']);
    }

    /*
     * 保存日志
     * type：1管理员，2用户
     * operator_id：操作员id
     * content：内容
     */
    public static function save(int $type = 0, int $operator_id = 0, string $content = '') :bool
    {
        $data['type'] = $type;
        $data['operator_id'] = $operator_id;
        $data['content'] = $content;
        return (new LogModel())->save($data);
    }
}
