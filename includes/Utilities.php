<?php
namespace App;

use PDOException;
class Utilities
{

    public static function DbTableExists($table)
    {
        try {
            Connection::__self()->query("SELECT 1 FROM " . $table . " LIMIT 1");
        } catch (PDOException $e) {
            return true;
        }
        return false;
    }
}

