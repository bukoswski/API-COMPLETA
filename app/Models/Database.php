<?php

namespace App\Models;

define("DB_SERVER", "localhost");
define("DB_USER", "root");
define("DB_PASSWORD", "");
define("DB_NAME", "api");

class Database
{

    private static $Connection;
    private function __construct() {}

    public static function getConnect()
    {

        if (is_null(self::$Connection)) {

            self::$Connection = new \mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME);
            if (self::$Connection->connect_error) {
                die("Connection Failed" . self::$Connection->connect_error);
            }
        }
        return self::$Connection;
    }
}