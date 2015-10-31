<?php
namespace QuarkPHP;

//日志记录类
class Logger {

    //日志记录级别（留空则不记录）
    public static $Level = '';
    public static $Path = '';

    public static function Debug($msg = '') {
        $level = self::level();
        if ($level > 0) {
            $trace = debug_backtrace()[0];
            $info['level'] = 'debug';
            $info['file'] = $trace['file'];
            $info['line'] = $trace['line'];
            $info['msg'] = $msg;
            self::output($info);
        }
    }

    public static function Info($msg = '') {
        $level = self::level();
        if ($level > 0 && $level <= 2) {
            $trace = debug_backtrace()[0];
            $info['level'] = 'info';
            $info['file'] = $trace['file'];
            $info['line'] = $trace['line'];
            $info['msg'] = $msg;
            self::output($info);
        }
    }

    public static function Warn($msg = '') {
        $level = self::level();
        if ($level > 0 && $level <= 3) {
            $trace = debug_backtrace()[0];
            $info['level'] = 'warn';
            $info['file'] = $trace['file'];
            $info['line'] = $trace['line'];
            $info['msg'] = $msg;
            self::output($info);
        }
    }

    public static function Error($msg = '') {
        $level = self::level();
        if ($level > 0 && $level <= 4) {
            $trace = debug_backtrace()[0];
            $info['level'] = 'error';
            $info['file'] = $trace['file'];
            $info['line'] = $trace['line'];
            $info['msg'] = $msg;
            self::output($info);
        }
    }

    public static function Fatal($msg = '') {
        $level = self::level();
        if ($level > 0 && $level <= 5) {
            $trace = debug_backtrace()[0];
            $info['level'] = 'fatal';
            $info['file'] = $trace['file'];
            $info['line'] = $trace['line'];
            $info['msg'] = $msg;
            self::output($info);
        }
    }

    private static function level() {
        switch (self::$Level) {
            case 'debug':
                return 1;
                break;
            case 'info':
                return 2;
                break;
            case 'warn':
                return 3;
                break;
            case 'error':
                return 4;
                break;
            case 'fatal':
                return 5;
                break;
            default:
                return 0;
                break;
        }
    }

    //写日志文件
    private static function output($info = array()) {
        $fp = fopen(ROOT_PATH . '/' . self::$Path . '/' . date('Y-m-d') . '.txt', 'a');
        flock($fp, LOCK_EX | LOCK_NB);
        $content = date('Y-m-d H:i') . ' | ' . $info['level'] . ' | ' . $info['file'] . ' | ' . $info['line'] . "\n" . $_SERVER['REQUEST_URI'] . "\n" . $info['msg'] . "\n\n";
        fwrite($fp, $content);
        flock($fp, LOCK_UN);
        fclose($fp);
    }
}