<?php
namespace QuarkPHP;

//可被控制器继承的基类，实现 获取路由参数值、执行视图、载入模型、载入插件等功能
class Base {

    //接收路由参数的变量
    public static $RouteParams = array();
    public static $ModelPath = '/model';

    //视图文件目录
    public static $ViewPath = '';

    //视图类型：空为不加载视图，HTML加载视图文件，JSON输出JSON格式
    public static $ViewType = '';
    //视图变量
    public static $ViewData = array();
    //视图文件，仅在$viewType='html'时有效
    public static $ViewFile = '';

    //手动载入HTML视图
    public static function ShowHTML($viewFile, $viewData = array()) {
        $viewFile = ROOT_PATH . '/view/' . $viewFile;
        $data = array_merge($viewData, Base::$ViewData);
        if (file_exists($viewFile)) {
            if (!empty($data)) {
                extract($data, EXTR_OVERWRITE);
            }
            include($viewFile);
        } else {
            echo '视图文件' . $viewFile . '不存在';
            exit();
        }
    }

    //手动载入JSON视图
    public static function ShowJSON($viewData = array()) {
		header('Content-type: text/json; charset=utf-8');
        $data = array_merge($viewData, Base::$ViewData);
        echo json_encode($data);
    }

    //载入模型
    public static function Model($quarkModelFile) {
        $quarkModelInfo = self::parsePath($quarkModelFile);
        $quarkModelFile = ROOT_PATH . '/model/' . $quarkModelInfo['path'] . '/' . $quarkModelInfo["class"] . '.php';
        if (file_exists($quarkModelFile)) {
            require_once($quarkModelFile);
        } else {
            echo '模型文件' . $quarkModelFile . '不存在';
            exit();
        }
    }

	//重定向
    public static function Redir($url = '') {
        header('Location: ' . $url);
        exit();
    }

    //输出信息并中断运行
    public static function Abort($code = 404) {
        switch ($code) {
            case 404:
                header("HTTP/1.1 404 Not Found");
                header("Status: 404 Not Found");
                break;
            case 500:
                header("HTTP/1.1 500 Internal Server Error");
                header("Status: 500 Internal Server Error");
                break;
        }
        exit();
    }
	
    //解析文件路径和类名
    private static function parsePath($path) {
        $pathinfo = pathinfo($path);
        $return["path"] = ($pathinfo["dirname"] == DIRECTORY_SEPARATOR || $pathinfo["dirname"] == '.') ? "" : $pathinfo["dirname"];
        $return["class"] = ($pathinfo["filename"] == "") ? "" : $pathinfo["filename"];
        return $return;
    }
}