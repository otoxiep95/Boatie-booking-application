<?php


//Singleton Database class (static class)
class Database
{

    protected static $conn = null;

    //Singleton's should not have these basic classes
    private function __construct()
    { }
    private function __clone()
    { }
    private function __destruct()
    { }

    /**
     * Connect to the database
     * 
     * @return PDO object
     */

    public static function connect()
    {
        if (self::$conn === null) {
            $conf = require(__DIR__ . '/../config/db-config.php'); //require database login credentials from the config file

            $dsn = "mysql:host={$conf->host};port={$conf->port};dbname={$conf->database};charset=utf8mb4";

            $conn = new PDO($dsn, $conf->username, $conf->password, $conf->options);

            static::$conn = $conn; //static
            return $conn;
        } else {
            return self::$conn;
        }
    }


    /**
     * Disconnect from the database !!! NOT NEEDED - REMOVE IN FUTURE PROJECTS !!
     * 
     * @return null
     */
    public static function disconnect()
    {
        if (self::$conn != null) {
            self::$conn = null;
        }
        return null;
    }
}
