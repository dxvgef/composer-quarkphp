<?php
namespace QuarkPHP;

class Upload {
    //�ϴ�����
    static public $option = array(
        'inputName' => '',        //�ϴ��ؼ���nameֵ
        'allowMIME' => array(),    //�����ϴ����ļ�MIMEֵ
        'allowSize' => 1024,        //�����ϴ����ļ���С��KB��
        'convertName' => 1,        //�Ƿ�ת���ļ�����ĸ��Сд��[0��ת��/1Сд/2��д]
        'savePath' => '',            //�ϴ��󱣴�ľ���·�������� ROOT_PATH ����
        'saveName' => '',            //�ϴ��󱣴���ļ�����������׺��
    );

    //ִ���ϴ�
    static function start() {
        //���û�ж����ļ���С����
        if (self::$option['allowSize'] == 0) {
            return 'allowSize����ֵ��Ч(' . self::$option['allowSize'] . ')';
        }
        //���û�ж��屣���ļ���
        if (self::$option['saveName'] == '') {
            return 'saveName����ֵ��Ч(' . self::$option['saveName'] . ')';
        }
        //���û���ϴ�����
        if (!isset($_FILES[self::$option['inputName']])) {
            return '��ѡ��Ҫ�ϴ����ļ�';
        }

        //��ȡԭʼ�ļ���
        $srcName = $_FILES[self::$option['inputName']]['name'];
        //��ȡԭʼ�ļ���չ��
        $srcSuffix = pathinfo($srcName, PATHINFO_EXTENSION);
        //��ȡԭʼ�ļ���С
        $srcSize = $_FILES[self::$option['inputName']]['size'];
        //��ȡԭʼ�ļ�MIMEֵ
        $srcMIME = $_FILES[self::$option['inputName']]['type'];

        //����ļ���С
        if (self::$option['allowSize'] < $srcSize) {
            return '�ļ���С��������(' . $srcSize . ')';
        }
        //����ļ�MIMEֵ
        if (empty(self::$option['allowMIME']) == false && in_array($srcMIME, self::$option['allowMIME'], true) == false) {
            return '�������ϴ������͵��ļ�(' . $srcMIME . ')';
        }

        //���Ŀ¼������
        if (!is_dir(self::$option['savePath'])) {
            //����Ŀ¼
            if (!mkdir(self::$option['savePath'])) {
                return '�޷������ļ�����Ŀ¼(' . self::$option['savePath'] . ')';
            }
        }

        //ת���ļ�����Сд
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

        //��ȡ�ϴ������ʱ�ļ���
        $tmpName = $_FILES[self::$option['inputName']]['tmp_name'];
        //�����ļ�
        if (!move_uploaded_file($tmpName, self::$option['savePath'] . '/' . self::$option['saveName'] . '.' . $srcSuffix)) {
            return '�ļ��ϴ�ʧЧ������Ŀ¼Ȩ��';
        }

        //��ȡ�ϴ����
        $filesError = $_FILES[self::$option['inputName']]['error'];

        //�ж��ϴ����
        switch ($filesError) {
            case 0:
                return self::$option['saveName'] . '.' . $srcSuffix;
                break;
            case 1:
                return '�ļ���С(' . $srcSize . ')����PHP������';
                break;
            case 2:
                return '�ļ���С����HTML����ָ��������';
                break;
            case 3:
                return '�ļ�δ�����ϴ�';
                break;
            case 4:
                return '�ļ��ϴ�ʧ��';
                break;
            case 5:
                return '�ϴ��ļ��Ĵ�СΪ0';
                break;
        }
        return true;
    }
}