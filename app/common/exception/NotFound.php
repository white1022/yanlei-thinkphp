<?php


namespace app\common\exception;


class NotFound extends Base
{
    public $httpCode = 404;
    public $errorMessage = '资源未找到';
    public $errorCode = 10004;
}
