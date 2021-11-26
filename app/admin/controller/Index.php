<?php
declare (strict_types = 1);

namespace app\admin\controller;

use app\common\service\Storage as StorageService;

class Index extends Base
{
    public function upload()
    {
        $data = [
            'title' => '石榴树上结樱桃',
            'author' => '李洱',
            'publisher' => '人民文学出版社',
            'language' => '中文',
            'rootFile' => 'http://www.baidu.com',
            'filePath' => 'http://www.baidu.com',
            'unzipPath' => 'http://www.baidu.com',
            'coverPath' => 'http://www.baidu.com',
            'originalName' => 'http://www.baidu.com',
            'cover' => 'http://yanlei.cn/storage/20210127/f002ed8b60ab43d7f90cb472c54504ca.gif',
            'contents' => '',
        ];
        return json($data);
    }

    public function create()
    {
        $param = input('post.');
        return json(['message' => '操作成功123']);
    }

    public function update()
    {
        $param = input('post.');
        return json(['message' => '操作成功456']);
    }

    public function get()
    {
        $param = input('get.');
        $data = [
            'title' => '石榴树上结樱桃',
            'author' => '李洱',
            'publisher' => '人民文学出版社',
            'language' => '中文',
            'rootFile' => 'http://www.baidu.com',
            'filePath' => 'http://www.baidu.com',
            'unzipPath' => 'http://www.baidu.com',
            'coverPath' => 'http://www.baidu.com',
            'originalName' => 'http://www.baidu.com',
            'cover' => 'http://yanlei.cn/storage/20210127/f002ed8b60ab43d7f90cb472c54504ca.gif',
            'contents' => '',
        ];
        return json($data);
    }
}
