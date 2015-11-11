<?php
namespace QuarkPHP;

//调度器
class Dispatcher {
    public static $htmlCache = false;
    public static $htmlCachePath = '';
    public static $controllerPath = '/controller';

    //执行调度
    static public function Run($controller, $routerParams = array()) {
        //判断并读取HTML缓存文件
        if (self::$htmlCache == true) {
            self::getHTMLCache();
        }

        //解析控制器信息
        $info = self::parseController($controller);
        //判断控制器文件是否存在
        if (!file_exists($info['path'])) {
            echo '控制器文件' . $info['path'] . '不存在';
            exit();
        }
        //载入控制器文件
        require_once($info['path']);
        //判断控制器类及方法是否存在
        if (!method_exists($info['class'], $info['func'])) {
            echo '控制器文件' . $info['path'] . '的' . $info["class"] . '类或其' . $info["func"] . '方法不存在';
            exit();
        }

        //把路由参数传入到
        Base::$RouteParams =& $routerParams;

        //打开输出缓存
        ob_start();

        //执行控制器
        $info['class']::$info['func']();

        //自动执行视图
        switch (Base::$ViewType) {
            case 'html':
                if (Base::$ViewFile != '') {
                    Base::ShowHTML(Base::$ViewFile, Base::$ViewData);
                }
                break;
            case 'json';
				header('Content-type: text/json; charset=utf-8');
				echo json_encode(Base::$ViewData);
                return;
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
        $content = ob_get_contents();//取得php页面输出的全部内容
        $fp = fopen(ROOT_PATH . '/' . Dispatcher::$htmlCachePath . '/' . $code . '.html', 'w'); //创建一个文件，并打开，准备写入
        fwrite($fp, $content); //把php页面的内容全部写入output00001.html，然后……
        fclose($fp);
    }

    //解析控制器文件路径、类名、方法名
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