<?php
declare(strict_types=1);

namespace app\common\service;

use app\common\model\Log as LogModel;

/*
 * 日志
 */
class Log
{
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
