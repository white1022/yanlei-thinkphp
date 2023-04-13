<?php
declare (strict_types = 1);

namespace app\common\exception;

use think\Exception;
use Throwable;

class Base extends Exception
{
    // HTTP状态码
    public $httpCode = 200;

    // 错误具体信息
    public $errorMessage = '请求成功';

    // 自定义错误码
    public $errorCode = 10000;

    public function __construct(array $param = [], Throwable $previous = null)
    {
        if (!is_array($param)) {
            // throw new Exception('参数必须是数组');
            return ;
        }
        if (array_key_exists('httpCode', $param)) {
            $this->httpCode = $param['httpCode'];
        }
        if (array_key_exists('errorMessage', $param)) {
            $this->errorMessage = $param['errorMessage'];
        }
        if (array_key_exists('errorCode', $param)) {
            $this->errorCode = $param['errorCode'];
        }

        parent::__construct($this->errorMessage, $this->errorCode, $previous);
    }
}
