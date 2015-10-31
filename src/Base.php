<?php
namespace QuarkPHP;
//�汾��
//public static $Version = '1.0.0';

//�ɱ��������̳еĻ��࣬ʵ�� ��ȡ·�ɲ���ֵ��ִ����ͼ������ģ�͡��������ȹ���
class Base {

    //����·�ɲ����ı���
    public static $RouteParams = array();
    public static $ModelPath = '/model';

    //��ͼ�ļ�Ŀ¼
    public static $ViewPath = '';

    //��ͼ���ͣ���Ϊ��������ͼ��HTML������ͼ�ļ���JSON���JSON��ʽ
    public static $ViewType = '';
    //��ͼ����
    public static $ViewData = array();
    //��ͼ�ļ�������$viewType='html'ʱ��Ч
    public static $ViewFile = '';

    //�ֶ�����HTML��ͼ
    public static function ShowHTML($viewFile, $viewData = array()) {
        $viewFile = ROOT_PATH . '/view/' . $viewFile;
        if (file_exists($viewFile)) {
            if (!empty($viewData)) {
                extract($viewData, EXTR_OVERWRITE);
            }
            include($viewFile);
        } else {
            echo '��ͼ�ļ�' . $viewFile . '������';
            exit();
        }
    }

    //�ֶ�����JSON��ͼ
    public static function ShowJSON($viewData = array()) {
        echo json_encode($viewData);
    }

    //����ģ��
    public static function Model($quarkModelFile) {
        $quarkModelInfo = self::parsePath($quarkModelFile);
        $quarkModelFile = ROOT_PATH . '/model/' . $quarkModelInfo['path'] . '/' . $quarkModelInfo["class"] . '.php';
        if (file_exists($quarkModelFile)) {
            require_once($quarkModelFile);
        } else {
            echo 'ģ���ļ�' . $quarkModelFile . '������';
            exit();
        }
    }

    //�����ļ�·��������
    private static function parsePath($path) {
        $pathinfo = pathinfo($path);
        $return["path"] = ($pathinfo["dirname"] == DIRECTORY_SEPARATOR || $pathinfo["dirname"] == '.') ? "" : $pathinfo["dirname"];
        $return["class"] = ($pathinfo["filename"] == "") ? "" : $pathinfo["filename"];
        return $return;
    }
}