<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

/*
 * 加密
 * 加密方式为 转json、然后加上一个随机6位的数字，之后base64
 */
function encryption($data)
{
    return base64_encode(json_encode($data) . rand(100000, 999999));
}

/*
 * 解密
 */
function decryption($data)
{
    return json_decode(substr(base64_decode($data), 0, -6), true);
}

/*
 * curl发送http请求
 */
function curl($url='', $method='', $header=[], $param=[], $ssl=false){
    if(is_array($param)){
        $param = http_build_query($param);
    }
    $ch = curl_init(); //初始化CURL句柄
    curl_setopt($ch, CURLOPT_URL, $url); //设置请求的URL
    //是否验证服务器证书
    if($ssl){
        // 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
        // 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    }else{
        //如果是https请求时，需要禁止ssl验证
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
    }
    curl_setopt ($ch, CURLOPT_HTTPHEADER, $header); //设置报文，如array('Content-type:application/json')
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST,$method); //设置请求方式，如"GET"，"POST"
    curl_setopt($ch, CURLOPT_POSTFIELDS, $param);//设置提交的字符串
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,true); //设为true把curl_exec()结果转化为字串，而不是直接输出
    $output = curl_exec($ch); //执行CURL，返回json字符串
    curl_close($ch); //释放CURL句柄
    return json_decode($output,true);
}

/*
 * 生成无限极分类树
 * 数据 $array
 */
function generateTree($array = [])
{
    /*$array = array(
        array('id' => 1, 'name' => '管理员', 'pid' => '0', 'level' =>0) ,
        array('id' => 2, 'name' => '二级组', 'pid' => '1', 'level' =>1) ,
        array('id' => 3, 'name' => '二级组', 'pid' => '1', 'level' =>1) ,
        array('id' => 4, 'name' => '三级组', 'pid' => '2', 'level' =>2) ,
        array('id' => 5, 'name' => '二级组', 'pid' => '1', 'level' =>1) ,
        array('id' => 6, 'name' => '三级组', 'pid' => '3', 'level' =>2) ,
    );*/

    //第一步 构造数据  （循环、函数 两种方法可以实现）
    $items = array_column($array,NULL,'id');

    //第二部 遍历数据 生成树状结构
    $tree = array();
    foreach($items as $key => $item){
        if(isset($items[$item['pid']])){
            $items[$item['pid']]['children'][] = &$items[$key];
        }else{
            $tree[] = &$items[$key];
        }
    }
    //这里树形结构已经生成 没有使用递归方法
    return $tree;
}

/*
 * 获取地区树
 * 数据 $data
 * 父级 $pid
 * 层级 $level
 */
function getRegionTree($data, $pid = 0, $level = 0)
{
    static $tree = array();
    foreach($data as $k=>$v){
        if($v['pid'] == $pid){
            $v['level'] = $level;
            $tree[]=$v;
            getRegionTree($data,$v['id'],$level+1);
        }
    }
    //这里树形结构已经生成 使用递归方法
    return $tree;
}

/*
 * 生成无限极分类树图谱
 * 数据 $data
 * 父级 $pid
 * 前缀 $prefix
 */
function generateTreeMap($data = [], $pid = 0, $prefix = '')
{
    /*$data = array(
        array('id' => 1, 'name' => '管理员', 'pid' => '0') ,
        array('id' => 2, 'name' => '二级组', 'pid' => '1') ,
        array('id' => 3, 'name' => '二级组', 'pid' => '1') ,
        array('id' => 4, 'name' => '三级组', 'pid' => '2') ,
        array('id' => 5, 'name' => '二级组', 'pid' => '1') ,
        array('id' => 6, 'name' => '三级组', 'pid' => '3') ,
    );*/

    static $icon = array('│', '├', '└');
    static $nbsp = " ";
    static $arr = array();
    $number = 1;
    foreach($data as $row) {
        if($row['pid'] == $pid) {
            $brotherCount = 0;
            //判断当前有多少个兄弟分类
            foreach($data as $r) {
                if($row['pid'] == $r['pid']) {
                    $brotherCount++;
                }
            }
            if($brotherCount >0) {
                $j = $k = '';
                if($number == $brotherCount) {
                    $j .= $icon[2];
                    $k = $prefix ? $nbsp : '';
                }else{
                    $j .= $icon[1];
                    $k = $prefix ? $icon[0] : '';
                }
                $spacer = $prefix ? $prefix . $j : '';
                $row['name'] = $spacer.$row['name'];
                $arr[] = $row;
                $number++;
                generateTreeMap($data, $row['id'], $prefix . $k . $nbsp);
            }
        }
    }
    return  $arr;
}

/*
 * 获取分页信息
 * page  页码
 * limit 每页显示条数
 */
function get_page_limit() :array
{
    return [input('get.page/d', 1), input('get.limit/d', 15)];
}

/*
 * 返回请求数据
 * $code 状态码（200成功，400失败，401登录状态失效）
 * $message 提示消息（支持多语言）
 * $data 数据
 * $count 数据总数，可选项 如果是渲染layui的table则需要此字段
 */
function returnResponse($code = 200, $message = '', $data = [], $count = null)
{
    //dump($message);exit();
    $array['code'] = $code;
    $array['message'] = $message; //lang($message)
    $array['data'] = $data;
    !is_null($count) && $array['count'] = $count;
    return json($array, 200);
}

/*
 * 返回请求数据
 * code 状态码（200成功，400失败）
 * message 提示消息（支持多语言）
 * data 数据
 * count 数据总数，可选项 如果是渲染layui的table则需要此字段
 */
