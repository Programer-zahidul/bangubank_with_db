<?php
namespace App;
// define("DB_HOST", "localhost");
// define("DB_NAME", "bangubank");
// define("DB_USERNAME", "root");
// define("DB_PASSWORD", "");
// define("USE_STORAGE", "isDatabase");

class Config
{
    // private $DB_HOST = "localhost";
    // private $DB_NAME = "bangubank";
    // private $DB_USERNAME = "root";
    // private $DB_PASSWORD = "";

    // // use storage is two type [isFile or isDatabase]
    // private $USE_STORAGE = "isDatabase";

    public static function DB_HOST()
    {
        return "localhost";
    }
    public static function DB_NAME()
    {
        return "bangubank";
    }
    public static function DB_USERNAME()
    {
        return "root";
    }
    public static function DB_PASSWORD()
    {
        return "";
    }
    // use storage is two type [isFile or isDatabase]
    public static function USE_STORAGE()
    {
        return "isDatabase";
    }

}