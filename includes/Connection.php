<?php

namespace App;

use PDO;
use PDOException;

// use singleton pattern
class Connection
{

    private $conn;
    public $config;

    public static function __self()
    {
        // $config = parse_ini_file("../config.ini");
        $dhost = Config::DB_HOST();
        $dname = Config::DB_NAME();
        $db_name = "mysql:host={$dhost};dbname={$dname};";
        $user_name = Config::DB_USERNAME();
        $password = Config::DB_PASSWORD();
        if (!isset($conn)) {
            try {
                $conn = new PDO($db_name, $user_name, $password);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("): Oops! Connection fail." . $e->getMessage());
            }
            return $conn;
        }
        return $conn;
    }

    public static function isFile(): bool
    {
        // $config = parse_ini_file("../config.ini");
        return Config::USE_STORAGE() === "isFile";
    }

    public static function isDB(): bool
    {
        // $config = parse_ini_file("../config.ini");
        return Config::USE_STORAGE() === "isDatabase";
    }

}
