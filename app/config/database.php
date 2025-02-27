<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'job_submissions');
define('DB_USER', 'root');
define('DB_PASS', '');

class Database
{
    private static $connection;

    public static function getConnection()
    {
        if (!self::$connection) {
            try {
                self::$connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Database connection failed: " . $e->getMessage());
            }
        }
        return self::$connection;
    }
}
