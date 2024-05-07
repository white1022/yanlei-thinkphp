<?php
declare (strict_types = 1);

namespace app\common\service;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\Result\PngResult;

/*
 * 二维码
 */
class Qrcode
{
    //私有属性，用于保存实例
    private static $instance;

    //qrcode 生成器对象
    private static $builder;

    //qrcode 参数
    private static $option = [
        'data' => 'https://www.baidu.com',//自定义二维码内容
        'encoding' => 'UTF-8',//编码类型
        'errorCorrectionLevel' => '',//容错等级，分为L、M、Q、H四级
        'size' => 300,//二维码大小 px
        'margin' => 10,//二维码内容相对于整张图片的外边距 px
        'logoPath' => '',//二维码logo路径
        'labelText' => '',//二维码标签
    ];

    //构造方法私有化，防止外部创建实例
    private function __construct()
    {
        self::$builder = Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([])
            ->data(self::$option['data'])
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size(300)
            ->margin(10)
            ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
            //->logoPath(self::$option['logoPath'])
            ->labelText(self::$option['labelText'])
            ->labelFont(new NotoSans(20))
            ->labelAlignment(new LabelAlignmentCenter())
            ->build();
    }

    //克隆方法私有化，防止复制实例
    private function __clone()
    {
    }

    //公有方法，用于获取实例
    public static function getInstance(array $option = []) :Qrcode
    {
        if (is_null(self::$instance) || !empty($option)) {
            if (!empty($option)) {
                //合并参数
                self::$option = array_merge(self::$option, $option);
            }
            self::$instance = new Qrcode();
        }
        // 返回单例
        return self::$instance;
    }

    /*
     * 直接输出二维码
     */
    public function output()
    {
        header('Content-Type: '.self::$builder->getMimeType());
        echo self::$builder->getString();
        exit();
    }

    /*
     * 将其保存到文件中
     */
    public function save() :string
    {
        $dir = date('Ymd');
        $path = app()->getRootPath() . 'public/storage/' . $dir;
        if(!is_dir($path)){
            mkdir($path, 0777);
        }
        $file = md5(microtime()) . '.png';
        self::$builder->saveToFile($path . '/' . $file);
        $url = '/storage/'.$dir.'/'.$file;
        // 替换字符串
        return str_replace('\\', '/', $url);
    }

    /*
     * 生成一个数据URI来包含内联的图像数据
     * 例如：在img标签中使用，即<img src="data：image/png; base64,iVBORw ..."/>；也可以直接输入到浏览器地址栏中访问。
     */
    public function dataUri() :string
    {
        return self::$builder->getDataUri();
    }


}