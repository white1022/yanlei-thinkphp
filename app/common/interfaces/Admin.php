<?php


namespace app\common\interfaces;


interface Admin extends Base
{
    //常量成员

    //抽象方法
    public function import();
    public function export();
}
