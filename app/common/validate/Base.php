<?php
declare (strict_types = 1);

namespace app\common\validate;

use app\common\exception\BadRequest as BadRequestException;
use think\facade\Request;
use think\Validate;

class Base extends Validate
{
    /**
     * 封装验证方法
     * @param string $scene 场景
     * @return bool
     * @throws BadRequestException
     */
    public function goCheck(string $scene) :bool
    {
        $param = Request::param();
        $result = $this->scene($scene)->check($param);
        if($result){
            return true;
        }else{
            throw new BadRequestException([
                'errorMessage' => $this->getError(),
            ]);
        }
    }
}