/*function returnResponse($array = [])
{
    //dump($message);exit();
    $code = $array['code'] ?? 200;
    $data['message'] = $array['message'] ?? ''; //lang($array['message'])
    $data['data'] = $array['data'] ?? [];
    isset($array['count']) && $data['count'] = $array['count'];
    return json($data, $code);
}*/

/*
 * 语言切换
 */
function change_lang($type)
{
    switch ($type) {
        case 'zh-cn':
            $lang = 'zh-cn';
            cookie('think_var', 'zh-cn');
            break;
        case 'en-us':
            $lang = 'en-us';
            cookie('think_var', 'en-us');
            break;
        default:
            $lang = '';
            break;
    }
    return $lang;
}

/*
 * 获取/采集图标
 */
function get_icon()
{
    //如果缓存为空、则写入文件
    //$url = "http://fontawesome.dashgame.com/";
    $url = "https://layui.itze.cn/doc/element/icon.html#table";
    $content = file_get_contents($url);
    //preg_match_all('/<i\s+class="fa\s+([^"]+)"\s+aria-hidden="true">/is', $content, $icon);
    preg_match_all('/<i\s+class="layui-icon\s+([^"]+)">/is', $content, $icon);
    return $icon[1] ?? [];
}

/*
 * 生成随机字符串
 * $length 随机字符串的长度
 * $type 类型：1大写+小写+数字，2大写，3小写，4数字，5大写+数字
 */
function random_string($length = 16, $type = 1)
{
    $array = [
        1 => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
        2 => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
        3 => 'abcdefghijklmnopqrstuvwxyz',
        4 => '0123456789',
        5 => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'
    ];
    $chars = $array[$type];
    $str = "";
    for ($i = 0; $i < $length; $i++) {
        $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
}

/*
 * 判断字符串是否是合法的json字符串
 */
function is_json($string = ''){
    json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE);
}

/*
 * 获取剩余时间的 日,时,分,秒展示
 * $expire_time 到期时间
 */
function time2day_hour_min_second($expire_time){
    $time = $expire_time - time();
    $data = [];
    if($time > 0){
        $day = floor($time/86400);
        $hour = floor(($time - $day * 86400)/3600);
        $min = floor((($time - $day * 86400) - $hour * 3600)/60);
        $second = floor((($time - $day * 86400) - $hour * 3600) - $min * 60);

        $data['day'] = $day;
        $data['hour'] = $hour;
        $data['min'] = $min;
        $data['second'] = $second;
    }
    return $data;
}

/*
 * 字符串转十进制
 */
function str_to_hex($str){
    $hex="";
    for($i=0;$i<strlen($str);$i++){
        //str_pad 使用另一个字符串填充字符串为指定长度
        //dechex 十进制转换为十六进制
        $hex.=str_pad(dechex(ord($str[$i])), 2, "0", STR_PAD_LEFT);

    }
    return strtoupper($hex);
}

/*
 * crc16/ModBus校验
 * $str 需要校验的字符串 例:010313010001
 */
function crc16_ModBus($str)
{
    //pack 函数把数据装入一个二进制字符串
    $data = pack('H*', $str);
    $crc = 0xFFFF;
    for ($i = 0; $i < strlen($data); $i++) {
        $crc ^= ord($data[$i]);
        for ($j = 8; $j != 0; $j--) {
            if (($crc & 0x0001) != 0) {
                $crc >>= 1;
                $crc ^= 0xA001;
            } else {
                $crc >>= 1;
            }
        }
    }
    //sprintf 函数返回已格式化的字符串
    $crc_str = sprintf("%X", $crc);
    echo '$crc_str = ' . $crc_str . PHP_EOL;
    return $crc_str;
}

/*
 * 处理crc验证码的顺序
 */
function handle_crc_str($crc_str)
{
    $array = str_split($crc_str, 2);
    $new_crc_str = $array[1] . $array[0];
    echo '$new_crc_str = ' . $new_crc_str . PHP_EOL;
    return $new_crc_str;
}

/**
 * PHP精确计算  主要用于货币的计算用
 * @param string $n1 第一个数
 * @param string $symbol 计算符号 + - * / %
 * @param string $n2 第二个数
 * @param int $scale  精度 默认为小数点后两位
 * @return  string
 */
function pricecalc($n1, $symbol, $n2, $scale = 2)
{
    switch ($symbol) {
        case "+"://加法
            $res = bcadd($n1, $n2, $scale);
            break;
        case "-"://减法
            $res = bcsub($n1, $n2, $scale);
            break;
        case "*"://乘法
            $res = bcmul($n1, $n2, $scale);
            break;
        case "/"://除法
            $res = bcdiv($n1, $n2, $scale);
            break;
        case "%"://求余、取模
            $res = bcmod($n1, $n2, $scale);
            break;
        default:
            $res = "0";
            break;
    }
    return $res;
}


/**
 * 价格格式化
 * @param int $price
 */
function priceformat($price)
{
    return number_format($price, 2, '.', '');
}

/**
 * 弥补差额
 * @param string $fixed 固定的数额
 * @param string $accumulated 累计的数额
 * @param string $incremental 增加的数额
 * @param int $scale 精度 默认为小数点后零位
 * @return  string
 */
function make_up_difference($fixed, $accumulated, $incremental, $scale = 2)
{
    $value = '0.00';

    $maximum = pricecalc($fixed, '-', $accumulated, $scale);
    if(bccomp($maximum, '0.00', $scale) == 1){
        if(bccomp($incremental, $maximum, $scale) == 1){
            $value = $maximum;
        }else{
            $value = $incremental;
        }
    }
    return $value;
}