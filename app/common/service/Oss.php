<?php
declare(strict_types=1);

namespace app\common\service;

use app\common\exception\BadRequest as BadRequestException;
use OSS\OssClient;
use think\facade\Filesystem;
use think\facade\Request;
use think\response\File;

/*
 * 云存储
 */
class Oss
{
    //私有属性
    private $ossClient;

    //构造方法
    public function __construct($endpointDomain = '')
    {
        $endpointDomain = empty($endpointDomain) ? config('config.aliyunoss.endpointDomain') : $endpointDomain;
        $this->ossClient = new OssClient(config('config.aliyunoss.accessKeyId'), config('config.aliyunoss.accessKeySecret'), $endpointDomain);
    }

    /*
     * 上传
     */
    public function upload(string $name = 'file') :string
    {
        // 获取上传文件
        $file = Request::file($name);
        if(!$file) throw new BadRequestException(['errorMessage' => '文件不存在']);

        /*$array['realPath'] = $file->getRealPath(); //获取存储在服务器的临时文件
        $array['originalName'] = $file->getOriginalName(); //上传文件名
        $array['originalExtension'] = $file->getOriginalExtension(); //获取上传文件扩展名
        $array['originalMime'] = $file->getOriginalMime(); //获获取上传文件类型信息*/

        $bucket= config('config.aliyunoss.bucketName'); //设置存储空间
        $objct = date('Ymd') . '/' . md5(microtime()) . '.' . $file->getOriginalExtension(); //设置上传文件的相对路径
        $content = file_get_contents($file->getRealPath()); //获取存储在服务器的临时文件，并把整个文件读入一个字符串中

        $info = $this->ossClient->putObject($bucket, $objct, $content);

        //https://help.aliyun.com/document_detail/31902.htm?spm=a2c4g.11186623.0.0.2f48535anx2L2O
        //return $info['oss-request-url']; //默认是下载行为，想要预览需要绑定自定义域名

        return '/' . $objct;
    }

    /*
     * 下载
     */
    public function download(string $file_name = '', string $name = '') :File
    {
        // todo 不知道行不行
        return download($file_name, $name);
    }


}
