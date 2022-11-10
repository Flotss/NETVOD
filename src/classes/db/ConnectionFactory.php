<?php

namespace iutnc\NetVOD\db;

use PDO;

/**
 * Class ConnectionFactory
 * Cette classe permet de créer une connexion à la base de données
 */
class ConnectionFactory
{
    /**
     * @var array $config tableau de configuration
     */
    public static $config = [];

    /**
     * @var PDO $db La connexion à la base de données
     */
    public static $db = null;


    /**
     * Methode de configuration de la connexion à la base de données
     * @param $file string chemin du fichier de configuration
     * @return void
     */
    public static function setConfig($file)
    {
        self::$config = parse_ini_file($file);
    }


    /**
     * Methode de création de la connexion à la base de données
     * @return PDO La connexion à la base de données
     */
    public static function makeConnection() : PDO
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

    /**
     * Methode de fermeture de la connexion à la base de données
     * @return void
     */
    public static function close()
    {
        self::$db = null;
    }

}

?>