<?php
declare (strict_types = 1);

namespace app\admin\controller;

use app\common\service\Rule as RuleService;
use app\common\validate\Rule as RuleValidate;
use think\response\Json;

class Rule extends Base
{
    /*
     * 列表
     */
    public function lists() :Json
    {
        list($data, $count) = RuleService::getRuleList();
        return returnResponse(200, '成功', $data, $count);
    }

    /*
     * 添加
     */
    public function add() :Json
    {
        (new RuleValidate())->goCheck('add');
        RuleService::addEditRuleInfo();
        return returnResponse(200, '成功', []);
    }

    /*
     * 修改
     */
    public function edit() :Json
    {
        (new RuleValidate())->goCheck('edit');
        RuleService::addEditRuleInfo();
        return returnResponse(200, '成功', []);
    }

    /*
     * 删除
     */
    public function delete() :Json
    {
        (new RuleValidate())->goCheck('delete');
        RuleService::deleteRuleInfo();
        return returnResponse(200, '成功', []);
    }

    /*
     * 获取父级菜单
     */
    public function pid() :Json
    {
        $list = RuleService::getPidList();
        return returnResponse(200, '成功', $list);
    }

    /*
     * 获取图标
     */
    public function icon() :Json
    {
        $list = RuleService::getIconByCache();
        return returnResponse(200, '成功', $list);
    }



    
}
