<?php


namespace app\common\exception;


class BadRequest extends Base
{
    public $httpCode = 400;
    public $errorMessage = '错误请求';
    public $errorCode = 10001;
}
