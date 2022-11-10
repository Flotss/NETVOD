<?php

namespace iutnc\NetVOD\db;

use PDO;

class ConnectionFactory
{
    public static $config = [];
    public static $db = null;

    public static function setConfig($file)
    {
        self::$config = parse_ini_file($file);
    }

    public static function makeConnection()
    {
        if (is_null(self::$db)) {
            $dsn = self::$config['driver'] . ':host=' . self::$config['host'] . ';dbname=' . self::$config['database'];

            self::$db = new PDO($dsn, self::$config['username'], self::$config['password'], [
                PDO::ATTR_PERSISTENT => true,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_STRINGIFY_FETCHES => true,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET utf8",]);

            return self::$db;
        }
        return self::$db;
    }

    public static function close()
    {
        self::$db = null;
    }

}

?>