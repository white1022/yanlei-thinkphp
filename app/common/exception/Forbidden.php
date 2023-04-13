<?php


namespace app\common\exception;


class Forbidden extends Base
{
    public $httpCode = 403;
    public $errorMessage = '权限不足';
    public $errorCode = 10003;
}
