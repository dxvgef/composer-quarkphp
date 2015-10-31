<?php
namespace QuarkPHP;

class VerifyCode {
    //-------------- ��֤����Ʋ��� -------------------
    public static $ImageWidth = 85; //ͼƬ���
    public static $ImageHeight = 25; //ͼƬ�߶�
    public static $ImageBgcolor = array(255, 255, 255); //ͼƬ�ı�����ɫ
    public static $StrCount = 4; //��ʾ�ַ�����
    public static $FontFace = '/data/www/simhei.ttf'; //�����ļ��ľ���·��
    public static $FontSize = 18; //���ִ�С(����)
    public static $FontRotate = 30; //������ת�Ƕ�(0-180)
    public static $FontSpace = 3; //���ּ��
    public static $DisturbLine = 15; //������������
    public static $DisturbPixel = 100; //�����������
    public static $VarName = 'vcode';   //��֤��session����������
    //������ֵ��ַ��������Ǻ���
    public static $AllowStr = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'J', 'K', 'M', 'P', 'R', 'S', 'U', 'W', 'X', 'Y', 'Z');

    public static function show() {
        //�������
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: no-cache');
        header('Content-type: image/jpeg');

        $vcode = '';
        //�������еĿ��������һ��ͼ��
        $image = imagecreatetruecolor(self::$ImageWidth, self::$ImageHeight);
        //����ͼ��ı���ɫ
        $bgColor = imagecolorallocate($image, self::$ImageBgcolor[0], self::$ImageBgcolor[1], self::$ImageBgcolor[2]);
        //���߿����һ������
        imagerectangle($image, 1, 1, self::$ImageWidth, self::$ImageHeight, $bgColor);
        //��䱳����ɫ
        imagefill($image, 0, 0, $bgColor);

        //ѭ�����Ƹ�������
        for ($i = 0; $i < self::$DisturbLine; $i++) {
            //�������������ɫ
            $lineColor = imagecolorallocate($image, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
            //���λ�ã��ǶȺͻ�������������
            imagearc($image, mt_rand(-10, self::$ImageWidth), mt_rand(-10, self::$ImageHeight), mt_rand(30, 300), mt_rand(20, 200), 55, 44, $lineColor);
        }

        //ѭ�����Ƹ������
        for ($i = 0; $i < self::$DisturbPixel; $i++) {
            //������������ɫ
            $pixelColor = imagecolorallocate($image, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
            //�������
            imagesetpixel($image, mt_rand(0, self::$ImageWidth), mt_rand(0, self::$ImageHeight), $pixelColor);
        }

        //��Ԥ�����ַ������������ȡ��ָ�������ļ����������Ƶ�һ���µ�����
        $randKey = array_rand(self::$AllowStr, self::$StrCount);

        //�������ֵĶ���
        $topSpace = self::$ImageHeight - ((self::$ImageHeight - self::$FontSize) / 2);
        $i = 0;
        //ѭ�������ַ�
        foreach ($randKey as $key) {
            $i++;
            //ƴ���ַ�
            $vcode .= self::$AllowStr[$key];
            //��������ַ���ɫ
            $fontColor = imagecolorallocate($image, mt_rand(0, 170), mt_rand(0, 170), mt_rand(0, 170));
            //д�����֣�ͼ�����ִ�С����ת�Ƕȣ����ּ�࣬���ֶ��࣬������ɫ�����壬�ַ���
            imagettftext($image, self::$FontSize, mt_rand(-self::$FontRotate, self::$FontRotate), (self::$FontSize + self::$FontSpace) * ($i - 0.8), $topSpace, $fontColor, self::$FontFace, self::$AllowStr[$key]);
        }
        //д��session
        $_SESSION[self::$VarName] = $vcode;
        //���ͼ��
        imagejpeg($image);
        //�ͷ��ڴ�
        imagedestroy($image);
    }
}