<?php
namespace QuarkPHP;

//连接器类
class Connect {
    //MySQL连接参数
    public static $MySQLconfig = array(
        0 => array(
            'host' => '127.0.0.1',      //主机
            'port' => 3306,             //端口
            'user' => 'root',           //账号
            'pwd' => 'password',        //密码
            'database' => 'test',       //数据库
            'charset' => 'utf-8',       //编码
            'timeout' => 10,            //超时
            'persistent' => false      //持久连接
        )
    );

    //PostgreSQL连接参数
    public static $PGSQLconfig = array(
        0 => array(
            'host' => '127.0.0.1',      //主机
            'port' => 5432,             //端口
            'user' => 'postgres',       //账号
            'pwd' => 'password',        //密码
            'database' => 'test',       //数据库
            'timeout' => 10,            //超时
            'persistent' => false      //持久连接
        )
    );

    //Redis连接参数
    public static $RedisConfig = array(
        0 => array(
            'host' => '127.0.0.1',      //主机
            'port' => 5432,             //端口
            'pwd' => 'password',        //密码
            'database' => 0,            //数据库序号
            'timeout' => 10             //超时
        )
    );

    //MongoDB连接参数
    public static $MongodbConfig = array(
        0 => array(
            'host' => '127.0.0.1',      //主机
            'port' => 5432,             //端口
            'user' => 'user',           //账号
            'pwd' => 'password',        //密码
            'database' => ''           //数据库序号
        )
    );

    //Memcached连接参数
    public static $MemcachedConfig = array(
        0 => array(
            'host' => '127.0.0.1',      //主机
            'port' => 5432,             //端口
            'user' => 10                //超时
        )
    );

    //创建MySQL数据库连接并返回连接对象
    public static function MySQL($configIndex = 0) {
        if (!class_exists('pdo')) {
            Logger::Error('不支持PDO扩展');
            return false;
        }

        try {
            $dsn = 'mysql:host=' . self::$MySQLconfig[$configIndex]['host'] . ';port=' . self::$MySQLconfig[$configIndex]['port'] . ';dbname=' . self::$MySQLconfig[$configIndex]['database'] . ';charset=' . self::$MySQLconfig[$configIndex]['charset'];
            $obj = new PDO($dsn, self::$MySQLconfig[$configIndex]['user'], self::$MySQLconfig[$configIndex]['pwd'], array(PDO::ATTR_TIMEOUT => self::$MySQLconfig[$configIndex]['timeout'], PDO::ATTR_PERSISTENT => self::$MySQLconfig[$configIndex]['persistent']));
            //关闭本地变量值处理，由mysql来转换绑定参数的变量值类型，防止SQL注入
            $obj->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            return $obj;
        } catch (PDOException $e) {
            Logger::Error($e->getMessage());
            return false;
        }
    }

    //创建PostgreSQL数据库连接并返回连接对象
    public static function PGSQL($configIndex = 0) {
        if (!class_exists('pdo')) {
            Logger::Error('PDO组件不存在');
            return false;
        }

        try {
            $dsn = 'pgsql:host=' . self::$PGSQLconfig[$configIndex]['host'] . ';port=' . self::$PGSQLconfig[$configIndex]['port'] . ';dbname=' . self::$PGSQLconfig[$configIndex]['database'];
            $obj = new PDO($dsn, self::$PGSQLconfig[$configIndex]['user'], self::$PGSQLconfig[$configIndex]['pwd'], array(PDO::ATTR_TIMEOUT => self::$PGSQLconfig[$configIndex]['timeout'], PDO::ATTR_PERSISTENT => self::$PGSQLconfig[$configIndex]['persistent']));
            //关闭本地变量值处理，由mysql来转换绑定参数的变量值类型，防止SQL注入
            $obj->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            return $obj;
        } catch (PDOException $e) {
            Logger::Error($e->getMessage());
            return false;
        }
    }

    //创建Redis连接并返回连接对象
    public static function Redis($configIndex = 0) {
        if (!class_exists('Redis')) {
            Logger::Error('不支持Redis扩展');
            return false;
        }

        $obj = new Redis();

        if ($obj->connect(self::$RedisConfig[$configIndex]['host'], self::$RedisConfig[$configIndex]['port'], self::$RedisConfig[$configIndex]['timeout'])) {
            if (self::$RedisConfig[$configIndex]['database'] != 0) {
                $obj->select(self::$RedisConfig[$configIndex]['database']);
            }
            if (self::$RedisConfig[$configIndex]['pwd'] != '') {
                $obj->auth(self::$RedisConfig[$configIndex]['pwd']);
            }
            return $obj;
        } else {
            Logger::Error('无法连接Redis服务器' . self::$RedisConfig[$configIndex]['host']);
            return false;
        }
    }

    //创建Mongodb连接并返回连接对象
    public static function Mongodb($configIndex = 0) {
        if (!class_exists('Mongo')) {
            Logger::Error('不支持Mongo扩展');
            return false;
        }

        $dsn = 'mongodb://' . self::$MongodbConfig[$configIndex]['user'] . ':' . self::$MongodbConfig[$configIndex]['pwd'] . '@' . self::$MongodbConfig[$configIndex]['host'] . ':' . self::$MongodbConfig[$configIndex]['port'] . '/' . self::$MongodbConfig[$configIndex]['database'];
        $conn = new Mongo($dsn);

        try {
            $obj = $conn->self::$MongodbConfig[$configIndex]['database'];
            return $obj;
        } catch (MongoConnectionException $e) {
            Logger::Error($e->getMessage());
            return false;
        }

    }

    //创建Memcached连接并返回连接对象
    public static function Memcached($configIndex = 0) {
        if (!class_exists('memcache')) {
            Logger::Error('不支持memcache扩展');
            return false;
        }

        $obj = new Memcache();

        if ($obj->connect(self::$RedisConfig[$configIndex]['host'], self::$RedisConfig[$configIndex]['port'], self::$RedisConfig[$configIndex]['timeout'])) {
            return $obj;
        } else {
            Logger::Error('无法连接Mmecached服务器' . self::$MemcachedConfig[$configIndex]['host']);
            return false;
        }
    }
}