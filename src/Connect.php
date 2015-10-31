<?php
namespace QuarkPHP;

//��������
class Connect {
    //MySQL���Ӳ���
    public static $MySQLconfig = array(
        0 => array(
            'host' => '127.0.0.1',      //����
            'port' => 3306,             //�˿�
            'user' => 'root',           //�˺�
            'pwd' => 'password',        //����
            'database' => 'test',       //���ݿ�
            'charset' => 'utf-8',       //����
            'timeout' => 10,            //��ʱ
            'persistent' => false      //�־�����
        )
    );

    //PostgreSQL���Ӳ���
    public static $PGSQLconfig = array(
        0 => array(
            'host' => '127.0.0.1',      //����
            'port' => 5432,             //�˿�
            'user' => 'postgres',       //�˺�
            'pwd' => 'password',        //����
            'database' => 'test',       //���ݿ�
            'timeout' => 10,            //��ʱ
            'persistent' => false      //�־�����
        )
    );

    //Redis���Ӳ���
    public static $RedisConfig = array(
        0 => array(
            'host' => '127.0.0.1',      //����
            'port' => 5432,             //�˿�
            'pwd' => 'password',        //����
            'database' => 0,            //���ݿ����
            'timeout' => 10             //��ʱ
        )
    );

    //MongoDB���Ӳ���
    public static $MongodbConfig = array(
        0 => array(
            'host' => '127.0.0.1',      //����
            'port' => 5432,             //�˿�
            'user' => 'user',           //�˺�
            'pwd' => 'password',        //����
            'database' => ''           //���ݿ����
        )
    );

    //Memcached���Ӳ���
    public static $MemcachedConfig = array(
        0 => array(
            'host' => '127.0.0.1',      //����
            'port' => 5432,             //�˿�
            'user' => 10                //��ʱ
        )
    );

    //����MySQL���ݿ����Ӳ��������Ӷ���
    public static function MySQL($configIndex = 0) {
        if (!class_exists('pdo')) {
            Logger::Error('��֧��PDO��չ');
            return false;
        }

        try {
            $dsn = 'mysql:host=' . self::$MySQLconfig[$configIndex]['host'] . ';port=' . self::$MySQLconfig[$configIndex]['port'] . ';dbname=' . self::$MySQLconfig[$configIndex]['database'] . ';charset=' . self::$MySQLconfig[$configIndex]['charset'];
            $obj = new PDO($dsn, self::$MySQLconfig[$configIndex]['user'], self::$MySQLconfig[$configIndex]['pwd'], array(PDO::ATTR_TIMEOUT => self::$MySQLconfig[$configIndex]['timeout'], PDO::ATTR_PERSISTENT => self::$MySQLconfig[$configIndex]['persistent']));
            //�رձ��ر���ֵ������mysql��ת���󶨲����ı���ֵ���ͣ���ֹSQLע��
            $obj->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            return $obj;
        } catch (PDOException $e) {
            Logger::Error($e->getMessage());
            return false;
        }
    }

    //����PostgreSQL���ݿ����Ӳ��������Ӷ���
    public static function PGSQL($configIndex = 0) {
        if (!class_exists('pdo')) {
            Logger::Error('PDO���������');
            return false;
        }

        try {
            $dsn = 'pgsql:host=' . self::$PGSQLconfig[$configIndex]['host'] . ';port=' . self::$PGSQLconfig[$configIndex]['port'] . ';dbname=' . self::$PGSQLconfig[$configIndex]['database'];
            $obj = new PDO($dsn, self::$PGSQLconfig[$configIndex]['user'], self::$PGSQLconfig[$configIndex]['pwd'], array(PDO::ATTR_TIMEOUT => self::$PGSQLconfig[$configIndex]['timeout'], PDO::ATTR_PERSISTENT => self::$PGSQLconfig[$configIndex]['persistent']));
            //�رձ��ر���ֵ������mysql��ת���󶨲����ı���ֵ���ͣ���ֹSQLע��
            $obj->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            return $obj;
        } catch (PDOException $e) {
            Logger::Error($e->getMessage());
            return false;
        }
    }

    //����Redis���Ӳ��������Ӷ���
    public static function Redis($configIndex = 0) {
        if (!class_exists('Redis')) {
            Logger::Error('��֧��Redis��չ');
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
            Logger::Error('�޷�����Redis������' . self::$RedisConfig[$configIndex]['host']);
            return false;
        }
    }

    //����Mongodb���Ӳ��������Ӷ���
    public static function Mongodb($configIndex = 0) {
        if (!class_exists('Mongo')) {
            Logger::Error('��֧��Mongo��չ');
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

    //����Memcached���Ӳ��������Ӷ���
    public static function Memcached($configIndex = 0) {
        if (!class_exists('memcache')) {
            Logger::Error('��֧��memcache��չ');
            return false;
        }

        $obj = new Memcache();

        if ($obj->connect(self::$RedisConfig[$configIndex]['host'], self::$RedisConfig[$configIndex]['port'], self::$RedisConfig[$configIndex]['timeout'])) {
            return $obj;
        } else {
            Logger::Error('�޷�����Mmecached������' . self::$MemcachedConfig[$configIndex]['host']);
            return false;
        }
    }
}