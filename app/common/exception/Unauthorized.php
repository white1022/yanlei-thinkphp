<?php


namespace app\common\exception;


class Unauthorized extends Base
{
    public $httpCode = 401;
    public $errorMessage = '未经授权';
    public $errorCode = 10002;
}
