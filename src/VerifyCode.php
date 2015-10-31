<?php
namespace QuarkPHP;

class VerifyCode {
    //-------------- 验证码绘制参数 -------------------
    public static $ImageWidth = 85; //图片宽度
    public static $ImageHeight = 25; //图片高度
    public static $ImageBgcolor = array(255, 255, 255); //图片的背景颜色
    public static $StrCount = 4; //显示字符数量
    public static $FontFace = '/data/www/simhei.ttf'; //字体文件的绝对路径
    public static $FontSize = 18; //文字大小(像素)
    public static $FontRotate = 30; //文字旋转角度(0-180)
    public static $FontSpace = 3; //文字间距
    public static $DisturbLine = 15; //干扰曲线数量
    public static $DisturbPixel = 100; //干扰噪点数量
    public static $VarName = 'vcode';   //验证码session变量的名称
    //允许出现的字符，可以是汉字
    public static $AllowStr = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'J', 'K', 'M', 'P', 'R', 'S', 'U', 'W', 'X', 'Y', 'Z');

    public static function show() {
        //缓存控制
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: no-cache');
        header('Content-type: image/jpeg');

        $vcode = '';
        //按参数中的宽高来创建一个图像
        $image = imagecreatetruecolor(self::$ImageWidth, self::$ImageHeight);
        //定义图像的背景色
        $bgColor = imagecolorallocate($image, self::$ImageBgcolor[0], self::$ImageBgcolor[1], self::$ImageBgcolor[2]);
        //按高宽绘制一个矩形
        imagerectangle($image, 1, 1, self::$ImageWidth, self::$ImageHeight, $bgColor);
        //填充背景颜色
        imagefill($image, 0, 0, $bgColor);

        //循环绘制干扰曲线
        for ($i = 0; $i < self::$DisturbLine; $i++) {
            //随机定义线条颜色
            $lineColor = imagecolorallocate($image, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
            //随机位置，角度和弧度来绘制曲线
            imagearc($image, mt_rand(-10, self::$ImageWidth), mt_rand(-10, self::$ImageHeight), mt_rand(30, 300), mt_rand(20, 200), 55, 44, $lineColor);
        }

        //循环绘制干扰噪点
        for ($i = 0; $i < self::$DisturbPixel; $i++) {
            //定义随机噪点颜色
            $pixelColor = imagecolorallocate($image, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
            //绘制噪点
            imagesetpixel($image, mt_rand(0, self::$ImageWidth), mt_rand(0, self::$ImageHeight), $pixelColor);
        }

        //从预定义字符数组中随机抽取出指定数量的键名，并复制到一个新的数组
        $randKey = array_rand(self::$AllowStr, self::$StrCount);

        //计算文字的顶距
        $topSpace = self::$ImageHeight - ((self::$ImageHeight - self::$FontSize) / 2);
        $i = 0;
        //循环绘制字符
        foreach ($randKey as $key) {
            $i++;
            //拼接字符
            $vcode .= self::$AllowStr[$key];
            //随机定义字符颜色
            $fontColor = imagecolorallocate($image, mt_rand(0, 170), mt_rand(0, 170), mt_rand(0, 170));
            //写入文字（图像，文字大小，旋转角度，文字间距，文字顶距，文字颜色，字体，字符）
            imagettftext($image, self::$FontSize, mt_rand(-self::$FontRotate, self::$FontRotate), (self::$FontSize + self::$FontSpace) * ($i - 0.8), $topSpace, $fontColor, self::$FontFace, self::$AllowStr[$key]);
        }
        //写入session
        $_SESSION[self::$VarName] = $vcode;
        //输出图像
        imagejpeg($image);
        //释放内存
        imagedestroy($image);
    }
}