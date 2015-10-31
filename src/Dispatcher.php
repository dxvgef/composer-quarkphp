<?php
namespace QuarkPHP;

//������
class Dispatcher {
    public static $htmlCache = false;
    public static $htmlCachePath = '';
    public static $controllerPath = '/controller';

    //ִ�е���
    static public function Run($controller, $routerParams = array()) {
        //�жϲ���ȡHTML�����ļ�
        if (self::$htmlCache == true) {
            self::getHTMLCache();
        }

        //������������Ϣ
        $info = self::parseController($controller);
        //�жϿ������ļ��Ƿ����
        if (!file_exists($info['path'])) {
            echo '�������ļ�' . $info['path'] . '������';
            exit();
        }
        //����������ļ�
        require_once($info['path']);
        //�жϿ������༰�����Ƿ����
        if (!method_exists($info['class'], $info['func'])) {
            echo '�������ļ�' . $info['path'] . '��' . $info["class"] . '�����' . $info["func"] . '����������';
            exit();
        }

        //��·�ɲ������뵽
        Base::$RouteParams =& $routerParams;

        //���������
        ob_start();

        //ִ�п�����
        $info['class']::$info['func']();

        //�Զ�ִ����ͼ
        switch (Base::$ViewType) {
            case 'html':
                if (Base::$ViewFile != '') {
                    Base::ShowHTML(Base::$ViewFile, Base::$ViewData);
                }
                break;
            case 'json';
                return json_encode(Base::$ViewData);
                break;
        }

        if (self::$htmlCache == true) {
            self::makeHTMLCache();
        }

    }

    private static function getHTMLCache() {
        if (self::$htmlCache == true) {
            $code = md5($_SERVER['REQUEST_URI']);
            $file = ROOT_PATH . '/' . Dispatcher::$htmlCachePath . '/' . $code . '.html';
            if (file_exists($file)) {
                readfile($file);
                exit();
            }
        }
    }

    private static function makeHTMLCache() {
        $code = md5($_SERVER['REQUEST_URI']);
        $file = ROOT_PATH . '/' . Dispatcher::$htmlCachePath . '/' . $code . '.html';
        $content = ob_get_contents();//ȡ��phpҳ�������ȫ������
        $fp = fopen(ROOT_PATH . '/' . Dispatcher::$htmlCachePath . '/' . $code . '.html', 'w'); //����һ���ļ������򿪣�׼��д��
        fwrite($fp, $content); //��phpҳ�������ȫ��д��output00001.html��Ȼ�󡭡�
        fclose($fp);
    }

    //�����������ļ�·����������������
    private static function parseController($path) {
        $pathinfo = pathinfo($path);
        $return["class"] = ($pathinfo["filename"] == "") ? "" : $pathinfo["filename"];
        $return["func"] = ($pathinfo["extension"] == "") ? "" : $pathinfo["extension"];
        if ($pathinfo["dirname"] == DIRECTORY_SEPARATOR || $pathinfo["dirname"] == '.') {
            $return["path"] = ROOT_PATH . '/controller/' . $return["class"] . '.php';
        } else {
            $return["path"] = ROOT_PATH . '/controller/' . $return["path"] . '/' . $return["class"] . '.php';
        }
        return $return;
    }
}