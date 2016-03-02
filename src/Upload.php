<?php
namespace QuarkPHP;

class Upload {
    //上传参数
    static public $option = array(
        'inputName' => '',        //上传控件的name值
        'allowMIME' => array(),    //允许上传的文件MIME值
        'allowSize' => 1024,        //允许上传的文件大小（KB）
        'convertName' => 1,        //是否转换文件名字母大小写，[0不转换/1小写/2大写]
        'savePath' => '',            //上传后保存的绝对路径，基于 ROOT_PATH 常量
        'saveName' => '',            //上传后保存的文件名，不含后缀名
    );

    //执行上传
    static function start() {
        $result = array(
            'filename' => '',
            'error' => '',
        );
        //如果没有定义文件大小限置
        if (self::$option['allowSize'] == 0) {
            $result['error'] = 'allowSize属性值无效(' . self::$option['allowSize'] . ')';
            return $result;
        }
        //如果没有定义保存文件名
        if (self::$option['saveName'] == '') {
            $result['error'] = 'saveName属性值无效(' . self::$option['saveName'] . ')';
            return $result;
        }
        //如果没有上传数据
        if (!isset($_FILES[self::$option['inputName']])) {
            $result['error'] = '请选择要上传的文件';
            return $result;
        }

        //获取原始文件名
        $srcName = $_FILES[self::$option['inputName']]['name'];
        //获取原始文件扩展名
        $srcSuffix = pathinfo($srcName, PATHINFO_EXTENSION);
        //获取原始文件大小
        $srcSize = $_FILES[self::$option['inputName']]['size'];
        //获取原始文件MIME值
        $srcMIME = $_FILES[self::$option['inputName']]['type'];

        //检查文件大小
        if (self::$option['allowSize'] < $srcSize) {
            $result['error'] = '文件大小超出限制(' . $srcSize . ')';
            return $result;
        }
        //检查文件MIME值
        if (empty(self::$option['allowMIME']) == false && in_array($srcMIME, self::$option['allowMIME'], true) == false) {
            $result['error'] = '不允许上传该类型的文件(' . $srcMIME . ')';
            return $result;
        }

        //如果目录不存在
        if (!is_dir(self::$option['savePath'])) {
            //创建目录
            if (!mkdir(self::$option['savePath'])) {
                $result['error'] = '无法创建文件保存目录(' . self::$option['savePath'] . ')';
                return $result;
            }
        }

        //转换文件名大小写
        switch (self::$option['convertName']) {
            case 1:
                self::$option['saveName'] = strtolower(self::$option['saveName']);
                $srcSuffix = strtolower($srcSuffix);
                break;
            case 2:
                self::$option['saveName'] = strtoupper(self::$option['saveName']);
                $srcSuffix = strtoupper($srcSuffix);
                break;
        }

        //获取上传后的临时文件名
        $tmpName = $_FILES[self::$option['inputName']]['tmp_name'];
        //保存文件
        if (!move_uploaded_file($tmpName, self::$option['savePath'] . '/' . self::$option['saveName'] . '.' . $srcSuffix)) {
            $result['error'] = '文件上传失效，请检查目录权限';
            return $result;
        }

        //获取上传结果
        $filesError = $_FILES[self::$option['inputName']]['error'];

        //判断上传结果
        switch ($filesError) {
            case 0:
                $result['filename'] = self::$option['saveName'] . '.' . $srcSuffix;
                return $result;
                break;
            case 1:
                $result['error'] = '文件大小(' . $srcSize . ')超出PHP的限制';
                return $result;
                break;
            case 2:
                $result['error'] = '文件大小超出HTML表单中指定的限制';
                return $result;
                break;
            case 3:
                $result['error'] = '文件未完整上传';
                return $result;
                break;
            case 4:
                $result['error'] = '文件上传失败';
                return $result;
                break;
            case 5:
                $result['error'] = '上传文件的大小为0';
                return $result;
                break;
        }
        return $result;
    }
}